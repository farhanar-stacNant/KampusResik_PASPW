<?php
$pageTitle = 'Detail Laporan';
require_once __DIR__ . '/../includes/api-config.php';
require_once __DIR__ . '/../includes/head-public.php';
require_once __DIR__ . '/../includes/navbar-public.php';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$id) {
    header('Location: laporan.php');
    exit;
}

// Fetch detail dari API
$report = [];
$timeline = [];
$errorMsg = '';

try {
    $apiUrl = API_BASE_URL . '/public/reports/' . $id;
    $response = api_get_contents($apiUrl);
    
    if ($response !== false) {
        $data = json_decode($response, true);
        $report = $data['data'] ?? [];
        $timeline = $report['timeline'] ?? [];
    } else {
        $errorMsg = 'Gagal memuat detail laporan.';
    }
} catch (Exception $e) {
    $errorMsg = 'Terjadi kesalahan saat memuat data.';
}

// Status config
$statusConfig = [
    'dikirim' => ['label' => 'Dikirim', 'badge' => 'bg-secondary', 'icon' => 'bi-send'],
    'diterima' => ['label' => 'Diterima', 'badge' => 'bg-info', 'icon' => 'bi-check-circle'],
    'diproses' => ['label' => 'Diproses', 'badge' => 'bg-warning text-dark', 'icon' => 'bi-arrow-repeat'],
    'selesai' => ['label' => 'Selesai', 'badge' => 'bg-success', 'icon' => 'bi-check-lg'],
    'ditolak' => ['label' => 'Ditolak', 'badge' => 'bg-danger', 'icon' => 'bi-x-circle']
];

$kategoriLabel = [
    'ORG' => 'Organik',
    'ANR' => 'Anorganik',
    'B3' => 'B3'
];

$config = $statusConfig[$report['status'] ?? 'dikirim'] ?? $statusConfig['dikirim'];
?>

<section class="page-header py-4">
    <div class="container-fluid px-3 px-md-4">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="fw-bold mb-1">Detail Laporan</h2>
                <p class="text-muted mb-0">Informasi lengkap dan riwayat status laporan.</p>
            </div>
            <div class="col-auto">
                <a href="laporan.php" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</section>

<section class="pb-5">
    <div class="container-fluid px-3 px-md-4">
        <?php if ($errorMsg): ?>
            <div class="alert alert-warning"><?= $errorMsg ?></div>
        <?php elseif (empty($report)): ?>
            <div class="alert alert-warning">Laporan tidak ditemukan.</div>
        <?php else: ?>
        
        <div class="row g-4">
            <!-- Kolom Kiri: Info Laporan -->
            <div class="col-lg-8">
                <div class="card-clean p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="badge <?= $config['badge'] ?> mb-2">
                                <i class="bi <?= $config['icon'] ?>"></i> <?= $config['label'] ?>
                            </span>
                            <h4 class="fw-bold mb-1"><?= htmlspecialchars($report['judul'] ?? 'Tanpa Judul') ?></h4>
                            <small class="text-muted">Kode: <?= htmlspecialchars($report['kode'] ?? '-') ?></small>
                        </div>
                        <?php if (($report['prioritas'] ?? 'normal') === 'tinggi'): ?>
                        <span class="badge bg-danger">Prioritas Tinggi</span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($report['foto'])): ?>
                    <div class="mb-4">
                        <img src="<?= htmlspecialchars($report['foto']) ?>" alt="Foto Laporan" class="img-fluid rounded" style="max-height: 400px; width: 100%; object-fit: cover;">
                    </div>
                    <?php endif; ?>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase">Lokasi</label>
                            <p class="mb-0"><i class="bi bi-geo-alt text-success"></i> <?= htmlspecialchars($report['lokasi'] ?? '-') ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase">Jenis Sampah</label>
                            <p class="mb-0">
                                <span class="badge bg-light text-dark">
                                    <?= $kategoriLabel[$report['jenis_sampah'] ?? ''] ?? 'Lainnya' ?>
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase">Tanggal Lapor</label>
                            <p class="mb-0"><i class="bi bi-calendar text-success"></i> <?= htmlspecialchars($report['created_at'] ?? '-') ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase">No. WhatsApp</label>
                            <p class="mb-0"><i class="bi bi-whatsapp text-success"></i> <?= htmlspecialchars($report['no_wa'] ?? '-') ?></p>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="text-muted small text-uppercase">Deskripsi</label>
                        <p class="mb-0"><?= nl2br(htmlspecialchars($report['deskripsi'] ?? '-')) ?></p>
                    </div>
                    
                    <?php if (!empty($report['catatan_petugas'])): ?>
                    <div class="alert alert-info">
                        <label class="fw-bold small text-uppercase mb-1"><i class="bi bi-info-circle"></i> Catatan Petugas</label>
                        <p class="mb-0"><?= nl2br(htmlspecialchars($report['catatan_petugas'])) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Kolom Kanan: Timeline -->
            <div class="col-lg-4">
                <div class="card-clean p-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-clock-history"></i> Timeline</h5>
                    
                    <?php if (!empty($timeline)): ?>
                        <div class="timeline">
                            <?php foreach ($timeline as $index => $item): 
                                $isLast = $index === count($timeline) - 1;
                                $iconClass = [
                                    'Dikirim' => 'bi-send',
                                    'Diterima' => 'bi-check-circle',
                                    'Diproses' => 'bi-arrow-repeat',
                                    'Selesai' => 'bi-check-lg',
                                    'Ditolak' => 'bi-x-circle'
                                ][$item['status']] ?? 'bi-circle';
                            ?>
                            <div class="timeline-item <?= $isLast ? 'active' : '' ?>">
                                <div class="timeline-icon">
                                    <i class="bi <?= $iconClass ?>"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="mb-1"><?= htmlspecialchars($item['status']) ?></h6>
                                    <p class="small text-muted mb-1"><?= htmlspecialchars($item['deskripsi'] ?? '') ?></p>
                                    <small class="text-muted"><?= htmlspecialchars($item['waktu'] ?? '') ?></small>
                                    <?php if (!empty($item['petugas'])): ?>
                                    <p class="small text-success mb-0"><i class="bi bi-person"></i> <?= htmlspecialchars($item['petugas']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-muted text-center py-4">
                            <i class="bi bi-clock-history fs-2"></i>
                            <p class="small mb-0 mt-2">Belum ada timeline</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- CTA -->
                <div class="card-clean p-4 bg-success text-white mt-4">
                    <h6 class="fw-bold mb-2">Pantau Status Laporan</h6>
                    <p class="small mb-3 opacity-75">Simpan kode laporan untuk memantau status kapan saja.</p>
                    <div class="bg-white bg-opacity-20 rounded p-2 text-center">
                        <small class="text-white-75">Kode Laporan</small>
                        <div class="fw-bold fs-5"><?= htmlspecialchars($report['kode'] ?? '-') ?></div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php endif; ?>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer-public.php'; ?>