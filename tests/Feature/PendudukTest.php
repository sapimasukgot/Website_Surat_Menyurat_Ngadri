<?php
namespace Tests\Feature;

use App\Models\Penduduk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PendudukTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create();
    }

    public function test_admin_dapat_menambah_penduduk(): void
    {
        $payload = Penduduk::factory()->make()->toArray();
        $payload['tanggal_lahir'] = '1990-01-01';

        $this->actingAs($this->admin())
            ->post(route('penduduk.store'), $payload)
            ->assertRedirect(route('penduduk.index'));

        $this->assertDatabaseHas('penduduks', ['nik' => $payload['nik']]);
    }

    public function test_nik_harus_unik(): void
    {
        $existing = Penduduk::factory()->create();
        $payload = Penduduk::factory()->make(['nik' => $existing->nik])->toArray();

        $this->actingAs($this->admin())
            ->post(route('penduduk.store'), $payload)
            ->assertSessionHasErrors('nik');
    }
}
