<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'      => 'Administrator',
            'email'     => 'admin@kampusresik.id',
            'password'  => Hash::make('admin123'),
            'role'      => 'admin',
            'telepon'   => '081234567890',
        ]);

        User::create([
            'name'      => 'Petugas Kebersihan',
            'email'     => 'petugas@kampusresik.id',
            'password'  => Hash::make('petugas123'),
            'role'      => 'petugas',
            'telepon'   => '081298765432',
        ]);
    }
}