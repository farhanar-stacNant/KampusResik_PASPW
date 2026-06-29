<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_timelines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_id')->constrained('laporan_sampahs')->onDelete('cascade');
            $table->string('status');
            $table->text('deskripsi')->nullable();
            $table->foreignId('petugas_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_timelines');
    }
};
