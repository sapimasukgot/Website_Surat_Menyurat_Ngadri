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
            $model = JenisSurat::updateOrCreate(['kode_surat' => $item['kode_surat']], $item);

            $this->attachTemplateStub($model);
        }
    }

    private function attachTemplateStub(JenisSurat $model): void
    {
        $stub = database_path('templates/'.Str::slug($model->kode_surat).'.docx');

        if (! file_exists($stub)) {
            return;
        }

        $path = 'templates/'.Str::slug($model->kode_surat).'.docx';

        if ($model->template_path && $model->template_path !== $path) {
            Storage::disk('public')->delete($model->template_path);
        }

        Storage::disk('public')->put($path, file_get_contents($stub));

        $model->update([
            'template_path' => $path,
            'template_original_name' => $model->kode_surat.'.docx',
        ]);
    }

    private function t(string $name, string $label, string $type = 'text', bool $required = false, array $options = []): array
    {
        $field = ['name' => $name, 'label' => $label, 'type' => $type, 'required' => $required];

        if ($type === 'select') {
            $field['options'] = $options ?: ['Ya', 'Tidak'];
        }

        return $field;
    }

    private function data(): array
    {
        return [
            [
                'nama_surat' => 'Surat Keterangan Tidak Mampu (SKTM)',
                'kode_surat' => 'SKTM',
                'kode_klasifikasi' => '420',
                'deskripsi' => 'Menerangkan warga tergolong keluarga kurang/tidak mampu.',
                'is_active' => true,
                'fields' => [
                    $this->t('keperluan', 'Keperluan', 'text', true),
                    $this->t('penghasilan', 'Penghasilan per Bulan'),
                    $this->t('keterangan_tambahan', 'Keterangan Tambahan', 'textarea'),
                ],
            ],
            [
                'nama_surat' => 'SKTM untuk Pemasangan Listrik PLN Gratis',
                'kode_surat' => 'SKTM-PLN',
                'kode_klasifikasi' => '400',
                'deskripsi' => 'SKTM khusus permohonan pemasangan sambungan listrik PLN gratis.',
                'is_active' => true,
                'fields' => [
                    $this->t('daya', 'Daya yang Dimohon'),
                    $this->t('keterangan_tambahan', 'Keterangan Tambahan', 'textarea'),
                ],
            ],
            [
                'nama_surat' => 'SKTM untuk Beasiswa / Sekolah Anak',
                'kode_surat' => 'SKTM-BEA',
                'kode_klasifikasi' => '422.5',
                'deskripsi' => 'SKTM untuk pengajuan beasiswa/keringanan biaya sekolah anak.',
                'is_active' => true,
                'fields' => [
                    $this->t('keperluan', 'Keperluan (mis. Pengajuan BKSM/KIP)', 'text', true),
                    $this->t('anak', 'Anak (dipilih dari satu KK pemohon)', 'anak_kk', true),
                    $this->t('sekolah', 'Sekolah / Pendidikan'),
                ],
            ],
            [
                'nama_surat' => 'Surat Keterangan Domisili',
                'kode_surat' => 'SKD',
                'kode_klasifikasi' => '470',
                'deskripsi' => 'Menerangkan tempat tinggal / domisili warga.',
                'is_active' => true,
                'fields' => [
                    $this->t('keperluan', 'Keperluan', 'text', true),
                    $this->t('lama_tinggal', 'Lama Tinggal'),
                ],
            ],
            [
                'nama_surat' => 'Surat Keterangan Usaha (SKU)',
                'kode_surat' => 'SKU',
                'kode_klasifikasi' => '470',
                'deskripsi' => 'Menerangkan warga memiliki usaha.',
                'is_active' => true,
                'fields' => [
                    $this->t('nama_usaha', 'Nama Usaha', 'text', true),
                    $this->t('jenis_usaha', 'Jenis Usaha', 'text', true),
                    $this->t('alamat_usaha', 'Alamat Usaha', 'textarea'),
                    $this->t('keperluan', 'Keperluan', 'text', true),
                ],
            ],
            [
                'nama_surat' => 'Surat Keterangan Domisili Usaha',
                'kode_surat' => 'SKDU',
                'kode_klasifikasi' => '470',
                'deskripsi' => 'Menerangkan domisili/lokasi usaha warga.',
                'is_active' => true,
                'fields' => [
                    $this->t('nama_usaha', 'Nama Usaha', 'text', true),
                    $this->t('jenis_usaha', 'Jenis Usaha', 'text', true),
                    $this->t('alamat_usaha', 'Alamat Usaha', 'textarea'),
                    $this->t('keperluan', 'Keperluan', 'text', true),
                ],
            ],
            [
                'nama_surat' => 'Surat Keterangan Kematian',
                'kode_surat' => 'SKM',
                'kode_klasifikasi' => null,
                'deskripsi' => 'Menerangkan peristiwa kematian warga.',
                'is_active' => true,
                'fields' => [
                    $this->t('tanggal_meninggal', 'Hari / Tanggal Meninggal', 'text', true),
                    $this->t('tempat_meninggal', 'Tempat Meninggal', 'text', true),
                    $this->t('sebab_meninggal', 'Sebab Meninggal'),
                    $this->t('dimakamkan', 'Dimakamkan di'),
                    $this->t('keperluan', 'Keperluan'),
                ],
            ],
            [
                'nama_surat' => 'Surat Keterangan Kelahiran',
                'kode_surat' => 'SKL',
                'kode_klasifikasi' => '474.1',
                'deskripsi' => 'Menerangkan peristiwa kelahiran.',
                'is_active' => true,
                'fields' => [
                    $this->t('nama_anak', 'Nama Anak', 'text', true),
                    $this->t('jenis_kelamin_anak', 'Jenis Kelamin Anak'),
                    $this->t('tempat_lahir_anak', 'Tempat Lahir Anak'),
                    $this->t('tanggal_lahir_anak', 'Tanggal Lahir Anak', 'date'),
                    $this->t('anak_ke', 'Anak ke-'),
                    $this->t('nama_ayah', 'Nama Ayah'),
                    $this->t('nama_ibu', 'Nama Ibu'),
                    $this->t('keperluan', 'Keperluan'),
                ],
            ],
            [
                'nama_surat' => 'Surat Keterangan Kehilangan',
                'kode_surat' => 'SKH',
                'kode_klasifikasi' => '470',
                'deskripsi' => 'Menerangkan kehilangan barang/dokumen.',
                'is_active' => true,
                'fields' => [
                    $this->t('barang_hilang', 'Barang / Dokumen yang Hilang', 'textarea', true),
                    $this->t('tempat_kehilangan', 'Tempat Kehilangan'),
                    $this->t('waktu_kehilangan', 'Waktu Kehilangan'),
                    $this->t('keperluan', 'Keperluan'),
                ],
            ],
            [
                'nama_surat' => 'Surat Keterangan Belum Menikah',
                'kode_surat' => 'SKBM',
                'kode_klasifikasi' => '470',
                'deskripsi' => 'Menerangkan warga belum pernah menikah.',
                'is_active' => true,
                'fields' => [
                    $this->t('keperluan', 'Keperluan', 'text', true),
                    $this->t('keterangan_tambahan', 'Keterangan Tambahan', 'textarea'),
                ],
            ],
            [
                'nama_surat' => 'Surat Keterangan (Umum)',
                'kode_surat' => 'SK',
                'kode_klasifikasi' => '470',
                'deskripsi' => 'Surat keterangan umum dengan isi bebas.',
                'is_active' => true,
                'fields' => [
                    $this->t('isi_keterangan', 'Isi Keterangan', 'textarea', true),
                    $this->t('keperluan', 'Keperluan'),
                ],
            ],
            [
                'nama_surat' => 'Surat Pengantar SKCK',
                'kode_surat' => 'SKCK',
                'kode_klasifikasi' => null,
                'deskripsi' => 'Surat pengantar untuk pembuatan SKCK di kepolisian.',
                'is_active' => true,
                'fields' => [
                    $this->t('nama_ayah', 'Nama Ayah'),
                    $this->t('nama_ibu', 'Nama Ibu'),
                    $this->t('keperluan', 'Keperluan', 'text', true),
                ],
            ],
            [
                'nama_surat' => 'Surat Izin Keramaian',
                'kode_surat' => 'SIK',
                'kode_klasifikasi' => '470',
                'deskripsi' => 'Izin penyelenggaraan keramaian / acara, dengan atau tanpa hiburan.',
                'is_active' => true,
                'fields' => [
                    $this->t('nama_acara', 'Nama / Jenis Acara (Hajat)', 'text', true),
                    $this->t('tempat_acara', 'Tempat Acara', 'text', true),
                    $this->t('tanggal_acara', 'Hari / Tanggal Acara', 'date', true),
                    $this->t('waktu_acara', 'Waktu Acara'),
                    // Bila "Ya", blok ${hiburan} ... ${/hiburan} pada template
                    // (surat pernyataan panitia penyelenggara hiburan) ikut tercetak.
                    $this->t('hiburan', 'Ada Hiburan?', 'select', true, ['Ya', 'Tidak']),
                    $this->t('jenis_hiburan', 'Jenis Hiburan (mis. Orkes Melayu)'),
                    $this->t('nama_organisasi', 'Nama Organisasi / Grup Hiburan'),
                    $this->t('nama_ketua', 'Nama Ketua Panitia'),
                    $this->t('wakil_ketua', 'Nama Wakil Ketua Panitia'),
                    $this->t('sekretaris', 'Nama Sekretaris Panitia'),
                    $this->t('hari_hiburan', 'Hari Pelaksanaan Hiburan'),
                    $this->t('tanggal_hiburan', 'Tanggal Pelaksanaan Hiburan', 'date'),
                    $this->t('jam_hiburan', 'Jam Pelaksanaan Hiburan'),
                    $this->t('tempat_hiburan', 'Tempat Pelaksanaan Hiburan'),
                    $this->t('keperluan', 'Keperluan'),
                ],
            ],
            [
                'nama_surat' => 'Surat Keterangan Kepergian Anggota Keluarga',
                'kode_surat' => 'SKKP',
                'kode_klasifikasi' => '470',
                'deskripsi' => 'Menerangkan suami/istri/ibu pemohon sedang pergi bekerja ke luar daerah atau luar negeri.',
                'is_active' => true,
                'fields' => [
                    $this->t('hubungan_keluarga', 'Hubungan Keluarga yang Pergi', 'select', true, ['Suami', 'Istri', 'Ibu']),
                    $this->t('nama_yang_pergi', 'Nama yang Bersangkutan', 'text', true),
                    $this->t('alamat_yang_pergi', 'Alamat / Dusun yang Bersangkutan', 'textarea'),
                    $this->t('tujuan_kepergian', 'Tujuan Kepergian (kota / negara)', 'text', true),
                    $this->t('sejak', 'Pergi Sejak (bulan / tahun)', 'text', true),
                    $this->t('keperluan', 'Keperluan Surat', 'textarea', true),
                ],
            ],
            [
                'nama_surat' => 'Surat Keterangan Ahli Waris',
                'kode_surat' => 'SKAW',
                'kode_klasifikasi' => '470',
                'deskripsi' => 'Menerangkan ahli waris yang sah.',
                'is_active' => true,
                'fields' => [
                    $this->t('nama_pewaris', 'Nama Almarhum/Almarhumah (Pewaris)', 'text', true),
                    $this->t('daftar_ahli_waris', 'Daftar Ahli Waris', 'textarea', true),
                    $this->t('keperluan', 'Keperluan', 'text', true),
                ],
            ],
            [
                'nama_surat' => 'Surat Keterangan Tanah',
                'kode_surat' => 'SKT',
                'kode_klasifikasi' => '590',
                'deskripsi' => 'Menerangkan kepemilikan/penguasaan tanah.',
                'is_active' => true,
                'fields' => [
                    $this->t('lokasi_tanah', 'Lokasi Tanah', 'text', true),
                    $this->t('luas_tanah', 'Luas Tanah'),
                    $this->t('batas_tanah', 'Batas-batas Tanah', 'textarea'),
                    $this->t('keperluan', 'Keperluan', 'text', true),
                ],
            ],
            [
                'nama_surat' => 'Surat Keterangan Penghasilan',
                'kode_surat' => 'SKP',
                'kode_klasifikasi' => '422.5',
                'deskripsi' => 'Menerangkan penghasilan warga.',
                'is_active' => true,
                'fields' => [
                    $this->t('penghasilan', 'Penghasilan per Bulan', 'text', true),
                    $this->t('keperluan', 'Keperluan', 'text', true),
                ],
            ],
            [
                'nama_surat' => 'Surat Keterangan Boro Bekerja',
                'kode_surat' => 'SKBK',
                // Kode klasifikasi belum diberikan kantor desa — bisa diisi
                // sendiri kapan saja lewat menu Jenis Surat.
                'kode_klasifikasi' => null,
                'deskripsi' => 'Keterangan warga yang merantau/boro bekerja ke luar daerah.',
                'is_active' => true,
                'fields' => [
                    $this->t('tempat_tujuan', 'Tempat Tujuan', 'textarea', true),
                    $this->t('bekal', 'Bekal yang Dibawa'),
                    $this->t('berlaku_mulai', 'Berlaku Mulai', 'date'),
                    $this->t('berlaku_sampai', 'Berlaku Sampai', 'date'),
                    $this->t('keperluan', 'Keperluan', 'text', true),
                ],
            ],
            [
                'nama_surat' => 'Surat Pernyataan',
                'kode_surat' => 'SPN',
                'kode_klasifikasi' => null,
                'deskripsi' => 'Surat pernyataan pribadi warga. Umumnya dicetak tanpa kop — hilangkan centang "Gunakan kop surat" saat membuat surat.',
                'is_active' => true,
                'fields' => [
                    $this->t('isi_pernyataan', 'Isi Pernyataan', 'textarea', true),
                ],
            ],
        ];
    }
}
