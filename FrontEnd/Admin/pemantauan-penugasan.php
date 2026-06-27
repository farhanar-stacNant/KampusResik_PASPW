<?php
require_once '../includes/api-config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';
require_admin();

$page_title = 'Pemantauan - Admin';
$token = get_auth_token();

$progress_data = [];
$petugas_jadwal = [];
$progress_harian = [];
$bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : date('n');
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');

try {
    $result = fetch_api('/admin/progress', 'GET', null, $token);
    if ($result['code'] === 200) {
        $progress_data = $result['body']['data'] ?? [];
    }

    $result = fetch_api('/admin/jadwal', 'GET', null, $token);
    if ($result['code'] === 200) {
        $petugas_jadwal = $result['body']['data'] ?? [];
    }

    $result = fetch_api('/admin/progress-harian?bulan=' . $bulan . '&tahun=' . $tahun, 'GET', null, $token);
    if ($result['code'] === 200) {
        $progress_harian = $result['body']['data'] ?? [];
    }

} catch (Exception $e) {
    $progress_data = [
        ['nama_tugas' => 'Penanganan Sampah Medis KRS-20260625-C3D4', 'progress' => 60, 'nama' => 'Ahmad Wijaya', 'lokasi' => 'Fakultas Kedokteran, area parkir belakang gedung C'],
        ['nama_tugas' => 'Penanganan Sampah Kertas KRS-20260618-Q7R8', 'progress' => 60, 'nama' => 'Ahmad Wijaya', 'lokasi' => 'Fakultas Hukum, ruang dosen lt. 2'],
        ['nama_tugas' => 'Penanganan Sampah Plastik KRS-20260622-I9J0', 'progress' => 60, 'nama' => 'Siti Nurhaliza', 'lokasi' => 'Perpustakaan Lt. 2, area baca koran dan majalah'],
        ['nama_tugas' => 'Penanganan Sampah Organik KRS-20260615-W3X4', 'progress' => 60, 'nama' => 'Siti Nurhaliza', 'lokasi' => 'Taman Kampus, area dekat air mancur'],
    ];
    $petugas_jadwal = [
        ['tanggal' => '2026-06-25', 'nama' => 'Siti Nurhaliza', 'lokasi_tugas' => 'Fakultas Kedokteran'],
        ['tanggal' => '2026-06-26', 'nama' => 'Ahmad Wijaya', 'lokasi_tugas' => 'Fakultas Hukum'],
    ];
    $progress_harian = [];
}

// Format jadwal untuk kalender
$jadwal_map = [];
foreach ($petugas_jadwal as $j) {
    $tgl = (int)date('j', strtotime($j['tanggal']));
    $jadwal_map[$tgl] = [
        'nama' => $j['nama'] ?? $j['petugas']['nama'] ?? 'Petugas',
        'lokasi' => $j['lokasi_tugas'] ?? ''
    ];
}

// Siapkan data untuk Chart.js
$chart_labels = [];
$chart_masuk = [];
$chart_selesai = [];
$chart_progress = [];
$chart_details = [];

if (!empty($progress_harian)) {
    foreach ($progress_harian as $h) {
        $chart_labels[] = $h['tanggal'];
        $chart_masuk[] = $h['total_masuk'];
        $chart_selesai[] = $h['total_selesai'];
        $chart_progress[] = $h['progress_rate'];
        $chart_details[$h['tanggal']] = $h['detail'] ?? [];
    }
} else {
    $daysInMonth = date('t', mktime(0, 0, 0, $bulan, 1, $tahun));
    for ($d = 1; $d <= $daysInMonth; $d++) {
        $chart_labels[] = $d;
        $chart_masuk[] = 0;
        $chart_selesai[] = 0;
        $chart_progress[] = 0;
    }
}

require_once '../includes/head-admin.php';
require_once '../includes/navbar-admin.php';
?>

<main class="admin-main">
    <div class="container-fluid">
        
        <!-- Header -->
        <div class="admin-header animate-in">
            <div>
                <h4>Pemantauan Progres & Penugasan</h4>
                <p>Kalender penugasan dan monitoring progress petugas.</p>
            </div>
            <button class="btn btn-admin btn-admin-primary" data-bs-toggle="modal" data-bs-target="#modalTambahJadwal">
                <i class="bi bi-plus-lg me-2"></i>Tambah Jadwal
            </button>
        </div>
        
        <!-- Progress Cards -->
        <div class="row g-3 mb-4">
            <?php foreach ($progress_data as $prog): ?>
            <div class="col-md-6 col-xl-3 animate-in">
                <div class="admin-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-semibold" style="font-size: 0.9rem;"><?= htmlspecialchars($prog['nama_tugas']) ?></span>
                            <span class="fw-bold text-primary"><?= $prog['progress'] ?>%</span>
                        </div>
                        <div class="progress-admin mb-2">
                            <div class="progress-bar" style="width: <?= $prog['progress'] ?>%; background: var(--adm-primary);"></div>
                        </div>
                        <p class="mb-0 text-muted" style="font-size: 0.8rem;">
                            <i class="bi bi-person me-1"></i><?= htmlspecialchars($prog['nama'] ?? '-') ?>
                            <span class="mx-1">•</span>
                            <i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($prog['lokasi'] ?? '-') ?>
                        </p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Kalender -->
        <div class="admin-card animate-in mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">Jadwal Petugas — <?= date('F Y', mktime(0,0,0,$bulan,1,$tahun)) ?></h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-secondary" onclick="changeMonth(-1)"><i class="bi bi-chevron-left"></i></button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="changeMonth(1)"><i class="bi bi-chevron-right"></i></button>
                    </div>
                </div>
                
                <!-- === KALENDER GRID === -->
                <div class="calendar-wrapper">
                    <!-- Header Hari -->
                    <div class="calendar-header-row">
                        <?php foreach (['Sen','Sel','Rab','Kam','Jum','Sab','Min'] as $h): ?>
                        <div class="calendar-header-cell"><?= $h ?></div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Grid Tanggal -->
                    <div class="calendar-days-grid">
                        <?php
                        $first_day = mktime(0, 0, 0, $bulan, 1, $tahun);
                        $start_weekday = date('N', $first_day) - 1; // 0 = Senin
                        $days_in_month = date('t', $first_day);
                        
                        // Cell kosong sebelum tanggal 1
                        for ($i = 0; $i < $start_weekday; $i++): ?>
                        <div class="calendar-day-cell empty"></div>
                        <?php endfor; ?>
                        
                        <?php for ($t = 1; $t <= $days_in_month; $t++): 
                            $has_jadwal = isset($jadwal_map[$t]);
                            $today = ($t == date('j') && $bulan == date('n') && $tahun == date('Y')) ? 'today' : '';
                            $jadwal = $jadwal_map[$t] ?? null;
                            $prog_hari = $progress_harian[$t-1] ?? null;
                            $has_progress = $prog_hari && $prog_hari['total_masuk'] > 0;
                        ?>
                        <div class="calendar-day-cell <?= $has_jadwal ? 'active' : '' ?> <?= $today ?> <?= $has_progress ? 'has-progress' : '' ?>"
                             data-tanggal="<?= $t ?>"
                             onclick="highlightChart(<?= $t ?>)">
                            <span class="day-number"><?= $t ?></span>
                            <?php if ($jadwal): ?>
                            <span class="petugas-name"><?= htmlspecialchars($jadwal['nama']) ?></span>
                            <span class="petugas-dot" title="<?= htmlspecialchars($jadwal['lokasi']) ?>"></span>
                            <?php endif; ?>
                            <?php if ($has_progress): ?>
                            <div class="day-progress">
                                <div class="day-progress-bar" style="width: <?= $prog_hari['progress_rate'] ?>%"></div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
                
            </div>
        </div>
        
        <!-- === LINE CHART: PROGRES HARIAN === -->
        <div class="admin-card animate-in" id="chart-progress-section">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold mb-1"><i class="bi bi-graph-up-arrow me-2 text-primary"></i>Progres Penanganan Harian</h5>
                        <p class="text-muted mb-0" style="font-size: 0.85rem;">Grafik laporan masuk vs selesai & tingkat penyelesaian per tanggal</p>
                    </div>
                    <div class="d-flex gap-2">
                        <span class="badge bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>Progress Rate
                        </span>
                        <span class="badge bg-success bg-opacity-10 text-success">
                            <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>Selesai
                        </span>
                        <span class="badge bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>Masuk
                        </span>
                    </div>
                </div>
                
                <div style="position: relative; height: 380px;">
                    <canvas id="chartProgressHarian"></canvas>
                </div>
                
                <!-- Detail Panel -->
                <div id="detail-progress" class="mt-4 p-3 rounded-3" style="background: rgba(21,101,192,0.05); border: 1px solid rgba(21,101,192,0.15); display: none;">
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="bi bi-calendar-check me-2"></i>Detail Laporan Tanggal <span id="detail-tanggal">-</span>
                    </h6>
                    <div class="row g-3" id="detail-list"></div>
                </div>
            </div>
        </div>
        
    </div>
</main>

<!-- Modal Tambah Jadwal -->
<div class="modal fade" id="modalTambahJadwal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tambah Jadwal Penugasan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formJadwal">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Petugas</label>
                        <select class="form-select" id="jadwalPetugas" required>
                            <option value="">Pilih Petugas</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal</label>
                        <input type="date" class="form-control" id="jadwalTanggal" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Lokasi Tugas</label>
                        <input type="text" class="form-control" id="jadwalLokasi" placeholder="Contoh: Gedung A - Lantai 1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan</label>
                        <textarea class="form-control" id="jadwalKeterangan" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-admin btn-admin-primary" onclick="saveJadwal()">Simpan Jadwal</button>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
const API_TOKEN = '<?= $token ?>';
const API_BASE = '<?= API_BASE_URL ?>';

const chartLabels = <?= json_encode($chart_labels) ?>;
const chartMasuk = <?= json_encode($chart_masuk) ?>;
const chartSelesai = <?= json_encode($chart_selesai) ?>;
const chartProgress = <?= json_encode($chart_progress) ?>;
const chartDetails = <?= json_encode($chart_details) ?>;
const progressHarian = <?= json_encode($progress_harian) ?>;

const ctx = document.getElementById('chartProgressHarian').getContext('2d');
const gradientProgress = ctx.createLinearGradient(0, 0, 0, 380);
gradientProgress.addColorStop(0, 'rgba(21, 101, 192, 0.3)');
gradientProgress.addColorStop(1, 'rgba(21, 101, 192, 0.02)');

const chartProgressHarian = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: chartLabels.map(d => 'Tgl ' + d),
        datasets: [
            {
                type: 'line',
                label: 'Progress Rate (%)',
                data: chartProgress,
                borderColor: '#1565C0',
                backgroundColor: gradientProgress,
                borderWidth: 2.5,
                pointBackgroundColor: '#1565C0',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 7,
                pointHoverBackgroundColor: '#0D47A1',
                fill: true,
                tension: 0.4,
                yAxisID: 'y1',
                order: 1
            },
            {
                type: 'bar',
                label: 'Laporan Masuk',
                data: chartMasuk,
                backgroundColor: 'rgba(255, 193, 7, 0.7)',
                borderColor: '#FFC107',
                borderWidth: 1,
                borderRadius: 4,
                yAxisID: 'y',
                order: 2
            },
            {
                type: 'bar',
                label: 'Laporan Selesai',
                data: chartSelesai,
                backgroundColor: 'rgba(25, 135, 84, 0.7)',
                borderColor: '#198754',
                borderWidth: 1,
                borderRadius: 4,
                yAxisID: 'y',
                order: 3
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: {
                position: 'top', align: 'end',
                labels: { usePointStyle: true, pointStyle: 'circle', padding: 20, font: { size: 12 } }
            },
            tooltip: {
                backgroundColor: 'rgba(21, 101, 192, 0.95)',
                titleColor: '#fff', bodyColor: '#fff',
                borderColor: '#1565C0', borderWidth: 1,
                cornerRadius: 8, padding: 12,
                callbacks: {
                    title: function(context) {
                        return 'Tanggal ' + context[0].label.replace('Tgl ', '');
                    },
                    afterBody: function(context) {
                        const idx = context[0].dataIndex;
                        const detail = progressHarian[idx];
                        if (detail && detail.detail && detail.detail.length > 0) {
                            return '\n📋 ' + detail.detail.length + ' laporan';
                        }
                        return '';
                    }
                }
            }
        },
        scales: {
            x: {
                grid: { display: false, drawBorder: false },
                ticks: { color: '#6c757d', font: { size: 11 } }
            },
            y: {
                type: 'linear', display: true, position: 'left',
                title: { display: true, text: 'Jumlah Laporan', color: '#6c757d', font: { size: 11 } },
                grid: { color: 'rgba(0,0,0,0.05)', drawBorder: false },
                ticks: { color: '#6c757d', font: { size: 11 }, stepSize: 1 }
            },
            y1: {
                type: 'linear', display: true, position: 'right',
                min: 0, max: 100,
                title: { display: true, text: 'Progress %', color: '#1565C0', font: { size: 11 } },
                grid: { display: false, drawBorder: false },
                ticks: { color: '#1565C0', font: { size: 11 }, callback: function(value) { return value + '%'; } }
            }
        },
        onClick: (e, elements) => {
            if (elements.length > 0) {
                const index = elements[0].index;
                const tanggal = chartLabels[index];
                highlightChart(tanggal);
            }
        }
    }
});

function highlightChart(tanggal) {
    document.getElementById('chart-progress-section').scrollIntoView({ behavior: 'smooth', block: 'center' });
    const dataset = chartProgressHarian.data.datasets[0];
    const pointColors = chartLabels.map(() => '#1565C0');
    const pointRadii = chartLabels.map(() => 4);
    const idx = chartLabels.indexOf(parseInt(tanggal));
    if (idx !== -1) {
        pointColors[idx] = '#FF5722';
        pointRadii[idx] = 10;
    }
    dataset.pointBackgroundColor = pointColors;
    dataset.pointRadius = pointRadii;
    chartProgressHarian.update();
    showDetailProgress(parseInt(tanggal));
}

function showDetailProgress(tanggal) {
    const detailDiv = document.getElementById('detail-progress');
    const detailList = document.getElementById('detail-list');
    const detailTanggal = document.getElementById('detail-tanggal');
    const bulanNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    const bulan = <?= $bulan ?> - 1;
    const tahun = <?= $tahun ?>;
    
    detailTanggal.textContent = tanggal + ' ' + bulanNames[bulan] + ' ' + tahun;
    detailDiv.style.display = 'block';
    
    const idx = tanggal - 1;
    const detailData = progressHarian[idx] ?? null;
    const laporanList = detailData?.detail ?? [];
    
    if (laporanList.length === 0) {
        detailList.innerHTML = `
            <div class="col-12 text-center text-muted py-4">
                <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                Tidak ada laporan untuk tanggal ini
            </div>
        `;
        return;
    }
    
    detailList.innerHTML = laporanList.map(lap => {
        let statusBadge = 'bg-secondary', statusText = lap.status;
        if (lap.status === 'selesai') { statusBadge = 'bg-success'; statusText = 'Selesai'; }
        else if (lap.status === 'diproses') { statusBadge = 'bg-info text-white'; statusText = 'Diproses'; }
        else if (lap.status === 'diterima') { statusBadge = 'bg-primary'; statusText = 'Diterima'; }
        else if (lap.status === 'dikirim') { statusBadge = 'bg-warning text-dark'; statusText = 'Dikirim'; }
        else if (lap.status === 'ditolak') { statusBadge = 'bg-danger'; statusText = 'Ditolak'; }
        
        return `
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #1565C0 !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge ${statusBadge}" style="font-size: 0.7rem;">${statusText}</span>
                        <small class="text-muted">${lap.created_at || '-'}</small>
                    </div>
                    <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: #1565C0;">${escapeHtml(lap.kode_laporan || '-')}</h6>
                    <p class="mb-1 text-dark fw-semibold" style="font-size: 0.85rem;">${escapeHtml(lap.nama_tugas || 'Penanganan Sampah')}</p>
                    <p class="mb-2 text-muted" style="font-size: 0.8rem;">
                        <i class="bi bi-person me-1"></i>${escapeHtml(lap.nama_petugas || '-')}
                    </p>
                    <p class="mb-0 text-muted" style="font-size: 0.8rem;">
                        <i class="bi bi-geo-alt me-1"></i>${escapeHtml(lap.lokasi || '-')}
                    </p>
                    <div class="mt-2">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted" style="font-size: 0.75rem;">Progress</small>
                            <small class="fw-bold text-primary" style="font-size: 0.75rem;">${lap.progress}%</small>
                        </div>
                        <div class="progress" style="height: 5px; border-radius: 3px;">
                            <div class="progress-bar" role="progressbar" 
                                 style="width: ${lap.progress}%; background: ${getProgressColor(lap.progress)}; border-radius: 3px;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        `;
    }).join('');
}

function getProgressColor(progress) {
    if (progress >= 80) return '#198754';
    if (progress >= 50) return '#1565C0';
    if (progress >= 30) return '#FFC107';
    return '#dc3545';
}

function escapeHtml(text) {
    if (!text) return '-';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

fetch(API_BASE + '/admin/petugas', {
    headers: { 'Authorization': 'Bearer ' + API_TOKEN, 'X-Api-Token': '<?= API_SECRET_TOKEN ?>' }
})
.then(r => r.json())
.then(data => {
    const select = document.getElementById('jadwalPetugas');
    const list = data.data ?? [];
    list.forEach(p => {
        const opt = document.createElement('option');
        opt.value = p.id;
        opt.textContent = (p.nama || p.name) + ' — ' + (p.lokasi_sekitar || '-');
        select.appendChild(opt);
    });
});

function saveJadwal() {
    fetch(API_BASE + '/admin/jadwal', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer <?= $token ?>',
            'X-Api-Token': '<?= API_SECRET_TOKEN ?>'
        },
        body: JSON.stringify({
            petugas_id: document.getElementById('jadwalPetugas').value,
            tanggal: document.getElementById('jadwalTanggal').value,
            lokasi_tugas: document.getElementById('jadwalLokasi').value,
            keterangan: document.getElementById('jadwalKeterangan').value
        })
    })
    .then(r => r.json())
    .then(() => location.reload());
}

function changeMonth(delta) {
    const url = new URL(window.location);
    let m = parseInt(url.searchParams.get('bulan') || '<?= date('n') ?>');
    let y = parseInt(url.searchParams.get('tahun') || '<?= date('Y') ?>');
    m += delta;
    if (m > 12) { m = 1; y++; }
    if (m < 1) { m = 12; y--; }
    url.searchParams.set('bulan', m);
    url.searchParams.set('tahun', y);
    window.location.href = url.toString();
}
</script>

<?php include '../includes/footer-admin.php'; ?>