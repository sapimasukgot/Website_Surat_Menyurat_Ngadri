<?php
namespace Database\Seeders;

use App\Models\JenisSurat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class JenisSuratSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->data() as $item) {
            $item['slug'] = Str::slug($item['nama_surat']);
            $model = JenisSurat::updateOrCreate(['slug' => $item['slug']], $item);

            $this->attachTemplateStub($model);
        }
    }

    private function attachTemplateStub(JenisSurat $model): void
    {
        if ($model->template_path) {
            return;
        }

        $stub = database_path('templates/'.Str::slug($model->kode_surat).'.docx');

        if (! file_exists($stub)) {
            return;
        }

        $path = 'templates/'.Str::slug($model->kode_surat).'-'.Str::random(8).'.docx';
        Storage::disk('public')->put($path, file_get_contents($stub));

        $model->update([
            'template_path' => $path,
            'template_original_name' => basename($stub),
        ]);
    }

    private function data(): array
    {
        return [
            [
                'nama_surat' => 'Surat Keterangan Tidak Mampu (SKTM)',
                'kode_surat' => 'SKTM',
                'deskripsi' => 'Menerangkan bahwa warga tergolong keluarga tidak mampu.',
                'is_active' => true,
                'fields' => [
                    ['name' => 'keperluan', 'label' => 'Keperluan', 'type' => 'text', 'required' => true],
                    ['name' => 'penghasilan', 'label' => 'Penghasilan per Bulan', 'type' => 'text', 'required' => false],
                    ['name' => 'keterangan_tambahan', 'label' => 'Keterangan Tambahan', 'type' => 'textarea', 'required' => false],
                ],
            ],
            [
                'nama_surat' => 'Surat Keterangan Domisili',
                'kode_surat' => 'SKD',
                'deskripsi' => 'Menerangkan tempat tinggal/domisili warga.',
                'is_active' => true,
                'fields' => [
                    ['name' => 'keperluan', 'label' => 'Keperluan', 'type' => 'text', 'required' => true],
                    ['name' => 'lama_tinggal', 'label' => 'Lama Tinggal', 'type' => 'text', 'required' => false],
                ],
            ],
            [
                'nama_surat' => 'Surat Keterangan Usaha (SKU)',
                'kode_surat' => 'SKU',
                'deskripsi' => 'Menerangkan bahwa warga memiliki usaha.',
                'is_active' => true,
                'fields' => [
                    ['name' => 'nama_usaha', 'label' => 'Nama Usaha', 'type' => 'text', 'required' => true],
                    ['name' => 'jenis_usaha', 'label' => 'Jenis Usaha', 'type' => 'text', 'required' => true],
                    ['name' => 'alamat_usaha', 'label' => 'Alamat Usaha', 'type' => 'textarea', 'required' => false],
                    ['name' => 'keperluan', 'label' => 'Keperluan', 'type' => 'text', 'required' => true],
                ],
            ],
            [
                'nama_surat' => 'Surat Pengantar',
                'kode_surat' => 'SP',
                'deskripsi' => 'Surat pengantar dari desa untuk berbagai keperluan.',
                'is_active' => true,
                'fields' => [
                    ['name' => 'keperluan', 'label' => 'Keperluan', 'type' => 'text', 'required' => true],
                    ['name' => 'tujuan', 'label' => 'Tujuan', 'type' => 'text', 'required' => false],
                ],
            ],
            [
                'nama_surat' => 'Surat Keterangan Boro Kerja',
                'kode_surat' => 'SKBK',
                'deskripsi' => 'Keterangan warga yang merantau/bekerja di luar daerah.',
                'is_active' => true,
                'fields' => [
                    ['name' => 'tujuan', 'label' => 'Kota/Daerah Tujuan', 'type' => 'text', 'required' => true],
                    ['name' => 'jenis_pekerjaan', 'label' => 'Jenis Pekerjaan', 'type' => 'text', 'required' => false],
                    ['name' => 'lama_kerja', 'label' => 'Lama Bekerja', 'type' => 'text', 'required' => false],
                ],
            ],
            [
                'nama_surat' => 'Surat Keterangan Kelahiran',
                'kode_surat' => 'SKL',
                'deskripsi' => 'Menerangkan peristiwa kelahiran.',
                'is_active' => true,
                'fields' => [
                    ['name' => 'nama_anak', 'label' => 'Nama Anak', 'type' => 'text', 'required' => true],
                    ['name' => 'tempat_lahir_anak', 'label' => 'Tempat Lahir Anak', 'type' => 'text', 'required' => true],
                    ['name' => 'tanggal_lahir_anak', 'label' => 'Tanggal Lahir Anak', 'type' => 'date', 'required' => true],
                    ['name' => 'jenis_kelamin_anak', 'label' => 'Jenis Kelamin Anak', 'type' => 'text', 'required' => true],
                    ['name' => 'nama_ibu', 'label' => 'Nama Ibu', 'type' => 'text', 'required' => false],
                ],
            ],
            [
                'nama_surat' => 'Surat Keterangan Kematian',
                'kode_surat' => 'SKM',
                'deskripsi' => 'Menerangkan peristiwa kematian warga.',
                'is_active' => true,
                'fields' => [
                    ['name' => 'tanggal_meninggal', 'label' => 'Tanggal Meninggal', 'type' => 'date', 'required' => true],
                    ['name' => 'tempat_meninggal', 'label' => 'Tempat Meninggal', 'type' => 'text', 'required' => true],
                    ['name' => 'sebab_meninggal', 'label' => 'Sebab Meninggal', 'type' => 'text', 'required' => false],
                ],
            ],
        ];
    }
}
