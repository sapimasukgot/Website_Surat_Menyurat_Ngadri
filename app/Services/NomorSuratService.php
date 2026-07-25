<?php
namespace App\Services;

use App\Models\JenisSurat;
use App\Models\Surat;
use Carbon\CarbonInterface;

class NomorSuratService
{
    private const ROMAWI = [1 => 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];

    public function generate(JenisSurat $jenisSurat, CarbonInterface $tanggal): string
    {
        $urut = $this->nextSequence($jenisSurat, (int) $tanggal->year);

        $format = config('nomor_surat.formats.'.$jenisSurat->kode_surat)
            ?? config('nomor_surat.default_format')
            ?? '{urut3}/{kode_surat}/{kode_desa}/{bulan_romawi}/{tahun}';

        return strtr($format, [
            '{urut}' => (string) $urut,
            '{urut2}' => str_pad((string) $urut, 2, '0', STR_PAD_LEFT),
            '{urut3}' => str_pad((string) $urut, 3, '0', STR_PAD_LEFT),
            '{urut4}' => str_pad((string) $urut, 4, '0', STR_PAD_LEFT),
            '{kode_surat}' => $jenisSurat->kode_surat,
            '{kode_desa}' => config('desa.kode'),
            '{bulan}' => (string) $tanggal->month,
            '{bulan2}' => str_pad((string) $tanggal->month, 2, '0', STR_PAD_LEFT),
            '{bulan_romawi}' => self::ROMAWI[$tanggal->month],
            // Tahun selalu mengikuti tanggal surat yang dipilih (default: tanggal hari ini
            // sesuai kalender/server), bukan angka tetap — jadi otomatis berganti tiap tahun.
            '{tahun}' => (string) $tanggal->year,
        ]);
    }

    private function nextSequence(JenisSurat $jenisSurat, int $tahun): int
    {
        $count = Surat::withTrashed()
            ->where('jenis_surat_id', $jenisSurat->id)
            ->whereYear('tanggal_surat', $tahun)
            ->count();

        return $count + 1;
    }
}
