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

    /**
     * Nomor urut dihitung per KELOMPOK kode klasifikasi (bukan per jenis surat),
     * karena nomor surat yang dicetak hanya membedakan surat lewat kode klasifikasi,
     * bukan lewat kode_surat. Jenis surat yang berbagi kode klasifikasi yang sama
     * (mis. beberapa jenis surat sama-sama "470") harus berbagi satu urutan nomor
     * yang sama supaya nomor akhirnya tidak pernah bentrok. Jenis surat yang belum
     * diisi kode klasifikasinya digabung jadi satu kelompok "tanpa klasifikasi".
     */
    private function nextSequence(JenisSurat $jenisSurat, int $tahun): int
    {
        $klasifikasi = $this->normalisasiKlasifikasi($jenisSurat->kode_klasifikasi);

        $jenisSuratIds = JenisSurat::query()
            ->when(
                $klasifikasi !== null,
                fn ($q) => $q->where('kode_klasifikasi', $klasifikasi),
                fn ($q) => $q->where(function ($qq) {
                    $qq->whereNull('kode_klasifikasi')->orWhere('kode_klasifikasi', '');
                })
            )
            ->pluck('id');

        $count = Surat::withTrashed()
            ->whereIn('jenis_surat_id', $jenisSuratIds)
            ->whereYear('tanggal_surat', $tahun)
            ->count();

        return $count + 1;
    }

    private function normalisasiKlasifikasi(?string $kode): ?string
    {
        $kode = trim((string) $kode);

        return $kode === '' ? null : $kode;
    }
}
