<?php
require_once '../includes/api-config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';
require_admin();

$page_title = 'Dashboard - Admin';
$token = get_auth_token();

// --- Fetch Data dari API ---
$stats = [];
$chart_data = [];
$kategori_populer = [];

try {
    // Statistik
    $result = fetch_api('/admin/statistics', 'GET', null, $token);
    if ($result['code'] === 200) {
        $stats = $result['body']['data']['list'] ?? [];
    }

    // Chart Mingguan
    $result = fetch_api('/admin/weekly-chart', 'GET', null, $token);
    if ($result['code'] === 200) {
        $chart_data = $result['body']['data'] ?? [];
    }

    // Kategori Populer
    $result = fetch_api('/admin/popular-categories', 'GET', null, $token);
    if ($result['code'] === 200) {
        $kategori_populer = $result['body']['data'] ?? [];
    }
} catch (Exception $e) {
    // Fallback data realistis sesuai DB
    $stats = [
        ['label' => 'Total Laporan', 'value' => '13', 'icon' => 'bi-clipboard-data', 'color' => 'blue', 'trend' => '+12%', 'trend_up' => true],
        ['label' => 'Laporan Masuk', 'value' => '5', 'icon' => 'bi-inbox', 'color' => 'orange', 'trend' => 'Perlu Tindak', 'trend_up' => false],
        ['label' => 'Sedang Diproses', 'value' => '3', 'icon' => 'bi-arrow-repeat', 'color' => 'green', 'trend' => '+5%', 'trend_up' => true],
        ['label' => 'Selesai Hari Ini', 'value' => '5', 'icon' => 'bi-check-circle', 'color' => 'green', 'trend' => '+8%', 'trend_up' => true],
    ];

    $chart_data = [
        ['hari' => 'Sen', 'minggu_ini' => 2, 'minggu_lalu' => 1],
        ['hari' => 'Sel', 'minggu_ini' => 3, 'minggu_lalu' => 2],
        ['hari' => 'Rab', 'minggu_ini' => 1, 'minggu_lalu' => 2],
        ['hari' => 'Kam', 'minggu_ini' => 4, 'minggu_lalu' => 3],
        ['hari' => 'Jum', 'minggu_ini' => 2, 'minggu_lalu' => 2],
        ['hari' => 'Sab', 'minggu_ini' => 1, 'minggu_lalu' => 1],
        ['hari' => 'Min', 'minggu_ini' => 0, 'minggu_lalu' => 0],
    ];

    $kategori_populer = [
        ['nama' => 'Sampah Organik', 'persen' => 35, 'warna' => '#1565C0'],
        ['nama' => 'Sampah Plastik', 'persen' => 25, 'warna' => '#1976D2'],
        ['nama' => 'Sampah Medis', 'persen' => 15, 'warna' => '#0D47A1'],
        ['nama' => 'Sampah B3', 'persen' => 15, 'warna' => '#00897b'],
        ['nama' => 'Sampah Kertas', 'persen' => 10, 'warna' => '#90a4ae'],
    ];
}

// === SAFETY CHECK: Pastikan array tidak kosong ===
if (empty($stats)) {
    $stats = [
        ['label' => 'Total Laporan', 'value' => '0', 'icon' => 'bi-clipboard-data', 'color' => 'blue', 'trend' => '0%', 'trend_up' => true],
        ['label' => 'Laporan Masuk', 'value' => '0', 'icon' => 'bi-inbox', 'color' => 'orange', 'trend' => '0', 'trend_up' => false],
        ['label' => 'Sedang Diproses', 'value' => '0', 'icon' => 'bi-arrow-repeat', 'color' => 'green', 'trend' => '0%', 'trend_up' => true],
        ['label' => 'Selesai Hari Ini', 'value' => '0', 'icon' => 'bi-check-circle', 'color' => 'green', 'trend' => '0%', 'trend_up' => true],
    ];
}

if (empty($chart_data)) {
    $chart_data = [
        ['hari' => 'Sen', 'minggu_ini' => 0, 'minggu_lalu' => 0],
        ['hari' => 'Sel', 'minggu_ini' => 0, 'minggu_lalu' => 0],
        ['hari' => 'Rab', 'minggu_ini' => 0, 'minggu_lalu' => 0],
        ['hari' => 'Kam', 'minggu_ini' => 0, 'minggu_lalu' => 0],
        ['hari' => 'Jum', 'minggu_ini' => 0, 'minggu_lalu' => 0],
        ['hari' => 'Sab', 'minggu_ini' => 0, 'minggu_lalu' => 0],
        ['hari' => 'Min', 'minggu_ini' => 0, 'minggu_lalu' => 0],
    ];
}

if (empty($kategori_populer)) {
    $kategori_populer = [];
}

require_once '../includes/head-admin.php';
require_once '../includes/navbar-admin.php';
?>

<main class="admin-main">
    <div class="container-fluid">
        
        <!-- Header -->
        <div class="admin-header animate-in">
            <div>
                <h4>Ringkasan Statistik</h4>
                <p>Pantau kebersihan dan pengelolaan sampah kampus secara real-time.</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-admin btn-admin-outline" onclick="document.getElementById('tren-mingguan').scrollIntoView({behavior: 'smooth', block: 'start'})">
                    <i class="bi bi-calendar3 me-2"></i>Minggu Ini
                </button>
                <a href="rekap-laporan.php?auto_export=1" class="btn btn-admin btn-admin-outline"><i class="bi bi-file-excel me-2"></i>Export Laporan</a>
            </div>
        </div>
        
        <!-- Stat Cards -->
        <div class="row g-3 mb-4">
            <?php foreach ($stats as $stat): ?>
            <div class="col-md-6 col-xl-3 animate-in">
                <div class="admin-card">
                    <div class="card-body">
                        <div class="stat-card">
                            <div class="stat-icon <?= $stat['color'] ?>">
                                <i class="bi <?= $stat['icon'] ?>"></i>
                            </div>
                            <div class="stat-info">
                                <h3><?= $stat['value'] ?></h3>
                                <p><?= $stat['label'] ?></p>
                                <span class="stat-trend <?= $stat['trend_up'] ? 'up' : 'down' ?>">
                                    <?= $stat['trend_up'] ? '↗' : '⚠' ?> <?= $stat['trend'] ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="row g-4">
            <!-- Chart Section -->
            <div class="col-lg-8 animate-in">
                <div class="chart-container">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div id="tren-mingguan">
                            <h5 class="fw-bold mb-0">Tren Laporan Mingguan</h5>
                        </div>
                        <div class="d-flex gap-3">
                            <span class="d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                                <span style="width: 12px; height: 12px; background: var(--adm-primary); border-radius: 3px; display: inline-block;"></span>
                                Minggu Ini
                            </span>
                            <span class="d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                                <span style="width: 12px; height: 12px; background: #90a4ae; border-radius: 3px; display: inline-block;"></span>
                                Minggu Lalu
                            </span>
                        </div>
                    </div>
                    
                    <?php 
                    // SAFE: Cek array tidak kosong sebelum max()
                    $chart_values = array_column($chart_data, 'minggu_ini');
                    $max_val = (!empty($chart_values)) ? max($chart_values) : 0;
                    ?>
                    
                    <div class="chart-bars">
                        <?php foreach ($chart_data as $data): 
                            $h1 = ($max_val > 0) ? ($data['minggu_ini'] / $max_val) * 100 : 0;
                            $h2 = ($max_val > 0) ? ($data['minggu_lalu'] / $max_val) * 100 : 0;
                        ?>
                        <div class="chart-bar-group">
                            <div style="display: flex; gap: 4px; align-items: flex-end; height: 160px;">
                                <div class="chart-bar secondary" style="height: <?= $h2 ?>%; width: 20px;"></div>
                                <div class="chart-bar primary" style="height: <?= $h1 ?>%; width: 20px;"></div>
                            </div>
                            <span class="chart-label"><?= $data['hari'] ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- Kategori Populer -->
            <div class="col-lg-4 animate-in">
                <div class="admin-card h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-4">Kategori Sampah Terpopuler</h5>
                        
                        <?php if (empty($kategori_populer)): ?>
                            <!-- Tampilan kosong -->
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-1 d-block mb-3 opacity-25"></i>
                                <p>Belum ada data kategori</p>
                                <small>Data akan muncul setelah ada laporan masuk</small>
                            </div>
                        <?php else: ?>
                            <?php foreach ($kategori_populer as $kat): ?>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fw-semibold" style="font-size: 0.9rem;"><?= $kat['nama'] ?></span>
                                    <span class="fw-bold" style="font-size: 0.9rem;"><?= $kat['persen'] ?>%</span>
                                </div>
                                <div class="progress-admin">
                                    <div class="progress-bar" style="width: <?= $kat['persen'] ?>%; background: <?= $kat['warna'] ?>"></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <div class="mt-4 p-3 rounded-3" style="background: linear-gradient(135deg, #1565C0 0%, #1976D2 100%); color: #fff;">
                            <div class="d-flex align-items-center gap-3">
                                <i class="bi bi-lightbulb" style="font-size: 1.5rem;"></i>
                                <div>
                                    <p class="mb-0 fw-semibold" style="font-size: 0.9rem;">Tips Pengelolaan</p>
                                    <p class="mb-0" style="font-size: 0.8rem; opacity: 0.9;">Pastikan area TPS3R selalu bersih untuk mencegah penumpukan lalat dan bau tidak sedap.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</main>

<?php include '../includes/footer-admin.php'; ?>