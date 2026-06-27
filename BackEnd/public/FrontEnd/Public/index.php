<?php
$pageTitle = 'Beranda';
require_once __DIR__ . '/../includes/api-config.php';
require_once __DIR__ . '/../includes/head-public.php';
require_once __DIR__ . '/../includes/navbar-public.php';

// Fallback kategori_label
if (!function_exists('kategori_label')) {
    function kategori_label($kode) {
        $map = ['ORG' => 'Organik', 'ANR' => 'Anorganik', 'B3' => 'B3'];
        return $map[$kode] ?? 'Lainnya';
    }
}
$stats = [];
$distribusi = ['organik' => 0, 'anorganik' => 0, 'b3' => 0];
$recentReports = [];
$kebiasaanData = [
    'senin' => 0, 'selasa' => 0, 'rabu' => 0,
    'kamis' => 0, 'jumat' => 0, 'sabtu' => 0, 'minggu' => 0
];
$errorMsg = '';

try {
    $statsJson = api_get_contents(API_BASE_URL . '/public/statistics');
    if ($statsJson) {
        $statsData = json_decode($statsJson, true);
        $stats = $statsData['data'] ?? [];
    }

    $distJson = api_get_contents(API_BASE_URL . '/public/waste-distribution');
    if ($distJson) {
        $distData = json_decode($distJson, true);
        $distribusi = array_merge($distribusi, $distData['data'] ?? []);
    }

    $kebiasaanJson = api_get_contents(API_BASE_URL . '/public/waste-habits?period=weekly');
    if ($kebiasaanJson) {
        $kebiasaanData = array_merge($kebiasaanData, json_decode($kebiasaanJson, true)['data'] ?? []);
    }

    $reportsJson = api_get_contents(API_BASE_URL . '/public/reports?limit=6');
    if ($reportsJson) {
        $reportsData = json_decode($reportsJson, true);
        $recentReports = $reportsData['data'] ?? [];
    }
} catch (Exception $e) {
    $errorMsg = 'Gagal memuat data dari server.';
}

$maxValue = max($kebiasaanData) ?: 1;
?>

<!-- HERO SECTION -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <h1 class="hero-title mb-4">
                    Selamat Datang, Di<br>
                    <span class="highlight">Kampus Resik!</span>
                </h1>
                <p class="hero-subtitle mb-4">
                    Pantau perkembangan kebersihan lingkungan kampus dan bagikan inspirasi berkelanjutan dari hari-hari. 
                    Kelola laporan sampah dan jaga lingkungan kampus tetap bersih.
                </p>
                <div class="hero-buttons">
                    <a href="pengaduan.php" class="btn-hero-primary">
                        <i class="bi bi-plus-lg"></i> Buat Laporan
                    </a>
                    <a href="laporan.php" class="btn-hero-outline">
                        <i class="bi bi-eye"></i> Lihat Laporan
                    </a>
                    <a href="status.php" class="btn-hero-dark">
                        <i class="bi bi-clipboard-check"></i> Status Laporan
                    </a>
                </div>
            </div>
            
            <div class="col-lg-5 mt-5 mt-lg-0">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="hero-stat-card">
                            <i class="bi bi-file-earmark-text hero-stat-icon text-success"></i>
                            <div class="hero-stat-value"><?= number_format($stats['laporan_minggu_ini'] ?? 0) ?></div>
                            <div class="hero-stat-label">Laporan Minggu Ini</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="hero-stat-card">
                            <i class="bi bi-check-circle hero-stat-icon text-success"></i>
                            <div class="hero-stat-value"><?= number_format($stats['laporan_selesai'] ?? 0) ?></div>
                            <div class="hero-stat-label">Laporan Selesai</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="hero-stat-card">
                            <div class="hero-stat-value text-success"><?= number_format($stats['total_sampah'] ?? 0) ?> kg</div>
                            <div class="hero-stat-label">Total Sampah Terkelola</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- DISTRIBUSI JENIS SAMPAH -->
<section class="container py-5">
    <h3 class="fw-bold mb-2">Distribusi Jenis Sampah</h3>
    <p class="text-muted mb-4">Total akumulasi pengelolaan sampah dari seluruh area kampus bulan ini</p>
    
    <div class="row g-4">
        <div class="col-md-4">
            <div class="distribusi-card">
                <span class="distribusi-badge organik">Organik</span>
                <div class="distribusi-value organik"><?= number_format($distribusi['organik']) ?></div>
                <div class="distribusi-label">Laporan</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="distribusi-card">
                <span class="distribusi-badge anorganik">Anorganik</span>
                <div class="distribusi-value anorganik"><?= number_format($distribusi['anorganik']) ?></div>
                <div class="distribusi-label">Laporan</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="distribusi-card">
                <span class="distribusi-badge b3">B3</span>
                <div class="distribusi-value b3"><?= number_format($distribusi['b3']) ?></div>
                <div class="distribusi-label">Laporan</div>
            </div>
        </div>
    </div>
</section>

<!-- KEBIASAAN PEMBUANGAN SAMPAH -->
<section class="container mb-5">
    <div class="chart-section">
        <div class="chart-header">
            <div>
                <h3 class="fw-bold mb-1">Kebiasaan Pembuangan Sampah</h3>
                <p class="text-muted mb-0">Pantau tren pembuangan sampah mingguan Anda</p>
            </div>
            <select class="chart-period-select" id="periodSelect">
                <option value="weekly" selected>Minggu Ini</option>
                <option value="monthly">Bulan Ini</option>
                <option value="yearly">Tahun Ini</option>
            </select>
        </div>
        
        <div class="cylinder-chart-container" id="cylinderChart">
            <?php 
            $hariLabels = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
            $hariKeys = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
            foreach ($hariKeys as $i => $key): 
                $value = $kebiasaanData[$key] ?? 0;
                $height = ($value / $maxValue) * 180;
                $height = max($height, 5);
            ?>
            <div class="cylinder-wrapper">
                <div class="cylinder" style="height: 200px;">
                    <div class="cylinder-fill" style="height: <?= $height ?>px;"></div>
                    <span class="cylinder-value"><?= $value ?> kg</span>
                </div>
                <span class="cylinder-label"><?= $hariLabels[$i] ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- LAPORAN TERBARU -->
<section class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">Laporan Terbaru</h3>
        <a href="laporan.php" class="btn btn-outline-success btn-sm">
            Lihat Semua <i class="bi bi-arrow-right"></i>
        </a>
    </div>
    
    <div class="row g-4">
        <?php if (!empty($recentReports)): ?>
            <?php foreach ($recentReports as $report): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="laporan-card">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <?php 
                                $status = $report['status'] ?? 'dikirim';
                                $badgeClass = match($status) {
                                    'selesai' => 'selesai',
                                    'diproses' => 'diproses',
                                    'diterima' => 'diterima',
                                    'ditolak' => 'ditolak',
                                    default => 'menunggu'
                                };
                                $kategoriLabel = kategori_label($report['jenis_sampah'] ?? $report['kategori'] ?? 'ORG');
                                ?>
                                <span class="laporan-badge <?= $badgeClass ?>"><?= ucfirst(htmlspecialchars($status)) ?></span>
                                <small class="text-muted"><?= htmlspecialchars($report['created_at'] ?? '-') ?></small>
                            </div>
                            <h5 class="laporan-title"><?= htmlspecialchars($report['judul'] ?? 'Tanpa Judul') ?></h5>
                            <p class="laporan-desc"><?= htmlspecialchars(mb_strimwidth($report['deskripsi'] ?? '-', 0, 100, '...')) ?></p>
                            <div class="laporan-meta">
                                <span class="laporan-location">
                                    <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($report['lokasi'] ?? 'Kampus') ?>
                                </span>
                                <a href="detail.php?id=<?= htmlspecialchars($report['id'] ?? '') ?>" class="btn-detail">Detail</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <p class="mt-3 text-muted">Belum ada laporan atau API sedang tidak dapat diakses.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- CARA PENGGUNAAN -->
<section class="guide-section">
    <div class="container">
        <h3 class="text-center fw-bold mb-5">Cara Menggunakan KampusResik</h3>
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="p-3">
                    <div class="guide-icon-wrapper">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                    <h5 class="guide-title">1. Buat Laporan</h5>
                    <p class="guide-desc">Isi formulir dengan detail masalah kebersihan yang kamu temui di kampus.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3">
                    <div class="guide-icon-wrapper">
                        <i class="bi bi-search"></i>
                    </div>
                    <h5 class="guide-title">2. Pantau Progress</h5>
                    <p class="guide-desc">Tim kampus akan memproses laporanmu. Pantau statusnya secara real-time.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3">
                    <div class="guide-icon-wrapper">
                        <i class="bi bi-check-lg"></i>
                    </div>
                    <h5 class="guide-title">3. Selesai</h5>
                    <p class="guide-desc">Laporan terverifikasi selesai. Kampus menjadi lebih bersih dan nyaman!</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer-public.php'; ?>