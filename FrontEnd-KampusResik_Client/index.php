<?php
require_once __DIR__ . '/includes/api_client.php';

$totalPengaduan = 0;
$laporanSelesai = 0;

$res = callApi('GET', 'statistik');

if (isset($res['status']) && $res['status'] === 'success') {
    $totalPengaduan = $res['data']['ringkasan']['total'] ?? 0;
    $laporanSelesai = $res['data']['ringkasan']['selesai'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - KampusResik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8fafc; color: #1e293b; }
        .hero-gradient { background: linear-gradient(to right, #0d9488, #06b6d4); }
        .card-stat { border-radius: 1rem; border: 1px solid #e2e8f0; }
        .icon-box { width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 0.75rem; }
    </style>
</head>
<body>
    <?php include 'includes/navbarPublic.php'; ?>
    <main class="container py-5">
        
        <div class="hero-gradient text-white rounded-4 p-4 p-md-5 mb-5 shadow-sm">
            <h1 class="display-5 fw-bold mb-3">Selamat Datang di KampusResik!</h1>
            <p class="mb-4 opacity-75" style="max-width: 600px;">Layanan platform pengaduan kebersihan area kampus secara real-time. Laporkan tumpukan sampah, pantau penanganannya, dan mari wujudkan lingkungan kampus yang sehat bersama.</p>
            <a href="pengaduan.php" class="btn btn-light text-teal fw-bold px-4 py-2 shadow-sm">
                Laporkan Temuan Sampah
            </a>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <div class="card card-stat p-4 shadow-sm border-0 d-flex flex-row align-items-center">
                    <div class="icon-box bg-teal-100 text-teal-600 me-3">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    </div>
                    <div>
                        <small class="text-secondary fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.1em;">Total Pengaduan Masuk</small>
                        <h3 class="fw-bold mb-0"><?= $totalPengaduan ?></h3>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-stat p-4 shadow-sm border-0 d-flex flex-row align-items-center">
                    <div class="icon-box bg-info-subtle text-cyan-600 me-3">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <small class="text-secondary fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.1em;">Laporan Selesai Dibersihkan</small>
                        <h3 class="fw-bold mb-0"><?= $laporanSelesai ?></h3>
                    </div>
                </div>
            </div>
                <div class="col-lg-4">
                <div class="tips-card d-flex flex-column justify-content-between">
                    <div class="tips-banner">
                        <div class="position-relative text-white p-3 z-3 w-100 text-start">
                            <span class="badge bg-teal-hover mb-2" style="background-color: var(--teal-primary);">Tips Pengelolaan</span>
                            <h6 class="fw-bold mb-0">Kebersihan TPS3R</h6>
                        </div>
                    </div>
                    <div class="p-3 flex-grow-1 d-flex flex-column justify-content-between">
                        <p class="text-muted small mb-3">Pastikan area TPS3R selalu bersih untuk mencegah penumpukan lalat dan timbulnya bau tidak sedap.</p>
                        <a href="#" class="text-teal text-decoration-none fw-semibold small d-inline-flex align-items-center gap-1">
                            Pelajari Selengkapnya <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
             </div>
        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>