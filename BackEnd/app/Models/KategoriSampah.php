<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriSampah extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'kategori_sampahs';

    protected $fillable = [
        'nama_kategori',
        'deskripsi',
        'level_risiko',
        'status_aktif',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
    ];

    // Relasi ke laporan
    public function laporans(): HasMany
    {
        return $this->hasMany(LaporanSampah::class, 'kategori_id');
    }
}