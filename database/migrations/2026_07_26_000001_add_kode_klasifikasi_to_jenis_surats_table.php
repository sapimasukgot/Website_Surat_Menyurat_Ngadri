<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jenis_surats', function (Blueprint $table) {
            $table->string('kode_klasifikasi', 20)->nullable()->after('kode_surat')
                  ->comment('Kode klasifikasi arsip pada nomor surat, mis. 470 / 422.5');
        });
    }

    public function down(): void
    {
        Schema::table('jenis_surats', function (Blueprint $table) {
            $table->dropColumn('kode_klasifikasi');
        });
    }
};
