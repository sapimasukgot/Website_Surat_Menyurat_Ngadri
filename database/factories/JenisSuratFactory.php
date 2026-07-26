<?php
namespace Database\Factories;

use App\Models\JenisSurat;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class JenisSuratFactory extends Factory
{
    protected $model = JenisSurat::class;

    public function definition(): array
    {
        $nama = 'Surat '.fake()->unique()->word();

        return [
            'nama_surat' => $nama,
            'slug' => Str::slug($nama),
            'kode_surat' => strtoupper(Str::random(4)),
            'kode_klasifikasi' => '470',
            'deskripsi' => fake()->sentence(),
            'fields' => [
                ['name' => 'keperluan', 'label' => 'Keperluan', 'type' => 'text', 'required' => false],
            ],
            'is_active' => true,
        ];
    }
}
