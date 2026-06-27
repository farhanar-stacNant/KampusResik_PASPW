<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LaporanSampah extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'laporan_sampahs';

    protected $fillable = [
        'kode_laporan',
        'nama_pelapor',
        'kontak_pelapor',
        'no_wa',
        'kategori_id',
        'jenis_sampah',
        'lokasi',
        'latitude',
        'longitude',
        'deskripsi',
        'foto',
        'status',
        'prioritas',
        'catatan_petugas',
        'petugas_id',
        'selesai_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'selesai_at' => 'datetime',
    ];

    // Relasi ke kategori
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriSampah::class, 'kategori_id');
    }

    // Relasi ke petugas (users)
    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    // Relasi ke timeline
    public function timelines(): HasMany
    {
        return $this->hasMany(LaporanTimeline::class, 'laporan_id')->orderBy('created_at');
    }
}