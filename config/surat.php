<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Field Blok Kondisional
    |--------------------------------------------------------------------------
    |
    | Sebuah field bertipe "select" bisa dipakai sebagai saklar untuk satu blok
    | pada template Word. Bungkus bagian yang opsional di berkas .docx dengan
    | ${nama_field} ... ${/nama_field}; blok itu hanya ikut tercetak bila
    | pilihannya "Ya" (lihat SuratGeneratorService::terapkanBlok()).
    |
    | Daftar di bawah ini murni untuk kenyamanan pengisian form: field-field
    | yang disebutkan akan disembunyikan otomatis di halaman "Buat Surat"
    | selama saklarnya belum dipilih "Ya", supaya operator tidak mengisi data
    | yang tidak akan tercetak. Menghapus/mengosongkan daftar ini tidak
    | mempengaruhi hasil surat — hanya tampilan formnya.
    |
    | Format: 'nama_field_select' => ['field_lain', 'field_lain_2', ...]
    |
    */

    'blok_fields' => [

        // Surat Izin Keramaian — blok surat pernyataan panitia penyelenggara
        // hiburan hanya dipakai bila acaranya memakai hiburan.
        'hiburan' => [
            'jenis_hiburan',
            'nama_organisasi',
            'nama_ketua',
            'wakil_ketua',
            'sekretaris',
            'hari_hiburan',
            'tanggal_hiburan',
            'jam_hiburan',
            'tempat_hiburan',
        ],

    ],

];
