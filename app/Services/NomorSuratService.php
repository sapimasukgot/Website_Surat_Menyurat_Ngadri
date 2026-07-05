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

        return implode('/', [
            str_pad((string) $urut, 3, '0', STR_PAD_LEFT),
            $jenisSurat->kode_surat,
            config('desa.kode'),
            self::ROMAWI[$tanggal->month],
            $tanggal->year,
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
