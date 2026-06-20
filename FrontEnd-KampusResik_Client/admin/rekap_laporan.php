<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

session_start();
if (!isset($_SESSION['token'])) {
    header('Location: ../login.php');
    exit;
}

if (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin') {
    header("Location: ../" . $_SESSION['role'] . "/dashboard.php");
    exit;
}

require_once __DIR__ . '/../includes/api_client.php';

// Fetch Laporan Data
$res = callApi('GET', 'admin/laporan');
$allReports = (isset($res['status']) && $res['status'] === 'success') ? ($res['data'] ?? []) : [];

// Filter only "selesai" (Completed) reports
$completedReports = array_filter($allReports, function($r) {
    return strtolower($r['status'] ?? '') === 'selesai';
});

// Handle Date Filter if set
$filterDate = $_GET['tanggal'] ?? '';
if (!empty($filterDate)) {
    $completedReports = array_filter($completedReports, function($r) use ($filterDate) {
        $reportDate = date('Y-m-d', strtotime($r['created_at']));
        return $reportDate === $filterDate;
    });
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arsip Laporan - Panel Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/navbarPrivate.php'; ?>
    <main class="container py-4">
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-body p-4">
                <h2 class="h5 fw-bold mb-4">Arsip Laporan (Data Selesai)</h2>
                
                <div class="d-flex flex-wrap gap-2 mb-4">
                    <form method="GET" class="d-flex gap-2 flex-grow-1">
                        <input type="date" name="tanggal" value="<?= htmlspecialchars($filterDate) ?>" class="form-control" style="max-width: 200px;">
                        <button type="submit" class="btn btn-dark">Filter Tanggal</button>
                        <?php if (!empty($filterDate)): ?>
                            <a href="rekap_laporan.php" class="btn btn-outline-secondary">Reset</a>
                        <?php endif; ?>
                    </form>
                    <button class="btn btn-success ms-auto" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i> Cetak / Simpan PDF
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Tanggal Laporan</th>
                                <th>Kategori</th>
                                <th>Pelapor</th>
                                <th>Petugas</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($completedReports)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada arsip laporan selesai.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($completedReports as $lap): ?>
                                    <tr>
                                        <td>#<?= $lap['id'] ?></td>
                                        <td><?= date('d M Y H:i', strtotime($lap['created_at'])) ?></td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                <?= htmlspecialchars($lap['kategori']['nama_kategori'] ?? 'Umum') ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($lap['nama_pelapor'] ?? 'Anonim') ?></td>
                                        <td><?= htmlspecialchars($lap['petugas']['name'] ?? '-') ?></td>
                                        <td><span class="badge bg-success">Selesai</span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>