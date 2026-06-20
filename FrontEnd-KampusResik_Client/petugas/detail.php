<?php
session_start();
if (!isset($_SESSION['token'])) { header('Location: ../login.php'); exit; }
if (isset($_SESSION['role']) && $_SESSION['role'] !== 'petugas') {
    header("Location: ../" . $_SESSION['role'] . "/dashboard.php");
    exit;
}
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
$id = $_GET['id'] ?? null;

require_once __DIR__ . '/../includes/api_client.php';
$res = callApi('GET', 'lacak-laporan?laporan_id=' . $id);
$laporan = (isset($res['status']) && $res['status'] === 'success') ? ($res['data'][0] ?? null) : null;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail & Proses Laporan - KampusResik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .detail-card { border-radius: 1rem; border: none; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/navbarPrivate.php'; ?>
    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                
                <a href="riwayat.php" class="text-decoration-none text-secondary mb-3 d-inline-block">&larr; Kembali ke Riwayat</a>
                
                <div class="card detail-card p-4 p-md-5">
                    <h2 class="h3 fw-bold mb-4">Proses Laporan #<?= htmlspecialchars((string)($id ?? '')) ?></h2>

                    <div class="mb-4 bg-light p-3 rounded-3 border">
                        <div class="mb-2"><strong>Kategori Sampah:</strong> <span class="badge bg-secondary"><?= htmlspecialchars((string)($laporan['kategori']['nama_kategori'] ?? 'Umum')) ?></span></div>
                        <div class="mb-2"><strong>Deskripsi:</strong> "<?= htmlspecialchars((string)($laporan['deskripsi_singkat'] ?? 'Tidak ada deskripsi')) ?>"</div>
                        <div class="mb-2"><strong>Pelapor:</strong> <?= htmlspecialchars((string)($laporan['nama_pelapor'] ?? 'Anonim')) ?></div>
                        <div class="mb-2"><strong>Koordinat Lokasi:</strong> <?= htmlspecialchars((string)($laporan['latitude'] ?? '-')) ?>, <?= htmlspecialchars((string)($laporan['longitude'] ?? '-')) ?></div>
                        <?php if (!empty($laporan['foto_sebelum'])): ?>
                            <div class="mt-2">
                                <strong>Foto Sebelum Dibersihkan:</strong><br>
                                <img src="<?= htmlspecialchars((string)($laporan['url_foto_sebelum'] ?? ('http://127.0.0.1:8000/storage/sampah/' . $laporan['foto_sebelum']))) ?>" class="img-fluid rounded-3 mt-1 border shadow-sm" style="max-height: 250px;">
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($laporan['foto_sesudah'])): ?>
                            <div class="mt-2">
                                <strong>Foto Setelah Dibersihkan:</strong><br>
                                <img src="<?= htmlspecialchars((string)($laporan['url_foto_sesudah'] ?? ('http://127.0.0.1:8000/storage/sampah/' . $laporan['foto_sesudah']))) ?>" class="img-fluid rounded-3 mt-1 border shadow-sm" style="max-height: 250px;">
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <form action="proses_update.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status Laporan</label>
                            <select name="status" class="form-select py-2">
                                <option value="diproses" <?= ($laporan['status'] ?? '') === 'diproses' ? 'selected' : '' ?>>Diproses</option>
                                <option value="selesai" <?= ($laporan['status'] ?? '') === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Bukti Foto (Setelah Dibersihkan)</label>
                            <input type="file" name="foto_bukti" accept="image/*" class="form-control py-2">
                            <small class="text-muted">Format: JPG, PNG. Maks 2MB.</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Catatan Petugas</label>
                            <textarea name="catatan" rows="3" class="form-control" placeholder="Berikan keterangan singkat..."></textarea>
                        </div>

                        <div class="d-flex gap-3 pt-3 border-top">
                            <button type="submit" name="action" value="update" class="btn btn-teal flex-grow-1 py-2 fw-bold text-white" style="background-color: #0d9488;">
                                Simpan Perubahan
                            </button>
                            <button type="submit" name="action" value="delete" onclick="return confirm('Yakin ingin menghapus laporan ini?')" class="btn btn-outline-danger px-4 fw-bold">
                                Hapus
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