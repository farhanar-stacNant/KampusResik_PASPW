<?php
require_once '../includes/api-config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';
require_admin();

$page_title = 'Laporan Masuk - Admin';
$token = get_auth_token();

$laporan_list = [];
$stats = ['menunggu' => 0, 'diproses' => 0, 'selesai' => 0];
$status_options = ['dikirim' => 'Dikirim', 'diterima' => 'Diterima', 'diproses' => 'Diproses', 'selesai' => 'Selesai', 'ditolak' => 'Ditolak'];

try {
    $result = fetch_api('/admin/reports?limit=50', 'GET', null, $token);
    if ($result['code'] === 200) {
        $laporan_list = $result['body']['data']['data'] ?? [];
    }

    $result = fetch_api('/admin/statistics', 'GET', null, $token);
    if ($result['code'] === 200) {
        $data = $result['body']['data'] ?? [];
        $stats['menunggu'] = ($data['laporan_dikirim'] ?? 0) + ($data['laporan_diterima'] ?? 0);
        $stats['diproses'] = $data['laporan_diproses'] ?? 0;
        $stats['selesai'] = $data['laporan_selesai'] ?? 0;
    }
} catch (Exception $e) {
    // Fallback sesuai DB
    $laporan_list = [
        ['id' => 1, 'kode_laporan' => 'KRS-20260626-A1B2', 'nama_pelapor' => 'Budi Santoso', 'foto' => null, 'lokasi' => 'Gedung Rektorat Lt. 1, depan lift utama', 'kategori_sampah' => ['nama_kategori' => 'Sampah Plastik'], 'status' => 'dikirim', 'created_at' => '26 Jun 2026'],
        ['id' => 2, 'kode_laporan' => 'KRS-20260625-C3D4', 'nama_pelapor' => 'Ani Wulandari', 'foto' => null, 'lokasi' => 'Fakultas Kedokteran, area parkir belakang', 'kategori_sampah' => ['nama_kategori' => 'Sampah Medis'], 'status' => 'diproses', 'created_at' => '25 Jun 2026'],
        ['id' => 3, 'kode_laporan' => 'KRS-20260624-E5F6', 'nama_pelapor' => 'Dedi Kurniawan', 'foto' => null, 'lokasi' => 'Kantin Pusat, area tempat sampah sementara', 'kategori_sampah' => ['nama_kategori' => 'Sampah Organik'], 'status' => 'selesai', 'created_at' => '24 Jun 2026'],
    ];
    $stats = ['menunggu' => 5, 'diproses' => 3, 'selesai' => 5];
}

require_once '../includes/head-admin.php';
require_once '../includes/navbar-admin.php';
?>

<main class="admin-main">
    <div class="container-fluid">
        
        <div class="admin-header animate-in">
            <div>
                <h4>Memproses Laporan Sampah</h4>
                <p>Daftar laporan masuk yang membutuhkan tindakan administratif segera.</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-admin btn-admin-outline" onclick="alert('Filter akan segera tersedia')">
                    <i class="bi bi-funnel me-2"></i>Filter
                </button>
                <button class="btn btn-admin btn-admin-primary" onclick="alert('Export akan segera tersedia')">
                    <i class="bi bi-download me-2"></i>Export Data
                </button>
            </div>
        </div>
        
        <!-- Stat Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-4 animate-in">
                <div class="admin-card">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-icon orange"><i class="bi bi-hourglass-split"></i></div>
                        <div>
                            <h3 class="mb-0 fw-bold"><?= $stats['menunggu'] ?></h3>
                            <p class="mb-0 text-muted">Laporan Menunggu</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 animate-in">
                <div class="admin-card">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-icon blue"><i class="bi bi-arrow-repeat"></i></div>
                        <div>
                            <h3 class="mb-0 fw-bold"><?= $stats['diproses'] ?></h3>
                            <p class="mb-0 text-muted">Laporan Diproses</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 animate-in">
                <div class="admin-card">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-icon green"><i class="bi bi-check-circle"></i></div>
                        <div>
                            <h3 class="mb-0 fw-bold"><?= $stats['selesai'] ?></h3>
                            <p class="mb-0 text-muted">Laporan Selesai</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tabel Laporan -->
        <div class="admin-card animate-in">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID Laporan</th>
                                <th>Pelapor</th>
                                <th>Foto</th>
                                <th>Lokasi</th>
                                <th>Kategori</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($laporan_list as $lap): 
                                $badge_class = match($lap['status'] ?? 'dikirim') {
                                    'dikirim' => 'badge-masuk',
                                    'diterima' => 'badge-masuk',
                                    'diproses' => 'badge-proses',
                                    'selesai' => 'badge-selesai',
                                    'ditolak' => 'badge-tertunda',
                                    default => 'badge-belum'
                                };
                                $kategori_nama = $lap['kategori_sampah']['nama_kategori'] ?? 'Lainnya';
                                $foto_url = !empty($lap['foto']) ? API_BASE_URL . '/storage/' . $lap['foto'] : null;
                            ?>
                            <tr>
                                <td><strong class="text-primary"><?= htmlspecialchars($lap['kode_laporan'] ?? 'KR-'.$lap['id']) ?></strong></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                            <i class="bi bi-person text-muted"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0 fw-semibold" style="font-size: 0.9rem;"><?= htmlspecialchars($lap['nama_pelapor']) ?></p>
                                            <p class="mb-0 text-muted" style="font-size: 0.75rem;"><?= htmlspecialchars($lap['created_at'] ?? '-') ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($foto_url): ?>
                                        <img src="<?= htmlspecialchars($foto_url) ?>" alt="Foto" class="rounded-3" style="width: 60px; height: 60px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="rounded-3 bg-light d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><i class="bi bi-geo-alt text-muted me-1"></i><?= htmlspecialchars($lap['lokasi']) ?></td>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($kategori_nama) ?></span></td>
                                <td>
                                    <select class="form-select form-select-sm status-select" data-id="<?= $lap['id'] ?>" style="min-width: 130px; border-radius: 20px;">
                                        <?php foreach ($status_options as $key => $label): ?>
                                        <option value="<?= $key ?>" <?= (($lap['status'] ?? 'dikirim') === $key) ? 'selected' : '' ?>><?= $label ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <a href="<?= base_admin() ?>/manajemen-laporan.php?id=<?= $lap['id'] ?>" class="btn btn-sm btn-outline-primary rounded-circle" style="width: 36px; height: 36px; padding: 0;">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <p class="text-muted mb-0" style="font-size: 0.85rem;">Menampilkan <?= count($laporan_list) ?> laporan</p>
                    <nav>
                        <ul class="pagination pagination-admin mb-0">
                            <li class="page-item"><a class="page-link" href="#"><i class="bi bi-chevron-left"></i></a></li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item"><a class="page-link" href="#"><i class="bi bi-chevron-right"></i></a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
        
    </div>
</main>

<script>
// Update status via AJAX
document.querySelectorAll('.status-select').forEach(select => {
    select.addEventListener('change', async function() {
        const id = this.dataset.id;
        const status = this.value;
        
        try {
            const response = await fetch('<?= API_BASE_URL ?>/admin/reports/' + id + '/status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer <?= $token ?>',
                    'X-Api-Token': '<?= API_SECRET_TOKEN ?>'
},
                body: JSON.stringify({ status: status })
            });
            
            const result = await response.json();
            if (response.ok) {
                alert('Status berhasil diupdate!');
            } else {
                alert('Gagal update status: ' + (result.message || 'Error'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Gagal terhubung ke server');
        }
    });
});
</script>

<?php include '../includes/footer-admin.php'; ?>