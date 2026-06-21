<?php
session_set_cookie_params(['path' => '/']);
session_start();
if (!isset($_SESSION['token'])) {
    header("Location: ../login.php");
    exit;
}

if (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin') {
    header("Location: ../" . $_SESSION['role'] . "/dashboard.php");
    exit;
}

$adminName = $_SESSION['user']['name'] ?? 'Administrator';
$apiUrl = "http://127.0.0.1:8000/api/lacak-laporan";
$daftarLaporan = [];

require_once __DIR__ . '/../includes/api_client.php';

$res = callApi('GET', 'admin/laporan');
$daftarLaporan = (isset($res['status']) && $res['status'] === 'success') ? ($res['data'] ?? []) : [];

// Calculate dynamic stats
$totalLaporan = count($daftarLaporan);
$perluDiproses = 0;
$kategoriCounts = [];
$statusCounts = ['menunggu' => 0, 'diproses' => 0, 'selesai' => 0];

foreach ($daftarLaporan as $lap) {
    $status = strtolower($lap['status'] ?? '');
    if (in_array($status, ['menunggu', 'diproses'])) {
        $perluDiproses++;
    }
    if (isset($statusCounts[$status])) {
        $statusCounts[$status]++;
    }
    
    $catName = $lap['kategori']['nama_kategori'] ?? 'Umum';
    if (!isset($kategoriCounts[$catName])) {
        $kategoriCounts[$catName] = 0;
    }
    $kategoriCounts[$catName]++;
}

// Sorted categories by popularity
arsort($kategoriCounts);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - KampusResik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

    <div class="wrapper">
        <?php include __DIR__ . '/../includes/navbarPrivate.php'; ?>
        <?php include __DIR__ . '/../includes/adminStyle.php'; ?>
        <!-- Page Content -->
        <div id="content">
            <!-- Topbar -->
            <div class="topbar">
                <div class="search-box d-none d-md-block">
                    <i class="bi bi-search"></i>
                    <input type="text" placeholder="Cari laporan atau kategori...">
                </div>
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-link text-secondary position-relative p-1">
                        <i class="bi bi-bell fs-5"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.55rem;" id="notification-badge">
                            <?= $perluDiproses ?>
                        </span>
                    </button>
                    <button class="btn btn-link text-secondary p-1">
                        <i class="bi bi-question-circle fs-5"></i>
                    </button>
                    
                    <div class="vr mx-2 text-muted" style="opacity: 0.2;"></div>

                    <div class="d-flex align-items-center gap-2">
                        <div class="text-end d-none d-sm-block">
                            <h6 class="fw-semibold mb-0 small" style="color: var(--deepsea-dark);"><?= htmlspecialchars($adminName) ?></h6>
                            <small class="text-muted" style="font-size: 0.7rem;">Administrator</small>
                        </div>
                        <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 40px; height: 40px; background-color: var(--deepsea-light) !important;">
                            <?= strtoupper(substr($adminName, 0, 2)) ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Content -->
            <main class="container-fluid p-4">
                <div class="mb-4">
                    <h2 class="fw-bold mb-1" style="color: var(--deepsea-dark);">Ringkasan Statistik</h2>
                    <p class="text-muted small mb-0">Pantau kebersihan dan pengelolaan sampah kampus secara real-time.</p>
                </div>

                <!-- Stats Cards -->
                <div class="row g-4 mb-4">
                    <!-- Card 1: Total Laporan -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card stat-card p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="bg-success-subtle p-2.5 rounded-3 text-success d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background-color: #d1fae5;">
                                    <i class="bi bi-file-earmark-text fs-4" style="color: #059669;"></i>
                                </div>
                                <span class="card-badge success">+12% &uarr;</span>
                            </div>
                            <small class="text-muted fw-semibold text-uppercase tracking-wider" style="font-size: 0.75rem;">Total Laporan</small>
                            <h2 class="fw-bold mb-0 mt-1" style="color: var(--deepsea-dark);" id="total-laporan-val"><?= number_format($totalLaporan, 0, ',', '.') ?></h2>
                        </div>
                    </div>

                    <!-- Card 2: Laporan Perlu Diproses -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card stat-card p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="bg-danger-subtle p-2.5 rounded-3 text-danger d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background-color: #fee2e2;">
                                    <i class="bi bi-exclamation-triangle fs-4" style="color: #dc2626;"></i>
                                </div>
                                <?php if ($perluDiproses > 0): ?>
                                    <span class="card-badge danger">Urgent</span>
                                <?php endif; ?>
                            </div>
                            <small class="text-muted fw-semibold text-uppercase tracking-wider" style="font-size: 0.75rem;">Laporan Perlu Diproses</small>
                            <h2 class="fw-bold mb-0 mt-1" style="color: var(--deepsea-dark);" id="perlu-diproses-val"><?= $perluDiproses ?></h2>
                        </div>
                    </div>

                    <!-- Card 3: Kategori Terpopuler -->
                    <div class="col-md-12 col-lg-4">
                        <div class="card stat-card p-4" id="kategori-terpopuler-card">
                            <small class="text-muted fw-semibold text-uppercase tracking-wider mb-3 d-block" style="font-size: 0.75rem;">Kategori Sampah Terpopuler</small>
                            
                            <?php 
                            $topCategories = array_slice($kategoriCounts, 0, 2, true);
                            $maxCount = count($daftarLaporan) > 0 ? count($daftarLaporan) : 1284;
                            
                            // fallback if zero
                            if (empty($topCategories)) {
                                $topCategories = ['Organik' => 950, 'Plastik' => 334];
                                $maxCount = 1284;
                            }
                            
                            foreach ($topCategories as $catName => $count):
                                $percent = round(($count / $maxCount) * 100);
                            ?>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1 small fw-semibold">
                                        <span><?= htmlspecialchars($catName) ?></span>
                                        <span class="text-teal"><?= $percent ?>%</span>
                                    </div>
                                    <div class="progress" style="height: 6px; border-radius: 3px;">
                                        <div class="progress-bar" role="progressbar" style="width: <?= $percent ?>%; background-color: var(--teal-primary); border-radius: 3px;" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Chart and Extra Section -->
                <div class="row g-4">
                    <!-- Left: Weekly Trend -->
                    <div class="col-lg-8">
                        <div class="chart-container">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold mb-0" style="color: var(--deepsea-dark);">Tren Laporan Mingguan</h6>
                                <div class="d-flex gap-3 small text-muted">
                                    <span><i class="bi bi-circle-fill text-teal me-1"></i> Minggu Ini</span>
                                    <span><i class="bi bi-circle-fill text-secondary opacity-50 me-1"></i> Periode Lalu</span>
                                </div>
                            </div>
                            
                            <div class="bar-chart">
                                <?php
                                // Mock weekly statistics matching visual graph
                                $weeklyData = [
                                    'Sen' => ['height' => 45, 'active' => false],
                                    'Sel' => ['height' => 70, 'active' => false],
                                    'Rab' => ['height' => 38, 'active' => false],
                                    'Kam' => ['height' => 90, 'active' => true],
                                    'Jum' => ['height' => 60, 'active' => false],
                                    'Sab' => ['height' => 50, 'active' => false],
                                    'Min' => ['height' => 30, 'active' => false]
                                ];

                                foreach ($weeklyData as $day => $data):
                                ?>
                                    <div class="chart-bar-wrapper">
                                        <div class="chart-bar <?= $data['active'] ? 'active' : '' ?>" style="height: <?= $data['height'] ?>%;"></div>
                                        <span class="bar-label"><?= $day ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>


                </div>
            </main>

            <!-- Footer -->
            <footer class="mt-5 p-4 border-top bg-white text-muted small d-flex justify-content-between align-items-center">
                <span>&copy; 2026 Kampus Resik Admin. System Version 2.4.0</span>
                <div class="d-flex gap-3">
                    <a href="#" class="text-muted text-decoration-none">Privacy Policy</a>
                    <a href="#" class="text-muted text-decoration-none">Terms of Service</a>
                </div>
            </footer>
        </div>
    </div>

    <!-- Bootstrap and jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            
            // Token for API call
            const API_TOKEN = "<?= $_SESSION['token'] ?>";

            async function updateStatsRealTime() {
                try {
                    const response = await fetch("http://127.0.0.1:8000/api/admin/laporan", {
                        headers: {
                            "Authorization": "Bearer " + API_TOKEN,
                            "Accept": "application/json"
                        }
                    });
                    if (!response.ok) return;
                    const res = await response.json();
                    if (res.status === 'success') {
                        const laporan = res.data || [];
                        const total = laporan.length;
                        
                        let waitingCount = 0;
                        let diprosesCount = 0;
                        let selesaiCount = 0;
                        let categoryCounts = {};

                        laporan.forEach(item => {
                            const status = (item.status || '').toLowerCase();
                            if (status === 'menunggu') waitingCount++;
                            else if (status === 'diproses') diprosesCount++;
                            else if (status === 'selesai') selesaiCount++;

                            const catName = (item.kategori && item.kategori.nama_kategori) || 'Umum';
                            categoryCounts[catName] = (categoryCounts[catName] || 0) + 1;
                        });

                        const needsProcessing = waitingCount + diprosesCount;

                        // 1. Update Total Laporan
                        document.getElementById('total-laporan-val').innerText = total.toLocaleString('id-ID');

                        // 2. Update Perlu Diproses
                        document.getElementById('perlu-diproses-val').innerText = needsProcessing;
                        document.getElementById('notification-badge').innerText = needsProcessing;

                        // 3. Update Kategori Terpopuler progress bars
                        let sortedCategories = Object.entries(categoryCounts).sort((a, b) => b[1] - a[1]);
                        if (sortedCategories.length === 0) {
                            sortedCategories = [['Organik', 0], ['Plastik', 0]];
                        }
                        const topCategories = sortedCategories.slice(0, 2);
                        const maxCount = total > 0 ? total : 1;

                        let kategoriCard = document.getElementById('kategori-terpopuler-card');
                        if (kategoriCard) {
                            let htmlContent = '<small class="text-muted fw-semibold text-uppercase tracking-wider mb-3 d-block" style="font-size: 0.75rem;">Kategori Sampah Terpopuler</small>';
                            topCategories.forEach(cat => {
                                const catName = cat[0];
                                const catCount = cat[1];
                                const percent = Math.round((catCount / maxCount) * 100);
                                htmlContent += `
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-1 small fw-semibold">
                                            <span>${catName}</span>
                                            <span class="text-teal">${percent}%</span>
                                        </div>
                                        <div class="progress" style="height: 6px; border-radius: 3px;">
                                            <div class="progress-bar" role="progressbar" style="width: ${percent}%; background-color: var(--teal-primary); border-radius: 3px;" aria-valuenow="${percent}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                `;
                            });
                            kategoriCard.innerHTML = htmlContent;
                        }
                    }
                } catch(e) {
                    console.error("Real-time sync failed:", e);
                }
            }

            // Sync stats in real-time every 5 seconds
            setInterval(updateStatsRealTime, 5000);
        });
    </script>
</body>
</html>