<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KategoriSampah;
use App\Models\LaporanSampah;
use App\Models\LaporanTimeline;
use App\Models\Jadwal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    // ============================================
    // STATISTICS & DASHBOARD
    // ============================================
    private function ensureAdmin()
    {
        if (auth('sanctum')->user()?->role !== 'admin') {
            abort(403, 'Hanya admin yang dapat melakukan aksi ini.');
        }
    }

    public function statistics()
    {
        $now = now();
        $totalLaporan = LaporanSampah::count();
        $laporanMasuk = LaporanSampah::whereIn('status', ['dikirim', 'baru'])->count();
        $laporanDiterima = LaporanSampah::where('status', 'diterima')->count();
        $sedangDiproses = LaporanSampah::where('status', 'diproses')->count();
        $selesaiTotal = LaporanSampah::where('status', 'selesai')->count();
        $selesaiHariIni = LaporanSampah::where('status', 'selesai')->whereDate('selesai_at', $now->toDateString())->count();
        $terpilahKg = $selesaiTotal * 5; 
        $persentase = $totalLaporan > 0 ? round(($selesaiTotal / $totalLaporan) * 100, 1) : 0;

        $risikoTinggi = LaporanSampah::whereHas('kategori', function ($q) {
            $q->where('level_risiko', 'tinggi');
        })->whereIn('status', ['dikirim', 'diterima', 'diproses'])->count();

        $statsList = [
            ['label' => 'Total Laporan', 'value' => $totalLaporan, 'icon' => 'bi-clipboard-data', 'color' => 'blue', 'trend' => 'Semua', 'trend_up' => true],
            ['label' => 'Laporan Masuk', 'value' => $laporanMasuk, 'icon' => 'bi-inbox', 'color' => 'orange', 'trend' => 'Baru', 'trend_up' => false],
            ['label' => 'Sedang Diproses', 'value' => $laporanDiterima + $sedangDiproses, 'icon' => 'bi-arrow-repeat', 'color' => 'green', 'trend' => 'Aktif', 'trend_up' => true],
            ['label' => 'Selesai Hari Ini', 'value' => $selesaiHariIni, 'icon' => 'bi-check-circle', 'color' => 'green', 'trend' => 'Selesai', 'trend_up' => true],
            ['label' => 'Risiko Tinggi', 'value' => $risikoTinggi, 'icon' => 'bi-exclamation-triangle', 'color' => 'red', 'trend' => 'Perlu Tindakan', 'trend_up' => false],
        ];

        return response()->json([
            'data' => [
                'list' => $statsList,
                'total_laporan' => $totalLaporan,
                'laporan_dikirim' => $laporanMasuk,
                'laporan_diterima' => $laporanDiterima,
                'laporan_diproses' => $sedangDiproses,
                'laporan_selesai' => $selesaiTotal,
                'laporan_risiko_tinggi' => $risikoTinggi,
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
        $categories = KategoriSampah::all()->map(function ($k) {
            return [
                'id' => $k->id,
                'nama' => $k->nama_kategori,
                'name' => $k->nama_kategori,
                'nama_kategori' => $k->nama_kategori,
                'deskripsi' => $k->deskripsi,
                'description' => $k->deskripsi,
                'tingkat_risiko' => $k->level_risiko,
                'risk_level' => $k->level_risiko,
                'level_risiko' => $k->level_risiko,
                'warna' => $k->level_risiko === 'tinggi' ? '#dc3545' : ($k->level_risiko === 'sedang' ? '#ffc107' : '#2e7d32'),
                'color' => $k->level_risiko === 'tinggi' ? '#dc3545' : ($k->level_risiko === 'sedang' ? '#ffc107' : '#2e7d32'),
                'status_aktif' => $k->status_aktif,
            ];
        });
        return response()->json(['data' => $categories]);
    }

    public function showCategory($id)
    {
        $cat = KategoriSampah::findOrFail($id);
        return response()->json(['data' => $cat]);
    }

    public function storeCategory(Request $request)
    {
        $this->ensureAdmin();
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'tingkat_risiko' => 'required|string',
            'deskripsi' => 'nullable|string',
        ]);

        $cat = KategoriSampah::create([
            'nama_kategori' => $validated['nama'],
            'level_risiko' => $validated['tingkat_risiko'],
            'deskripsi' => $validated['deskripsi'] ?? null,
        ]);
        return response()->json(['message' => 'Kategori berhasil dibuat', 'data' => $cat]);
    }

    public function updateCategory(Request $request, $id)
    {
        $this->ensureAdmin();
        $cat = KategoriSampah::findOrFail($id);
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'tingkat_risiko' => 'required|string',
            'deskripsi' => 'nullable|string',
        ]);

        $cat->update([
            'nama_kategori' => $validated['nama'],
            'level_risiko' => $validated['tingkat_risiko'],
            'deskripsi' => $validated['deskripsi'] ?? null,
        ]);
        return response()->json(['message' => 'Kategori berhasil diubah', 'data' => $cat]);
    }

    public function deleteCategory($id)
    {
        $this->ensureAdmin();
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
                'name' => $p->name,
                'email' => $p->email,
                'telepon' => $p->telepon ?? '-',
                'no_hp' => $p->telepon ?? '-',
                'lokasi_sekitar' => $p->lokasi_sekitar ?? 'Kampus Terpadu',
                'jadwal_harian' => 'Senin-Jumat 08:00-16:00', // dummy
                'status_aktif' => $p->status_aktif ?? 1
            ];
        });
        return response()->json(['data' => $data]);
    }

    public function storePetugas(Request $request)
    {
        $this->ensureAdmin();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => 'petugas',
            'telepon' => $validated['no_hp'] ?? null,
            'lokasi_sekitar' => $validated['alamat'] ?? null,
        ]);

        return response()->json(['message' => 'Petugas berhasil ditambahkan', 'data' => $user], 201);
    }

    // ============================================
    // REPORTS
    // ============================================
    public function reports(Request $request)
    {
        $query = LaporanSampah::with(['petugas', 'kategori']);
        if ($request->has('status') && $request->status) {
            if ($request->status === 'baru' || $request->status === 'dikirim') {
                $query->whereIn('status', ['baru', 'dikirim']);
            } else {
                $query->where('status', $request->status);
            }
        }
        if ($request->has('kategori_id') && $request->kategori_id) {
            $query->where('kategori_id', $request->kategori_id);
        }
        if ($request->has('level_risiko') && $request->level_risiko) {
            $query->whereHas('kategori', function ($q) use ($request) {
                $q->where('level_risiko', $request->level_risiko);
            });
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
                'foto' => $report->foto ? asset('storage/' . $report->foto) : null,
                'lokasi' => $report->lokasi,
                'status' => $report->status,
                'created_at' => $report->created_at->format('d M Y'),
                'deskripsi' => $report->deskripsi,
                'latitude' => $report->latitude,
                'longitude' => $report->longitude,
                'kategori_sampah' => [
                    'nama_kategori' => $report->kategori ? $report->kategori->nama_kategori : ($report->jenis_sampah === 'ORG' ? 'Sampah Organik' : ($report->jenis_sampah === 'ANR' ? 'Sampah Anorganik' : 'Sampah B3')),
                    'level_risiko' => $report->kategori ? $report->kategori->level_risiko : 'rendah',
                ],
                'level_risiko' => $report->kategori ? $report->kategori->level_risiko : 'rendah',
            ];
        });

        return response()->json(['data' => $reports]);
    }

    public function updateReportStatus(Request $request, $id)
    {
        $this->ensureAdmin();
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
        $this->ensureAdmin();
        $total = LaporanSampah::count();
        $tertunda = LaporanSampah::whereIn('status', ['baru', 'dikirim', 'diterima'])->count();
        $diproses = LaporanSampah::where('status', 'diproses')->count();
        $selesai = LaporanSampah::where('status', 'selesai')->count();

        $data = [
            ['label' => 'Total Tugas', 'value' => (string)$total, 'icon' => 'bi-list-task', 'color' => 'blue', 'trend' => 'Semua waktu', 'trend_up' => true],
            ['label' => 'Selesai', 'value' => (string)$selesai, 'icon' => 'bi-check-circle', 'color' => 'green', 'trend' => $total > 0 ? round(($selesai / $total) * 100) . '%' : '0%', 'trend_up' => true],
            ['label' => 'Diproses', 'value' => (string)$diproses, 'icon' => 'bi-arrow-repeat', 'color' => 'orange', 'trend' => 'Sedang dikerjakan', 'trend_up' => true],
            ['label' => 'Tertunda', 'value' => (string)$tertunda, 'icon' => 'bi-clock', 'color' => 'red', 'trend' => 'Menunggu', 'trend_up' => false],
        ];

        return response()->json(['data' => $data]);
    }

    public function jadwal()
    {
        $this->ensureAdmin();

        $reports = LaporanSampah::with(['petugas'])
            ->whereNotNull('petugas_id')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($r) {
                return [
                    'tanggal' => $r->created_at->toDateString(),
                    'nama' => $r->petugas ? ($r->petugas->nama ?: $r->petugas->name) : 'Petugas',
                    'lokasi_tugas' => $r->lokasi
                ];
            });

        $jadwals = Jadwal::with('petugas')->get()->map(function($j) {
            return [
                'tanggal' => $j->tanggal->toDateString(),
                'nama' => $j->petugas ? ($j->petugas->nama ?: $j->petugas->name) : 'Petugas',
                'lokasi_tugas' => $j->lokasi
            ];
        });

        $data = $reports->concat($jadwals)->sortByDesc('tanggal')->values();

        return response()->json(['data' => $data]);
    }

    public function storeJadwal(Request $request)
    {
        $this->ensureAdmin();
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'petugas_id' => 'required|exists:users,id',
            'lokasi' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $jadwal = Jadwal::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil ditambahkan',
            'data' => $jadwal
        ], 201);
    }

    public function exportExcel(Request $request)
    {
        $this->ensureAdmin();
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
        $this->ensureAdmin();
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

    public function progressHarian(Request $request)
    {
        $this->ensureAdmin();
        $bulan = $request->get('bulan', date('n'));
        $tahun = $request->get('tahun', date('Y'));
        $daysInMonth = date('t', mktime(0, 0, 0, $bulan, 1, $tahun));
        $data = [];

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = sprintf('%s-%s-%s', $tahun, str_pad($bulan, 2, '0', STR_PAD_LEFT), str_pad($d, 2, '0', STR_PAD_LEFT));
            $totalMasuk = LaporanSampah::whereDate('created_at', $date)->count();
            $totalSelesai = LaporanSampah::whereDate('selesai_at', $date)->count();
            $progressRate = $totalMasuk > 0 ? round(($totalSelesai / $totalMasuk) * 100) : 0;

            $details = LaporanSampah::with('petugas')
                ->whereDate('created_at', $date)
                ->get()
                ->map(function ($r) {
                    return [
                        'kode_laporan' => $r->kode_laporan,
                        'nama_tugas' => 'Penanganan ' . ($r->kategori ? $r->kategori->nama_kategori : 'Sampah'),
                        'status' => $r->status,
                        'nama_petugas' => $r->petugas ? ($r->petugas->name ?? '-') : '-',
                        'lokasi' => $r->lokasi,
                        'progress' => $r->status === 'selesai' ? 100 : ($r->status === 'diproses' ? 60 : 25),
                        'created_at' => $r->created_at->format('d M Y'),
                    ];
                });

            $data[] = [
                'tanggal' => $d,
                'total_masuk' => $totalMasuk,
                'total_selesai' => $totalSelesai,
                'progress_rate' => $progressRate,
                'detail' => $details,
            ];
        }

        return response()->json(['data' => $data]);
    }

    // ============================================
    // CRUD LAPORAN (Admin)
    // ============================================
    public function storeLaporan(Request $request)
    {
        $this->ensureAdmin();
        $validated = $request->validate([
            'nama_pelapor' => 'required|string|max:100',
            'kontak_pelapor' => 'nullable|string|max:20',
            'kategori_id' => 'required|exists:kategori_sampahs,id',
            'lokasi' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'deskripsi' => 'required|string',
            'status' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $tanggal = date('Ymd');
        $urut = LaporanSampah::whereDate('created_at', today())->count() + 1;
        $kode = sprintf('KR-%s-%03d', $tanggal, $urut);

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
            'status' => $validated['status'] ?? 'baru',
            'petugas_id' => auth('sanctum')->id(),
        ]);

        LaporanTimeline::create([
            'laporan_id' => $laporan->id,
            'status' => $laporan->status,
            'deskripsi' => 'Laporan dibuat oleh Admin',
            'petugas_id' => auth('sanctum')->id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Laporan berhasil dibuat', 'data' => $laporan], 201);
    }

    public function updateLaporan(Request $request, $id)
    {
        $this->ensureAdmin();
        $laporan = LaporanSampah::findOrFail($id);
        $validated = $request->validate([
            'nama_pelapor' => 'nullable|string|max:100',
            'kontak_pelapor' => 'nullable|string|max:20',
            'kategori_id' => 'nullable|exists:kategori_sampahs,id',
            'lokasi' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'status' => 'nullable|string',
            'catatan_petugas' => 'nullable|string',
        ]);

        $laporan->update($validated);

        if ($request->has('status') && $request->status !== $laporan->status) {
            LaporanTimeline::create([
                'laporan_id' => $laporan->id,
                'status' => $request->status,
                'deskripsi' => 'Status diubah oleh Admin',
                'petugas_id' => auth('sanctum')->id(),
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Laporan berhasil diupdate', 'data' => $laporan]);
    }

    public function deleteLaporan($id)
    {
        $this->ensureAdmin();
        $laporan = LaporanSampah::findOrFail($id);
        $laporan->timelines()->delete();
        $laporan->delete();
        return response()->json(['success' => true, 'message' => 'Laporan berhasil dihapus']);
    }

    // ============================================
    // REKAP (untuk Koordinator & Admin)
    // ============================================
    public function rekap(Request $request)
    {
        $query = LaporanSampah::with(['petugas', 'kategori']);

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        if ($request->has('kategori_id') && $request->kategori_id) {
            $query->where('kategori_id', $request->kategori_id);
        }
        if ($request->has('level_risiko') && $request->level_risiko) {
            $query->whereHas('kategori', function ($q) use ($request) {
                $q->where('level_risiko', $request->level_risiko);
            });
        }
        $from = $request->tanggal_mulai ?? $request->from;
        $to = $request->tanggal_akhir ?? $request->to;
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        $limit = $request->get('limit', 50);
        $reports = $query->orderBy('created_at', 'desc')->paginate($limit);

        $total = LaporanSampah::count();
        $tertunda = LaporanSampah::whereIn('status', ['dikirim', 'diterima'])->count();
        $selesai = LaporanSampah::where('status', 'selesai')->count();
        $persen = $total > 0 ? round(($selesai / $total) * 100, 1) : 0;

        $reports->getCollection()->transform(function ($r) {
            return [
                'id' => $r->id,
                'kode_laporan' => $r->kode_laporan,
                'created_at' => $r->created_at->format('d M Y'),
                'nama_pelapor' => $r->nama_pelapor,
                'kategori' => $r->kategori ? ['nama' => $r->kategori->nama_kategori, 'nama_kategori' => $r->kategori->nama_kategori] : null,
                'lokasi' => $r->lokasi,
                'status' => $r->status,
                'level_risiko' => $r->kategori ? $r->kategori->level_risiko : 'rendah',
            ];
        });

        return response()->json([
            'data' => $reports,
            'stats' => [
                'total' => $total,
                'tertunda' => $tertunda,
                'selesai' => $selesai,
                'penyelesaian' => $persen,
            ]
        ]);
    }
}