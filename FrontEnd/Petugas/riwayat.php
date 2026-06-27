<?php
require_once '../includes/api-config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if (!function_exists('fetch_api')) {
    function fetch_api($endpoint, $method = 'GET', $data = null, $token = null) {
        $url = rtrim(API_BASE_URL, '/') . '/' . ltrim($endpoint, '/');
        $ch = curl_init($url);
        $headers = ['Accept: application/json'];
        if ($token) $headers[] = 'Authorization: Bearer ' . $token;
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            $headers[] = 'Content-Type: application/json';
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ['code' => $httpCode, 'body' => json_decode($response, true)];
    }
}

require_petugas();

$pageTitle = 'Riwayat - Petugas';
$token = get_auth_token();

function mapJenisLabel($jenis) {
    return match($jenis) {
        'ORG' => 'Sampah Organik',
        'ANR' => 'Sampah Anorganik',
        'B3' => 'Sampah B3',
        default => $jenis,
    };
}

// Fetch riwayat (laporan selesai)
$riwayat_list = [];
try {
    $result = fetch_api('/public/reports?limit=50&status=selesai', 'GET', null, $token);
    if ($result['code'] === 200 && isset($result['body']['data'])) {
        $riwayat_list = $result['body']['data'];
    }
} catch (Exception $e) {
    $riwayat_list = [];
}

$total = count($riwayat_list);
$lokasi_unik = count(array_unique(array_column($riwayat_list, 'lokasi')));
$hari_unik = count(array_unique(array_map(function($r) { return $r['created_at'] ?? ''; }, $riwayat_list)));

include '../includes/head-petugas.php';
include '../includes/navbar-petugas.php';
?>

<main class="main-content">
    <div class="container-fluid">
        <div class="row mb-4 animate-fade-in">
            <div class="col-12">
                <h4 class="fw-bold text-dark mb-1"><i class="bi bi-clock-history me-2"></i>Riwayat Pembersihan</h4>
                <p class="text-muted mb-0">Histori laporan yang telah diselesaikan</p>
            </div>
        </div>

        <div class="row mb-3 animate-fade-in">
            <div class="col-12">
                <div class="card card-stat">
                    <div class="card-body py-3">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-8">
                                <label class="text-muted small mb-1">Cari Lokasi</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                                    <input type="text" class="form-control border-start-0" placeholder="Nama lokasi..." id="searchLokasi">
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button class="btn btn-petugas btn-petugas-primary w-100" onclick="filterRiwayat()"><i class="bi bi-funnel me-1"></i>Filter</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row animate-fade-in">
            <div class="col-12">
                <div class="card card-stat">
                    <div class="card-body">
                        <h5 class="fw-bold mb-4">Histori Tugas</h5>
                        <div class="timeline" id="timelineContainer">
                            <?php if (empty($riwayat_list)): ?>
                            <div class="text-center text-muted py-4">Belum ada riwayat pembersihan</div>
                            <?php else: ?>
                            <?php foreach ($riwayat_list as $index => $item): 
                                $border_color = ['#1a237e', '#1565c0', '#2e7d32', '#6a1b9a', '#e65100'][$index % 5];
                            ?>
                            <div class="timeline-item mb-4 pb-4" data-lokasi="<?= strtolower(htmlspecialchars($item['lokasi'] ?? '')) ?>" style="border-left: 3px solid <?= $border_color ?>; padding-left: 1.5rem; position: relative;">
                                <div style="position: absolute; left: -8px; top: 0; width: 14px; height: 14px; border-radius: 50%; background: <?= $border_color ?>; border: 2px solid #fff; box-shadow: 0 0 0 3px <?= $border_color ?>20;"></div>
                                <div class="d-flex justify-content-between align-items-start flex-wrap">
                                    <div>
                                        <h6 class="fw-bold mb-1"><i class="bi bi-geo-alt-fill me-1" style="color: <?= $border_color ?>"></i><?= htmlspecialchars($item['lokasi'] ?? '-') ?></h6>
                                        <p class="text-muted small mb-1"><i class="bi bi-tag me-1"></i><?= mapJenisLabel($item['kategori'] ?? '-') ?></p>
                                        <p class="mb-0 text-dark"><?= htmlspecialchars($item['deskripsi'] ?? '-') ?></p>
                                    </div>
                                    <div class="text-end mt-2 mt-md-0">
                                        <span class="status-badge status-selesai mb-1 d-inline-block">Selesai</span>
                                        <p class="text-muted small mb-0"><i class="bi bi-calendar me-1"></i><?= htmlspecialchars($item['created_at'] ?? '-') ?></p>
                                        <?php if (!empty($item['selesai_at'])): ?>
                                        <p class="text-muted small mb-0"><i class="bi bi-check-circle me-1"></i><?= htmlspecialchars($item['selesai_at']) ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4 animate-fade-in">
            <div class="col-md-4 mb-3">
                <div class="card card-stat text-center">
                    <div class="card-body">
                        <div class="stat-value text-success"><?= $total ?></div>
                        <div class="stat-label">Total Pembersihan</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card card-stat text-center">
                    <div class="card-body">
                        <div class="stat-value text-primary"><?= $lokasi_unik ?></div>
                        <div class="stat-label">Lokasi Berbeda</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card card-stat text-center">
                    <div class="card-body">
                        <div class="stat-value text-info"><?= $hari_unik ?></div>
                        <div class="stat-label">Hari Aktif</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
function filterRiwayat() {
    const search = document.getElementById('searchLokasi').value.toLowerCase();
    const items = document.querySelectorAll('.timeline-item');
    items.forEach(item => {
        const lokasi = item.dataset.lokasi;
        item.style.display = (!search || lokasi.includes(search)) ? '' : 'none';
    });
}
</script>

<?php include '../includes/footer-petugas.php'; ?>