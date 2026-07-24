<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_surats', function (Blueprint $table) {
            $table->id();
            $table->string('nama_surat');
            $table->string('slug')->unique();
            $table->string('kode_surat', 20)->comment('Kode singkat untuk penomoran, mis. SKTM');
            $table->text('deskripsi')->nullable();
            $table->string('template_path')->nullable()->comment('Path relatif template .docx pada disk public');
            $table->string('template_original_name')->nullable();
            $table->json('fields')->nullable()->comment('Definisi field tambahan dinamis (schema)');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_surats');
    }
};
