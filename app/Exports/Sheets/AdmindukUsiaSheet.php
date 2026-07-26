<?php
namespace App\Exports\Sheets;

use App\Models\Penduduk;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Lembar 2: jumlah penduduk per umur (satuan tahun), ditutup rekap
 * kelompok usia mengikuti konstanta Penduduk::KELOMPOK_USIA.
 *
 * Umur dihitung di PHP dari tanggal_lahir agar tidak bergantung pada fungsi
 * tanggal bawaan salah satu jenis database.
 */
class AdmindukUsiaSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths, WithStrictNullComparison
{
    private const LEBAR = 5;

    /** @var array<int, int> */
    private array $barisTebal = [];

    /** @var array<int, string> */
    private array $rentangTabel = [];

    public function title(): string
    {
        return 'usia';
    }

    public function columnWidths(): array
    {
        return ['A' => 6, 'B' => 26, 'C' => 12, 'D' => 12, 'E' => 10];
    }

    public function array(): array
    {
        $this->barisTebal = [];
        $this->rentangTabel = [];

        [$perUmur, $perKelompok, $tanpaTanggalLahir] = $this->hitung();

        $rows = [];
        $rows[] = ['JUMLAH PENDUDUK MENURUT KELOMPOK UMUR', null, null, null, null];
        $this->barisTebal[] = 1;
        $rows[] = array_fill(0, self::LEBAR, null);

        $barisHeader = count($rows) + 1;
        $rows[] = ['NO', 'KELOMPOK UMUR', 'LAKI-LAKI', 'PEREMPUAN', 'JUMLAH'];
        $this->barisTebal[] = $barisHeader;

        $umurMaksimal = $perUmur === [] ? 0 : max(array_keys($perUmur));
        $totalL = 0;
        $totalP = 0;

        for ($umur = 0; $umur <= $umurMaksimal; $umur++) {
            $l = $perUmur[$umur]['L'] ?? 0;
            $p = $perUmur[$umur]['P'] ?? 0;
            $totalL += $l;
            $totalP += $p;

            $rows[] = [$umur + 1, $umur, $l, $p, $l + $p];
        }

        if ($tanpaTanggalLahir['L'] + $tanpaTanggalLahir['P'] > 0) {
            $rows[] = [
                $umurMaksimal + 2,
                'TANGGAL LAHIR KOSONG',
                $tanpaTanggalLahir['L'],
                $tanpaTanggalLahir['P'],
                $tanpaTanggalLahir['L'] + $tanpaTanggalLahir['P'],
            ];
            $totalL += $tanpaTanggalLahir['L'];
            $totalP += $tanpaTanggalLahir['P'];
        }

        $rows[] = [null, 'JUMLAH TOTAL', $totalL, $totalP, $totalL + $totalP];
        $this->barisTebal[] = count($rows);
        $this->rentangTabel[] = 'A'.$barisHeader.':E'.count($rows);

        // ---- Rekap kelompok usia (balita / anak / remaja / dewasa / lansia).
        $rows[] = array_fill(0, self::LEBAR, null);
        $rows[] = ['REKAP KELOMPOK USIA', null, null, null, null];
        $this->barisTebal[] = count($rows);
        $rows[] = array_fill(0, self::LEBAR, null);

        $barisHeader = count($rows) + 1;
        $rows[] = ['NO', 'KELOMPOK USIA', 'LAKI-LAKI', 'PEREMPUAN', 'JUMLAH'];
        $this->barisTebal[] = $barisHeader;

        $no = 1;

        foreach (Penduduk::KELOMPOK_USIA as $kunci => $rentang) {
            $l = $perKelompok[$kunci]['L'] ?? 0;
            $p = $perKelompok[$kunci]['P'] ?? 0;
            $rows[] = [$no++, $rentang['label'], $l, $p, $l + $p];
        }

        $this->rentangTabel[] = 'A'.$barisHeader.':E'.count($rows);

        return $rows;
    }

    /**
     * @return array{0: array<int, array{L:int,P:int}>, 1: array<string, array{L:int,P:int}>, 2: array{L:int,P:int}}
     */
    private function hitung(): array
    {
        $perUmur = [];
        $perKelompok = [];
        $tanpaTanggalLahir = ['L' => 0, 'P' => 0];
        $hariIni = now();

        $penduduks = Penduduk::query()
            ->select(['tanggal_lahir', 'jenis_kelamin'])
            ->cursor();

        foreach ($penduduks as $p) {
            $jk = $p->jenis_kelamin === 'P' ? 'P' : 'L';

            if (! $p->tanggal_lahir) {
                $tanpaTanggalLahir[$jk]++;

                continue;
            }

            $umur = (int) abs($p->tanggal_lahir->diffInYears($hariIni));

            $perUmur[$umur] ??= ['L' => 0, 'P' => 0];
            $perUmur[$umur][$jk]++;

            $kunci = $this->kelompokUsia($umur);

            if ($kunci !== null) {
                $perKelompok[$kunci] ??= ['L' => 0, 'P' => 0];
                $perKelompok[$kunci][$jk]++;
            }
        }

        ksort($perUmur);

        return [$perUmur, $perKelompok, $tanpaTanggalLahir];
    }

    private function kelompokUsia(int $umur): ?string
    {
        foreach (Penduduk::KELOMPOK_USIA as $kunci => $rentang) {
            $cocok = $umur >= $rentang['min']
                && (is_null($rentang['max']) || $umur <= $rentang['max']);

            if ($cocok) {
                return $kunci;
            }
        }

        return null;
    }

    public function styles(Worksheet $sheet): array
    {
        foreach ($this->rentangTabel as $rentang) {
            $sheet->getStyle($rentang)->applyFromArray([
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '999999']],
                ],
            ]);
        }

        $gaya = [];

        foreach (array_unique($this->barisTebal) as $baris) {
            $gaya[$baris] = ['font' => ['bold' => true]];
        }

        return $gaya;
    }
}
