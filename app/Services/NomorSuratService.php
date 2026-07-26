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
            ?? '{kode_klasifikasi}/{urut3}/{kode_desa}/{tahun}';

        $nomor = strtr($format, [
            '{urut}' => (string) $urut,
            '{urut2}' => str_pad((string) $urut, 2, '0', STR_PAD_LEFT),
            '{urut3}' => str_pad((string) $urut, 3, '0', STR_PAD_LEFT),
            '{urut4}' => str_pad((string) $urut, 4, '0', STR_PAD_LEFT),
            '{kode_surat}' => $jenisSurat->kode_surat,
            // Kode klasifikasi arsip (mis. 470, 422.5) diisi per jenis surat di
            // menu Jenis Surat. Boleh kosong — segmennya dibuang di bawah.
            '{kode_klasifikasi}' => (string) ($jenisSurat->kode_klasifikasi ?? ''),
            '{kode_desa}' => config('desa.kode'),
            '{bulan}' => (string) $tanggal->month,
            '{bulan2}' => str_pad((string) $tanggal->month, 2, '0', STR_PAD_LEFT),
            '{bulan_romawi}' => self::ROMAWI[$tanggal->month],
            // Tahun selalu mengikuti tanggal surat yang dipilih (default: tanggal hari ini
            // sesuai kalender/server), bukan angka tetap — jadi otomatis berganti tiap tahun.
            '{tahun}' => (string) $tanggal->year,
        ]);

        return $this->rapikan($nomor);
    }

    /**
     * Buang segmen kosong akibat placeholder yang tidak terisi, mis.
     * "/007/409.40.13/2026" menjadi "007/409.40.13/2026".
     */
    private function rapikan(string $nomor): string
    {
        $segments = array_filter(
            array_map('trim', explode('/', $nomor)),
            fn ($segment) => $segment !== ''
        );

        return implode('/', $segments);
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
