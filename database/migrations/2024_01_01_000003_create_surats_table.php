<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surats', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat')->unique();
            $table->foreignId('jenis_surat_id')->constrained('jenis_surats')->restrictOnDelete();
            $table->foreignId('penduduk_id')->constrained('penduduks')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete()
                  ->comment('Admin pembuat surat');
            $table->date('tanggal_surat');
            $table->json('data_surat')->comment('Snapshot seluruh data terisi agar surat konsisten walau template/penduduk berubah');
            $table->string('file_path')->nullable()->comment('Path hasil generate .docx');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('tanggal_surat');
            $table->index(['jenis_surat_id', 'tanggal_surat']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surats');
    }
};
