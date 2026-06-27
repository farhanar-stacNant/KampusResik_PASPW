<?php
$pageTitle = 'Laporan';
require_once __DIR__ . '/../includes/api-config.php';
require_once __DIR__ . '/../includes/head-public.php';
require_once __DIR__ . '/../includes/navbar-public.php';

// Ambil parameter dari URL
$currentPage = $_GET['page'] ?? 1;
$search = $_GET['search'] ?? '';
$filterStatus = $_GET['status'] ?? '';
$filterKategori = $_GET['jenis_sampah'] ?? '';
$filterTanggalMulai = $_GET['tanggal_mulai'] ?? '';
$filterTanggalAkhir = $_GET['tanggal_akhir'] ?? '';
$filterUrutan = $_GET['sort'] ?? 'terbaru';

// Build query string untuk API
$queryParams = [
    'page' => $currentPage,
    'limit' => 6,
    'sort' => $filterUrutan
];

if ($search) $queryParams['search'] = $search;
if ($filterStatus) $queryParams['status'] = $filterStatus;
if ($filterKategori) $queryParams['jenis_sampah'] = $filterKategori;
if ($filterTanggalMulai) $queryParams['tanggal_mulai'] = $filterTanggalMulai;
if ($filterTanggalAkhir) $queryParams['tanggal_akhir'] = $filterTanggalAkhir;

$queryString = http_build_query($queryParams);

// Fetch dari API
$laporanList = [];
$totalPages = 1;
$errorMsg = '';

try {
    $apiUrl = API_BASE_URL . '/public/reports?' . $queryString;
    $response = api_get_contents($apiUrl);
    
    if ($response !== false) {
        $data = json_decode($response, true);
        $laporanList = $data['data'] ?? [];
        $totalPages = $data['last_page'] ?? 1;
        $currentPage = $data['current_page'] ?? 1;
    } else {
        $errorMsg = 'Gagal memuat data dari server.';
    }
} catch (Exception $e) {
    $errorMsg = 'Terjadi kesalahan saat memuat data.';
}

// Status config untuk badge
$statusConfig = [
    'dikirim' => ['label' => 'Dikirim', 'badge' => 'bg-secondary', 'icon' => 'bi-send'],
    'diterima' => ['label' => 'Diterima', 'badge' => 'bg-info', 'icon' => 'bi-check-circle'],
    'diproses' => ['label' => 'Diproses', 'badge' => 'bg-warning text-dark', 'icon' => 'bi-arrow-repeat'],
    'selesai' => ['label' => 'Selesai', 'badge' => 'bg-success', 'icon' => 'bi-check-lg'],
    'ditolak' => ['label' => 'Ditolak', 'badge' => 'bg-danger', 'icon' => 'bi-x-circle']
];

// Kategori label
$kategoriLabel = [
    'ORG' => 'Organik',
    'ANR' => 'Anorganik',
    'B3' => 'B3'
];
?>

<!-- Page Header -->
<section class="page-header py-4">
    <div class="container-fluid px-3 px-md-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="fw-bold mb-1">Laporan Publik</h2>
                <p class="text-muted mb-0">Transparansi pengelolaan kebersihan kampus. Lihat semua laporan yang masuk dan pantau perkembangannya secara langsung.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="pengaduan.php" class="btn-lapor-nav">
                    <i class="bi bi-plus-lg"></i> Buat Laporan Baru
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Filter & Search Section -->
<section class="pb-4">
    <div class="container-fluid px-3 px-md-4">
        <form method="GET" action="laporan.php" id="filterForm">
            <div class="row g-3 align-items-center">
                <!-- Search -->
                <div class="col-md-8">
                    <div class="search-laporan">
                        <i class="bi bi-search"></i>
                        <input type="search" class="search-laporan-input" 
                               placeholder="Cari berdasarkan lokasi atau jenis masalah..." 
                               name="search" value="<?= htmlspecialchars($search) ?>">
                    </div>
                </div>
                
                <!-- Filter -->
                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <select class="form-select filter-select" name="jenis_sampah" onchange="document.getElementById('filterForm').submit()">
                            <option value="">Semua Kategori</option>
                            <option value="ORG" <?= $filterKategori === 'ORG' ? 'selected' : '' ?>>Organik</option>
                            <option value="ANR" <?= $filterKategori === 'ANR' ? 'selected' : '' ?>>Anorganik</option>
                            <option value="B3" <?= $filterKategori === 'B3' ? 'selected' : '' ?>>B3</option>
                        </select>
                        
                        <select class="form-select filter-select" name="status" onchange="document.getElementById('filterForm').submit()">
                            <option value="">Status: Semua</option>
                            <option value="dikirim" <?= $filterStatus === 'dikirim' ? 'selected' : '' ?>>Dikirim</option>
                            <option value="diterima" <?= $filterStatus === 'diterima' ? 'selected' : '' ?>>Diterima</option>
                            <option value="diproses" <?= $filterStatus === 'diproses' ? 'selected' : '' ?>>Diproses</option>
                            <option value="selesai" <?= $filterStatus === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                            <option value="ditolak" <?= $filterStatus === 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                        </select>
                        
                        <!-- Burger Filter -->
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary filter-burger" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-sliders"></i> <span class="d-none d-sm-inline">Filter</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end p-3 shadow-lg" style="width: 280px; border-radius: 12px;">
                                <h6 class="fw-bold mb-3">Filter Lanjut</h6>
                                
                                <div class="mb-3">
                                    <label class="form-label small text-muted">Tanggal Mulai</label>
                                    <input type="date" class="form-control form-control-sm" name="tanggal_mulai" value="<?= $filterTanggalMulai ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label small text-muted">Tanggal Akhir</label>
                                    <input type="date" class="form-control form-control-sm" name="tanggal_akhir" value="<?= $filterTanggalAkhir ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label small text-muted">Urutkan</label>
                                    <select class="form-select form-select-sm" name="sort">
                                        <option value="terbaru" <?= $filterUrutan === 'terbaru' ? 'selected' : '' ?>>Terbaru</option>
                                        <option value="terlama" <?= $filterUrutan === 'terlama' ? 'selected' : '' ?>>Terlama</option>
                                        <option value="populer" <?= $filterUrutan === 'populer' ? 'selected' : '' ?>>Terpopuler</option>
                                    </select>
                                </div>
                                
                                <hr class="my-2">
                                
                                <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="resetFilter()">
                                    <i class="bi bi-trash"></i> Bersihkan Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- Grid Laporan -->
<section class="pb-5">
    <div class="container-fluid px-3 px-md-4">
        <?php if ($errorMsg): ?>
            <div class="alert alert-warning"><?= $errorMsg ?></div>
        <?php endif; ?>
        
        <div class="row g-4">
            <?php if (!empty($laporanList)): ?>
                <?php foreach ($laporanList as $laporan): 
                    $config = $statusConfig[$laporan['status'] ?? 'dikirim'] ?? $statusConfig['dikirim'];
                    $katLabel = $kategoriLabel[$laporan['kategori'] ?? ''] ?? 'Lainnya';
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="laporan-grid-card">
                        <!-- Thumbnail -->
                        <div class="laporan-grid-img">
                            <img src="<?= $laporan['foto'] ?? '../Assets/images/icon/logo.png' ?>" alt="<?= htmlspecialchars($laporan['judul'] ?? 'Laporan') ?>" onerror="this.src='../Assets/images/icon/logo.png'">
                            <span class="badge <?= $config['badge'] ?> badge-grid">
                                <i class="bi <?= $config['icon'] ?>"></i> <?= $config['label'] ?>
                            </span>
                            <?php if (($laporan['prioritas'] ?? 'normal') === 'tinggi'): ?>
                            <span class="badge bg-danger badge-prioritas">Prioritas Tinggi</span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Content -->
                        <div class="laporan-grid-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="laporan-grid-title"><?= htmlspecialchars($laporan['judul'] ?? 'Tanpa Judul') ?></h6>
                                <small class="text-muted laporan-grid-kode"><?= htmlspecialchars($laporan['kode'] ?? '-') ?></small>
                            </div>
                            
                            <div class="laporan-grid-meta">
                                <span><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($laporan['lokasi'] ?? '-') ?></span>
                                <span><i class="bi bi-calendar"></i> <?= htmlspecialchars($laporan['created_at'] ?? '-') ?></span>
                            </div>
                            
                            <p class="laporan-grid-desc"><?= htmlspecialchars(mb_strimwidth($laporan['deskripsi'] ?? '-', 0, 100, '...')) ?></p>
                            
                            <div class="laporan-grid-footer">
                                <div class="laporan-grid-stats">
                                    <span class="badge bg-light text-dark"><?= $katLabel ?></span>
                                </div>
                                <a href="detail.php?id=<?= $laporan['id'] ?? 0 ?>" class="btn btn-sm btn-link text-success text-decoration-none fw-semibold">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <p class="mt-3 text-muted">Belum ada laporan yang sesuai dengan filter.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <nav class="mt-5">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage - 1])) ?>"><i class="bi bi-chevron-left"></i></a>
                </li>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
                
                <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage + 1])) ?>"><i class="bi bi-chevron-right"></i></a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</section>

<script>
function resetFilter() {
    window.location.href = 'laporan.php';
}
</script>
<?php require_once __DIR__ . '/../includes/footer-public.php'; ?>