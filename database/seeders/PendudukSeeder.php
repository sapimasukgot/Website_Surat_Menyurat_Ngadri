<?php
namespace Database\Seeders;

use App\Models\Penduduk;
use Illuminate\Database\Seeder;

class PendudukSeeder extends Seeder
{
    public function run(): void
    {
        $contoh = [
            [
                'nik' => '3505010101800001', 'no_kk' => '3505011234560001',
                'nama_lengkap' => 'Budi Santoso', 'tempat_lahir' => 'Blitar',
                'tanggal_lahir' => '1980-01-01', 'jenis_kelamin' => 'L',
                'agama' => 'Islam', 'pendidikan' => 'SMA', 'pekerjaan' => 'Petani',
                'status_kawin' => 'Kawin', 'alamat' => 'Dusun Krajan',
                'rt' => '001', 'rw' => '002', 'dusun' => 'Krajan', 'no_hp' => '081234567890',
            ],
            [
                'nik' => '3505014505850002', 'no_kk' => '3505011234560001',
                'nama_lengkap' => 'Siti Aminah', 'tempat_lahir' => 'Blitar',
                'tanggal_lahir' => '1985-05-05', 'jenis_kelamin' => 'P',
                'agama' => 'Islam', 'pendidikan' => 'SMP', 'pekerjaan' => 'Ibu Rumah Tangga',
                'status_kawin' => 'Kawin', 'alamat' => 'Dusun Krajan',
                'rt' => '001', 'rw' => '002', 'dusun' => 'Krajan', 'no_hp' => '081234567891',
            ],
            [
                'nik' => '3505011203900003', 'no_kk' => '3505017654320002',
                'nama_lengkap' => 'Agus Wijaya', 'tempat_lahir' => 'Malang',
                'tanggal_lahir' => '1990-03-12', 'jenis_kelamin' => 'L',
                'agama' => 'Kristen', 'pendidikan' => 'S1', 'pekerjaan' => 'Wiraswasta',
                'status_kawin' => 'Belum Kawin', 'alamat' => 'Dusun Sumber',
                'rt' => '003', 'rw' => '001', 'dusun' => 'Sumber', 'no_hp' => '081234567892',
            ],
        ];

        foreach ($contoh as $row) {
            Penduduk::updateOrCreate(['nik' => $row['nik']], $row);
        }

        if (app()->environment('local', 'testing')) {
            Penduduk::factory()->count(30)->create();
        }
    }
}
