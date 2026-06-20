<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['token'])) {
    header('Location: ../login.php');
    exit;
}

if (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin') {
    header("Location: ../" . $_SESSION['role'] . "/dashboard.php");
    exit;
}

require_once __DIR__ . '/../includes/api_client.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: laporan.php');
    exit;
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'] ?? 'menunggu';
    $petugas_id = !empty($_POST['petugas_id']) ? $_POST['petugas_id'] : null;

    $updateData = [
        'status' => $status,
        'petugas_id' => $petugas_id
    ];

    $updateRes = callApi('PUT', 'admin/laporan/' . $id, $updateData);

    if (isset($updateRes['status']) && $updateRes['status'] === 'success') {
        header('Location: laporan.php');
        exit;
    } else {
        $errorMsg = $updateRes['message'] ?? "Gagal menyimpan perubahan.";
    }
}

// Fetch Report Detail
$reportRes = callApi('GET', 'admin/laporan/' . $id);
$laporan = (isset($reportRes['status']) && $reportRes['status'] === 'success') ? ($reportRes['data'] ?? null) : null;

if (!$laporan) {
    die("Laporan sampah tidak ditemukan.");
}

// Fetch All Users (to filter and show Petugas dropdown)
$usersRes = callApi('GET', 'admin/users');
$users = (isset($usersRes['status']) && $usersRes['status'] === 'success') ? ($usersRes['data'] ?? []) : [];
$petugasList = array_filter($users, function($u) {
    return strtolower($u['role']) === 'petugas';
});
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proses Laporan - KampusResik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .form-card { border-radius: 1rem; border: none; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075); }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/navbarPrivate.php'; ?>
    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                
                <a href="laporan.php" class="text-decoration-none text-secondary mb-3 d-inline-block">&larr; Kembali ke Daftar Laporan</a>

                <?php if (isset($errorMsg)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($errorMsg) ?></div>
                <?php endif; ?>

                <div class="card form-card p-4">
                    <h2 class="h5 fw-bold mb-4">Detail Laporan #<?= htmlspecialchars($id) ?></h2>
                    
                    <div class="mb-4 text-sm bg-light p-3 rounded">
                        <div class="mb-2"><strong>Pelapor:</strong> <?= htmlspecialchars($laporan['nama_pelapor'] ?? 'Anonim') ?></div>
                        <div class="mb-2"><strong>Kategori:</strong> <?= htmlspecialchars($laporan['kategori']['nama_kategori'] ?? 'Umum') ?></div>
                        <div class="mb-2"><strong>Deskripsi:</strong> <?= htmlspecialchars($laporan['deskripsi_singkat']) ?></div>
                        <div class="mb-2"><strong>Lokasi (Lat, Long):</strong> <?= htmlspecialchars($laporan['latitude'] ?? '-') ?>, <?= htmlspecialchars($laporan['longitude'] ?? '-') ?></div>
                        <?php if (!empty($laporan['foto_sebelum'])): ?>
                            <div class="mt-2">
                                <strong>Foto Kondisi Sebelum:</strong><br>
                                <img src="<?= htmlspecialchars($laporan['url_foto_sebelum'] ?? ('http://127.0.0.1:8000/storage/sampah/' . $laporan['foto_sebelum'])) ?>" class="img-fluid rounded mt-1 border" style="max-height: 250px;">
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($laporan['foto_sesudah'])): ?>
                            <div class="mt-2">
                                <strong>Foto Kondisi Setelah:</strong><br>
                                <img src="<?= htmlspecialchars($laporan['url_foto_sesudah'] ?? ('http://127.0.0.1:8000/storage/sampah/' . $laporan['foto_sesudah'])) ?>" class="img-fluid rounded mt-1 border" style="max-height: 250px;">
                            </div>
                        <?php endif; ?>
                    </div>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Ubah Status:</label>
                            <select name="status" class="form-select py-2">
                                <option value="menunggu" <?= $laporan['status'] === 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
                                <option value="diproses" <?= $laporan['status'] === 'diproses' ? 'selected' : '' ?>>Diproses</option>
                                <option value="selesai" <?= $laporan['status'] === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tugaskan Petugas:</label>
                            <select name="petugas_id" class="form-select py-2">
                                <option value="">-- Belum Ditugaskan / Cari Petugas --</option>
                                <?php foreach ($petugasList as $petugas): ?>
                                    <option value="<?= $petugas['id'] ?>" <?= ($laporan['petugas_id'] == $petugas['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($petugas['name']) ?> (<?= htmlspecialchars($petugas['wilayah_tugas'] ?? 'Umum') ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-teal text-white py-2 fw-bold" style="background-color: #0d9488;">
                                Simpan Perubahan Status
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>