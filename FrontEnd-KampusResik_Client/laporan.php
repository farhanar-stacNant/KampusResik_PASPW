<?php
require_once __DIR__ . '/includes/api_client.php';
$res = callApi('GET', 'lacak-laporan');
$daftarLaporan = (isset($res['status']) && $res['status'] === 'success') ? ($res['data'] ?? []) : [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Publik - KampusResik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8fafc; color: #334155; }
        .card { border-radius: 1rem; border: 1px solid #e2e8f0; transition: transform 0.2s; }
        .badge { font-size: 0.7rem; }
    </style>
</head>
<body>
    <?php include 'includes/navbarPublic.php'; ?>
    <main class="container py-5">
        <div class="mb-5">
            <h1 class="fw-bold text-dark">Laporan Kebersihan Publik</h1>
            <p class="text-secondary">Daftar transparansi penanganan aduan sampah liar di lingkungan kampus.</p>
        </div>

        <?php if (empty($daftarLaporan)): ?>
            <div class="card p-5 text-center shadow-sm border-0">
                <p class="text-secondary">Belum ada laporan pengaduan sampah yang masuk saat ini.</p>
            </div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach ($daftarLaporan as $lap): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="position-relative" style="height: 200px;">
                            <?php if ($lap['status'] === 'selesai' && !empty($lap['foto_sesudah'])): ?>
                                <img src="<?= htmlspecialchars($lap['url_foto_sesudah'] ?? ('http://127.0.0.1:8000/storage/sampah/' . $lap['foto_sesudah'])) ?>" 
                                     class="card-img-top w-100 h-100 object-fit-cover" alt="Foto Sampah Selesai">
                            <?php else: ?>
                                <img src="<?= htmlspecialchars($lap['url_foto_sebelum'] ?? ('http://127.0.0.1:8000/storage/sampah/' . $lap['foto_sebelum'])) ?>" 
                                     class="card-img-top w-100 h-100 object-fit-cover" alt="Foto Sampah Sebelum">
                            <?php endif; ?>
                            
                            <div class="position-absolute top-0 end-0 m-3">
                                <?php 
                                    $statusClass = 'bg-warning text-dark';
                                    if ($lap['status'] === 'diproses') $statusClass = 'bg-primary text-white';
                                    if ($lap['status'] === 'selesai') $statusClass = 'bg-success text-white';
                                ?>
                                <span class="badge rounded-pill px-3 py-2 <?= $statusClass ?>">
                                    <?= ucfirst($lap['status']) ?>
                                </span>
                            </div>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-light text-secondary me-2">ID: #<?= $lap['id'] ?></span>
                                <small class="text-muted"><?= date('d M Y, H:i', strtotime($lap['created_at'])) ?></small>
                            </div>

                            <p class="text-secondary small text-uppercase fw-bold mb-1">
                                Kategori: <span class="text-teal"><?= htmlspecialchars($lap['kategori']['nama_kategori'] ?? 'Umum') ?></span>
                            </p>
                            
                            <p class="card-text text-dark flex-grow-1 mb-4">"<?= htmlspecialchars($lap['deskripsi_singkat']) ?>"</p>

                            <div class="pt-3 border-top d-flex justify-content-between align-items-center">
                                <small class="text-muted">Oleh: <strong><?= htmlspecialchars($lap['nama_pelapor']) ?></strong></small>
                                <a href="status.php?id=<?= $lap['id'] ?>" class="text-decoration-none fw-bold" style="color: #0d9488;">
                                    Detail Lacak &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>