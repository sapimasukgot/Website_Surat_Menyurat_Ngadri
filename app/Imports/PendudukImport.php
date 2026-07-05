<?php
namespace App\Imports;

use App\Models\Penduduk;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class PendudukImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    use Importable;

    public int $inserted = 0;

    public int $updated = 0;

    public int $failed = 0;

    public int $total = 0;

    public array $errors = [];

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $this->total++;
            $nomorBaris = $index + 2;

            $data = $this->mapRow($row);

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

    private function mapRow(Collection $row): array
    {
        return [
            'nik' => preg_replace('/\D/', '', (string) $row->get('nik')),
            'no_kk' => preg_replace('/\D/', '', (string) $row->get('no_kk')),
            'nama_lengkap' => trim((string) $row->get('nama_lengkap')),
            'tempat_lahir' => $row->get('tempat_lahir'),
            'tanggal_lahir' => $this->parseDate($row->get('tanggal_lahir')),
            'jenis_kelamin' => $this->normalizeGender($row->get('jenis_kelamin')),
            'agama' => $row->get('agama'),
            'pendidikan' => $row->get('pendidikan'),
            'pekerjaan' => $row->get('pekerjaan'),
            'status_kawin' => $this->normalizeStatus($row->get('status_kawin')),
            'alamat' => trim((string) $row->get('alamat')),
            'rt' => $row->get('rt') ? str_pad((string) $row->get('rt'), 3, '0', STR_PAD_LEFT) : null,
            'rw' => $row->get('rw') ? str_pad((string) $row->get('rw'), 3, '0', STR_PAD_LEFT) : null,
            'dusun' => $row->get('dusun'),
            'no_hp' => $row->get('no_hp') ? preg_replace('/[^\d+]/', '', (string) $row->get('no_hp')) : null,
        ];
    }

    private function parseDate(mixed $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        try {
            if (is_numeric($value)) {
                return Carbon::instance(ExcelDate::excelToDateTimeObject($value))->format('Y-m-d');
            }

            return Carbon::parse($value)->format('Y-m-d');
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

    private function normalizeStatus(mixed $value): string
    {
        $v = ucwords(strtolower(trim((string) $value)));

        return in_array($v, Penduduk::STATUS_KAWIN, true) ? $v : 'Belum Kawin';
    }
}
