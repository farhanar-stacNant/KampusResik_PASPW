<?php
require_once '../includes/api-config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';
require_admin();

$page_title = 'Pengaturan - Admin';
$token = get_auth_token();

// Fetch admin profile dari API
$admin = [];
try {
    $result = fetch_api('/auth/me', 'GET', null, $token);
    if ($result['code'] === 200) {
        $admin = $result['body']['data'] ?? [];
    }
} catch (Exception $e) {
    $admin = [
        'nama' => 'Administrator Kampus',
        'email' => 'admin@kampusresik.id',
        'no_hp' => '081111111111',
        'lokasi_sekitar' => 'Kantor Admin Pusat',
        'jadwal_harian' => 'Senin-Jumat 08:00-17:00',
        'created_at' => '2026-06-26 13:07:52'
    ];
}

require_once '../includes/head-admin.php';
require_once '../includes/navbar-admin.php';
?>

<main class="admin-main">
    <div class="container-fluid">
        
        <div class="admin-header animate-in">
            <div>
                <h4>Pengaturan</h4>
                <p>Kelola profil dan preferensi akun admin.</p>
            </div>
        </div>
        
        <div class="row g-4">
            <!-- Profil Card -->
            <div class="col-lg-4 animate-in">
                <div class="admin-card h-100">
                    <div class="card-body text-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 120px; height: 120px; background: linear-gradient(135deg, #1565C0, #1976D2);">
                            <i class="bi bi-person" style="font-size: 3.5rem; color: #fff;"></i>
                        </div>
                        <h5 class="fw-bold mb-1"><?= htmlspecialchars($admin['nama'] ?? $admin['name']) ?></h5>
                        <p class="text-muted mb-2">Koordinator Kebersihan</p>
                        <span class="badge bg-primary bg-opacity-10 text-primary">Admin Aktif</span>
                        
                        <hr class="my-4">
                        
                        <div class="text-start">
                            <p class="mb-2"><i class="bi bi-envelope text-muted me-2"></i><small class="text-muted">Email</small><br><span class="fw-semibold"><?= htmlspecialchars($admin['email']) ?></span></p>
                            <p class="mb-2"><i class="bi bi-telephone text-muted me-2"></i><small class="text-muted">Telepon</small><br><span class="fw-semibold"><?= htmlspecialchars($admin['no_hp'] ?? '-') ?></span></p>
                            <p class="mb-2"><i class="bi bi-geo-alt text-muted me-2"></i><small class="text-muted">Lokasi</small><br><span class="fw-semibold"><?= htmlspecialchars($admin['lokasi_sekitar'] ?? '-') ?></span></p>
                            <p class="mb-0"><i class="bi bi-calendar text-muted me-2"></i><small class="text-muted">Bergabung</small><br><span class="fw-semibold"><?= date('d F Y', strtotime($admin['created_at'] ?? 'now')) ?></span></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Edit Profil -->
            <div class="col-lg-8 animate-in">
                <div class="admin-card">
                    <div class="card-body">
                        <h5 class="fw-bold mb-4"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Profil</h5>
                        <form id="formProfil" onsubmit="return saveProfil(event)">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nama Lengkap</label>
                                    <div class="input-group"><span class="input-group-text"><i class="bi bi-person"></i></span><input type="text" class="form-control" id="nama" value="<?= htmlspecialchars($admin['nama'] ?? $admin['name']) ?>"></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Email</label>
                                    <div class="input-group"><span class="input-group-text"><i class="bi bi-envelope"></i></span><input type="email" class="form-control" id="email" value="<?= htmlspecialchars($admin['email']) ?>" readonly></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Telepon</label>
                                    <div class="input-group"><span class="input-group-text"><i class="bi bi-telephone"></i></span><input type="tel" class="form-control" id="telepon" value="<?= htmlspecialchars($admin['no_hp'] ?? '') ?>"></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Lokasi Sekitar</label>
                                    <div class="input-group"><span class="input-group-text"><i class="bi bi-geo-alt"></i></span><input type="text" class="form-control" id="lokasiSekitar" value="<?= htmlspecialchars($admin['lokasi_sekitar'] ?? '') ?>"></div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-admin btn-admin-primary"><i class="bi bi-save me-2"></i>Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Ganti Password -->
                <div class="admin-card mt-4 animate-in">
                    <div class="card-body">
                        <h5 class="fw-bold mb-4"><i class="bi bi-shield-lock me-2 text-primary"></i>Ganti Password</h5>
                        <form id="formPassword" onsubmit="return changePassword(event)">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Password Saat Ini</label>
                                    <input type="password" class="form-control" id="currentPassword" placeholder="••••••••" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Password Baru</label>
                                    <input type="password" class="form-control" id="newPassword" placeholder="••••••••" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Konfirmasi Password</label>
                                    <input type="password" class="form-control" id="confirmPassword" placeholder="••••••••" required>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-admin btn-admin-primary"><i class="bi bi-key me-2"></i>Update Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Logout -->
        <div class="admin-card mt-4 border-danger border-opacity-25 animate-in">
            <div class="card-body">
                <h5 class="fw-bold mb-3 text-danger"><i class="bi bi-shield-exclamation me-2"></i>Zona Berbahaya</h5>
                <p class="text-muted mb-3">Keluar dari akun akan menghapus semua session dan cache. Anda tidak dapat mengakses halaman admin hingga login kembali.</p>
                <a href="<?= base_url() ?>/includes/auth.php?action=logout"class="btn btn-danger w-100" style="border-radius: 10px; padding: 0.875rem; font-weight: 600;">
                    <i class="bi bi-box-arrow-right me-2"></i>Keluar dari Akun
                </a>
            </div>
        </div>
        
    </div>
</main>

<script>
const API_TOKEN = '<?= $token ?>';
const API_BASE = '<?= API_BASE_URL ?>';

function saveProfil(e) {
    e.preventDefault();
    
    fetch(API_BASE + '/auth/update-profile', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + API_TOKEN
        },
        body: JSON.stringify({
            nama: document.getElementById('nama').value,
            no_hp: document.getElementById('telepon').value,
            lokasi_sekitar: document.getElementById('lokasiSekitar').value
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success || data.status === 'success') {
            alert('Profil berhasil diupdate!');
            location.reload();
        } else {
            alert('Gagal: ' + (data.message || 'Error'));
        }
    });
    
    return false;
}

function changePassword(e) {
    e.preventDefault();
    
    const newPass = document.getElementById('newPassword').value;
    const confirmPass = document.getElementById('confirmPassword').value;
    
    if (newPass !== confirmPass) {
        alert('Password baru dan konfirmasi tidak cocok!');
        return false;
    }
    
    fetch(API_BASE + '/auth/change-password', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + API_TOKEN
        },
        body: JSON.stringify({
            current_password: document.getElementById('currentPassword').value,
            new_password: newPass
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success || data.status === 'success') {
            alert('Password berhasil diubah!');
            document.getElementById('formPassword').reset();
        } else {
            alert('Gagal: ' + (data.message || 'Error'));
        }
    });
    
    return false;
}
</script>

<?php include '../includes/footer-admin.php'; ?>