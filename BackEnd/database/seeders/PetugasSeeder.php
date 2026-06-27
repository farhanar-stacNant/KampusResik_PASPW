<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Petugas;

class PetugasSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'nama' => 'Ahmad Wijaya',
                'nip' => 'PTG001',
                'email' => 'ahmad@kampusresik.id',
                'password' => bcrypt('petugas123'),
                'jabatan' => 'petugas_lapangan',
                'no_telepon' => '083333333333',
                'alamat' => 'Jl. Melati No. 12, Depok',
                'area_tugas' => 'Gedung Rektorat & Fakultas Teknik'
            ],
            [
                'nama' => 'Siti Nurhaliza',
                'nip' => 'PTG002',
                'email' => 'siti@kampusresik.id',
                'password' => bcrypt('petugas123'),
                'jabatan' => 'petugas_lapangan',
                'no_telepon' => '084444444444',
                'alamat' => 'Jl. Mawar No. 45, Depok',
                'area_tugas' => 'Fakultas Kedokteran & Klinik'
            ],
            [
                'nama' => 'Bambang Sutrisno',
                'nip' => 'PTG003',
                'email' => 'bambang@kampusresik.id',
                'password' => bcrypt('petugas123'),
                'jabatan' => 'petugas_lapangan',
                'no_telepon' => '085555555555',
                'alamat' => 'Jl. Anggrek No. 78, Depok',
                'area_tugas' => 'Seluruh Area Kampus'
            ]
        ];

        foreach ($data as $item) {
            Petugas::create($item);
        }
    }
}