<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            $table->boolean('pakai_kop')->default(true)->after('tanggal_surat')
                  ->comment('Jika false, tabel kop surat dihapus saat berkas .docx dibuat');
        });
    }

    public function down(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            $table->dropColumn('pakai_kop');
        });
    }
};
