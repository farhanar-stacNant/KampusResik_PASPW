<?php
require_once '../includes/api-config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';
require_admin();

$page_title = 'Rekap Laporan - Admin';
$token = get_auth_token();

$rekap_data = [];
$stats = ['total' => 0, 'tertunda' => 0, 'terpilah' => 0, 'penyelesaian' => 0];

// Filter params
$filter_tanggal_mulai = $_GET['tanggal_mulai'] ?? '';
$filter_tanggal_akhir = $_GET['tanggal_akhir'] ?? '';
$filter_kategori = $_GET['kategori'] ?? '';
$filter_status = $_GET['status'] ?? '';

try {
    $query = '/admin/reports?limit=50';
    if ($filter_tanggal_mulai) $query .= '&tanggal_mulai=' . urlencode($filter_tanggal_mulai);
    if ($filter_tanggal_akhir) $query .= '&tanggal_akhir=' . urlencode($filter_tanggal_akhir);
    if ($filter_kategori) $query .= '&kategori_id=' . urlencode($filter_kategori);
    if ($filter_status) $query .= '&status=' . urlencode($filter_status);
    
    $result = fetch_api($query, 'GET', null, $token);
    if ($result['code'] === 200) {
        $rekap_data = $result['body']['data']['data'] ?? [];
    }
    
    $result = fetch_api('/admin/statistics', 'GET', null, $token);
    if ($result['code'] === 200) {
        $data = $result['body']['data'] ?? [];
        $stats['total'] = $data['total_laporan'] ?? 0;
        $stats['tertunda'] = ($data['laporan_dikirim'] ?? 0) + ($data['laporan_diterima'] ?? 0);
        $stats['terpilah'] = $data['sampah_terpilah'] ?? '0 kg';
        $stats['penyelesaian'] = $data['persentase_selesai'] ?? 0;
    }
} catch (Exception $e) {
    $rekap_data = [
        ['kode_laporan' => 'KRS-20260626-A1B2', 'created_at' => '2026-06-26 14:16:14', 'nama_pelapor' => 'Budi Santoso', 'kategori_sampah' => ['nama_kategori' => 'Sampah Plastik'], 'lokasi' => 'Gedung Rektorat Lt. 1', 'status' => 'dikirim'],
        ['kode_laporan' => 'KRS-20260625-C3D4', 'created_at' => '2026-06-25 14:16:14', 'nama_pelapor' => 'Ani Wulandari', 'kategori_sampah' => ['nama_kategori' => 'Sampah Medis'], 'lokasi' => 'Fakultas Kedokteran', 'status' => 'diproses'],
        ['kode_laporan' => 'KRS-20260624-E5F6', 'created_at' => '2026-06-24 14:16:14', 'nama_pelapor' => 'Dedi Kurniawan', 'kategori_sampah' => ['nama_kategori' => 'Sampah Organik'], 'lokasi' => 'Kantin Pusat', 'status' => 'selesai'],
    ];
    $stats = ['total' => 13, 'tertunda' => 5, 'terpilah' => '892 kg', 'penyelesaian' => 38.5];
}

require_once '../includes/head-admin.php';
require_once '../includes/navbar-admin.php';
?>

<main class="admin-main">
    <div class="container-fluid">
        
        <div class="admin-header animate-in">
            <div>
                <h4>Rekap Laporan</h4>
                <p>Pantau dan analisis seluruh statistik pengelolaan sampah kampus.</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-admin btn-admin-outline" onclick="exportExcel()"><i class="bi bi-file-excel me-2"></i>Export Excel</button>
                <button class="btn btn-admin btn-admin-primary" onclick="exportPDF()"><i class="bi bi-file-pdf me-2"></i>Export PDF</button>
            </div>
        </div>
        
        <!-- Stat Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3 animate-in">
                <div class="admin-card">
                    <div class="card-body text-center">
                        <div class="stat-icon blue mx-auto mb-2"><i class="bi bi-clipboard-data"></i></div>
                        <h3 class="fw-bold"><?= number_format($stats['total']) ?></h3>
                        <p class="text-muted mb-0">Total Laporan</p>
                        <span class="stat-trend up">↗ +12% Bulan Ini</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 animate-in">
                <div class="admin-card">
                    <div class="card-body text-center">
                        <div class="stat-icon orange mx-auto mb-2"><i class="bi bi-exclamation-triangle"></i></div>
                        <h3 class="fw-bold"><?= $stats['tertunda'] ?></h3>
                        <p class="text-muted mb-0">Laporan Tertunda</p>
                        <span class="stat-trend down">⚠ Perlu penanganan segera</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 animate-in">
                <div class="admin-card">
                    <div class="card-body text-center">
                        <div class="stat-icon green mx-auto mb-2"><i class="bi bi-recycle"></i></div>
                        <h3 class="fw-bold"><?= $stats['terpilah'] ?></h3>
                        <p class="text-muted mb-0">Sampah Terpilah</p>
                        <span class="stat-trend up">↗ Data minggu terakhir</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 animate-in">
                <div class="admin-card">
                    <div class="card-body text-center">
                        <div class="stat-icon blue mx-auto mb-2"><i class="bi bi-check-circle"></i></div>
                        <h3 class="fw-bold"><?= $stats['penyelesaian'] ?>%</h3>
                        <p class="text-muted mb-0">Penyelesaian</p>
                        <div class="progress-admin mt-2">
                            <div class="progress-bar" style="width: <?= min($stats['penyelesaian'], 100) ?>%; background: var(--adm-success);"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Filter -->
        <div class="admin-card mb-4 animate-in">
            <div class="card-body">
                <form method="GET" action="" id="filterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Rentang Tanggal</label>
                            <div class="input-group">
                                <input type="date" class="form-control" name="tanggal_mulai" value="<?= htmlspecialchars($filter_tanggal_mulai) ?>">
                                <span class="input-group-text">s/d</span>
                                <input type="date" class="form-control" name="tanggal_akhir" value="<?= htmlspecialchars($filter_tanggal_akhir) ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Kategori</label>
                            <select class="form-select" name="kategori">
                                <option value="">Semua Kategori</option>
                                <option value="1" <?= $filter_kategori === '1' ? 'selected' : '' ?>>Sampah Organik</option>
                                <option value="2" <?= $filter_kategori === '2' ? 'selected' : '' ?>>Sampah Plastik</option>
                                <option value="3" <?= $filter_kategori === '3' ? 'selected' : '' ?>>Sampah Medis</option>
                                <option value="4" <?= $filter_kategori === '4' ? 'selected' : '' ?>>Sampah B3</option>
                                <option value="5" <?= $filter_kategori === '5' ? 'selected' : '' ?>>Sampah Kertas</option>
                                <option value="6" <?= $filter_kategori === '6' ? 'selected' : '' ?>>Sampah Elektronik</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Status</label>
                            <select class="form-select" name="status">
                                <option value="">Semua Status</option>
                                <option value="selesai" <?= $filter_status === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                                <option value="diproses" <?= $filter_status === 'diproses' ? 'selected' : '' ?>>Diproses</option>
                                <option value="dikirim" <?= $filter_status === 'dikirim' ? 'selected' : '' ?>>Menunggu</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-admin btn-admin-primary w-100"><i class="bi bi-funnel me-2"></i>Terapkan</button>
                        </div>
                        <div class="col-md-3 text-end">
                            <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('filterForm').reset(); location.href='rekap-laporan.php'">
                                <i class="bi bi-x-circle"></i> Reset
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Tabel Rekap -->
        <div class="admin-card animate-in">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID Laporan</th>
                                <th>Tanggal</th>
                                <th>Pelapor</th>
                                <th>Kategori</th>
                                <th>Lokasi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rekap_data as $r): 
                                $badge = match($r['status'] ?? 'dikirim'){
                                    'selesai' => 'badge-selesai',
                                    'diproses' => 'badge-proses',
                                    'dikirim' => 'badge-masuk',
                                    'diterima' => 'badge-masuk',
                                    'ditolak' => 'badge-tertunda',
                                    default => 'badge-belum'
                                };
                                $kategori_nama = $r['kategori_sampah']['nama_kategori'] ?? 'Lainnya';
                                $tanggal = date('d M Y', strtotime($r['created_at'] ?? 'now'));
                            ?>
                            <tr>
                                <td><strong class="text-primary"><?= htmlspecialchars($r['kode_laporan'] ?? 'KR-'.$r['id']) ?></strong></td>
                                <td><?= htmlspecialchars($tanggal) ?></td>
                                <td><?= htmlspecialchars($r['nama_pelapor']) ?></td>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($kategori_nama) ?></span></td>
                                <td><i class="bi bi-geo-alt text-muted me-1"></i><?= htmlspecialchars($r['lokasi']) ?></td>
                                <td><span class="badge-status <?= $badge ?>"><?= ucfirst($r['status'] ?? 'dikirim') ?></span></td>
                                <td><button class="btn btn-sm btn-outline-primary" onclick="viewDetail('<?= $r['id'] ?>')"><i class="bi bi-eye"></i></button></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <p class="text-muted mb-0" style="font-size: 0.85rem;">Menampilkan <?= count($rekap_data) ?> laporan</p>
                    <ul class="pagination pagination-admin mb-0">
                        <li class="page-item"><a class="page-link" href="#"><i class="bi bi-chevron-left"></i></a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item"><a class="page-link" href="#"><i class="bi bi-chevron-right"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>
        
    </div>
</main>

<script>
<?php if ($auto_export): ?>
    window.addEventListener('DOMContentLoaded', function(){
        setTimeout(function(){exportExcel();}, 800);
    });
<?php endif; ?>
function viewDetail(id) {
    window.open('<?= base_admin() ?>/manajemen-laporan.php?id=' + id, '_blank');
}

function exportExcel() {
    const params = new URLSearchParams(window.location.search);
    params.append('api_token', '<?= API_SECRET_TOKEN ?>');
    window.open('<?= API_BASE_URL ?>/admin/export/excel?' + params.toString(), '_blank');
}

function exportPDF() {
    const params = new URLSearchParams(window.location.search);
    params.append('api_token', '<?= API_SECRET_TOKEN ?>');
    window.open('<?= API_BASE_URL ?>/admin/export/pdf?' + params.toString(), '_blank');
}
</script>

<?php include '../includes/footer-admin.php'; ?>