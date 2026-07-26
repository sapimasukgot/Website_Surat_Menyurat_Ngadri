<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/**
 * Laporan Administrasi Kependudukan (Adminduk) — mengikuti format berkas
 * "ADMINDUK TAHUN ... SEMESTER ..." milik kantor desa.
 *
 * Terdiri dari dua lembar:
 *   1. Rekap per kategori (wilayah, agama, pendidikan, hubungan keluarga,
 *      golongan darah, status perkawinan, dan pekerjaan).
 *   2. Rekap jumlah penduduk per umur (satuan tahun) + kelompok usia.
 */
class AdmindukExport implements WithMultipleSheets
{
    public function __construct(private readonly ?string $judulPeriode = null)
    {
    }

    public function sheets(): array
    {
        return [
            new Sheets\AdmindukRekapSheet($this->judulPeriode),
            new Sheets\AdmindukUsiaSheet(),
        ];
    }
}
