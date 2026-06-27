<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LaporanSampah;

class LaporanSampahSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'kode_laporan' => 'KRS-20260626-A1B2',
                'nama_pelapor' => 'Budi Santoso',
                'kontak_pelapor' => '081234567890',
                'kategori_id' => 2,
                'lokasi' => 'Gedung Rektorat Lt. 1, depan lift utama',
                'latitude' => -6.2088,
                'longitude' => 106.8456,
                'deskripsi' => 'Tumpukan kantong plastik dan botol minum berserakan di pojok koridor sebelah kanan lift. Sudah 3 hari tidak diangkut dan mulai bau.',
                'status' => 'baru'
            ],
            [
                'kode_laporan' => 'KRS-20260625-C3D4',
                'nama_pelapor' => 'Ani Wulandari',
                'kontak_pelapor' => '082345678901',
                'kategori_id' => 3,
                'lokasi' => 'Fakultas Kedokteran, area parkir belakang gedung C',
                'latitude' => -6.1751,
                'longitude' => 106.8650,
                'deskripsi' => 'Ditemukan masker bekas dan jarum suntik terbuang di semak-semak dekat tempat parkir motor. Sangat berbahaya untuk mahasiswa yang lewat.',
                'status' => 'diproses',
                'catatan_petugas' => 'Petugas Siti Nurhaliza sedang menuju lokasi. Mohon warga tidak menyentuh barang tersebut.',
                'petugas_id' => 2
            ],
            [
                'kode_laporan' => 'KRS-20260624-E5F6',
                'nama_pelapor' => 'Dedi Kurniawan',
                'kontak_pelapor' => '083456789012',
                'kategori_id' => 1,
                'lokasi' => 'Kantin Pusat, area tempat sampah sementara di belakang',
                'latitude' => -6.2000,
                'longitude' => 106.8000,
                'deskripsi' => 'Sampah sisa makanan menumpuk dan bau tidak sedap. Banyak lalat yang berkerumun. Perlu segera diangkut.',
                'status' => 'selesai',
                'catatan_petugas' => 'Sampah sudah diangkut oleh petugas Ahmad Wijaya pada pukul 10:30. Area sudah disemprot disinfektan.',
                'petugas_id' => 1
            ],
            [
                'kode_laporan' => 'KRS-20260623-G7H8',
                'nama_pelapor' => 'Siti Rahayu',
                'kontak_pelapor' => '084567890123',
                'kategori_id' => 4,
                'lokasi' => 'Laboratorium Kimia Lt. 2, dekat tempat cuci tangan koridor',
                'latitude' => -6.2200,
                'longitude' => 106.8300,
                'deskripsi' => 'Tumpukan botol reagent bekas dan limbah cair tumpah di lantai. Bau kimia menyengat. Area perlu diisolasi sementara.',
                'status' => 'baru'
            ],
            [
                'kode_laporan' => 'KRS-20260622-I9J0',
                'nama_pelapor' => 'Rudi Hartono',
                'kontak_pelapor' => '085678901234',
                'kategori_id' => 2,
                'lokasi' => 'Perpustakaan Lt. 2, area baca koran dan majalah',
                'latitude' => -6.2100,
                'longitude' => 106.8400,
                'deskripsi' => 'Kardus dan plastik pembungkus buku baru berserakan di sudut ruangan. Belum dibersihkan sejak kedatangan kiriman buku kemarin.',
                'status' => 'diproses',
                'catatan_petugas' => 'Tim kebersihan sedang bertugas di lokasi. Estimasi selesai dalam 1 jam.',
                'petugas_id' => 3
            ],
            [
                'kode_laporan' => 'KRS-20260621-K1L2',
                'nama_pelapor' => 'Maya Anggraini',
                'kontak_pelapor' => '086789012345',
                'kategori_id' => 1,
                'lokasi' => 'Asrama Mahasiswa Blok C, depan kamar 12-15',
                'latitude' => -6.2050,
                'longitude' => 106.8500,
                'deskripsi' => 'Sampah daun dan ranting menutupi selokan setelah hujan deras kemarin. Air tidak bisa mengalir dengan lancar.',
                'status' => 'selesai',
                'catatan_petugas' => 'Selokan sudah dibersihkan oleh petugas Rudi Hartono. Daun diangkut ke TPS. Selokan sudah lancar kembali.',
                'petugas_id' => 1
            ],
            [
                'kode_laporan' => 'KRS-20260620-M3N4',
                'nama_pelapor' => 'Fajar Pratama',
                'kontak_pelapor' => '087890123456',
                'kategori_id' => 3,
                'lokasi' => 'Klinik Kampus, ruang tunggu pasien',
                'latitude' => -6.2150,
                'longitude' => 106.8250,
                'deskripsi' => 'Tempat sampah medis penuh dan ada tumpahan di sampingnya. Perlu penggantian kantong sampah medis segera.',
                'status' => 'baru'
            ],
            [
                'kode_laporan' => 'KRS-20260619-O5P6',
                'nama_pelapor' => 'Lina Susanti',
                'kontak_pelapor' => '088901234567',
                'kategori_id' => 6,
                'lokasi' => 'Gedung Teknik Elektro, lab komputer Lt. 3',
                'latitude' => -6.2300,
                'longitude' => 106.8200,
                'deskripsi' => 'Ditemukan charger laptop rusak dan kabel USB bekas tergeletak di meja lab. Perlu dikumpulkan sebagai sampah elektronik.',
                'status' => 'selesai',
                'catatan_petugas' => 'Sampah elektronik sudah dikumpulkan dan akan diantar ke tempat pengolahan e-waste.',
                'petugas_id' => 3
            ],
            [
                'kode_laporan' => 'KRS-20260618-Q7R8',
                'nama_pelapor' => 'Doni Kusuma',
                'kontak_pelapor' => '089012345678',
                'kategori_id' => 5,
                'lokasi' => 'Fakultas Hukum, ruang dosen lt. 2',
                'latitude' => -6.1950,
                'longitude' => 106.8600,
                'deskripsi' => 'Tumpukan kardus bekas dan kertas koran lama menumpuk di gudang belakang. Perlu didaur ulang.',
                'status' => 'diproses',
                'catatan_petugas' => 'Petugas sedang menuju lokasi untuk mengangkut kertas dan kardus ke tempat daur ulang.',
                'petugas_id' => 2
            ],
            [
                'kode_laporan' => 'KRS-20260617-S9T0',
                'nama_pelapor' => 'Rina Marlina',
                'kontak_pelapor' => '081098765432',
                'kategori_id' => 4,
                'lokasi' => 'Gedung Olahraga, area ganti baju dekat shower',
                'latitude' => -6.2400,
                'longitude' => 106.8100,
                'deskripsi' => 'Botol sampo dan sabun bekas berserakan di lantai kamar mandi. Ada juga tumpahan cairan pembersih yang licin.',
                'status' => 'baru'
            ],
            [
                'kode_laporan' => 'KRS-20260616-U1V2',
                'nama_pelapor' => 'Hendra Wibowo',
                'kontak_pelapor' => '082109876543',
                'kategori_id' => 2,
                'lokasi' => 'Parkiran Motor Fakultas Ekonomi, pojok timur',
                'latitude' => -6.1850,
                'longitude' => 106.8700,
                'deskripsi' => 'Kantong plastik dan bungkus makanan berserakan di sudut parkiran. Sudah menjadi sarang tikus kecil.',
                'status' => 'selesai',
                'catatan_petugas' => 'Area sudah dibersihkan dan disemprot anti hama. Tikus sudah tidak ada.',
                'petugas_id' => 1
            ],
            [
                'kode_laporan' => 'KRS-20260615-W3X4',
                'nama_pelapor' => 'Nina Sari',
                'kontak_pelapor' => '083210987654',
                'kategori_id' => 1,
                'lokasi' => 'Taman Kampus, area dekat air mancur',
                'latitude' => -6.2250,
                'longitude' => 106.8350,
                'deskripsi' => 'Daun kering menumpuk di sekitar air mancur setelah badai kemarin. Perlu disapu agar tidak mengganggu estetika.',
                'status' => 'diproses',
                'catatan_petugas' => 'Petugas sedang membersihkan area taman. Estimasi selesai sore ini.',
                'petugas_id' => 3
            ]
        ];

        foreach ($data as $item) {
            LaporanSampah::create($item);
        }
    }
}