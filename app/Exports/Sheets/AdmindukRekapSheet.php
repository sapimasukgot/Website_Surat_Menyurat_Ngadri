<?php
namespace App\Exports\Sheets;

use App\Models\Penduduk;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Lembar 1: rekap jumlah penduduk per kategori (laki-laki / perempuan / jumlah).
 *
 * Tata letaknya mengikuti berkas contoh kantor desa: kolom A–E berisi tabel
 * wilayah, agama, pendidikan, hubungan keluarga, golongan darah, dan status
 * perkawinan; kolom M–Q berisi tabel pekerjaan yang panjang.
 */
class AdmindukRekapSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths, WithStrictNullComparison
{
    /** Baris penampung nilai yang tidak cocok dengan kategori resmi. */
    private const LABEL_TIDAK_TERDATA = 'LAINNYA / TIDAK TERDATA';

    private const LEBAR = 17;          // A..Q
    private const KOLOM_KIRI = 0;      // A
    private const KOLOM_KANAN = 12;    // M

    private const BULAN = [
        1 => 'JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI',
        'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER',
    ];

    /** @var array<int, array<int, mixed>> */
    private array $grid = [];

    /** @var array<int, int> Baris yang perlu dicetak tebal (judul & header tabel). */
    private array $barisTebal = [];

    /** @var array<int, string> Rentang sel yang perlu diberi garis tabel. */
    private array $rentangTabel = [];

    public function __construct(private readonly ?string $judulPeriode = null)
    {
    }

    public function title(): string
    {
        return self::BULAN[(int) now()->month].' '.now()->year;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6, 'B' => 30, 'C' => 12, 'D' => 12, 'E' => 10,
            'F' => 3,
            'M' => 6, 'N' => 34, 'O' => 12, 'P' => 12, 'Q' => 10,
        ];
    }

    public function array(): array
    {
        $this->grid = [];
        $this->barisTebal = [];
        $this->rentangTabel = [];

        $judul = $this->judulPeriode
            ?: 'JUMLAH PENDUDUK BULAN '.self::BULAN[(int) now()->month].' '.now()->year;

        // Baris 1: judul kiri + judul tabel pekerjaan di kanan.
        $this->tulis(1, self::KOLOM_KIRI, $judul);
        $this->tulis(1, self::KOLOM_KANAN + 1, 'PEKERJAAN');
        $this->barisTebal[] = 1;

        // ---- Kolom kanan: tabel pekerjaan (paling panjang, ~99 kategori).
        $this->tabel(
            barisAwal: 3,
            kolom: self::KOLOM_KANAN,
            judul: null,
            labelHeader: 'PEKERJAAN',
            kategori: AdmindukKategori::PEKERJAAN,
            rekap: $this->rekapPekerjaan(),
        );

        // ---- Kolom kiri: enam tabel berurutan ke bawah.
        $baris = 3;

        $baris = $this->tabel($baris, self::KOLOM_KIRI, null, 'WILAYAH', [$this->namaWilayah()], $this->rekapWilayah(), sertakanTotal: false) + 2;
        $baris = $this->tabel($baris, self::KOLOM_KIRI, 'AGAMA', 'AGAMA', AdmindukKategori::AGAMA, $this->rekap('agama')) + 2;
        $baris = $this->tabel($baris, self::KOLOM_KIRI, 'PENDIDIKAN', 'PENDIDIKAN', AdmindukKategori::PENDIDIKAN, $this->rekap('pendidikan')) + 2;
        $baris = $this->tabel($baris, self::KOLOM_KIRI, 'KEPALA RUMAH TANGGA', 'HUBUNGAN KELUARGA', AdmindukKategori::HUBUNGAN_KELUARGA, $this->rekap('status_hubungan')) + 2;
        $baris = $this->tabel($baris, self::KOLOM_KIRI, 'GOLONGAN DARAH', 'GOLONGAN DARAH', AdmindukKategori::GOLONGAN_DARAH, $this->rekap('golongan_darah')) + 2;
        $this->tabel($baris, self::KOLOM_KIRI, 'STATUS PERKAWINAN', 'STATUS PERKAWINAN', AdmindukKategori::STATUS_PERKAWINAN, $this->rekap('status_kawin'));

        return $this->gridRapi();
    }

    /**
     * Tulis satu tabel kategori (judul opsional, header, isi, baris total).
     * Mengembalikan nomor baris terakhir yang terpakai.
     */
    private function tabel(
        int $barisAwal,
        int $kolom,
        ?string $judul,
        string $labelHeader,
        array $kategori,
        array $rekap,
        bool $sertakanTotal = true,
    ): int {
        $baris = $barisAwal;

        if ($judul !== null) {
            $this->tulis($baris, $kolom + 1, $judul);
            $this->barisTebal[] = $baris;
            $baris += 2;
        }

        $barisHeader = $baris;
        $this->tulisBaris($baris, $kolom, ['NO', $labelHeader, 'LAKI-LAKI', 'PEREMPUAN', 'JUMLAH']);
        $this->barisTebal[] = $baris;
        $baris++;

        $totalL = 0;
        $totalP = 0;
        $no = 0;

        foreach (array_values($kategori) as $nama) {
            $l = (int) ($rekap[$nama]['L'] ?? 0);
            $p = (int) ($rekap[$nama]['P'] ?? 0);
            $totalL += $l;
            $totalP += $p;

            $this->tulisBaris($baris, $kolom, [++$no, $nama, $l, $p, $l + $p]);
            $baris++;
        }

        // Nilai yang tidak cocok dengan kategori resmi (mis. penulisan pendidikan
        // yang berbeda) tetap dihitung agar jumlah total sama dengan jumlah
        // penduduk. Baris ini hanya muncul bila memang ada datanya.
        [$sisaL, $sisaP] = $this->sisa($kategori, $rekap);

        if ($sisaL + $sisaP > 0) {
            $totalL += $sisaL;
            $totalP += $sisaP;

            $this->tulisBaris($baris, $kolom, [++$no, self::LABEL_TIDAK_TERDATA, $sisaL, $sisaP, $sisaL + $sisaP]);
            $baris++;
        }

        if ($sertakanTotal) {
            $this->tulisBaris($baris, $kolom, [null, 'JUMLAH TOTAL', $totalL, $totalP, $totalL + $totalP]);
            $this->barisTebal[] = $baris;
        } else {
            $baris--;
        }

        $this->rentangTabel[] = $this->huruf($kolom).$barisHeader.':'.$this->huruf($kolom + 4).$baris;

        return $baris;
    }

    /**
     * Jumlah data yang nilainya di luar daftar kategori resmi.
     *
     * @return array{0:int, 1:int}
     */
    private function sisa(array $kategori, array $rekap): array
    {
        $dikenal = array_flip($kategori);
        $l = 0;
        $p = 0;

        foreach ($rekap as $nama => $jumlah) {
            if (isset($dikenal[$nama])) {
                continue;
            }

            $l += (int) ($jumlah['L'] ?? 0);
            $p += (int) ($jumlah['P'] ?? 0);
        }

        return [$l, $p];
    }

    /**
     * Nama wilayah pada baris rekap, tanpa awalan "Desa" agar seragam dengan
     * berkas contoh kantor desa (mis. "Desa Ngadri" -> "NGADRI").
     */
    private function namaWilayah(): string
    {
        return strtoupper(trim(preg_replace('/^desa\s+/i', '', (string) config('desa.nama'))));
    }

    /**
     * Rekap jumlah penduduk per nilai kolom, dipisah jenis kelamin.
     *
     * @return array<string, array{L:int, P:int}>
     */
    private function rekap(string $kolom): array
    {
        return $this->kelompokkan(
            Penduduk::query()->select([$kolom.' as nilai', 'jenis_kelamin'])->get(),
            fn ($row) => AdmindukKategori::normalisasi($row->nilai),
        );
    }

    /**
     * Pekerjaan yang tidak cocok dengan kategori baku dimasukkan ke
     * "PEKERJAAN LAINNYA" agar jumlah total tetap sama dengan jumlah penduduk.
     */
    private function rekapPekerjaan(): array
    {
        $baku = array_flip(AdmindukKategori::PEKERJAAN);

        return $this->kelompokkan(
            Penduduk::query()->select(['pekerjaan as nilai', 'jenis_kelamin'])->get(),
            function ($row) use ($baku) {
                $nama = AdmindukKategori::normalisasi($row->nilai);

                return isset($baku[$nama]) ? $nama : AdmindukKategori::PEKERJAAN_LAINNYA;
            },
        );
    }

    private function rekapWilayah(): array
    {
        $desa = $this->namaWilayah();

        return $this->kelompokkan(
            Penduduk::query()->select(['jenis_kelamin'])->get(),
            fn () => $desa,
        );
    }

    /**
     * @return array<string, array{L:int, P:int}>
     */
    private function kelompokkan(Collection $rows, callable $kunci): array
    {
        $hasil = [];

        foreach ($rows as $row) {
            $nama = $kunci($row);
            $jk = $row->jenis_kelamin === 'P' ? 'P' : 'L';

            $hasil[$nama] ??= ['L' => 0, 'P' => 0];
            $hasil[$nama][$jk]++;
        }

        return $hasil;
    }

    private function tulis(int $baris, int $kolom, mixed $nilai): void
    {
        $this->grid[$baris] ??= array_fill(0, self::LEBAR, null);
        $this->grid[$baris][$kolom] = $nilai;
    }

    private function tulisBaris(int $baris, int $kolom, array $nilai): void
    {
        foreach (array_values($nilai) as $i => $v) {
            $this->tulis($baris, $kolom + $i, $v);
        }
    }

    /**
     * Ubah grid bernomor baris menjadi array berurutan tanpa lubang.
     */
    private function gridRapi(): array
    {
        $maksimal = $this->grid === [] ? 0 : max(array_keys($this->grid));
        $hasil = [];

        for ($i = 1; $i <= $maksimal; $i++) {
            $hasil[] = $this->grid[$i] ?? array_fill(0, self::LEBAR, null);
        }

        return $hasil;
    }

    private function huruf(int $indeks): string
    {
        return Coordinate::stringFromColumnIndex($indeks + 1);
    }

    public function styles(Worksheet $sheet): array
    {
        foreach ($this->rentangTabel as $rentang) {
            $sheet->getStyle($rentang)->applyFromArray([
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '999999']],
                ],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);
        }

        $gaya = [];

        foreach (array_unique($this->barisTebal) as $baris) {
            $gaya[$baris] = ['font' => ['bold' => true]];
        }

        return $gaya;
    }
}
