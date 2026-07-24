<?php

namespace App\Imports;

use App\Models\Penduduk;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class PendudukImport implements ToCollection
{
    use Importable;

    public int $inserted = 0;

    public int $updated = 0;

    public int $failed = 0;

    public int $total = 0;

    public array $errors = [];

    private const ALIASES = [
        'nik' => ['nik'],
        'no_kk' => ['no_kk', 'nokk', 'kk', 'nomor_kk', 'no_kartu_keluarga'],
        'nama_lengkap' => ['nama_lengkap', 'nama'],
        'tempat_lahir' => ['tempat_lahir', 'tempat'],
        'tanggal_lahir' => ['tanggal_lahir', 'tgl_lahir', 'tanggal'],
        'jenis_kelamin' => ['jenis_kelamin', 'jk', 'l_p', 'kelamin'],
        'golongan_darah' => ['gol_drh', 'golongan_darah', 'gol_darah', 'goldar', 'gol_dar'],
        'agama' => ['agama'],
        'pendidikan' => ['pendidikan', 'pendidikan_terakhir'],
        'pekerjaan' => ['pekerjaan'],
        'status_kawin' => ['status_kawin', 'status_perkawinan', 'status'],
        'status_hubungan' => ['shdk', 'status_hubungan', 'hubungan_keluarga', 'status_hubungan_dalam_keluarga', 'hubungan'],
        'nama_ayah' => ['nama_ayah', 'ayah'],
        'nama_ibu' => ['nama_ibu', 'ibu'],
        'alamat' => ['alamat'],
        'rt' => ['rt', 'no_rt'],
        'rw' => ['rw', 'no_rw'],
        'dusun' => ['dusun'],
        'no_hp' => ['no_hp', 'hp', 'no_telp', 'telepon', 'no_handphone', 'telp'],
    ];

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

            $validator = Validator::make($data, [
                'nik' => ['required', 'digits:16'],
                'no_kk' => ['required', 'digits:16'],
                'nama_lengkap' => ['required', 'string'],
                'jenis_kelamin' => ['required', 'in:L,P'],
                'status_kawin' => ['required', 'in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati'],
                'alamat' => ['required', 'string'],
            ]);

            if ($validator->fails()) {
                $this->failed++;
                $this->errors[] = [
                    'baris' => $nomorBaris,
                    'nik' => (string) ($data['nik'] ?? '-'),
                    'pesan' => implode('; ', $validator->errors()->all()),
                ];

                continue;
            }

            $existing = Penduduk::where('nik', $data['nik'])->exists();
            Penduduk::updateOrCreate(['nik' => $data['nik']], $data);

            $existing ? $this->updated++ : $this->inserted++;
        }
    }

    private function detectHeader(array $values): ?array
    {
        $slugs = array_map(fn ($v) => $this->slug($v), $values);

        if (! in_array('nik', $slugs, true)) {
            return null;
        }

        $map = [];

        foreach (self::ALIASES as $field => $keys) {
            foreach ($values as $i => $value) {
                if (in_array($this->slug($value), $keys, true)) {
                    $map[$field] = $i;

                    break;
                }
            }
        }

        return $map;
    }

    private function extract(array $values, array $map): array
    {
        $get = fn (string $field) => isset($map[$field]) ? ($values[$map[$field]] ?? null) : null;

        $alamat = trim((string) $get('alamat'));
        $dusunCol = $get('dusun');
        $dusun = filled($dusunCol) ? trim((string) $dusunCol) : $this->dusunFromAlamat($alamat);

        return [
            'nik' => preg_replace('/\D/', '', (string) $get('nik')),
            'no_kk' => preg_replace('/\D/', '', (string) $get('no_kk')),
            'nama_lengkap' => trim((string) $get('nama_lengkap')),
            'tempat_lahir' => $this->clean($get('tempat_lahir')),
            'tanggal_lahir' => $this->parseDate($get('tanggal_lahir')),
            'jenis_kelamin' => $this->normalizeGender($get('jenis_kelamin')),
            'golongan_darah' => $this->normalizeGolDarah($get('golongan_darah')),
            'agama' => $this->normalizeAgama($get('agama')),
            'pendidikan' => $this->clean($get('pendidikan')),
            'pekerjaan' => $this->clean($get('pekerjaan')),
            'status_kawin' => $this->normalizeStatus($get('status_kawin')),
            'status_hubungan' => $this->titleize($get('status_hubungan')),
            'nama_ayah' => $this->clean($get('nama_ayah')),
            'nama_ibu' => $this->clean($get('nama_ibu')),
            'alamat' => $alamat !== '' ? $alamat : ($dusun ? 'Dusun '.$dusun : ''),
            'rt' => $this->padDigits($get('rt')),
            'rw' => $this->padDigits($get('rw')),
            'dusun' => $dusun ?: null,
            'no_hp' => filled($get('no_hp')) ? preg_replace('/[^\d+]/', '', (string) $get('no_hp')) : null,
        ];
    }

    private function slug(mixed $value): string
    {
        return Str::slug((string) $value, '_');
    }

    private function isBlank(array $values): bool
    {
        foreach ($values as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function clean(mixed $value): ?string
    {
        $s = trim((string) $value);

        return $s === '' ? null : $s;
    }

    private function titleize(mixed $value): ?string
    {
        $s = trim((string) $value);

        return $s === '' ? null : Str::title(strtolower($s));
    }

    private function normalizeGolDarah(mixed $value): ?string
    {
        $v = strtoupper(trim((string) $value));

        if ($v === '' || $v === '-') {
            return null;
        }

        if (in_array($v, ['TIDAK TAHU', 'TIDAK DIKETAHUI', 'TDK TAHU', 'TT'], true)) {
            return 'Tidak Tahu';
        }

        if (in_array($v, ['A', 'B', 'AB', 'O', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'], true)) {
            return $v;
        }

        return Str::title(strtolower($v));
    }

    private function padDigits(mixed $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        $digits = preg_replace('/\D/', '', (string) $value);

        return str_pad($digits === '' ? (string) $value : $digits, 3, '0', STR_PAD_LEFT);
    }

    private function dusunFromAlamat(string $alamat): ?string
    {
        if ($alamat === '') {
            return null;
        }

        $s = trim(preg_replace('/^(dsn\.?|dusun|ds\.?)\s*/i', '', $alamat));

        return $s === '' ? null : Str::title($s);
    }

    private function parseDate(mixed $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value)->format('Y-m-d');
        }

        if (is_numeric($value)) {
            try {
                return Carbon::instance(ExcelDate::excelToDateTimeObject((float) $value))->format('Y-m-d');
            } catch (\Throwable) {
                return null;
            }
        }

        $s = trim((string) $value);

        if (preg_match('/^(\d{1,2})[-\/.](\d{1,2})[-\/.](\d{4})$/', $s, $m)) {
            return sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
        }

        if (preg_match('/^(\d{4})[-\/.](\d{1,2})[-\/.](\d{1,2})$/', $s, $m)) {
            return sprintf('%04d-%02d-%02d', $m[1], $m[2], $m[3]);
        }

        try {
            return Carbon::parse($s)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    private function normalizeGender(mixed $value): ?string
    {
        $v = strtoupper(trim((string) $value));

        return match (true) {
            in_array($v, ['L', 'LK', 'LAKI-LAKI', 'LAKI LAKI', 'PRIA'], true) => 'L',
            in_array($v, ['P', 'PR', 'PEREMPUAN', 'WANITA'], true) => 'P',
            default => null,
        };
    }

    private function normalizeAgama(mixed $value): ?string
    {
        $v = strtoupper(trim((string) $value));

        if ($v === '') {
            return null;
        }

        return match ($v) {
            'ISLAM' => 'Islam',
            'KRISTEN', 'KRISTEN PROTESTAN', 'PROTESTAN' => 'Kristen',
            'KATOLIK', 'KATHOLIK', 'KRISTEN KATOLIK' => 'Katolik',
            'HINDU' => 'Hindu',
            'BUDHA', 'BUDDHA' => 'Buddha',
            'KONGHUCU', 'KHONGHUCU', 'KONG HU CU' => 'Konghucu',
            default => Str::title(strtolower($v)),
        };
    }

    private function normalizeStatus(mixed $value): string
    {
        $v = ucwords(strtolower(trim((string) $value)));

        return in_array($v, Penduduk::STATUS_KAWIN, true) ? $v : 'Belum Kawin';
    }
}
