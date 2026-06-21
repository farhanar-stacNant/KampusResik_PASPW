<?php
session_start();
require_once __DIR__ . '/../includes/api_client.php';
$status = $_GET['status'] ?? 'semua';
$laporanRes = callApi('GET', 'lacak-laporan');

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['token'])) {
    header('Location: ../login.php');
    exit;
}

if (isset($_SESSION['role']) && $_SESSION['role'] !== 'petugas') {
    header("Location: ../" . $_SESSION['role'] . "/dashboard.php");
    exit;
}

$daftarLaporan = (isset($laporanRes['status']) && $laporanRes['status'] === 'success') ? ($laporanRes['data'] ?? []) : [];
$myId = $_SESSION['user']['id'] ?? null;

$filteredLaporan = array_filter($daftarLaporan, function($item) use ($status, $myId) {
    $itemStatus = strtolower($item['status'] ?? '');
    
    // Only show reports assigned to this officer, or new reports (menunggu)
    if (!empty($item['petugas_id']) && $item['petugas_id'] != $myId) {
        return false;
    }
    
    if ($status === 'semua') {
        return true;
    }
    return $itemStatus === $status;
});
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Laporan - KampusResik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { border-radius: 1rem; border: none; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/navbarPrivate.php'; ?>
    <main class="container py-5">
        <h1 class="h3 fw-bold mb-4">Riwayat & Monitoring</h1>
        
        <div class="btn-group mb-4" role="group" aria-label="Filter Status">
            <a href="?status=semua" class="btn btn-dark px-4">Semua</a>
            <a href="?status=menunggu" class="btn btn-warning px-4">Menunggu</a>
            <a href="?status=selesai" class="btn btn-success px-4">Selesai</a>
        </div>

        <div class="card p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="p-3">ID</th>
                            <th class="p-3">Lokasi</th>
                            <th class="p-3">Status</th>
                            <th class="p-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($filteredLaporan)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Belum ada riwayat laporan.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($filteredLaporan as $item): ?>
                                <tr>
                                    <td class="p-3 fw-bold text-secondary">#<?= $item['id'] ?></td>
                                    <td class="p-3"><?= htmlspecialchars($item['deskripsi_singkat']) ?></td>
                                    <td class="p-3">
                                        <?php 
                                        $badgeClass = 'bg-secondary';
                                        if ($item['status'] === 'menunggu') $badgeClass = 'bg-warning text-dark';
                                        elseif ($item['status'] === 'diproses') $badgeClass = 'bg-primary';
                                        elseif ($item['status'] === 'selesai') $badgeClass = 'bg-success';
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= ucfirst($item['status']) ?></span>
                                    </td>
                                    <td class="p-3">
                                        <a href="detail.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-primary fw-bold">Proses</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>