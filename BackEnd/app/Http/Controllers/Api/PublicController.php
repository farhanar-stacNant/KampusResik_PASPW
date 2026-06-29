<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KategoriSampah;
use App\Models\LaporanSampah;
use App\Models\LaporanTimeline;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    // ============================================
    // GET /api/kategori-sampah
    // ============================================
    public function getKategoriSampah()
    {
        $kategori = KategoriSampah::where('status_aktif', true)->get();
        return response()->json([
            'success' => true,
            'data' => $kategori
        ]);
    }

    // ============================================
    // POST /api/laporan-sampah
    // ============================================
    public function storeLaporan(Request $request)
    {
        $validated = $request->validate([
            'nama_pelapor' => 'required|string|max:100',
            'kontak_pelapor' => 'nullable|string|max:20',
            'kategori_id' => 'required|exists:kategori_sampahs,id',
            'lokasi' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'deskripsi' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Generate kode laporan otomatis: KR-YYYYMMDD-XXX
        $tanggal = date('Ymd');
        $urut = LaporanSampah::whereDate('created_at', today())->count() + 1;
        $kode = sprintf('KR-%s-%03d', $tanggal, $urut);

        // Upload foto
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('laporan', 'public');
        }

        $laporan = LaporanSampah::create([
            'kode_laporan' => $kode,
            'nama_pelapor' => $validated['nama_pelapor'],
            'kontak_pelapor' => $validated['kontak_pelapor'] ?? '-',
            'kategori_id' => $validated['kategori_id'],
            'lokasi' => $validated['lokasi'],
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'deskripsi' => $validated['deskripsi'],
            'foto' => $fotoPath,
            'status' => 'dikirim',
            'catatan_petugas' => null,
        ]);

        // Buat timeline awal
        LaporanTimeline::create([
            'laporan_id' => $laporan->id,
            'status' => 'dikirim',
            'deskripsi' => 'Laporan telah dikirim oleh ' . $validated['nama_pelapor'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil dikirim',
            'data' => [
                'kode_laporan' => $kode,
                'id' => $laporan->id,
                'status' => $laporan->status,
            ]
        ], 201);
    }

    // ============================================
    // GET /api/laporan-sampah/{kode_laporan}
    // ============================================
    public function getLaporanByKode($kode_laporan)
    {
        $laporan = LaporanSampah::with(['kategori', 'timelines'])
            ->where('kode_laporan', $kode_laporan)
            ->first();

        if (!$laporan) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan tidak ditemukan'
            ], 404);
        }

        $timeline = $laporan->timelines->map(function ($t) {
            return [
                'status' => $t->status,
                'deskripsi' => $t->deskripsi,
                'waktu' => $t->created_at->format('d M Y, H:i'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $laporan->id,
                'kode_laporan' => $laporan->kode_laporan,
                'nama_pelapor' => $laporan->nama_pelapor,
                'kontak_pelapor' => $laporan->kontak_pelapor,
                'kategori' => $laporan->kategori ? [
                    'id' => $laporan->kategori->id,
                    'nama_kategori' => $laporan->kategori->nama_kategori,
                    'level_risiko' => $laporan->kategori->level_risiko,
                ] : null,
                'lokasi' => $laporan->lokasi,
                'latitude' => $laporan->latitude,
                'longitude' => $laporan->longitude,
                'deskripsi' => $laporan->deskripsi,
                'foto' => $laporan->foto ? asset('storage/' . $laporan->foto) : null,
                'status' => $laporan->status,
                'catatan_petugas' => $laporan->catatan_petugas,
                'created_at' => $laporan->created_at->format('d M Y, H:i'),
                'updated_at' => $laporan->updated_at->format('d M Y, H:i'),
                'timeline' => $timeline,
            ]
        ]);
    }

    // ============================================
    // GET /api/laporan-sampah/status/{status}
    // ============================================
    public function getLaporanByStatus($status)
    {
        $validStatuses = ['baru', 'diproses', 'selesai', 'ditolak'];
        if (!in_array($status, $validStatuses)) {
            return response()->json([
                'success' => false,
                'message' => 'Status tidak valid. Gunakan: baru, diproses, selesai, ditolak'
            ], 400);
        }

        $laporan = LaporanSampah::with('kategori')
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $data = $laporan->map(function ($r) {
            return [
                'id' => $r->id,
                'kode_laporan' => $r->kode_laporan,
                'nama_pelapor' => $r->nama_pelapor,
                'kategori' => $r->kategori ? [
                    'nama_kategori' => $r->kategori->nama_kategori,
                    'level_risiko' => $r->kategori->level_risiko,
                ] : null,
                'lokasi' => $r->lokasi,
                'deskripsi' => $r->deskripsi,
                'foto' => $r->foto ? asset('storage/' . $r->foto) : null,
                'status' => $r->status,
                'created_at' => $r->created_at->format('d M Y'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'current_page' => $laporan->currentPage(),
            'last_page' => $laporan->lastPage(),
            'total' => $laporan->total(),
        ]);
    }

    // ============================================
    // GET /api/laporan-sampah (listing with filters)
    // ============================================
    public function getLaporan(Request $request)
    {
        $query = LaporanSampah::with('kategori');

        // Filter status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter kategori
        if ($request->has('kategori_id') && $request->kategori_id) {
            $query->where('kategori_id', $request->kategori_id);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_laporan', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%")
                  ->orWhere('nama_pelapor', 'like', "%{$search}%");
            });
        }

        // Urutkan
        $sort = $request->get('sort', 'terbaru');
        if ($sort === 'terlama') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $limit = min($request->get('limit', 10), 50);
        $reports = $query->paginate($limit);

        $data = $reports->map(function ($r) {
            $risiko = $r->kategori ? $r->kategori->level_risiko : 'rendah';
            return [
                'id' => $r->id,
                'kode_laporan' => $r->kode_laporan,
                'nama_pelapor' => $r->nama_pelapor,
                'kategori' => $r->kategori ? [
                    'nama_kategori' => $r->kategori->nama_kategori,
                    'level_risiko' => $risiko,
                ] : null,
                'lokasi' => $r->lokasi,
                'deskripsi' => $r->deskripsi,
                'foto' => $r->foto ? asset('storage/' . $r->foto) : null,
                'status' => $r->status,
                'created_at' => $r->created_at->format('d M Y'),
                'risiko' => $risiko,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'current_page' => $reports->currentPage(),
            'last_page' => $reports->lastPage(),
            'total' => $reports->total(),
        ]);
    }

    // ============================================
    // GET /api/statistik (untuk frontend)
    // ============================================
    public function getStatistik()
    {
        $now = now();
        $weekStart = $now->copy()->startOfWeek();

        return response()->json([
            'success' => true,
            'data' => [
                'laporan_minggu_ini' => LaporanSampah::where('created_at', '>=', $weekStart)->count(),
                'laporan_selesai' => LaporanSampah::where('status', 'selesai')->count(),
                'laporan_baru' => LaporanSampah::where('status', 'baru')->count(),
                'laporan_diproses' => LaporanSampah::where('status', 'diproses')->count(),
                'total_laporan' => LaporanSampah::count(),
            ]
        ]);
    }

    // ============================================
    // GET /api/public/reports/{id}
    // ============================================
    public function getLaporanById($id)
    {
        $laporan = LaporanSampah::with(['kategori', 'timelines', 'petugas'])->find($id);
        if (!$laporan) {
            return response()->json(['success' => false, 'message' => 'Laporan tidak ditemukan'], 404);
        }

        $timeline = $laporan->timelines->map(function ($t) {
            return [
                'status' => $t->status,
                'deskripsi' => $t->deskripsi,
                'waktu' => $t->created_at->format('d M Y, H:i'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $laporan->id,
                'kode' => $laporan->kode_laporan,
                'kode_laporan' => $laporan->kode_laporan,
                'nama_pelapor' => $laporan->nama_pelapor,
                'kontak_pelapor' => $laporan->kontak_pelapor,
                'kategori' => $laporan->kategori ? $laporan->kategori->nama_kategori : ($laporan->jenis_sampah ?? '-'),
                'kategori_sampah' => $laporan->kategori ? ['nama_kategori' => $laporan->kategori->nama_kategori] : null,
                'lokasi' => $laporan->lokasi,
                'latitude' => $laporan->latitude,
                'longitude' => $laporan->longitude,
                'deskripsi' => $laporan->deskripsi,
                'foto' => $laporan->foto ? asset('storage/' . $laporan->foto) : null,
                'status' => $laporan->status,
                'prioritas' => $laporan->prioritas ?? 'Normal',
                'petugas' => $laporan->petugas ? ($laporan->petugas->name ?? 'Ditugaskan') : 'Belum ditugaskan',
                'catatan_petugas' => $laporan->catatan_petugas,
                'created_at' => $laporan->created_at->format('d M Y, H:i'),
                'updated_at' => $laporan->updated_at->format('d M Y, H:i'),
                'selesai_at' => $laporan->selesai_at ? date('d M Y', strtotime($laporan->selesai_at)) : null,
                'timeline' => $timeline,
                'jenis_sampah' => $laporan->jenis_sampah,
            ]
        ]);
    }

    // ============================================
    // POST /api/laporan/{id}/status
    // ============================================
    public function updateLaporanStatus(Request $request, $id)
    {
        $validated = $request->validate(['status' => 'required|string']);
        $report = LaporanSampah::findOrFail($id);
        $report->status = $validated['status'];
        if ($validated['status'] === 'selesai' && !$report->selesai_at) {
            $report->selesai_at = now();
        }
        $report->save();

        LaporanTimeline::create([
            'laporan_id' => $report->id,
            'status' => $validated['status'],
            'deskripsi' => 'Status diperbarui oleh Petugas',
            'petugas_id' => auth('sanctum')->id(),
        ]);

        return response()->json(['message' => 'Status berhasil diperbarui', 'data' => $report], 200);
    }

    // ============================================
    // GET /api/me
    // ============================================
    public function getProfile(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'nama' => $user->name,
                'name' => $user->name,
                'email' => $user->email,
                'no_hp' => $user->telepon ?? '-',
                'telepon' => $user->telepon ?? '-',
                'role' => $user->role,
                'lokasi_tugas' => $user->lokasi_sekitar ?? 'Belum ditentukan',
                'created_at' => $user->created_at->format('Y-m-d'),
            ]
        ]);
    }

    // ============================================
    // PUT /api/me
    // ============================================
    public function updateProfilePetugas(Request $request)
    {
        $user = $request->user();
        if ($request->has('nama')) $user->name = $request->nama;
        if ($request->has('email')) $user->email = $request->email;
        if ($request->has('no_hp')) $user->telepon = $request->no_hp;
        if ($request->has('lokasi_sekitar')) $user->lokasi_sekitar = $request->lokasi_sekitar;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'data' => [
                'nama' => $user->name,
                'email' => $user->email,
                'no_hp' => $user->telepon,
                'lokasi_sekitar' => $user->lokasi_sekitar,
            ]
        ]);
    }
}
