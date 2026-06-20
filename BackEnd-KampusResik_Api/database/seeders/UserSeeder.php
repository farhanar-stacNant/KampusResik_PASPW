<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'Admin KampusResik',
                'email' => 'admin@kampus.ac.id',
                'password' => Hash::make('password123'), // Password Test
                'role' => 'admin',
                'created_at' => now(),
            ],
            [
                'name' => 'Petugas Lapangan',
                'email' => 'petugas@kampus.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'petugas',
                'created_at' => now(),
            ]
        ]);
    }
}
