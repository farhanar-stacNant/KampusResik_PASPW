<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriSampah;

class KategoriSampahSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'nama_kategori' => 'Sampah Organik',
                'deskripsi' => 'Sisa makanan, daun, ranting, dan bahan alami lainnya yang mudah terurai',
                'level_risiko' => 'rendah'
            ],
            [
                'nama_kategori' => 'Sampah Plastik',
                'deskripsi' => 'Botol plastik, kantong kresek, kemasan makanan, sedotan, dan produk plastik lainnya',
                'level_risiko' => 'sedang'
            ],
            [
                'nama_kategori' => 'Sampah Medis',
                'deskripsi' => 'Masker bekas, jarum suntik, perban, kapas, dan limbah rumah sakit/klinik',
                'level_risiko' => 'tinggi'
            ],
            [
                'nama_kategori' => 'Sampah B3',
                'deskripsi' => 'Baterai bekas, limbah kimia, cat, pelarut, dan bahan berbahaya beracun lainnya',
                'level_risiko' => 'tinggi'
            ],
            [
                'nama_kategori' => 'Sampah Kertas',
                'deskripsi' => 'Kardus, koran, kertas bekas, dokumen, dan produk dari serat kayu',
                'level_risiko' => 'rendah'
            ],
            [
                'nama_kategori' => 'Sampah Elektronik',
                'deskripsi' => 'Charger rusak, kabel USB, baterai laptop, dan perangkat elektronik bekas',
                'level_risiko' => 'sedang'
            ]
        ];

        foreach ($data as $item) {
            KategoriSampah::create($item);
        }
    }
}