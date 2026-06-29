<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $fillable = [
        'tanggal',
        'petugas_id',
        'lokasi',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}
