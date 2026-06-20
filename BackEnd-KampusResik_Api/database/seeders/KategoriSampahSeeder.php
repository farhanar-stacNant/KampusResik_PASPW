<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KategoriSampahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
public function run(): void
{
    \App\Models\KategoriSampah::create([
        'nama_kategori' => 'Organik',
        'deskripsi_penanganan' => 'Sampah sisa makanan, daun, atau kertas. Tempatkan di komposter atau pisahkan agar tidak membusuk di tempat terbuka.'
    ]);

    \App\Models\KategoriSampah::create([
        'nama_kategori' => 'Anorganik',
        'deskripsi_penanganan' => 'Sampah plastik, botol, kaleng, atau kaca. Kumpulkan untuk disalurkan ke bank sampah kampus/daur ulang.'
    ]);

    \App\Models\KategoriSampah::create([
        'nama_kategori' => 'B3 (Bahan Berbahaya & Beracun)',
        'deskripsi_penanganan' => 'Sampah masker bekas, baterai, lampu, atau limbah lab kimia. Wajib ditangani dengan sarung tangan dan wadah khusus.'
    ]);
}
}
