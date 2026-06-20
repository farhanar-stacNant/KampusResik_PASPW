<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::create('laporan_sampah', function (Blueprint $table) {
        $table->id();
        // Relasi ke tabel kategori_sampah yang sudah Anda buat
        $table->foreignId('kategori_id')->constrained('kategori_sampah')->onDelete('cascade');
        $table->string('nama_pelapor')->nullable()->default('Anonim');
        $table->string('latitude');
        $table->string('longitude');
        $table->text('deskripsi_singkat');
        $table->string('foto_sebelum'); 
        $table->string('foto_sesudah')->nullable(); 
        $table->enum('status', ['menunggu', 'diproses', 'selesai'])->default('menunggu');
        // Relasi ke tabel users (Petugas yang menangani)
        $table->foreignId('petugas_id')->nullable()->constrained('users')->onDelete('set null'); 
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_sampah');
    }
};
