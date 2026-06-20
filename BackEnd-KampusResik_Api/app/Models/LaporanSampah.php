<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanSampah extends Model
{
    use HasFactory;
    protected $table = 'laporan_sampah';

    protected $fillable = [
        'kategori_id',
        'nama_pelapor',
        'latitude',
        'longitude',
        'deskripsi_singkat',
        'foto_sebelum',
        'foto_sesudah',
        'status',
        'petugas_id',
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriSampah::class, 'kategori_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}