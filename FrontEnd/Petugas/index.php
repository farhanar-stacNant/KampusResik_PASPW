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

$pageTitle = 'Beranda - Petugas';
$token = get_auth_token();
$user = get_user_data();

function mapStatusClass($status) {
    return match($status) {
        'dikirim' => 'status-belum',
        'diterima' => 'status-proses',
        'diproses' => 'status-proses',
        'selesai' => 'status-selesai',
        'ditolak' => 'status-tertunda',
        default => 'status-belum',
    };
}
function mapStatusLabel($status) {
    return match($status) {
        'dikirim' => 'Dikirim',
        'diterima' => 'Diterima',
        'diproses' => 'Diproses',
        'selesai' => 'Selesai',
        'ditolak' => 'Ditolak',
        default => ucfirst($status),
    };
}
function mapJenisLabel($jenis) {
    return match($jenis) {
        'ORG' => 'Sampah Organik',
        'ANR' => 'Sampah Anorganik',
        'B3' => 'Sampah B3',
        default => $jenis,
    };
}

// Fetch data dari API
$reports = [];
$stats = [
    ['label' => 'Total Laporan', 'value' => 0, 'icon' => 'bi-clipboard-check', 'color' => '#1a237e', 'bg' => '#e8eaf6'],
    ['label' => 'Diproses', 'value' => 0, 'icon' => 'bi-arrow-repeat', 'color' => '#1565c0', 'bg' => '#e3f2fd'],
    ['label' => 'Selesai', 'value' => 0, 'icon' => 'bi-check-circle', 'color' => '#2e7d32', 'bg' => '#e8f5e9'],
    ['label' => 'Dikirim', 'value' => 0, 'icon' => 'bi-send', 'color' => '#e65100', 'bg' => '#fff3e0'],
];

try {
    $result = fetch_api('/public/reports?limit=50', 'GET', null, $token);
    if ($result['code'] === 200 && isset($result['body']['data'])) {
        $reports = $result['body']['data'];
        $total = count($reports);
        $diproses = 0; $selesai = 0; $dikirim = 0;
        foreach ($reports as $r) {
            $s = $r['status'] ?? '';
            if ($s === 'diproses' || $s === 'diterima') $diproses++;
            elseif ($s === 'selesai') $selesai++;
            elseif ($s === 'dikirim') $dikirim++;
        }
        $stats[0]['value'] = $total;
        $stats[1]['value'] = $diproses;
        $stats[2]['value'] = $selesai;
        $stats[3]['value'] = $dikirim;
    }
} catch (Exception $e) {
    // Fallback kosong
}

// Lokasi tugas: laporan yang belum selesai (max 5)
$lokasi_tugas = array_filter($reports, function($r) {
    return ($r['status'] ?? '') !== 'selesai';
});
$lokasi_tugas = array_slice($lokasi_tugas, 0, 5);

include '../includes/head-petugas.php';
include '../includes/navbar-petugas.php';
?>

<main class="main-content">
    <div class="container-fluid">
        <div class="row mb-4 animate-fade-in">
            <div class="col-12">
                <h4 class="fw-bold text-dark mb-1">Selamat Datang, <?= htmlspecialchars($user['nama'] ?? $user['name'] ?? 'Petugas') ?>!</h4>
                <p class="text-muted mb-0">Berikut ringkasan tugas Anda hari ini</p>
            </div>
        </div>
        
        <div class="row g-3 mb-4">
            <?php foreach ($stats as $stat): ?>
            <div class="col-6 col-lg-3 animate-fade-in">
                <div class="card card-stat h-100">
                    <div class="card-body">
                        <div class="stat-icon" style="background: <?= $stat['bg'] ?>; color: <?= $stat['color'] ?>;">
                            <i class="bi <?= $stat['icon'] ?>"></i>
                        </div>
                        <div class="stat-value"><?= $stat['value'] ?></div>
                        <div class="stat-label"><?= $stat['label'] ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="row animate-fade-in">
            <div class="col-12">
                <div class="card card-stat">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0"><i class="bi bi-geo-alt-fill text-primary me-2"></i>Lokasi Tugas Hari Ini</h5>
                            <a href="<?= base_petugas() ?>/detail-laporan.php" class="btn btn-sm btn-petugas btn-petugas-primary">Lihat Semua <i class="bi bi-arrow-right ms-1"></i></a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-petugas mb-0">
                                <thead><tr><th>Lokasi</th><th>Kategori</th><th>Status</th><th>Aksi</th></tr></thead>
                                <tbody>
                                    <?php if (empty($lokasi_tugas)): ?>
                                    <tr><td colspan="4" class="text-center text-muted py-4">Tidak ada tugas aktif</td></tr>
                                    <?php else: ?>
                                    <?php foreach ($lokasi_tugas as $lokasi): 
                                        $status_class = mapStatusClass($lokasi['status'] ?? 'dikirim');
                                        $status_label = mapStatusLabel($lokasi['status'] ?? 'dikirim');
                                    ?>
                                    <tr>
                                        <td><i class="bi bi-geo-alt text-muted me-2"></i><?= htmlspecialchars($lokasi['lokasi'] ?? '-') ?></td>
                                        <td><span class="badge bg-light text-dark border"><?= mapJenisLabel($lokasi['kategori'] ?? '-') ?></span></td>
                                        <td><span class="status-badge <?= $status_class ?>"><?= $status_label ?></span></td>
                                        <td><a href="<?= base_petugas() ?>/detail-laporan.php" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square"></i></a></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer-petugas.php'; ?>