<?php
namespace App\Services;

use App\Models\JenisSurat;
use App\Models\Surat;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class NomorSuratService
{
    private const ROMAWI = [1 => 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];

    /**
     * Nomor surat lengkap untuk surat baru, memakai nomor urut berikutnya.
     */
    public function generate(JenisSurat $jenisSurat, CarbonInterface $tanggal): string
    {
        return $this->format($jenisSurat, $tanggal, $this->nextUrut((int) $tanggal->year));
    }

    /**
     * Susun nomor surat dari nomor urut yang sudah ditentukan.
     */
    public function format(JenisSurat $jenisSurat, CarbonInterface $tanggal, int $urut): string
    {
        $nomor = strtr($this->formatString($jenisSurat), [
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
     * Nomor urut berikutnya — SATU urutan berjalan yang dipakai bersama oleh
     * semua jenis surat dan semua kode klasifikasi (sesuai buku agenda surat
     * kantor desa: satu nomor urut untuk seluruh surat keluar).
     *
     * Patokannya adalah nomor urut TERTINGGI yang pernah dipakai pada tahun
     * tersebut. Jadi kalau operator mengubah nomor surat secara manual ke angka
     * yang lebih tinggi, angka itu otomatis menjadi patokan baru dan surat
     * berikutnya melanjutkan dari sana.
     *
     * Karena dihitung per tahun tanggal surat, nomor urut otomatis kembali ke
     * 001 begitu masuk tahun baru — tanpa perlu direset manual.
     */
    public function nextUrut(int $tahun, bool $kunci = false): int
    {
        return $this->urutTertinggi($tahun, kunci: $kunci) + 1;
    }

    /**
     * Nomor urut tertinggi yang sudah terpakai di suatu tahun (0 bila belum ada).
     * Surat yang dihapus tetap dihitung supaya nomornya tidak dipakai ulang.
     *
     * $kunci = true memasang lock baris (lockForUpdate) sehingga dua operator yang
     * menyimpan surat pada saat yang sama tidak bisa sama-sama membaca nomor
     * tertinggi yang sama. Hanya berefek bila dipanggil di dalam transaksi.
     */
    public function urutTertinggi(int $tahun, ?int $kecualiSuratId = null, bool $kunci = false): int
    {
        return (int) Surat::withTrashed()
            // Rentang tanggal (bukan whereYear) supaya query bisa memakai indeks dan
            // lock-nya hanya mengunci baris tahun itu, bukan seluruh tabel.
            ->whereBetween('tanggal_surat', [
                Carbon::create($tahun, 1, 1)->startOfDay(),
                Carbon::create($tahun, 12, 31)->endOfDay(),
            ])
            ->when($kecualiSuratId, fn ($q) => $q->where('id', '!=', $kecualiSuratId))
            ->when($kunci, fn ($q) => $q->lockForUpdate())
            ->max('nomor_urut');
    }

    /**
     * Baca kembali angka nomor urut dari sebuah string nomor surat — dipakai saat
     * operator menyunting nomor surat secara manual, supaya kolom nomor_urut
     * (dan karenanya patokan nomor berikutnya) tetap ikut menyesuaikan.
     */
    public function bacaUrut(string $nomor, ?JenisSurat $jenisSurat = null): ?int
    {
        $segmen = $this->pecahSegmen($nomor);

        // Format tanpa placeholder nomor urut sama sekali — tidak ada yang bisa
        // dibaca, dan menebak-nebak justru berisiko salah (mis. malah membaca kode
        // klasifikasi sebagai nomor urut).
        if ($jenisSurat && !str_contains($this->formatString($jenisSurat), '{urut')) {
            return null;
        }

        // Posisi segmen dihitung dari nomor yang SUDAH tersusun (bukan dari string
        // format), karena segmen kosong — mis. kode klasifikasi yang belum diisi —
        // dibuang saat penyusunan sehingga posisinya bergeser.
        $indeks = $jenisSurat ? $this->indeksSegmenUrut($jenisSurat) : null;

        if ($indeks !== null && ctype_digit($segmen[$indeks] ?? '')) {
            return (int) $segmen[$indeks];
        }

        // Cadangan: segmen angka pertama yang jelas bukan tahun. Dipakai bila jenis
        // suratnya tidak diketahui, atau bila susunan nomor yang diketik operator
        // tidak persis mengikuti format (mis. segmen klasifikasi ikut dihapus).
        foreach ($segmen as $s) {
            if (ctype_digit($s) && !(strlen($s) === 4 && (int) $s >= 1900 && (int) $s <= 2999)) {
                return (int) $s;
            }
        }

        return null;
    }

    /**
     * Posisi segmen nomor urut pada nomor surat yang sudah tersusun. Dicari dengan
     * menyusun dua nomor contoh yang hanya beda nomor urutnya, lalu melihat segmen
     * mana yang berubah — jadi selalu akurat untuk format apa pun.
     */
    private function indeksSegmenUrut(JenisSurat $jenisSurat): ?int
    {
        $referensi = Carbon::create(2000, 1, 1);
        $a = $this->pecahSegmen($this->format($jenisSurat, $referensi, 1));
        $b = $this->pecahSegmen($this->format($jenisSurat, $referensi, 2));

        foreach ($a as $i => $segmen) {
            if (($b[$i] ?? null) !== $segmen) {
                return $i;
            }
        }

        return null;
    }

    /** @return list<string> */
    private function pecahSegmen(string $nomor): array
    {
        return array_values(array_filter(
            array_map('trim', explode('/', $nomor)),
            fn ($s) => $s !== ''
        ));
    }

    private function formatString(?JenisSurat $jenisSurat): string
    {
        return ($jenisSurat ? config('nomor_surat.formats.'.$jenisSurat->kode_surat) : null)
            ?? config('nomor_surat.default_format')
            ?? '{kode_klasifikasi}/{urut3}/{kode_desa}/{tahun}';
    }

    /**
     * Buang segmen kosong dan spasi berlebih, mis. "/007 / 409.40.13/2026"
     * menjadi "007/409.40.13/2026". Dipakai untuk placeholder yang tidak terisi
     * maupun untuk merapikan nomor yang diketik manual oleh operator.
     */
    public function rapikan(string $nomor): string
    {
        return implode('/', $this->pecahSegmen($nomor));
    }
}
