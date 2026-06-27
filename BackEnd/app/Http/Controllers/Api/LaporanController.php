<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LaporanSampah;
use App\Models\KategoriSampah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class LaporanController extends Controller
{
    public function publik(Request $request)
    {
        $query = LaporanSampah::with('kategori')
            ->where('status', 'selesai')
            ->whereDate('updated_at', today());

        // Filter mingguan
        if ($request->has('minggu')) {
            $query->whereBetween('updated_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ]);
        }

        return response()->json($query->orderBy('updated_at', 'desc')->get());
    }

    public function show($kode)
    {
        $laporan = LaporanSampah::with(['kategori', 'petugas'])
            ->where('kode_laporan', $kode)
            ->firstOrFail();

        return response()->json($laporan);
    }
=
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pelapor' => 'required|string|max:100',
            'kontak_pelapor' => 'nullable|string|max:20',
            'kategori_id' => 'required|exists:kategori_sampahs,id',
            'lokasi' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'deskripsi' => 'required|string',
            'foto' => 'required|image|max:10240', // Max 10MB sebelum kompresi
        ]);

        // Generate token: JENIS-TANGGAL-URUT
        $kategoriId = $validated['kategori_id'];
        $tanggal = date('dmY');
        $urut = LaporanSampah::whereDate('created_at', today())->count() + 1;
        $kode = sprintf('%d-%s-%03d', $kategoriId, $tanggal, $urut);

        // Kompresi foto
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $this->kompresiFoto($request->file('foto'));
        }

        $laporan = LaporanSampah::create([
            'kode_laporan' => $kode,
            'nama_pelapor' => $validated['nama_pelapor'],
            'kontak_pelapor' => $validated['kontak_pelapor'] ?? null,
            'kategori_id' => $validated['kategori_id'],
            'lokasi' => $validated['lokasi'],
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'deskripsi' => $validated['deskripsi'],
            'foto' => $fotoPath,
            'status' => 'baru',
        ]);

        return response()->json([
            'message' => 'Laporan berhasil dikirim',
            'kode_laporan' => $kode,
            'data' => $laporan->load('kategori')
        ], 201);
    }

    public function dashboardStats()
    {
        $today = today();
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();

        // Per kategori (Organik=1, Anorganik=2, B3=3,4)
        $organik = LaporanSampah::where('kategori_id', 1)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->count();
        
        $anorganik = LaporanSampah::whereIn('kategori_id', [2, 5, 6])
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->count();
        
        $b3 = LaporanSampah::whereIn('kategori_id', [3, 4])
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->count();

        // Mingguan per hari
        $mingguan = [];
        for ($i = 0; $i < 7; $i++) {
            $hari = $weekStart->copy()->addDays($i);
            $mingguan[] = [
                'hari' => $hari->format('D'),
                'total' => LaporanSampah::whereDate('created_at', $hari)->count()
            ];
        }

        return response()->json([
            'total_minggu_ini' => LaporanSampah::whereBetween('created_at', [$weekStart, $weekEnd])->count(),
            'total_selesai' => LaporanSampah::where('status', 'selesai')->count(),
            'total_berat_kg' => rand(500, 1000), // Simulasi, nanti dari data aktual
            'per_kategori' => [
                'organik' => $organik,
                'anorganik' => $anorganik,
                'b3' => $b3
            ],
            'mingguan' => $mingguan
        ]);
    }

    private function kompresiFoto($file)
    {
        $image = Image::read($file->getRealPath());
        
        // Resize jika terlalu besar
        $image->scaleDown(width: 1920);
        
        // Kompresi kualitas 80%
        $filename = 'laporan/' . uniqid() . '.jpg';
        $path = storage_path('app/public/' . $filename);
        
        $image->save($path, 80, 'jpg');
        
        // Hapus file original jika perlu
        // Simpan path ke JSON/database sementara
        
        return $filename;
    }
}