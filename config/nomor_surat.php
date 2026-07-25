<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Format Nomor Surat
    |--------------------------------------------------------------------------
    |
    | Placeholder yang bisa dipakai di format:
    |   {urut}          nomor urut polos, mis. 7
    |   {urut2}         nomor urut 2 digit, mis. 07
    |   {urut3}         nomor urut 3 digit, mis. 007
    |   {urut4}         nomor urut 4 digit, mis. 0007
    |   {kode_surat}    kode dari menu Jenis Surat, mis. SKTM
    |   {kode_desa}     dari .env DESA_KODE / config/desa.php
    |   {bulan}         angka bulan, mis. 7
    |   {bulan2}        angka bulan 2 digit, mis. 07
    |   {bulan_romawi}  angka bulan romawi, mis. VII
    |   {tahun}         tahun 4 digit, DIAMBIL DARI TANGGAL SURAT (mengikuti
    |                    tanggal yang dipilih di form, defaultnya hari ini
    |                    sesuai kalender/tanggal server) — bukan nilai statis.
    |
    */

    // Dipakai kalau kode_surat yang bersangkutan tidak ada di 'formats' di bawah.
    'default_format' => '{urut3}/{kode_surat}/{kode_desa}/{bulan_romawi}/{tahun}',

    // Silakan tambah/ubah baris di bawah ini sesuai format nomor resmi yang
    // sudah kamu punya untuk masing-masing jenis surat. Key = kode_surat
    // (lihat kolom "Kode Surat" di menu Jenis Surat).
    //
    // Contoh kalau format resmi Surat Keterangan Tidak Mampu adalah
    // 470/007/409.204.05.2026/2026, kamu bisa isi:
    //   'SKTM' => '470/{urut3}/{kode_desa}/{tahun}',
    //
    'formats' => [

        // 'SKTM'     => '{urut3}/{kode_surat}/{kode_desa}/{bulan_romawi}/{tahun}',
        // 'SKTM-PLN' => '{urut3}/{kode_surat}/{kode_desa}/{bulan_romawi}/{tahun}',
        // 'SKTM-BEA' => '{urut3}/{kode_surat}/{kode_desa}/{bulan_romawi}/{tahun}',
        // 'SKD'      => '{urut3}/{kode_surat}/{kode_desa}/{bulan_romawi}/{tahun}',
        // 'SKU'      => '{urut3}/{kode_surat}/{kode_desa}/{bulan_romawi}/{tahun}',
        // 'SKDU'     => '{urut3}/{kode_surat}/{kode_desa}/{bulan_romawi}/{tahun}',
        // 'SKM'      => '{urut3}/{kode_surat}/{kode_desa}/{bulan_romawi}/{tahun}',
        // 'SKL'      => '{urut3}/{kode_surat}/{kode_desa}/{bulan_romawi}/{tahun}',
        // 'SKH'      => '{urut3}/{kode_surat}/{kode_desa}/{bulan_romawi}/{tahun}',
        // 'SKBM'     => '{urut3}/{kode_surat}/{kode_desa}/{bulan_romawi}/{tahun}',
        // 'SK'       => '{urut3}/{kode_surat}/{kode_desa}/{bulan_romawi}/{tahun}',
        // 'SKCK'     => '{urut3}/{kode_surat}/{kode_desa}/{bulan_romawi}/{tahun}',
        // 'SIK'      => '{urut3}/{kode_surat}/{kode_desa}/{bulan_romawi}/{tahun}',
        // 'SKAW'     => '{urut3}/{kode_surat}/{kode_desa}/{bulan_romawi}/{tahun}',
        // 'SKT'      => '{urut3}/{kode_surat}/{kode_desa}/{bulan_romawi}/{tahun}',
        // 'SKP'      => '{urut3}/{kode_surat}/{kode_desa}/{bulan_romawi}/{tahun}',
        // 'SKBK'     => '{urut3}/{kode_surat}/{kode_desa}/{bulan_romawi}/{tahun}',
        // 'SPN'      => '{urut3}/{kode_surat}/{kode_desa}/{bulan_romawi}/{tahun}',

    ],

];
