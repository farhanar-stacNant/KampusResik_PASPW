<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LaporanSampah;
use App\Models\LaporanTimeline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicController extends Controller
{
    // ============================================
    // GET /api/public/statistics
    // ============================================
    public function statistics()
    {
        $now = now();
        $weekStart = $now->copy()->startOfWeek();

        $stats = [
            'laporan_minggu_ini' => LaporanSampah::where('created_at', '>=', $weekStart)->count(),
            'laporan_selesai' => LaporanSampah::where('status', 'selesai')->count(),
            'total_sampah' => LaporanSampah::where('status', 'selesai')->count(),
        ];

        return response()->json(['data' => $stats]);
    }

    // ============================================
    // GET /api/public/waste-distribution
    // ============================================
    public function wasteDistribution()
    {
        $dist = [
            'organik' => LaporanSampah::where('jenis_sampah', 'ORG')->count(),
            'anorganik' => LaporanSampah::where('jenis_sampah', 'ANR')->count(),
            'b3' => LaporanSampah::where('jenis_sampah', 'B3')->count(),
        ];

        return response()->json(['data' => $dist]);
    }

    // ============================================
    // GET /api/public/waste-habits?period=weekly
    // ============================================
    public function wasteHabits(Request $request)
    {
        $period = $request->get('period', 'weekly');

        $data = [
            'senin' => 0, 'selasa' => 0, 'rabu' => 0,
            'kamis' => 0, 'jumat' => 0, 'sabtu' => 0, 'minggu' => 0
        ];

        if ($period === 'weekly') {
            $weekStart = now()->startOfWeek();
            $reports = LaporanSampah::where('created_at', '>=', $weekStart)->get();

            foreach ($reports as $report) {
                $day = strtolower($report->created_at->format('l'));
                $dayMap = [
                    'monday' => 'senin', 'tuesday' => 'selasa', 'wednesday' => 'rabu',
                    'thursday' => 'kamis', 'friday' => 'jumat', 'saturday' => 'sabtu', 'sunday' => 'minggu'
                ];
                $hari = $dayMap[$day] ?? 'senin';
                $data[$hari] += rand(1, 15);
            }
        }

        return response()->json(['data' => $data]);
    }

    // ============================================
    // GET /api/public/reports?limit=10&status=&search=
    // ============================================
    public function reports(Request $request)
    {
        $query = LaporanSampah::with(['petugas', 'kategori']);

        // Filter status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter jenis sampah
        if ($request->has('jenis_sampah') && $request->jenis_sampah) {
            $query->where('jenis_sampah', $request->jenis_sampah);
        }

        // Filter lokasi/search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_laporan', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%")
                  ->orWhere('nama_pelapor', 'like', "%{$search}%");
            });
        }

        // Filter tanggal
        if ($request->has('tanggal_mulai') && $request->tanggal_mulai) {
            $query->whereDate('created_at', '>=', $request->tanggal_mulai);
        }
        if ($request->has('tanggal_akhir') && $request->tanggal_akhir) {
            $query->whereDate('created_at', '<=', $request->tanggal_akhir);
        }

        // Prioritas
        if ($request->has('prioritas') && $request->prioritas) {
            $query->where('prioritas', $request->prioritas);
        }

        // Urutan
        $sort = $request->get('sort', 'terbaru');
        if ($sort === 'terlama') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sort === 'populer') {
            $query->orderBy('id', 'desc'); // placeholder
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $limit = min($request->get('limit', 10), 50);
        $reports = $query->paginate($limit);

        // Format response
        $data = $reports->map(function($report) {
            return [
                'id' => $report->id,
                'kode' => $report->kode_laporan,
                'judul' => $this->generateJudul($report->jenis_sampah, $report->lokasi),
                'deskripsi' => $report->deskripsi,
                'lokasi' => $report->lokasi,
                'status' => $report->status,
                'kategori' => $report->jenis_sampah,
                'foto' => $report->foto ? asset('storage/' . $report->foto) : null,
                'created_at' => $report->created_at->format('d M Y'),
                'prioritas' => $report->prioritas,
                'petugas' => $report->petugas ? $report->petugas->nama : null,
            ];
        });

        return response()->json([
            'data' => $data,
            'current_page' => $reports->currentPage(),
            'last_page' => $reports->lastPage(),
            'total' => $reports->total(),
        ]);
    }

    // ============================================
    // GET /api/public/reports/{id}
    // ============================================
    public function reportDetail($id)
    {
        $report = LaporanSampah::with(['timelines.petugas', 'petugas', 'kategori'])->findOrFail($id);

        $timelineData = $report->timelines->map(function($t) {
            return [
                'status' => $t->status,
                'deskripsi' => $t->deskripsi,
                'waktu' => $t->created_at->format('d M Y, H:i'),
                'petugas' => $t->petugas ? $t->petugas->nama : null,
            ];
        });

        return response()->json([
            'data' => [
                'id' => $report->id,
                'kode' => $report->kode_laporan,
                'judul' => $this->generateJudul($report->jenis_sampah, $report->lokasi),
                'deskripsi' => $report->deskripsi,
                'lokasi' => $report->lokasi,
                'latitude' => $report->latitude,
                'longitude' => $report->longitude,
                'jenis_sampah' => $report->jenis_sampah,
                'foto' => $report->foto ? asset('storage/' . $report->foto) : null,
                'no_wa' => $report->no_wa,
                'status' => $report->status,
                'prioritas' => $report->prioritas,
                'catatan_petugas' => $report->catatan_petugas,
                'petugas' => $report->petugas ? $report->petugas->nama : null,
                'created_at' => $report->created_at->format('d M Y, H:i'),
                'selesai_at' => $report->selesai_at ? $report->selesai_at->format('d M Y, H:i') : null,
                'timeline' => $timelineData,
            ]
        ]);
    }

    // ============================================
    // POST /api/public/reports
    // ============================================
    public function storeReport(Request $request)
    {
        $validated = $request->validate([
            'lokasi' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'jenis_sampah' => 'required|in:ORG,ANR,B3',
            'deskripsi' => 'nullable|string',
            'no_wa' => 'nullable|string|max:20',
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:1024',
        ]);

        // Generate kode unik
        $now = now();
        $tanggal = $now->format('dmy');
        $hari = $now->dayOfWeekIso;
        $prefix = $validated['jenis_sampah'];
        $random = str_pad(random_int(1, 999), 3, '0', STR_PAD_LEFT);
        $kode = "KRS-{$tanggal}-{$prefix}{$random}";

        // Upload foto
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('reports', 'public');
        }

        // Buat laporan
        $report = LaporanSampah::create([
            'kode_laporan' => $kode,
            'nama_pelapor' => 'Anonymous', // Bisa diganti kalau ada auth
            'kontak_pelapor' => $validated['no_wa'] ?? '-',
            'no_wa' => $validated['no_wa'] ?? null,
            'kategori_id' => $this->mapJenisToKategori($validated['jenis_sampah']),
            'jenis_sampah' => $validated['jenis_sampah'],
            'lokasi' => $validated['lokasi'],
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'deskripsi' => $validated['deskripsi'] ?? '-',
            'foto' => $fotoPath,
            'status' => 'dikirim',
            'prioritas' => 'normal',
        ]);

        // Buat timeline awal
        LaporanTimeline::create([
            'laporan_id' => $report->id,
            'status' => 'dikirim',
            'deskripsi' => 'Laporan telah dikirim oleh pengguna',
        ]);

        return response()->json([
            'message' => 'Laporan berhasil dikirim',
            'data' => [
                'id' => $report->id,
                'kode' => $report->kode_laporan,
                'status' => $report->status,
            ]
        ], 201);
    }

    // ============================================
    // GET /api/public/status/{kode}
    // ============================================
    public function checkStatus($kode)
    {
        $report = LaporanSampah::with(['timelines.petugas', 'petugas'])
            ->where('kode_laporan', $kode)
            ->firstOrFail();

        $timelineData = $report->timelines->map(function($t) {
            return [
                'status' => $t->status,
                'deskripsi' => $t->deskripsi,
                'waktu' => $t->created_at->format('d M Y, H:i'),
                'petugas' => $t->petugas ? $t->petugas->nama : null,
            ];
        });

        return response()->json([
            'data' => [
                'id' => $report->id,
                'kode' => $report->kode_laporan,
                'judul' => $this->generateJudul($report->jenis_sampah, $report->lokasi),
                'status' => $report->status,
                'lokasi' => $report->lokasi,
                'created_at' => $report->created_at->format('d M Y, H:i'),
                'selesai_at' => $report->selesai_at ? $report->selesai_at->format('d M Y, H:i') : null,
                'timeline' => $timelineData,
            ]
        ]);
    }

    // ============================================
    // POST /api/laporan/{id}/status
    // ============================================
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:dikirim,diterima,diproses,selesai,ditolak',
        ]);

        $report = LaporanSampah::findOrFail($id);
        $report->status = $validated['status'];
        
        if ($validated['status'] === 'selesai' && !$report->selesai_at) {
            $report->selesai_at = now();
        }

        $report->save();

        LaporanTimeline::create([
            'laporan_id' => $report->id,
            'status' => $validated['status'],
            'deskripsi' => 'Status laporan diperbarui menjadi ' . ucfirst($validated['status']),
            'petugas_id' => auth('sanctum')->user() ? auth('sanctum')->user()->id : null,
        ]);

        return response()->json([
            'message' => 'Status berhasil diperbarui',
            'data' => [
                'id' => $report->id,
                'status' => $report->status
            ]
        ]);
    }

    // ============================================
    // HELPER METHODS
    // ============================================
    private function generateJudul($jenis, $lokasi)
    {
        $jenisLabel = ['ORG' => 'Sampah Organik', 'ANR' => 'Sampah Anorganik', 'B3' => 'Sampah B3'];
        $label = $jenisLabel[$jenis] ?? 'Laporan Sampah';
        return $label . ' di ' . $lokasi;
    }

    private function mapJenisToKategori($jenis)
    {
        $map = ['ORG' => 1, 'ANR' => 2, 'B3' => 4]; // 1=Organik, 2=Plastik, 4=B3
        return $map[$jenis] ?? 1;
    }
}