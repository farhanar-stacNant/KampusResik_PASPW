<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Petugas extends Model
{
    protected $fillable = [
        'nama',
        'nip',
        'email',
        'password',
        'jabatan',
        'no_telepon',
        'alamat',
        'area_tugas',
        'status_aktif',
        'foto'
    ];

    protected $hidden = ['password'];
}