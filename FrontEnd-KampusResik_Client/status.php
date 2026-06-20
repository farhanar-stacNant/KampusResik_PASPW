<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/includes/api_client.php';
$res = callApi('GET', 'lacak-laporan');
$daftarLaporan = (isset($res['status']) && $res['status'] === 'success') ? ($res['data'] ?? []) : [];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Laporan - KampusResik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Leaflet Map CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body { background-color: #f8f9fa; }
        .report-card { border-radius: 1rem; border: 1px solid #dee2e6; transition: 0.3s; }
        #map { height: 450px; border-radius: 1rem; margin-bottom: 2rem; border: 1px solid #dee2e6; }
    </style>
</head>
<body>

    <?php include 'includes/navbarPublic.php'; ?>
    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <h2 class="fw-bold mb-4">Status & Lacak Lokasi Laporan</h2>

                <!-- Interactive OpenStreetMap -->
                <div id="map" class="shadow-sm"></div>

                <div class="d-grid gap-3">
                    <?php if (empty($daftarLaporan)): ?>
                        <div class="card p-5 text-center border-0 shadow-sm rounded-4">
                            <p class="text-secondary mb-0">Belum ada laporan atau koneksi ke server sedang bermasalah.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($daftarLaporan as $lap): ?>
                            <div class="card report-card p-4 shadow-sm">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="fw-bold mb-1">#Laporan <?= htmlspecialchars($lap['id'] ?? 'N/A') ?></h5>
                                        <p class="text-muted mb-1 small"><?= htmlspecialchars($lap['deskripsi_singkat'] ?? 'Tanpa deskripsi') ?></p>
                                        <small class="text-secondary">Lokasi: <?= htmlspecialchars($lap['latitude'] ?? '-') ?>, <?= htmlspecialchars($lap['longitude'] ?? '-') ?></small>
                                    </div>
                                    
                                    <?php 
                                        $status = strtolower($lap['status'] ?? 'menunggu');
                                        $badgeClass = 'bg-secondary';
                                        if ($status == 'selesai') $badgeClass = 'bg-success';
                                        elseif ($status == 'diproses') $badgeClass = 'bg-primary';
                                        elseif ($status == 'terhenti') $badgeClass = 'bg-danger';
                                    ?>
                                    <span class="badge <?= $badgeClass ?> rounded-pill px-3 py-2 text-uppercase">
                                        <?= htmlspecialchars($status) ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Leaflet Map JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Default center of map (Surabaya/Indonesia context)
            var map = L.map('map').setView([-7.250445, 112.768845], 14);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            var bounds = [];

            <?php foreach ($daftarLaporan as $lap): 
                if (!empty($lap['latitude']) && !empty($lap['longitude'])):
            ?>
                var markerPos = [<?= floatval($lap['latitude']) ?>, <?= floatval($lap['longitude']) ?>];
                bounds.push(markerPos);
                
                L.marker(markerPos)
                    .addTo(map)
                    .bindPopup("<b>Laporan #<?= $lap['id'] ?></b><br>Kategori: <?= htmlspecialchars($lap['kategori']['nama_kategori'] ?? 'Umum') ?><br>Deskripsi: <?= htmlspecialchars($lap['deskripsi_singkat']) ?><br>Status: <b><?= ucfirst($lap['status']) ?></b>");
            <?php 
                endif;
            endforeach; ?>

            if (bounds.length > 0) {
                map.fitBounds(bounds);
            }
        });
    </script>
</body>
</html>