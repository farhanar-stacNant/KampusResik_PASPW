<?php
$pageTitle = 'Status';
require_once __DIR__ . '/../includes/api-config.php';
require_once __DIR__ . '/../includes/head-public.php';
require_once __DIR__ . '/../includes/navbar-public.php';
$search = $_GET['search'] ?? '';

// Fetch dari API
$laporanList = [];
$total = 0;
$aktif = 0;
$selesai = 0;
$errorMsg = '';

try {
    if ($search) {
        // Cari berdasarkan kode
        $apiUrl = API_BASE_URL . '/public/status/' . urlencode($search);
        $response = api_get_contents($apiUrl);
        
        if ($response !== false) {
            $data = json_decode($response, true);
            if (isset($data['data'])) {
                $laporanList = [$data['data']]; // Wrap dalam array
            }
        }
    } else {
        // Ambil semua laporan
        $apiUrl = API_BASE_URL . '/public/reports?limit=50';
        $response = api_get_contents($apiUrl);
        
        if ($response !== false) {
            $data = json_decode($response, true);
            $laporanList = $data['data'] ?? [];
        }
    }
    
    // Hitung statistik
    $total = count($laporanList);
    $aktif = count(array_filter($laporanList, fn($l) => in_array($l['status'] ?? '', ['dikirim', 'diterima', 'diproses'])));
    $selesai = count(array_filter($laporanList, fn($l) => ($l['status'] ?? '') === 'selesai'));
    
} catch (Exception $e) {
    $errorMsg = 'Gagal memuat data dari server.';
}

// Status config untuk badge & progress
$statusConfig = [
    'dikirim' => ['label' => 'Dikirim', 'badge' => 'bg-secondary', 'icon' => 'bi-send', 'progress' => 25],
    'diterima' => ['label' => 'Diterima', 'badge' => 'bg-info', 'icon' => 'bi-check-circle', 'progress' => 50],
    'diproses' => ['label' => 'Diproses', 'badge' => 'bg-warning', 'icon' => 'bi-arrow-repeat', 'progress' => 75],
    'selesai' => ['label' => 'Selesai', 'badge' => 'bg-success', 'icon' => 'bi-check-lg', 'progress' => 100],
    'ditolak' => ['label' => 'Ditolak', 'badge' => 'bg-danger', 'icon' => 'bi-x-circle', 'progress' => 0]
];
?>

<!-- Page Header -->
<section class="page-header py-4">
    <div class="container-fluid px-3 px-md-4">
        <h2 class="fw-bold mb-1">Status Laporan Anda</h2>
        <p class="text-muted mb-0">Pantau perkembangan laporan kebersihan yang telah Anda sampaikan.</p>
    </div>
</section>

<!-- Statistik -->
<section class="pb-4">
    <div class="container-fluid px-3 px-md-4">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <i class="bi bi-folder stat-icon text-primary"></i>
                            <h3 class="stat-value mb-0"><?= $total ?></h3>
                            <small class="stat-label">Laporan Dikirim</small>
                        </div>
                        <span class="badge bg-light text-dark">Total</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <i class="bi bi-clock stat-icon text-warning"></i>
                            <h3 class="stat-value mb-0"><?= str_pad($aktif, 2, '0', STR_PAD_LEFT) ?></h3>
                            <small class="stat-label">Dalam Proses</small>
                        </div>
                        <span class="badge bg-warning text-dark">Aktif</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card bg-success text-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <i class="bi bi-check-circle stat-icon text-white-50"></i>
                            <h3 class="stat-value mb-0 text-white"><?= str_pad($selesai, 2, '0', STR_PAD_LEFT) ?></h3>
                            <small class="stat-label text-white-75">Masalah Teratasi</small>
                        </div>
                        <span class="badge bg-white text-success">Selesai</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Daftar Laporan -->
<section class="pb-5">
    <div class="container-fluid px-3 px-md-4">
        <div class="row g-4">
            <!-- Kolom Kiri: Daftar Laporan -->
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Daftar Laporan Terkini</h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                </div>
                
                <?php if ($errorMsg): ?>
                    <div class="alert alert-warning"><?= $errorMsg ?></div>
                <?php endif; ?>
                
                <div class="laporan-list">
                    <?php if (!empty($laporanList)): ?>
                        <?php foreach ($laporanList as $laporan): 
                            $config = $statusConfig[$laporan['status'] ?? 'dikirim'] ?? $statusConfig['dikirim'];
                            $progress = $config['progress'];
                        ?>
                        <div class="laporan-item" data-kode="<?= htmlspecialchars($laporan['kode'] ?? '') ?>" onclick="showDetail(<?= $laporan['id'] ?? 0 ?>)">
                            <div class="row g-3 align-items-center">
                                <div class="col-auto">
                                    <div class="laporan-thumb">
                                        <img src="<?= $laporan['foto'] ?? '../Assets/images/icon/logo.png' ?>" alt="<?= htmlspecialchars($laporan['judul'] ?? '') ?>" onerror="this.src='../Assets/images/icon/logo.png'">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <h6 class="laporan-title mb-0"><?= htmlspecialchars($laporan['judul'] ?? 'Tanpa Judul') ?></h6>
                                        <span class="badge <?= $config['badge'] ?> badge-status">
                                            <i class="bi <?= $config['icon'] ?>"></i> <?= $config['label'] ?>
                                        </span>
                                    </div>
                                    <p class="laporan-lokasi mb-2">
                                        <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($laporan['lokasi'] ?? 'Kampus') ?>
                                    </p>
                                    
                                    <?php if (($laporan['status'] ?? '') !== 'ditolak'): ?>
                                    <div class="progress-track">
                                        <div class="progress-step <?= $progress >= 25 ? 'active' : '' ?>">
                                            <span class="step-dot"></span>
                                            <small>Diterima</small>
                                        </div>
                                        <div class="progress-step <?= $progress >= 50 ? 'active' : '' ?>">
                                            <span class="step-dot"></span>
                                            <small>Diproses</small>
                                        </div>
                                        <div class="progress-step <?= $progress >= 100 ? 'active' : '' ?>">
                                            <span class="step-dot"></span>
                                            <small>Selesai</small>
                                        </div>
                                    </div>
                                    <?php else: ?>
                                    <div class="text-danger small">
                                        <i class="bi bi-x-circle"></i> Laporan ditolak
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (($laporan['status'] ?? '') === 'selesai' && isset($laporan['selesai_at'])): ?>
                                    <small class="text-muted">Selesai pada: <?= htmlspecialchars($laporan['selesai_at']) ?></small>
                                    <?php endif; ?>
                                </div>
                                <div class="col-auto">
                                    <a href="detail.php?id=<?= $laporan['id'] ?? 0 ?>" class="btn btn-sm btn-link text-success text-decoration-none" onclick="event.stopPropagation();">
                                        Lihat Detail <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="mt-3 text-muted">Belum ada laporan. <a href="pengaduan.php">Buat laporan baru</a></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Kolom Kanan: Timeline & CTA -->
            <div class="col-lg-4">
                <div class="sticky-top" style="top: 80px; z-index: 1;">
                    <!-- Timeline -->
                    <div class="card-clean p-4 mb-4">
                        <h6 class="fw-bold mb-3">Timeline Terakhir</h6>
                        <div class="timeline" id="timelineContainer">
                            <div class="text-muted text-center py-3">
                                <i class="bi bi-clock-history fs-4"></i>
                                <p class="small mb-0 mt-2">Klik laporan untuk melihat timeline</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- CTA -->
                    <div class="card-clean p-4 bg-success text-white">
                        <h6 class="fw-bold mb-2">Bantu Kampus Tetap Resik!</h6>
                        <p class="small mb-3 opacity-75">Laporan Anda sangat berarti bagi kenyamanan bersama. Terus berikan kontribusi positif.</p>
                        <a href="pengaduan.php" class="btn btn-light btn-sm w-100">
                            <i class="bi bi-plus-lg"></i> Buat Laporan Baru
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function showDetail(id) {
    if (!id) return;
    
    const timelineContainer = document.getElementById('timelineContainer');
    timelineContainer.innerHTML = '<div class="text-center py-3"><div class="spinner-border spinner-border-sm text-success"></div><p class="small mb-0 mt-2">Memuat...</p></div>';
    
    fetch('<?= API_BASE_URL ?>/public/reports/' + id, {
        headers: {
            'X-Api-Token': '<?= API_SECRET_TOKEN ?>'
        }
    })
        .then(response => response.json())
        .then(data => {
            const report = data.data;
            
            if (report.timeline && report.timeline.length > 0) {
                let html = '';
                report.timeline.forEach((item, index) => {
                    const isLast = index === report.timeline.length - 1;
                    const iconClass = {
                        'Dikirim': 'bi-send',
                        'Diterima': 'bi-check-circle',
                        'Diproses': 'bi-arrow-repeat',
                        'Selesai': 'bi-check-lg',
                        'Ditolak': 'bi-x-circle'
                    }[item.status] || 'bi-circle';
                    
                    html += `
                        <div class="timeline-item ${isLast ? 'active' : ''}">
                            <div class="timeline-icon">
                                <i class="bi ${iconClass}"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="mb-1">${item.status}</h6>
                                <p class="small text-muted mb-1">${item.deskripsi || ''}</p>
                                <small class="text-muted">${item.waktu || ''}</small>
                            </div>
                        </div>
                    `;
                });
                timelineContainer.innerHTML = html;
            } else {
                timelineContainer.innerHTML = `
                    <div class="text-muted text-center py-3">
                        <p class="small mb-0">Belum ada timeline</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            timelineContainer.innerHTML = `
                <div class="text-muted text-center py-3">
                    <p class="small mb-0 text-danger">Gagal memuat timeline</p>
                </div>
            `;
        });
}
</script>
<?php require_once __DIR__ . '/../includes/footer-public.php'; ?>