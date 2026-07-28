<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Nomor urut surat disimpan eksplisit sebagai angka, bukan hanya "tersembunyi"
     * di dalam string nomor_surat. Ini yang membuat satu nomor urut berjalan bisa
     * dipakai bersama oleh SEMUA jenis surat, dan membuat nomor urut tertinggi
     * bisa dijadikan patokan nomor berikutnya (termasuk kalau operator mengubah
     * nomor surat secara manual ke angka yang lebih tinggi).
     */
    public function up(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            $table->unsignedInteger('nomor_urut')->nullable()->after('nomor_surat');
            // Indeks gabungan: pencarian nomor urut tertinggi selalu dibatasi per
            // tahun tanggal surat, jadi kedua kolom dipakai bersama.
            $table->index(['tanggal_surat', 'nomor_urut']);
        });

        $this->backfill();
    }

    public function down(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            $table->dropIndex(['tanggal_surat', 'nomor_urut']);
            $table->dropColumn('nomor_urut');
        });
    }

    /**
     * Isi nomor_urut untuk surat yang sudah ada dengan membaca ulang segmen
     * nomor urut dari nomor_surat yang tercetak, supaya penomoran berikutnya
     * melanjutkan angka yang sudah dipakai (tidak mulai dari 1 lagi).
     */
    private function backfill(): void
    {
        $service = app(\App\Services\NomorSuratService::class);
        $jenisCache = [];

        DB::table('surats')->select('id', 'nomor_surat', 'jenis_surat_id')
            ->orderBy('id')
            ->chunkById(200, function ($surats) use ($service, &$jenisCache) {
                foreach ($surats as $surat) {
                    $jenisId = $surat->jenis_surat_id;

                    $jenisCache[$jenisId] ??= \App\Models\JenisSurat::withTrashed()->find($jenisId);

                    $urut = $service->bacaUrut((string) $surat->nomor_surat, $jenisCache[$jenisId]);

                    if ($urut !== null && $urut >= 1 && $urut <= 99999) {
                        DB::table('surats')->where('id', $surat->id)->update(['nomor_urut' => $urut]);
                    }
                }
            });

        $belum = DB::table('surats')->whereNull('nomor_urut')->count();

        if ($belum > 0) {
            // Bukan kegagalan fatal: nomor surat yang sudah terbit tidak diubah.
            // Tapi operator perlu tahu supaya bisa menyesuaikan nomor urut lewat
            // menu Sunting Surat kalau ternyata ada yang penting.
            echo PHP_EOL.'  [!] '.$belum.' surat tidak bisa dibaca nomor urutnya secara otomatis '
                .'(susunan nomornya tidak mengikuti format resmi). Nomor surat lama tetap utuh; '
                .'nomor urutnya bisa disesuaikan lewat menu Sunting Surat bila perlu.'.PHP_EOL;
        }
    }

};
