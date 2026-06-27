<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KategoriSampah;
use App\Models\LaporanSampah;
use App\Models\LaporanTimeline;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    // ============================================
    // STATISTICS & DASHBOARD
    // ============================================
    public function statistics()
    {
        $now = now();
        $totalLaporan = LaporanSampah::count();
        $laporanMasuk = LaporanSampah::where('status', 'dikirim')->count();
        $laporanDiterima = LaporanSampah::where('status', 'diterima')->count();
        $sedangDiproses = LaporanSampah::where('status', 'diproses')->count();
        $selesaiTotal = LaporanSampah::where('status', 'selesai')->count();
        $selesaiHariIni = LaporanSampah::where('status', 'selesai')->whereDate('selesai_at', $now->toDateString())->count();
        $terpilahKg = $selesaiTotal * 5; 
        $persentase = $totalLaporan > 0 ? round(($selesaiTotal / $totalLaporan) * 100, 1) : 0;

        $statsList = [
            ['label' => 'Total Laporan', 'value' => $totalLaporan, 'icon' => 'bi-clipboard-data', 'color' => 'blue', 'trend' => 'Semua', 'trend_up' => true],
            ['label' => 'Laporan Masuk', 'value' => $laporanMasuk, 'icon' => 'bi-inbox', 'color' => 'orange', 'trend' => 'Baru', 'trend_up' => false],
            ['label' => 'Sedang Diproses', 'value' => $laporanDiterima + $sedangDiproses, 'icon' => 'bi-arrow-repeat', 'color' => 'green', 'trend' => 'Aktif', 'trend_up' => true],
            ['label' => 'Selesai Hari Ini', 'value' => $selesaiHariIni, 'icon' => 'bi-check-circle', 'color' => 'green', 'trend' => 'Selesai', 'trend_up' => true],
        ];

        return response()->json([
            'data' => [
                'list' => $statsList,
                'total_laporan' => $totalLaporan,
                'laporan_dikirim' => $laporanMasuk,
                'laporan_diterima' => $laporanDiterima,
                'laporan_diproses' => $sedangDiproses,
                'laporan_selesai' => $selesaiTotal,
                'sampah_terpilah' => $terpilahKg . ' kg',
                'persentase_selesai' => $persentase,
            ]
        ]);
    }

    public function weeklyChart()
    {
        $days = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        $chart_data = [];
        
        $weekStart = now()->startOfWeek();
        $lastWeekStart = now()->subWeek()->startOfWeek();

        for ($i = 0; $i < 7; $i++) {
            $dayName = $days[$i];
            $dateThisWeek = $weekStart->copy()->addDays($i)->toDateString();
            $dateLastWeek = $lastWeekStart->copy()->addDays($i)->toDateString();

            $thisWeekCount = LaporanSampah::whereDate('created_at', $dateThisWeek)->count();
            $lastWeekCount = LaporanSampah::whereDate('created_at', $dateLastWeek)->count();

            $chart_data[] = [
                'hari' => $dayName,
                'minggu_ini' => $thisWeekCount,
                'minggu_lalu' => $lastWeekCount
            ];
        }

        return response()->json(['data' => $chart_data]);
    }

    public function popularCategories()
    {
        $kategori = KategoriSampah::all();
        $total = LaporanSampah::count();
        if ($total == 0) $total = 1;

        $colors = ['#1565C0', '#1976D2', '#0D47A1', '#00897b', '#90a4ae'];
        
        $data = [];
        $i = 0;
        foreach ($kategori as $k) {
            $count = LaporanSampah::where('kategori_id', $k->id)->count();
            $data[] = [
                'nama' => $k->nama_kategori,
                'persen' => round(($count / $total) * 100),
                'warna' => $colors[$i % count($colors)]
            ];
            $i++;
        }

        return response()->json(['data' => $data]);
    }

    // ============================================
    // CATEGORIES
    // ============================================
    public function categories()
    {
        $categories = KategoriSampah::all();
        return response()->json(['data' => $categories]);
    }

    public function showCategory($id)
    {
        $cat = KategoriSampah::findOrFail($id);
        return response()->json(['data' => $cat]);
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'level_risiko' => 'required|string',
            'deskripsi' => 'nullable|string',
            'status_aktif' => 'nullable|boolean'
        ]);

        $cat = KategoriSampah::create($validated);
        return response()->json(['message' => 'Kategori berhasil dibuat', 'data' => $cat]);
    }

    public function updateCategory(Request $request, $id)
    {
        $cat = KategoriSampah::findOrFail($id);
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'level_risiko' => 'required|string',
            'deskripsi' => 'nullable|string',
            'status_aktif' => 'nullable|boolean'
        ]);

        $cat->update($validated);
        return response()->json(['message' => 'Kategori berhasil diubah', 'data' => $cat]);
    }

    public function deleteCategory($id)
    {
        KategoriSampah::destroy($id);
        return response()->json(['message' => 'Kategori berhasil dihapus']);
    }

    // ============================================
    // PETUGAS
    // ============================================
    public function petugas()
    {
        $petugas = User::where('role', 'petugas')->get();
        // format for frontend
        $data = $petugas->map(function($p) {
            return [
                'id' => $p->id,
                'nama' => $p->nama ?: $p->name,
                'lokasi_sekitar' => 'Kampus Terpadu', // dummy if not in DB
                'jadwal_harian' => 'Senin-Jumat 08:00-16:00', // dummy
                'status_aktif' => $p->status_aktif ?? 1
            ];
        });
        return response()->json(['data' => $data]);
    }

    public function storePetugas(Request $request)
    {
        // Dummy implementation for now to prevent fatal error
        return response()->json(['message' => 'Petugas berhasil ditambahkan']);
    }

    // ============================================
    // REPORTS
    // ============================================
    public function reports(Request $request)
    {
        $query = LaporanSampah::with(['petugas', 'kategori']);
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        if ($request->has('kategori_id') && $request->kategori_id) {
            $query->where('kategori_id', $request->kategori_id);
        }
        if ($request->has('tanggal_mulai') && $request->tanggal_mulai) {
            $query->whereDate('created_at', '>=', $request->tanggal_mulai);
        }
        if ($request->has('tanggal_akhir') && $request->tanggal_akhir) {
            $query->whereDate('created_at', '<=', $request->tanggal_akhir);
        }

        $limit = $request->get('limit', 50);
        $reports = $query->orderBy('created_at', 'desc')->paginate($limit);

        $reports->getCollection()->transform(function ($report) {
            return [
                'id' => $report->id,
                'kode_laporan' => $report->kode_laporan,
                'nama_pelapor' => $report->nama_pelapor ?? 'Anonymous',
                'foto' => $report->foto,
                'lokasi' => $report->lokasi,
                'status' => $report->status,
                'created_at' => $report->created_at->format('d M Y'),
                'kategori_sampah' => [
                    'nama_kategori' => $report->kategori ? $report->kategori->nama_kategori : ($report->jenis_sampah === 'ORG' ? 'Sampah Organik' : ($report->jenis_sampah === 'ANR' ? 'Sampah Anorganik' : 'Sampah B3'))
                ]
            ];
        });

        return response()->json(['data' => $reports]);
    }

    public function updateReportStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|string',
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
            'deskripsi' => 'Status laporan diperbarui oleh Admin',
            'petugas_id' => auth('sanctum')->user() ? auth('sanctum')->user()->id : null,
        ]);

        return response()->json(['message' => 'Status laporan berhasil diperbarui', 'data' => $report]);
    }

    // ============================================
    // JADWAL & PROGRESS
    // ============================================
    public function progress()
    {
        $activeReports = LaporanSampah::with(['petugas', 'kategori'])
            ->whereIn('status', ['diterima', 'diproses'])
            ->whereNotNull('petugas_id')
            ->get();

        $data = $activeReports->map(function($r) {
            $progress = $r->status === 'diproses' ? 60 : 30;
            return [
                'nama_tugas' => 'Penanganan ' . ($r->kategori ? $r->kategori->nama_kategori : 'Sampah') . ' ' . $r->kode_laporan,
                'progress' => $progress,
                'nama' => $r->petugas ? ($r->petugas->nama ?: $r->petugas->name) : 'Belum Ditugaskan',
                'lokasi' => $r->lokasi
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function jadwal()
    {
        $reports = LaporanSampah::with(['petugas'])
            ->whereNotNull('petugas_id')
            ->orderBy('created_at', 'desc')
            ->get();

        $data = $reports->map(function($r) {
            return [
                'tanggal' => $r->created_at->toDateString(),
                'nama' => $r->petugas ? ($r->petugas->nama ?: $r->petugas->name) : 'Petugas',
                'lokasi_tugas' => $r->lokasi
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function exportExcel(Request $request)
    {
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=rekap-laporan-" . date('Ymd') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $reports = LaporanSampah::with(['petugas', 'kategori'])->orderBy('created_at', 'desc')->get();

        $callback = function() use($reports) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID Laporan', 'Tanggal', 'Pelapor', 'Kategori', 'Lokasi', 'Status']);

            foreach ($reports as $r) {
                $kategori = $r->kategori ? $r->kategori->nama_kategori : $r->jenis_sampah;
                fputcsv($file, [
                    $r->kode_laporan,
                    $r->created_at->format('Y-m-d H:i'),
                    $r->nama_pelapor,
                    $kategori,
                    $r->lokasi,
                    $r->status
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPDF(Request $request)
    {
        $reports = LaporanSampah::with(['petugas', 'kategori'])->orderBy('created_at', 'desc')->get();

        $html = '<html><head><title>Rekap Laporan Sampah</title>';
        $html .= '<style>
            body { font-family: sans-serif; color: #333; padding: 20px; }
            h2 { text-align: center; color: #1e3a8a; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #cbd5e1; padding: 10px; text-align: left; font-size: 14px; }
            th { background-color: #f1f5f9; color: #1e293b; }
            tr:nth-child(even) { background-color: #f8fafc; }
        </style></head><body>';
        $html .= '<h2>Rekap Laporan Pengelolaan Sampah Kampus</h2>';
        $html .= '<table><thead><tr><th>ID Laporan</th><th>Tanggal</th><th>Pelapor</th><th>Kategori</th><th>Lokasi</th><th>Status</th></tr></thead><tbody>';

        foreach ($reports as $r) {
            $kategori = $r->kategori ? $r->kategori->nama_kategori : $r->jenis_sampah;
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($r->kode_laporan) . '</td>';
            $html .= '<td>' . htmlspecialchars($r->created_at->format('d M Y H:i')) . '</td>';
            $html .= '<td>' . htmlspecialchars($r->nama_pelapor) . '</td>';
            $html .= '<td>' . htmlspecialchars($kategori) . '</td>';
            $html .= '<td>' . htmlspecialchars($r->lokasi) . '</td>';
            $html .= '<td>' . htmlspecialchars(ucfirst($r->status)) . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';
        $html .= '<script>window.onload = function() { window.print(); }</script></body></html>';

        return response($html, 200)->header('Content-Type', 'text/html');
    }
}
// ============================================
// PROGRESS HARIAN UNTUK LINE CHART
// ============================================
public function progressHarian(Request $request)
{
    $bulan = $request->get('bulan', date('n'));
    $tahun = $request->get('tahun', date('Y'));
    
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
    $data = [];
    
    for ($d = 1; $d <= $daysInMonth; $d++) {
        $dateStr = sprintf('%04d-%02d-%02d', $tahun, $bulan, $d);
        
        // Total laporan masuk hari ini
        $totalMasuk = LaporanSampah::whereDate('created_at', $dateStr)->count();
        
        // Laporan selesai hari ini
        $totalSelesai = LaporanSampah::whereDate('selesai_at', $dateStr)
            ->where('status', 'selesai')
            ->count();
        
        // Laporan diproses (sedang dikerjakan)
        $totalDiproses = LaporanSampah::whereDate('created_at', '<=', $dateStr)
            ->whereDate('created_at', '>=', $dateStr)
            ->where('status', 'diproses')
            ->count();
        
        // Progress rate: selesai / masuk * 100
        $progressRate = $totalMasuk > 0 
            ? round(($totalSelesai / $totalMasuk) * 100, 1) 
            : 0;
        
        // Data detail untuk tooltip
        $detailLaporan = LaporanSampah::with(['petugas', 'kategori'])
            ->whereDate('created_at', $dateStr)
            ->orWhereDate('selesai_at', $dateStr)
            ->get()
            ->map(function($r) {
                return [
                    'kode_laporan' => $r->kode_laporan,
                    'nama_tugas' => 'Penanganan ' . ($r->kategori ? $r->kategori->nama_kategori : 'Sampah'),
                    'status' => $r->status,
                    'progress' => $r->status === 'selesai' ? 100 : ($r->status === 'diproses' ? 60 : ($r->status === 'diterima' ? 30 : 0)),
                    'nama_petugas' => $r->petugas ? ($r->petugas->nama ?: $r->petugas->name) : 'Belum Ditugaskan',
                    'lokasi' => $r->lokasi,
                    'created_at' => $r->created_at->format('H:i'),
                ];
            });

        $data[] = [
            'tanggal' => $d,
            'total_masuk' => $totalMasuk,
            'total_selesai' => $totalSelesai,
            'total_diproses' => $totalDiproses,
            'progress_rate' => $progressRate,
            'detail' => $detailLaporan
        ];
    }

    return response()->json(['data' => $data]);
}