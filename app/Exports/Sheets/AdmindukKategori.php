<?php
namespace App\Exports\Sheets;

/**
 * Daftar kategori resmi untuk laporan Adminduk beserta padanan penulisan
 * (alias) yang mungkin dipakai pada data penduduk.
 *
 * Urutan kategori sengaja dikunci agar hasil export sama persis dengan berkas
 * contoh dari kantor desa. Nilai pada database dicocokkan tanpa membedakan
 * huruf besar/kecil; nilai yang tidak cocok masuk ke kategori "lainnya"
 * (khusus pekerjaan) atau diabaikan dari rekap kategori bersangkutan.
 */
class AdmindukKategori
{
    public const AGAMA = [
        'ISLAM', 'KRISTEN', 'KATHOLIK', 'HINDU', 'BUDHA', 'KHONGHUCU', 'KEPERCAYAAN',
    ];

    public const PENDIDIKAN = [
        'TIDAK/BLM SEKOLAH', 'BELUM TAMAT SD/SEDERAJAT', 'TAMAT SD/SEDERAJAT',
        'SLTP/SEDERAJAT', 'SLTA/SEDERAJAT', 'DIPLOMA I/II',
        'AKADEMI/DIPL.III/S. MUDA', 'DIPLOMA IV/STRATA I', 'STRATA-II', 'STRATA-III',
    ];

    public const HUBUNGAN_KELUARGA = [
        'KEPALA KELUARGA', 'SUAMI', 'ISTERI', 'ANAK', 'MENANTU', 'CUCU',
        'ORANG TUA', 'MERTUA', 'FAMILI LAIN', 'PEMBANTU', 'LAINNYA',
    ];

    public const GOLONGAN_DARAH = [
        'A', 'B', 'AB', 'O', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-', 'TIDAK TAHU',
    ];

    public const STATUS_PERKAWINAN = [
        'BELUM KAWIN', 'KAWIN', 'CERAI HIDUP', 'CERAI MATI',
    ];

    /** Kategori pekerjaan baku (99 kategori + baris "PEKERJAAN LAINNYA" sebagai penampung). */
    public const PEKERJAAN = [
        'BELUM/TIDAK BEKERJA', 'MENGURUS RUMAH TANGGA', 'PELAJAR/MAHASISWA', 'PENSIUNAN',
        'PEGAWAI NEGERI SIPIL (PNS)', 'TENTARA NASIONAL INDONESIA', 'KEPOLISIAN RI (POLRI)',
        'PERDAGANGAN', 'PETANI/PEKEBUN', 'PETERNAK', 'NELAYAN/PERIKANAN', 'INDUSTRI',
        'KONSTRUKSI', 'TRANSPORTASI', 'KARYAWAN SWASTA', 'KARYAWAN BUMN', 'KARYAWAN BUMD',
        'KARYAWAN HONORER', 'BURUH HARIAN LEPAS', 'BURUH TANI/PERKEBUNAN',
        'BURUH NELAYAN/PERIKANAN', 'BURUH PETERNAKAN', 'PEMBANTU RUMAH TANGGA',
        'TUKANG CUKUR', 'TUKANG LISTRIK', 'TUKANG BATU', 'TUKANG KAYU', 'TUKANG SOL SEPATU',
        'TUKANG LAS/PANDAI BESI', 'TUKANG JAHIT', 'TUKANG GIGI', 'PENATA RIAS',
        'PENATA BUSANA', 'PENATA RAMBUT', 'MEKANIK', 'SENIMAN', 'TABIB', 'PARAJI',
        'PERANCANG BUSANA', 'PENTERJEMAH', 'IMAM MASJID', 'PENDETA', 'PASTOR', 'WARTAWAN',
        'USTADZ/MUBALIGH', 'JURU MASAK', 'PROMOTOR ACARA', 'ANGGOTA DPR RI', 'ANGGOTA DPD RI',
        'ANGGOTA BPK', 'PRESIDEN', 'WAKIL PRESIDEN', 'ANGGOTA MAHKAMAH KONSTITUSI',
        'ANGGOTA KABINET KEMENTRIAN', 'DUTA BESAR', 'GUBERNUR', 'WAKIL GUBERNUR', 'BUPATI',
        'WAKIL BUPATI', 'WALIKOTA', 'WAKIL WALIKOTA', 'ANGGOTA DPRD PROP.',
        'ANGGOTA DPRD KAB./KOTA', 'DOSEN', 'GURU', 'PILOT', 'PENGACARA', 'NOTARIS',
        'ARSITEK', 'AKUNTAN', 'KONSULTAN', 'DOKTER', 'BIDAN', 'PERAWAT', 'APOTEKER',
        'PSIKIATER/PSIKOLOG', 'PENYIAR TELEVISI', 'PENYIAR RADIO', 'PELAUT', 'PENELITI',
        'SOPIR', 'PIALANG', 'PARANORMAL', 'PEDAGANG', 'PERANGKAT DESA', 'KEPALA DESA',
        'BIARAWAN/BIARAWATI', 'WIRASWASTA', 'ANGGOTA LEMB. TINGGI LAINNYA', 'ARTIS', 'ATLIT',
        'CHEFF', 'MANAJER', 'TENAGA TATA USAHA', 'OPERATOR', 'PEKERJA PENGOLAHAN KERAJINAN',
        'TEKNISI', 'ASISTEN AHLI', 'PEKERJAAN LAINNYA',
    ];

    /** Penampung nilai pekerjaan yang tidak cocok dengan kategori baku. */
    public const PEKERJAAN_LAINNYA = 'PEKERJAAN LAINNYA';

    /**
     * Padanan penulisan: nilai di database (kiri) dianggap sama dengan
     * kategori resmi (kanan). Kunci sudah dalam bentuk ternormalisasi
     * (huruf besar, spasi rapat).
     */
    public const ALIAS = [
        // Agama — penulisan pada aplikasi vs format Dukcapil.
        'KATOLIK' => 'KATHOLIK',
        'BUDDHA' => 'BUDHA',
        'KONGHUCU' => 'KHONGHUCU',
        'PENGHAYAT KEPERCAYAAN' => 'KEPERCAYAAN',

        // Hubungan keluarga.
        'ISTRI' => 'ISTERI',

        // Pendidikan.
        'AKADEMI/DIPLOMA III/SARJANA MUDA' => 'AKADEMI/DIPL.III/S. MUDA',
        'AKADEMI/DIPLOMA III/S. MUDA' => 'AKADEMI/DIPL.III/S. MUDA',
        'DIPLOMA III' => 'AKADEMI/DIPL.III/S. MUDA',
        'TIDAK/BELUM SEKOLAH' => 'TIDAK/BLM SEKOLAH',
        'STRATA I' => 'DIPLOMA IV/STRATA I',
        'STRATA II' => 'STRATA-II',
        'STRATA III' => 'STRATA-III',

        // Pekerjaan.
        'TENTARA NASIONAL INDONESIA (TNI)' => 'TENTARA NASIONAL INDONESIA',
        'TNI' => 'TENTARA NASIONAL INDONESIA',
        'POLRI' => 'KEPOLISIAN RI (POLRI)',
        'PNS' => 'PEGAWAI NEGERI SIPIL (PNS)',

        // Golongan darah.
        'TIDAK DIKETAHUI' => 'TIDAK TAHU',
        '-' => 'TIDAK TAHU',
    ];

    /**
     * Samakan penulisan nilai dari database dengan kategori resmi.
     */
    public static function normalisasi(?string $nilai): string
    {
        $bersih = strtoupper(trim(preg_replace('/\s+/', ' ', (string) $nilai)));

        return self::ALIAS[$bersih] ?? $bersih;
    }
}
