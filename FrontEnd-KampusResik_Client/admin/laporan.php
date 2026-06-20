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

// Handle Delete Request
if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['id'])) {
    $deleteRes = callApi('DELETE', 'admin/laporan/' . $_POST['id']);
    if (isset($deleteRes['status']) && $deleteRes['status'] === 'success') {
        $msgSuccess = "Laporan berhasil dihapus.";
    } else {
        $msgError = $deleteRes['message'] ?? "Gagal menghapus laporan.";
    }
}

// Fetch Laporan Data
$res = callApi('GET', 'admin/laporan');
$daftarLaporan = (isset($res['status']) && $res['status'] === 'success') ? ($res['data'] ?? []) : [];

// Calculate stats
$total = count($daftarLaporan);
$menunggu = 0;
$diproses = 0;
$selesai = 0;
foreach ($daftarLaporan as $l) {
    $status = strtolower($l['status'] ?? '');
    if ($status === 'menunggu') $menunggu++;
    elseif ($status === 'diproses') $diproses++;
    elseif ($status === 'selesai') $selesai++;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Laporan - Panel Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/navbarPrivate.php'; ?>
    <main class="container py-4">
        
        <?php if (isset($msgSuccess)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($msgSuccess) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($msgError)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($msgError) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="p-3 bg-warning-subtle text-warning-emphasis rounded-3 fw-bold shadow-sm">
                    Menunggu: <?= $menunggu ?>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 bg-primary-subtle text-primary-emphasis rounded-3 fw-bold shadow-sm">
                    Diproses: <?= $diproses ?>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 bg-success-subtle text-success-emphasis rounded-3 fw-bold shadow-sm">
                    Selesai: <?= $selesai ?>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 bg-secondary-subtle text-secondary-emphasis rounded-3 fw-bold shadow-sm">
                    Total: <?= $total ?>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">Data Laporan Masuk</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Pelapor</th>
                                <th>Deskripsi</th>
                                <th>Status</th>
                                <th>Foto</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($daftarLaporan)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada laporan sampah.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($daftarLaporan as $lap): ?>
                                    <tr>
                                        <td>#<?= $lap['id'] ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($lap['nama_pelapor'] ?? 'Anonim') ?></strong>
                                            <div class="text-muted small"><?= date('d/m/Y H:i', strtotime($lap['created_at'])) ?></div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary mb-1">
                                                <?= htmlspecialchars($lap['kategori']['nama_kategori'] ?? 'Umum') ?>
                                            </span>
                                            <div class="small text-truncate" style="max-width: 250px;">
                                                <?= htmlspecialchars($lap['deskripsi_singkat']) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php 
                                            $badgeClass = 'bg-warning text-dark';
                                            if ($lap['status'] === 'diproses') $badgeClass = 'bg-primary';
                                            elseif ($lap['status'] === 'selesai') $badgeClass = 'bg-success';
                                            ?>
                                            <span class="badge <?= $badgeClass ?>"><?= ucfirst($lap['status']) ?></span>
                                        </td>
                                        <td>
                                            <?php if (!empty($lap['foto_sebelum'])): ?>
                                                <a href="http://127.0.0.1:8000/storage/sampah/<?= $lap['foto_sebelum'] ?>" target="_blank" class="btn btn-sm btn-outline-info py-0 px-2 small">Sebelum</a>
                                            <?php endif; ?>
                                            <?php if (!empty($lap['foto_sesudah'])): ?>
                                                <a href="http://127.0.0.1:8000/storage/sampah/<?= $lap['foto_sesudah'] ?>" target="_blank" class="btn btn-sm btn-outline-success py-0 px-2 small">Sesudah</a>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="proses.php?id=<?= $lap['id'] ?>" class="btn btn-sm btn-dark">Proses</a>
                                                <form method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan ini?')">
                                                    <input type="hidden" name="id" value="<?= $lap['id'] ?>">
                                                    <input type="hidden" name="action" value="delete">
                                                    <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
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