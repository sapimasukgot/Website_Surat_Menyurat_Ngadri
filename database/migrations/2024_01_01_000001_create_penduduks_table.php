<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penduduks', function (Blueprint $table) {
            $table->id();
            $table->char('nik', 16)->unique()->comment('Nomor Induk Kependudukan, 16 digit, unik');
            $table->char('no_kk', 16)->index()->comment('Nomor Kartu Keluarga');
            $table->string('nama_lengkap');
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->comment('L = Laki-laki, P = Perempuan');
            $table->string('golongan_darah', 15)->nullable()->comment('BIP: GOL. DRH');
            $table->string('agama')->nullable();
            $table->string('pendidikan')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->enum('status_kawin', ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'])
                  ->default('Belum Kawin');
            $table->string('status_hubungan')->nullable()->comment('BIP: SHDK (Status Hubungan Dalam Keluarga)');
            $table->string('nama_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->text('alamat');
            $table->string('rt', 3)->nullable();
            $table->string('rw', 3)->nullable();
            $table->string('dusun')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('nama_lengkap');
            $table->index(['rt', 'rw']);
            $table->index('dusun');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penduduks');
    }
};
