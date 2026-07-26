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
