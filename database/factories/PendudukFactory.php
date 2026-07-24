<?php
namespace Database\Factories;

use App\Models\Penduduk;
use Illuminate\Database\Eloquent\Factories\Factory;

class PendudukFactory extends Factory
{
    protected $model = Penduduk::class;

    public function definition(): array
    {
        $jk = fake()->randomElement(['L', 'P']);

        return [
            'nik' => fake()->unique()->numerify('35050###########'),
            'no_kk' => fake()->numerify('35050###########'),
            'nama_lengkap' => fake()->name($jk === 'L' ? 'male' : 'female'),
            'tempat_lahir' => fake()->randomElement(['Blitar', 'Malang', 'Kediri', 'Tulungagung']),
            'tanggal_lahir' => fake()->dateTimeBetween('-70 years', '-17 years')->format('Y-m-d'),
            'jenis_kelamin' => $jk,
            'agama' => fake()->randomElement(Penduduk::AGAMA),
            'pendidikan' => fake()->randomElement(['SD', 'SMP', 'SMA', 'D3', 'S1']),
            'pekerjaan' => fake()->randomElement(['Petani', 'Pedagang', 'Guru', 'Wiraswasta', 'Buruh']),
            'status_kawin' => fake()->randomElement(Penduduk::STATUS_KAWIN),
            'alamat' => fake()->streetAddress(),
            'rt' => fake()->numerify('00#'),
            'rw' => fake()->numerify('00#'),
            'dusun' => fake()->randomElement(['Krajan', 'Sumber', 'Ngadri', 'Sukorejo']),
            'no_hp' => fake()->numerify('08##########'),
        ];
    }
}
