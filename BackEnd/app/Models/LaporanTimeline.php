<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanTimeline extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'laporan_timelines';

    protected $fillable = [
        'laporan_id',
        'status',
        'deskripsi',
        'petugas_id',
    ];

    // Relasi ke laporan
    public function laporan(): BelongsTo
    {
        return $this->belongsTo(LaporanSampah::class, 'laporan_id');
    }

    // Relasi ke petugas (users)
    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}