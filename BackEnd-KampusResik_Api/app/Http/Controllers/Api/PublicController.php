<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KategoriSampah;
use App\Models\LaporanSampah;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    // 1. Mengambil semua Kategori Sampah (untuk Dropdown Form Pengaduan)
    public function getKategori()
    {
        $kategori = KategoriSampah::all();
        return response()->json(['status' => 'success', 'data' => $kategori], 200);
    }

    // 2. Submit Pengaduan Sampah Baru (Tanpa Login)
    public function kirimPengaduan(Request $request)
    {
        $request->validate([
            'kategori_id'       => 'required|exists:kategori_sampah,id',
            'nama_pelapor'      => 'nullable|string|max:255',
            'latitude'          => 'required|string',
            'longitude'         => 'required|string',
            'deskripsi_singkat' => 'required|string',
            'foto_sebelum'      => 'required|image|mimes:jpeg,png,jpg|max:5120', // Maks 5MB
        ]);

        // Proses simpan berkas foto ke storage/app/public/sampah dengan kompresi
        $file = $request->file('foto_sebelum');
        $namaFoto = time() . '_' . uniqid() . '.jpg';
        $destPath = storage_path('app/public/sampah/' . $namaFoto);
        
        if (!file_exists(dirname($destPath))) {
            mkdir(dirname($destPath), 0755, true);
        }
        
        $this->compressAndResize($file->getRealPath(), $destPath);

        $laporan = LaporanSampah::create([
            'kategori_id'       => $request->kategori_id,
            'nama_pelapor'      => $request->nama_pelapor ?? 'Anonim',
            'latitude'          => $request->latitude,
            'longitude'         => $request->longitude,
            'deskripsi_singkat' => $request->deskripsi_singkat,
            'foto_sebelum'      => $namaFoto,
            'status'            => 'menunggu'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Laporan pengaduan berhasil dikirim!',
            'data' => $laporan
        ], 201);
    }

    private function compressAndResize($sourcePath, $destinationPath, $quality = 60, $maxWidth = 1000)
    {
        $info = getimagesize($sourcePath);
        if ($info === false) {
            return false;
        }

        $mime = $info['mime'];
        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($sourcePath);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($sourcePath);
                break;
            default:
                return false;
        }

        $origWidth = imagesx($image);
        $origHeight = imagesy($image);
        
        if ($origWidth > $maxWidth) {
            $ratio = $maxWidth / $origWidth;
            $newWidth = $maxWidth;
            $newHeight = (int)($origHeight * $ratio);
            
            $newImage = imagecreatetruecolor($newWidth, $newHeight);
            
            if ($mime === 'image/png' || $mime === 'image/gif') {
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
                $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
                imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
            }
            
            imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
            imagedestroy($image);
            $image = $newImage;
        }

        $result = imagejpeg($image, $destinationPath, $quality);
        imagedestroy($image);
        
        return $result;
    }

    // 3. Mengambil Data Statistik Singkat (Untuk Dashboard Utama & Grafik Laporan Publik)
    public function getStatistik()
    {
        $totalLaporan = LaporanSampah::count();
        $menunggu     = LaporanSampah::where('status', 'menunggu')->count();
        $diproses     = LaporanSampah::where('status', 'diproses')->count();
        $selesai      = LaporanSampah::where('status', 'selesai')->count();

        // Mengambil 6 laporan terbaru yang berstatus selesai/proses untuk dipajang di Laporan Publik
        $laporanTerbaru = LaporanSampah::with('kategori')
                            ->orderBy('created_at', 'desc')
                            ->take(6)
                            ->get()
                            ->map(function ($item) {
                                $item->url_foto_sebelum = asset('storage/sampah/' . $item->foto_sebelum);
                                $item->url_foto_sesudah = $item->foto_sesudah ? asset('storage/sampah/' . $item->foto_sesudah) : null;
                                return $item;
                            });

        return response()->json([
            'status' => 'success',
            'data' => [
                'ringkasan' => [
                    'total' => $totalLaporan,
                    'menunggu' => $menunggu,
                    'diproses' => $diproses,
                    'selesai' => $selesai
                ],
                'laporan_publik' => $laporanTerbaru
            ]
        ], 200);
    }

    // 4. Melacak Detail Status Laporan Berdasarkan ID atau Lokasi Terdekat
    public function lacakLaporan(Request $request)
    {
        $request->validate([
            'laporan_id' => 'nullable|integer'
        ]);

        if ($request->has('laporan_id')) {
            $laporan = LaporanSampah::with(['kategori', 'petugas'])->find($request->laporan_id);
            if (!$laporan) {
                return response()->json(['status' => 'error', 'message' => 'ID Laporan tidak ditemukan'], 404);
            }
            
            $laporan->url_foto_sebelum = asset('storage/sampah/' . $laporan->foto_sebelum);
            $laporan->url_foto_sesudah = $laporan->foto_sesudah ? asset('storage/sampah/' . $laporan->foto_sesudah) : null;
            
            return response()->json(['status' => 'success', 'data' => [$laporan]], 200);
        }

        // Jika tidak menyertakan ID, tampilkan seluruh laporan aktif agar disinkronkan di peta sekitar pengguna publik
        $semuaLaporan = LaporanSampah::with('kategori')->orderBy('updated_at', 'desc')->get()->map(function ($item) {
            $item->url_foto_sebelum = asset('storage/sampah/' . $item->foto_sebelum);
            $item->url_foto_sesudah = $item->foto_sesudah ? asset('storage/sampah/' . $item->foto_sesudah) : null;
            return $item;
        });

        return response()->json(['status' => 'success', 'data' => $semuaLaporan], 200);
    }
}