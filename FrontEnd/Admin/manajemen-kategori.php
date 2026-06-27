<?php
require_once '../includes/api-config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';
require_admin();

$page_title = 'Kategori & Petugas - Admin';
$token = get_auth_token();

// Fetch data dari API
$kategori_list = [];
$petugas_list = [];

try {
    $result = fetch_api('/admin/categories', 'GET', null, $token);
    if ($result['code'] === 200) {
        $kategori_list = $result['body']['data'] ?? [];
    }

    $result = fetch_api('/admin/petugas', 'GET', null, $token);
    if ($result['code'] === 200) {
        $petugas_list = $result['body']['data'] ?? [];
    }
} catch (Exception $e) {
    // Fallback sesuai struktur DB
    $kategori_list = [
        ['id' => 1, 'nama_kategori' => 'Sampah Organik', 'level_risiko' => 'rendah'],
        ['id' => 2, 'nama_kategori' => 'Sampah Plastik', 'level_risiko' => 'sedang'],
        ['id' => 3, 'nama_kategori' => 'Sampah Medis', 'level_risiko' => 'tinggi'],
        ['id' => 4, 'nama_kategori' => 'Sampah B3', 'level_risiko' => 'tinggi'],
        ['id' => 5, 'nama_kategori' => 'Sampah Kertas', 'level_risiko' => 'rendah'],
        ['id' => 6, 'nama_kategori' => 'Sampah Elektronik', 'level_risiko' => 'sedang'],
    ];
    $petugas_list = [
        ['id' => 2, 'nama' => 'Ahmad Wijaya', 'lokasi_sekitar' => 'Gedung A & B, Perpustakaan', 'jadwal_harian' => 'Senin-Jumat 08:00-16:00', 'status_aktif' => 1],
        ['id' => 3, 'nama' => 'Siti Nurhaliza', 'lokasi_sekitar' => 'Kantin Pusat & Parkiran', 'jadwal_harian' => 'Senin-Sabtu 07:00-15:00', 'status_aktif' => 1],
        ['id' => 4, 'nama' => 'Bambang Sutrisno', 'lokasi_sekitar' => 'Gedung C & Lab Kimia', 'jadwal_harian' => 'Senin-Jumat 09:00-17:00', 'status_aktif' => 1],
    ];
}

require_once '../includes/head-admin.php';
require_once '../includes/navbar-admin.php';
?>

<main class="admin-main">
    <div class="container-fluid">
        
        <div class="admin-header animate-in">
            <div>
                <h4>Manajemen Kategori & Petugas</h4>
                <p>Kelola kategori sampah dan data petugas kebersihan.</p>
            </div>
        </div>
        
        <!-- Kategori Sampah -->
        <div class="admin-card mb-4 animate-in">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">Kategori Sampah</h5>
                    <button class="btn btn-admin btn-admin-primary btn-sm" onclick="showAddKategoriModal()">
                        <i class="bi bi-plus-lg me-1"></i>Tambah Kategori
                    </button>
                </div>
                <div class="row g-3">
                    <?php foreach ($kategori_list as $kat): 
                        $risiko = $kat['level_risiko'] ?? 'rendah';
                        $risiko_badge = match($risiko) {
                            'rendah' => 'bg-success',
                            'sedang' => 'bg-warning text-dark',
                            'tinggi' => 'bg-danger',
                            default => 'bg-secondary'
                        };
                    ?>
                    <div class="col-md-4 col-lg-3">
                        <div class="d-flex align-items-center justify-content-between p-3 rounded-3" style="background: #f8f9fa; border: 2px solid #e9ecef;">
                            <div>
                                <span class="fw-semibold d-block"><?= htmlspecialchars($kat['nama_kategori']) ?></span>
                                <span class="badge <?= $risiko_badge ?> mt-1" style="font-size: 0.7rem;"><?= ucfirst($risiko) ?></span>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-primary" style="padding: 0.25rem 0.5rem;" onclick="editKategori(<?= $kat['id'] ?>)"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-outline-danger" style="padding: 0.25rem 0.5rem;" onclick="deleteKategori(<?= $kat['id'] ?>)"><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Manajemen Petugas -->
        <div class="admin-card animate-in">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">Data Petugas</h5>
                    <span class="text-muted" style="font-size: 0.85rem;"><i class="bi bi-info-circle me-1"></i>Klik Edit untuk ubah Lokasi & Jadwal</span>
                </div>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama</th>
                                <th>Lokasi Sekitar</th>
                                <th>Jadwal Harian</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($petugas_list as $p): 
                                $badge = ($p['status_aktif'] ?? 1) ? 'badge-selesai' : 'badge-tertunda';
                                $status_text = ($p['status_aktif'] ?? 1) ? 'Aktif' : 'Nonaktif';
                            ?>
                            <tr>
                                <td><strong>#<?= $p['id'] ?></strong></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                            <i class="bi bi-person text-muted"></i>
                                        </div>
                                        <span class="fw-semibold"><?= htmlspecialchars($p['nama'] ?? $p['name']) ?></span>
                                    </div>
                                </td>
                                <td><i class="bi bi-geo-alt text-muted me-1"></i><?= htmlspecialchars($p['lokasi_sekitar'] ?? '-') ?></td>
                                <td><i class="bi bi-clock text-muted me-1"></i><?= htmlspecialchars($p['jadwal_harian'] ?? '-') ?></td>
                                <td><span class="badge-status <?= $badge ?>"><?= $status_text ?></span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary me-1" onclick="editPetugas(<?= $p['id'] ?>)"><i class="bi bi-pencil"></i></button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
    </div>
</main>

<!-- Modal Tambah/Edit Kategori -->
<div class="modal fade" id="modalKategori" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalKategoriTitle">Tambah Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formKategori">
                    <input type="hidden" id="kategoriId">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kategori</label>
                        <input type="text" class="form-control" id="namaKategori" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Level Risiko</label>
                        <select class="form-select" id="levelRisiko">
                            <option value="rendah">Rendah</option>
                            <option value="sedang">Sedang</option>
                            <option value="tinggi">Tinggi</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi</label>
                        <textarea class="form-control" id="deskripsiKategori" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-admin btn-admin-primary" onclick="saveKategori()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
const API_TOKEN = '<?= $token ?>';
const API_BASE = '<?= API_BASE_URL ?>';

function showAddKategoriModal() {
    document.getElementById('modalKategoriTitle').textContent = 'Tambah Kategori';
    document.getElementById('formKategori').reset();
    document.getElementById('kategoriId').value = '';
    new bootstrap.Modal(document.getElementById('modalKategori')).show();
}

function editKategori(id) {
    fetch(API_BASE + '/admin/categories/' + id, {
        headers: { 'Authorization': 'Bearer ' + API_TOKEN, 'X-Api-Token': '<?= API_SECRET_TOKEN ?>' }
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('modalKategoriTitle').textContent = 'Edit Kategori';
        document.getElementById('kategoriId').value = id;
        document.getElementById('namaKategori').value = data.data.nama_kategori;
        document.getElementById('levelRisiko').value = data.data.level_risiko;
        document.getElementById('deskripsiKategori').value = data.data.deskripsi || '';
        new bootstrap.Modal(document.getElementById('modalKategori')).show();
    });
}

function saveKategori() {
    const id = document.getElementById('kategoriId').value;
    const method = id ? 'PUT' : 'POST';
    const url = id ? API_BASE + '/admin/categories/' + id : API_BASE + '/admin/categories';
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer <?= $token ?>',
            'X-Api-Token': '<?= API_SECRET_TOKEN ?>'
},
        body: JSON.stringify({
            nama_kategori: document.getElementById('namaKategori').value,
            level_risiko: document.getElementById('levelRisiko').value,
            deskripsi: document.getElementById('deskripsiKategori').value
        })
    })
    .then(r => r.json())
    .then(() => location.reload());
}

function deleteKategori(id) {
    if (!confirm('Hapus kategori ini?')) return;
    fetch(API_BASE + '/admin/categories/' + id, {
        method: 'DELETE',
        headers: { 'Authorization': 'Bearer ' + API_TOKEN, 'X-Api-Token': '<?= API_SECRET_TOKEN ?>' }
    })
    .then(() => location.reload());
}

function editPetugas(id) {
    alert('Fitur edit petugas (lokasi & jadwal) akan diarahkan ke halaman Pengaturan Petugas. ID: ' + id);
}
</script>

<?php include '../includes/footer-admin.php'; ?>