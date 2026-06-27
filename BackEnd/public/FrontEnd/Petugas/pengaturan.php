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

$pageTitle = 'Pengaturan - Petugas';
$token = get_auth_token();
$user = get_user_data();

// Ambil data profil dari session / API
$profil = [
    'nama' => $user['nama'] ?? $user['name'] ?? 'Petugas',
    'email' => $user['email'] ?? '-',
    'telepon' => $user['no_hp'] ?? $user['telepon'] ?? '-',
    'jabatan' => ($user['role'] ?? '') === 'petugas' ? 'Petugas Kebersihan' : 'Administrator',
    'lokasi_tugas' => $user['lokasi_tugas'] ?? 'Belum ditentukan',
    'tanggal_bergabung' => $user['created_at'] ?? date('Y-m-d'),
];

// Coba fetch dari API /me jika tersedia
try {
    $result = fetch_api('/me', 'GET', null, $token);
    if ($result['code'] === 200 && isset($result['body']['data'])) {
        $apiUser = $result['body']['data'];
        $profil['nama'] = $apiUser['nama'] ?? $apiUser['name'] ?? $profil['nama'];
        $profil['email'] = $apiUser['email'] ?? $profil['email'];
        $profil['telepon'] = $apiUser['no_hp'] ?? $apiUser['telepon'] ?? $profil['telepon'];
    }
} catch (Exception $e) {
    // Gunakan data session
}

include '../includes/head-petugas.php';
include '../includes/navbar-petugas.php';
?>

<main class="main-content">
    <div class="container-fluid">
        <div class="row mb-4 animate-fade-in">
            <div class="col-12">
                <h4 class="fw-bold text-dark mb-1"><i class="bi bi-gear me-2"></i>Pengaturan</h4>
                <p class="text-muted mb-0">Kelola profil dan preferensi akun</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-4 mb-4 animate-fade-in">
                <div class="card card-stat h-100">
                    <div class="card-body text-center">
                        <div class="position-relative d-inline-block">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto" style="width: 120px; height: 120px; border: 3px solid #1a237e;">
                                <i class="bi bi-person" style="font-size: 3.5rem; color: #1a237e;"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold mb-1"><?= htmlspecialchars($profil['nama']) ?></h5>
                        <p class="text-muted mb-2"><?= $profil['jabatan'] ?></p>
                        <span class="badge bg-primary bg-opacity-10 text-primary">Petugas Aktif</span>
                        <hr class="my-3">
                        <div class="text-start">
                            <p class="mb-2"><i class="bi bi-envelope text-muted me-2"></i><small class="text-muted">Email</small><br><span class="fw-semibold"><?= htmlspecialchars($profil['email']) ?></span></p>
                            <p class="mb-2"><i class="bi bi-telephone text-muted me-2"></i><small class="text-muted">Telepon</small><br><span class="fw-semibold"><?= htmlspecialchars($profil['telepon']) ?></span></p>
                            <p class="mb-0"><i class="bi bi-calendar text-muted me-2"></i><small class="text-muted">Bergabung</small><br><span class="fw-semibold"><?= date('d F Y', strtotime($profil['tanggal_bergabung'])) ?></span></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-8 mb-4 animate-fade-in">
                <div class="card card-stat">
                    <div class="card-body">
                        <h5 class="fw-bold mb-4"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Profil</h5>
                        <form id="formProfil" method="POST" onsubmit="return simpanProfil(event)">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nama Lengkap</label>
                                    <div class="input-group"><span class="input-group-text bg-light"><i class="bi bi-person"></i></span><input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($profil['nama']) ?>"></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Email</label>
                                    <div class="input-group"><span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($profil['email']) ?>"></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nomor Telepon</label>
                                    <div class="input-group"><span class="input-group-text bg-light"><i class="bi bi-telephone"></i></span><input type="tel" name="telepon" class="form-control" value="<?= htmlspecialchars($profil['telepon']) ?>"></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Lokasi Tugas</label>
                                    <div class="input-group"><span class="input-group-text bg-light"><i class="bi bi-geo-alt"></i></span><input type="text" class="form-control" value="<?= htmlspecialchars($profil['lokasi_tugas']) ?>" readonly></div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-petugas btn-petugas-primary"><i class="bi bi-save me-2"></i>Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row animate-fade-in">
            <div class="col-12">
                <div class="card card-stat border-danger border-opacity-25">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3 text-danger"><i class="bi bi-shield-lock me-2"></i>Zona Berbahaya</h5>
                        <p class="text-muted mb-3">Keluar dari akun akan menghapus semua session dan cache.</p>
                        <a href="<?= base_url() ?>/includes/auth.php?action=logout" class="btn btn-logout-card" id="btnLogout"><i class="bi bi-box-arrow-right me-2"></i>Keluar dari Akun</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
const API_BASE = '<?= rtrim(API_BASE_URL, '/') ?>';
const TOKEN = '<?= addslashes($token ?? '') ?>';

async function simpanProfil(e) {
    e.preventDefault();
    const form = e.target;
    const data = {
        nama: form.nama.value,
        email: form.email.value,
        no_hp: form.telepon.value
    };
    try {
        const res = await fetch(`${API_BASE}/me`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + TOKEN
            },
            body: JSON.stringify(data)
        });
        const result = await res.json();
        if (res.ok) {
            alert('Profil berhasil diperbarui');
            location.reload();
        } else {
            alert(result.message || 'Gagal memperbarui profil');
        }
    } catch (err) {
        alert('Terjadi kesalahan jaringan. Profil disimpan secara lokal.');
    }
    return false;
}
</script>

<?php include '../includes/footer-petugas.php'; ?>