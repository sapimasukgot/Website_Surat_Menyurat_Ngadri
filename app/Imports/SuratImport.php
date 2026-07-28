<?php

namespace App\Imports;

use App\Models\JenisSurat;
use App\Models\Penduduk;
use App\Models\Surat;
use App\Models\User;
use App\Services\NomorSuratService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

/**
 * Restore riwayat surat dari berkas Excel hasil Export Riwayat Surat.
 *
 * Aturan: surat dikenali dari NOMOR SURAT — nomor yang belum ada akan
 * ditambahkan, nomor yang sudah ada diperbarui (aman diimpor berulang).
 * Jenis surat dicocokkan via Kode Surat, pemohon via NIK.
 */
class SuratImport implements ToCollection
{
    use Importable;

    public int $inserted = 0;

    public int $updated = 0;

    public int $failed = 0;

    public int $total = 0;

    public array $errors = [];

    private const ALIASES = [
        'nomor_surat' => ['nomor_surat', 'nomor', 'no_surat'],
        'kode_surat' => ['kode_surat', 'kode'],
        'nik' => ['nik_pemohon', 'nik'],
        'tanggal_surat' => ['tanggal_surat', 'tanggal'],
        'email' => ['dibuat_oleh_email', 'email', 'dibuat_oleh'],
        'created_at' => ['dibuat_pada'],
        'data_surat' => ['data_surat_json', 'data_surat', 'data'],
    ];

    public function __construct(private readonly User $fallbackUser)
    {
    }

    public function collection(Collection $rows): void
    {
        $map = null;

        foreach ($rows as $rowIndex => $row) {
            $values = array_values($row->all());

            if ($map === null) {
                $map = $this->detectHeader($values);

                continue;
            }

            if ($this->isBlank($values)) {
                continue;
            }

            $this->total++;
            $nomorBaris = (int) $rowIndex + 1;
            $data = $this->extract($values, $map);

            $error = $this->importRow($data);

            if ($error !== null) {
                $this->failed++;
                $this->errors[] = [
                    'baris' => $nomorBaris,
                    'nik' => (string) ($data['nomor_surat'] ?? '-'),
                    'pesan' => $error,
                ];
            }
        }
    }

    private function importRow(array $data): ?string
    {
        $nomor = trim((string) ($data['nomor_surat'] ?? ''));

        if ($nomor === '') {
            return 'Nomor surat kosong.';
        }

        $jenis = JenisSurat::withTrashed()
            ->where('kode_surat', trim((string) ($data['kode_surat'] ?? '')))
            ->first();

        if (! $jenis) {
            return 'Kode surat "'.($data['kode_surat'] ?? '-').'" tidak ditemukan pada Jenis Surat.';
        }

        $nik = preg_replace('/\D/', '', (string) ($data['nik'] ?? ''));
        $penduduk = $nik !== '' ? Penduduk::where('nik', $nik)->first() : null;

        if (! $penduduk) {
            return 'NIK pemohon "'.$nik.'" tidak ditemukan. Import data penduduk terlebih dahulu.';
        }

        $tanggal = $this->parseDate($data['tanggal_surat'] ?? null);

        if (! $tanggal) {
            return 'Tanggal surat tidak valid.';
        }

        $dataSurat = $this->parseDataSurat($data['data_surat'] ?? null);

        if ($dataSurat === null) {
            return 'Kolom Data Surat (JSON) tidak valid.';
        }

        $user = User::where('email', trim((string) ($data['email'] ?? '')))->first() ?? $this->fallbackUser;

        $attributes = [
            'jenis_surat_id' => $jenis->id,
            'penduduk_id' => $penduduk->id,
            'user_id' => $user->id,
            'tanggal_surat' => $tanggal,
            'data_surat' => $dataSurat,
        ];

        $surat = Surat::withTrashed()->where('nomor_surat', $nomor)->first();

        // Nomor urut dibaca ulang dari nomor suratnya. Tanpa ini, surat hasil
        // restore tidak terhitung saat menentukan nomor urut berikutnya —
        // penomoran bisa mundur dan menabrak nomor yang sudah pernah terpakai.
        // Kalau tidak terbaca, nilai yang sudah ada JANGAN ditimpa jadi kosong.
        $urut = $this->bacaUrut($nomor, $jenis) ?? $surat?->nomor_urut;

        if ($urut !== null) {
            $attributes['nomor_urut'] = $urut;
        }

        if ($surat) {
            if ($surat->trashed()) {
                $surat->restore();
            }

            $surat->fill($attributes);
            $isNew = false;
        } else {
            $surat = new Surat(array_merge(['nomor_surat' => $nomor], $attributes));
            $isNew = true;
        }

        // created_at bukan kolom fillable — harus di-set langsung.
        if ($createdAt = $this->parseDate($data['created_at'] ?? null, withTime: true)) {
            $surat->created_at = $createdAt;
        }

        $surat->save();
        $isNew ? $this->inserted++ : $this->updated++;

        return null;
    }

    /**
     * Nomor urut dari nomor surat, dibatasi ke rentang yang wajar supaya angka
     * hasil salah baca tidak menjadi patokan penomoran berikutnya.
     */
    private function bacaUrut(string $nomor, JenisSurat $jenis): ?int
    {
        $urut = app(NomorSuratService::class)->bacaUrut($nomor, $jenis);

        return $urut !== null && $urut >= 1 && $urut <= 99999 ? $urut : null;
    }

    private function parseDataSurat(mixed $raw): ?array
    {
        $raw = trim((string) $raw);

        if ($raw === '' || $raw === '[]' || $raw === '{}') {
            return [];
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : null;
    }

    private function parseDate(mixed $value, bool $withTime = false): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            try {
                return Carbon::instance(ExcelDate::excelToDateTimeObject((float) $value));
            } catch (\Throwable) {
                return null;
            }
        }

        $value = trim((string) $value);

        foreach ($withTime ? ['d-m-Y H:i:s', 'd/m/Y H:i:s', 'Y-m-d H:i:s'] : [] as $format) {
            try {
                return Carbon::createFromFormat($format, $value);
            } catch (\Throwable) {
                continue;
            }
        }

        foreach (['d-m-Y', 'd/m/Y', 'Y-m-d'] as $format) {
            try {
                return Carbon::createFromFormat($format, $value)->startOfDay();
            } catch (\Throwable) {
                continue;
            }
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    private function detectHeader(array $values): ?array
    {
        $normalized = array_map(fn ($v) => $this->slugify((string) $v), $values);
        $map = [];

        foreach (self::ALIASES as $field => $aliases) {
            foreach ($normalized as $index => $header) {
                if (in_array($header, $aliases, true)) {
                    $map[$field] = $index;

                    break;
                }
            }
        }

        // Minimal harus mengenali nomor surat + kode + NIK agar dianggap header.
        return isset($map['nomor_surat'], $map['kode_surat'], $map['nik']) ? $map : null;
    }

    private function extract(array $values, array $map): array
    {
        $data = [];

        foreach ($map as $field => $index) {
            $data[$field] = $values[$index] ?? null;
        }

        return $data;
    }

    private function isBlank(array $values): bool
    {
        foreach ($values as $v) {
            if (trim((string) $v) !== '') {
                return false;
            }
        }

        return true;
    }

    private function slugify(string $value): string
    {
        $value = strtolower(trim($value));

        return trim(preg_replace('/[^a-z0-9]+/', '_', $value), '_');
    }
}
