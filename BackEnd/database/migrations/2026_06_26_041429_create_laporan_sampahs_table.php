<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_sampahs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_laporan')->unique();
            $table->string('nama_pelapor');
            $table->string('kontak_pelapor');
            $table->string('no_wa')->nullable();
            $table->string('jenis_sampah', 10)->nullable();
            $table->foreignId('kategori_id')->constrained('kategori_sampahs');
            $table->text('lokasi');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->text('deskripsi');
            $table->string('foto')->nullable();
            $table->enum('status', ['baru', 'dikirim', 'diterima', 'diproses', 'selesai', 'ditolak'])->default('baru');
            $table->enum('prioritas', ['rendah', 'normal', 'tinggi'])->default('normal');
            $table->text('catatan_petugas')->nullable();
            $table->foreignId('petugas_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('selesai_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_sampahs');
    }
};