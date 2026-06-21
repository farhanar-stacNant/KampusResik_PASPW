<?php
// Set path cookie agar sesi sinkron di semua folder
session_set_cookie_params(['path' => '/']);
session_start();

// Periksa login: Jika tidak ada token, paksa balik ke login.php di root
if (!isset($_SESSION['token'])) {
    header("Location: ../login.php");
    exit;
}

// Pastikan akses hanya untuk petugas (mencegah akses lintas role)
if (isset($_SESSION['role']) && $_SESSION['role'] !== 'petugas') {
    header("Location: ../" . $_SESSION['role'] . "/dashboard.php");
    exit;
}

require_once __DIR__ . '/../includes/api_client.php';

$res = callApi('GET', 'lacak-laporan');
$daftarLaporan = (isset($res['status']) && $res['status'] === 'success') ? ($res['data'] ?? []) : [];

$laporanBaru = 0;
$sedangDiproses = 0;
$selesai = 0;
$myId = $_SESSION['user']['id'] ?? null;

foreach ($daftarLaporan as $lap) {
    $status = strtolower($lap['status'] ?? '');
    if ($status === 'menunggu') {
        $laporanBaru++;
    } elseif ($status === 'diproses' && ($lap['petugas_id'] == $myId)) {
        $sedangDiproses++;
    } elseif ($status === 'selesai' && $lap['petugas_id'] == $myId) {
        $selesai++;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Petugas - KampusResik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .stat-card { border-radius: 1rem; border: none; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/navbarPrivate.php'; ?>
    
    <main class="container py-4">
        <header class="mb-5">
            <h1 class="fw-bold text-dark">Halo, <?= htmlspecialchars($_SESSION['user']['name'] ?? 'Petugas') ?>!</h1>
            <p class="text-secondary">Siap bertugas menjaga kampus hari ini?</p>
        </header>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card stat-card p-4">
                    <p class="text-secondary small mb-1">Laporan Baru</p>
                    <h3 class="fw-bold text-warning"><?= $laporanBaru ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card p-4">
                    <p class="text-secondary small mb-1">Sedang Diproses</p>
                    <h3 class="fw-bold text-primary"><?= $sedangDiproses ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card p-4">
                    <p class="text-secondary small mb-1">Selesai</p>
                    <h3 class="fw-bold text-teal" style="color: #0A2947;"><?= $selesai ?></h3>
                </div>
            </div>
        </div>

        <section class="card stat-card p-4">
            <h2 class="h5 fw-bold mb-3">Tindakan Cepat</h2>
            <div>
                <a href="riwayat.php" class="btn btn-teal text-white fw-bold px-4 py-2" style="background-color: #293681;">
                    Lihat Semua Laporan
                </a>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>