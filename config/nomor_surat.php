<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Format Nomor Surat
    |--------------------------------------------------------------------------
    |
    | Placeholder yang bisa dipakai di format:
    |   {urut}              nomor urut polos, mis. 7
    |   {urut2}             nomor urut 2 digit, mis. 07
    |   {urut3}             nomor urut 3 digit, mis. 007
    |   {urut4}             nomor urut 4 digit, mis. 0007
    |   {kode_klasifikasi}  kode klasifikasi arsip dari menu Jenis Surat,
    |                        mis. 470 / 422.5. Kalau belum diisi, segmennya
    |                        otomatis dibuang dari nomor surat.
    |   {kode_surat}        kode singkat dari menu Jenis Surat, mis. SKTM
    |   {kode_desa}         dari .env DESA_KODE / config/desa.php, mis. 409.40.13
    |   {bulan}             angka bulan, mis. 7
    |   {bulan2}            angka bulan 2 digit, mis. 07
    |   {bulan_romawi}      angka bulan romawi, mis. VII
    |   {tahun}             tahun 4 digit, DIAMBIL DARI TANGGAL SURAT (mengikuti
    |                        tanggal yang dipilih di form, defaultnya hari ini
    |                        sesuai kalender/tanggal server) — bukan nilai statis.
    |
    */

    // Format resmi Desa Ngadri:
    //   {kode klasifikasi}/{nomor urut}/{kode desa}/{tahun}
    //   contoh: 470/212/409.40.13/2026
    //
    // Kode klasifikasi TIDAK lagi di-hardcode di sini — setiap jenis surat
    // mengisi sendiri kodenya lewat menu Jenis Surat, sehingga satu format
    // di bawah ini sudah cukup untuk semua jenis surat.
    'default_format' => '{kode_klasifikasi}/{urut3}/{kode_desa}/{tahun}',

    // Isi array di bawah HANYA jika ada jenis surat yang butuh susunan nomor
    // yang benar-benar berbeda dari format standar di atas.
    // Key = kode_surat (lihat kolom "Kode Surat" di menu Jenis Surat).
    //
    // Contoh:
    //   'SKTM' => '{kode_klasifikasi}/{urut3}/{kode_desa}/{bulan_romawi}/{tahun}',
    //
    'formats' => [

    ],

];
