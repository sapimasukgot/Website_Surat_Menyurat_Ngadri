<?php

return [
    'required' => 'Kolom :attribute wajib diisi.',
    'email' => 'Kolom :attribute harus berupa alamat email yang valid.',
    'unique' => ':attribute sudah digunakan.',
    'digits' => 'Kolom :attribute harus terdiri dari :digits digit.',
    'date' => 'Kolom :attribute bukan tanggal yang valid.',
    'max' => [
        'string' => 'Kolom :attribute tidak boleh lebih dari :max karakter.',
        'file' => 'Berkas :attribute tidak boleh lebih dari :max kilobita.',
    ],
    'mimes' => 'Kolom :attribute harus berupa berkas bertipe: :values.',
    'mimetypes' => 'Kolom :attribute harus berupa berkas bertipe: :values.',
    'in' => ':attribute yang dipilih tidak valid.',
    'regex' => 'Format kolom :attribute tidak sesuai.',
    'boolean' => 'Kolom :attribute harus berupa ya atau tidak.',
    'integer' => 'Kolom :attribute harus berupa angka bulat.',
    'numeric' => 'Kolom :attribute harus berupa angka.',
    'array' => 'Kolom :attribute harus berupa daftar.',
    'exists' => ':attribute yang dipilih tidak ditemukan.',
    'custom' => [
        'kode_klasifikasi' => [
            'regex' => 'Kode klasifikasi hanya boleh berisi angka dan titik, mis. 470 atau 422.5.',
        ],
    ],
    'attributes' => [
        'nama_lengkap' => 'nama lengkap',
        'nik' => 'NIK',
        'no_kk' => 'nomor KK',
        'nama_surat' => 'nama surat',
        'kode_surat' => 'kode surat',
        'kode_klasifikasi' => 'kode klasifikasi',
        'tanggal_surat' => 'tanggal surat',
        'nomor_surat' => 'nomor surat',
    ],
];
