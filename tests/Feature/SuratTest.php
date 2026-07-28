<?php
namespace Tests\Feature;

use App\Models\JenisSurat;
use App\Models\Penduduk;
use App\Models\User;
use App\Services\NomorSuratService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuratTest extends TestCase
{
    use RefreshDatabase;

    public function test_nomor_surat_terformat_dengan_benar(): void
    {
        $jenis = JenisSurat::factory()->create([
            'kode_surat' => 'SKTM',
            'kode_klasifikasi' => '420',
        ]);

        $nomor = app(NomorSuratService::class)->generate($jenis, Carbon::create(2026, 7, 5));

        // Format resmi: {kode klasifikasi}/{nomor urut}/{kode desa}/{tahun}
        $this->assertSame('420/001/'.config('desa.kode').'/2026', $nomor);
    }

    public function test_nomor_surat_tanpa_kode_klasifikasi_tidak_menyisakan_garis_miring(): void
    {
        $jenis = JenisSurat::factory()->create([
            'kode_surat' => 'SPN',
            'kode_klasifikasi' => null,
        ]);

        $nomor = app(NomorSuratService::class)->generate($jenis, Carbon::create(2026, 7, 5));

        $this->assertSame('001/'.config('desa.kode').'/2026', $nomor);
    }

    public function test_nomor_urut_berjalan_bersama_untuk_semua_jenis_dan_klasifikasi(): void
    {
        // Satu nomor urut berjalan dipakai bersama SEMUA jenis surat, terlepas dari
        // jenis maupun kode klasifikasinya — sesuai buku agenda surat keluar desa.
        $jenisA = JenisSurat::factory()->create(['kode_surat' => 'SKD', 'kode_klasifikasi' => '470']);
        $jenisB = JenisSurat::factory()->create(['kode_surat' => 'SKTM', 'kode_klasifikasi' => '420']);
        $jenisC = JenisSurat::factory()->create(['kode_surat' => 'SPN', 'kode_klasifikasi' => null]);

        $tanggal = Carbon::create(2026, 7, 5);

        $suratA = $this->buatSurat($jenisA, $tanggal);
        $suratB = $this->buatSurat($jenisB, $tanggal);

        // Meski klasifikasinya berbeda-beda, nomor urutnya lanjut: 1, 2, lalu 3.
        $this->assertSame('470/001/'.config('desa.kode').'/2026', $suratA->nomor_surat);
        $this->assertSame('420/002/'.config('desa.kode').'/2026', $suratB->nomor_surat);
        $this->assertSame(
            '003/'.config('desa.kode').'/2026',
            app(NomorSuratService::class)->generate($jenisC, $tanggal)
        );
    }

    public function test_menyunting_nomor_urut_lebih_tinggi_menjadi_patokan_baru(): void
    {
        $jenis = JenisSurat::factory()->create(['kode_surat' => 'SKD', 'kode_klasifikasi' => '470']);
        $tanggal = Carbon::create(2026, 7, 5);
        $surat = $this->buatSurat($jenis, $tanggal);

        // Operator menyunting nomor surat ke nomor urut yang jauh lebih tinggi
        // (mis. menyesuaikan dengan buku agenda manual yang sudah sampai 212).
        $this->actingAs(User::factory()->create())
            ->put(route('surat.update', $surat), [
                'nomor_surat' => '470/212/'.config('desa.kode').'/2026',
                'tanggal_surat' => '2026-07-05',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('surat.show', $surat));

        $this->assertSame(212, $surat->fresh()->nomor_urut);

        // Surat berikutnya harus melanjutkan dari patokan baru itu, bukan dari 2.
        $this->assertSame(213, app(NomorSuratService::class)->nextUrut(2026));
    }

    public function test_nomor_urut_terbaca_untuk_jenis_surat_tanpa_kode_klasifikasi(): void
    {
        // Jenis surat tanpa kode klasifikasi menghasilkan nomor 3 segmen
        // ("007/kode_desa/tahun"), jadi posisi segmen nomor urut bergeser ke
        // depan. Pembacaannya harus tetap benar, bukan malah membaca kode desa.
        $jenis = JenisSurat::factory()->create(['kode_surat' => 'SPN', 'kode_klasifikasi' => null]);
        $surat = $this->buatSurat($jenis, Carbon::create(2026, 7, 5));

        $this->assertSame(
            77,
            app(NomorSuratService::class)->bacaUrut('077/'.config('desa.kode').'/2026', $jenis)
        );

        $this->actingAs(User::factory()->create())
            ->put(route('surat.update', $surat), [
                'nomor_surat' => '077/'.config('desa.kode').'/2026',
                'tanggal_surat' => '2026-07-05',
            ])->assertSessionHasNoErrors();

        $this->assertSame(77, $surat->fresh()->nomor_urut);
    }

    public function test_surat_lama_dengan_nomor_urut_kembar_tetap_bisa_disunting(): void
    {
        // Data warisan penomoran versi lama (dihitung per jenis surat) bisa punya
        // angka urut kembar di dua klasifikasi berbeda. Surat seperti ini harus
        // tetap bisa disunting selama nomornya sendiri tidak diubah.
        $jenisA = JenisSurat::factory()->create(['kode_surat' => 'SKD', 'kode_klasifikasi' => '470']);
        $jenisB = JenisSurat::factory()->create(['kode_surat' => 'SKTM', 'kode_klasifikasi' => '420']);

        $suratA = $this->buatSurat($jenisA, Carbon::create(2026, 7, 5), 1);
        $suratB = $this->buatSurat($jenisB, Carbon::create(2026, 7, 6), 1);

        $this->actingAs(User::factory()->create())
            ->put(route('surat.update', $suratB), [
                'nomor_surat' => $suratB->nomor_surat,
                'tanggal_surat' => '2026-07-06',
                'keterangan' => 'Perbaikan isi tanpa mengubah nomor.',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('surat.show', $suratB));

        // Sebaliknya, MENGUBAH nomor ke angka urut yang sudah dipakai surat lain
        // harus ditolak — nomor urut berjalan bersama, jadi tidak boleh kembar.
        $this->actingAs(User::factory()->create())
            ->put(route('surat.update', $suratB), [
                'nomor_surat' => '420/0001/'.config('desa.kode').'/2026',
                'tanggal_surat' => '2026-07-06',
            ])->assertSessionHasErrors('nomor_surat');

        $this->assertSame(1, $suratA->fresh()->nomor_urut);
    }

    public function test_nomor_urut_kembali_ke_satu_saat_ganti_tahun(): void
    {
        $jenis = JenisSurat::factory()->create(['kode_surat' => 'SKD', 'kode_klasifikasi' => '470']);

        // Tahun 2026 sudah sampai nomor urut 212.
        $this->buatSurat($jenis, Carbon::create(2026, 12, 30), 212);

        $service = app(NomorSuratService::class);

        $this->assertSame(213, $service->nextUrut(2026));
        $this->assertSame(1, $service->nextUrut(2027));
        $this->assertSame(
            '470/001/'.config('desa.kode').'/2027',
            $service->generate($jenis, Carbon::create(2027, 1, 2))
        );
    }

    private function buatSurat(JenisSurat $jenis, Carbon $tanggal, ?int $urut = null): \App\Models\Surat
    {
        $service = app(NomorSuratService::class);
        $urut ??= $service->nextUrut((int) $tanggal->year);

        return \App\Models\Surat::create([
            'nomor_surat' => $service->format($jenis, $tanggal, $urut),
            'nomor_urut' => $urut,
            'jenis_surat_id' => $jenis->id,
            'penduduk_id' => Penduduk::factory()->create()->id,
            'user_id' => User::factory()->create()->id,
            'tanggal_surat' => $tanggal,
            'data_surat' => [],
        ]);
    }

    public function test_membuat_draft_surat_menyimpan_snapshot(): void
    {
        $admin = User::factory()->create();
        $jenis = JenisSurat::factory()->create();
        $penduduk = Penduduk::factory()->create();

        $this->actingAs($admin)->post(route('surat.store'), [
            'jenis_surat_id' => $jenis->id,
            'penduduk_id' => $penduduk->id,
            'tanggal_surat' => '2026-07-05',
            'data' => ['keperluan' => 'Beasiswa'],
        ])->assertRedirect();

        $this->assertDatabaseCount('surats', 1);
        $surat = \App\Models\Surat::first();
        $this->assertEquals($penduduk->nama_lengkap, $surat->data('nama'));
        $this->assertEquals('Beasiswa', $surat->data('keperluan'));
    }
}
