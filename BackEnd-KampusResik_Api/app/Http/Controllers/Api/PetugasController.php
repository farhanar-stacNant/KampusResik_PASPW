<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LaporanSampah;
use Illuminate\Http\Request;

class PetugasController extends Controller
{
    // 1. Mengambil daftar tugas sampah yang belum selesai (menunggu / diproses)
    public function getTugas(Request $request)
    {
        // Mengambil semua laporan aktif agar petugas bisa memilah tugas di wilayah mereka
        $tugas = LaporanSampah::with('kategori')
            ->whereIn('status', ['menunggu', 'diproses'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                $item->url_foto_sebelum = asset('storage/sampah/' . $item->foto_sebelum);
                return $item;
            });

        return response()->json([
            'status' => 'success',
            'data' => $tugas
        ], 200);
    }

    // 2. Memperbarui status penanganan sampah (Ambil Tugas / Selesai Bersih-bersih)
    public function updateStatus(Request $request, $id)
    {
        $laporan = LaporanSampah::find($id);

        if (!$laporan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data laporan sampah tidak ditemukan.'
            ], 404);
        }

        $request->validate([
            'status' => 'required|in:diproses,selesai',
            'foto_sesudah' => 'required_if:status,selesai|image|mimes:jpeg,png,jpg|max:5120', // Wajib melampirkan foto jika status selesai
        ]);

        // Update data dasar
        $laporan->status = $request->status;
        $laporan->petugas_id = $request->user()->id; // Menyimpan ID petugas yang login saat ini

        // Jika status diubah menjadi selesai, proses upload foto sesudah dibersihkan dengan kompresi
        if ($request->status === 'selesai' && $request->hasFile('foto_sesudah')) {
            $file = $request->file('foto_sesudah');
            $namaFoto = time() . '_complete_' . uniqid() . '.jpg';
            $destPath = storage_path('app/public/sampah/' . $namaFoto);
            
            if (!file_exists(dirname($destPath))) {
                mkdir(dirname($destPath), 0755, true);
            }
            
            $this->compressAndResize($file->getRealPath(), $destPath);
            
            $laporan->foto_sesudah = $namaFoto;
        }

        $laporan->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Status laporan berhasil diperbarui menjadi ' . $request->status,
            'data' => $laporan
        ], 200);
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
}