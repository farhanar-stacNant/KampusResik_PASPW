<?php
session_start();

require_once __DIR__ . '/includes/api-config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

// Redirect jika sudah login
if (is_admin()) {
    redirect(base_admin() . '/index.php');
}
if (is_petugas()) {
    redirect(base_petugas() . '/index.php');
}

$error = '';
$role = $_POST['role'] ?? 'petugas';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'petugas';
    
    if (empty($email) || empty($password)) {
        $error = 'Email dan password wajib diisi!';
    } else {
        // Login via API Laravel
        $result = fetch_api('/auth/login', 'POST', [
            'email' => $email,
            'password' => $password
        ]);
        
        if ($result['code'] === 200 && isset($result['body']['data']['token'])) {
            $token = $result['body']['data']['token'];
            $user = $result['body']['data']['user'] ?? null;
            
            if ($user && isset($user['role'])) {
                // Strict role validation
                if ($user['role'] !== $role) {
                    $error = 'Bro, Anda salah Halaman';
                } else {
                    // Set session
                    set_auth_token($token, $user);
                    
                    // Redirect berdasarkan role
                    switch ($user['role']) {
                        case 'admin':
                            redirect(base_admin() . '/index.php');
                            break;
                        case 'petugas':
                            redirect(base_petugas() . '/index.php');
                            break;
                        default:
                            $error = 'Akses ditolak. Hanya admin dan petugas yang boleh login.';
                            clear_auth();
                    }
                }
            } else {
                $error = 'Gagal mendapatkan data user dari server.';
            }
        } else {
            $error = $result['body']['message'] ?? 'Email atau password salah!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KampusResik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --primary: #2e7d32; --primary-dark: #1b5e20; }
        body { font-family: 'Segoe UI', sans-serif; min-height: 100vh; background: linear-gradient(135deg, #e8f5e9, #c8e6c9); }
        
        .navbar-public { background: linear-gradient(135deg, #2e7d32, #00897b); padding: 0.75rem 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .navbar-public .navbar-brand { color: #fff !important; font-weight: 700; font-size: 1.3rem; display: flex; align-items: center; gap: 0.5rem; }
        .navbar-public .nav-link { color: rgba(255,255,255,0.9) !important; font-weight: 500; padding: 0.5rem 1rem !important; border-radius: 8px; }
        .navbar-public .nav-link:hover { background: rgba(255,255,255,0.15); color: #fff !important; }
        .navbar-public .btn-login-nav { background: #fff; color: #2e7d32; font-weight: 600; padding: 0.5rem 1.25rem; border-radius: 8px; border: none; }
        
        .login-wrapper { min-height: calc(100vh - 70px); display: flex; align-items: center; justify-content: center; padding: 2rem 1rem; }
        .login-card { background: #fff; border-radius: 20px; box-shadow: 0 10px 40px rgba(46,125,50,0.15); width: 66.666%; max-width: 800px; min-width: 320px; overflow: hidden; }
        .login-header { background: linear-gradient(135deg, #2e7d32, #00897b); padding: 2rem; text-align: center; color: #fff; }
        .login-header .logo-icon { width: 70px; height: 70px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 2rem; }
        .login-body { padding: 2rem; }
        
        .form-floating > .form-control { border-radius: 12px; border: 2px solid #e0e0e0; height: 56px; }
        .form-floating > .form-control:focus { border-color: #2e7d32; box-shadow: 0 0 0 0.2rem rgba(46,125,50,0.15); }
        .input-group-text { background: #e8f5e9; border: 2px solid #e0e0e0; border-right: none; color: #2e7d32; border-radius: 12px 0 0 12px; }
        
        .btn-login-submit { background: linear-gradient(135deg, #2e7d32, #00897b); color: #fff; font-weight: 600; padding: 0.875rem; border-radius: 12px; border: none; width: 100%; font-size: 1rem; }
        .btn-login-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(46,125,50,0.3); }
        
        .role-selector { display: flex; gap: 0.5rem; margin-bottom: 1.5rem; }
        .role-selector .btn-role { flex: 1; padding: 0.625rem; border: 2px solid #e0e0e0; background: #fff; border-radius: 10px; font-weight: 600; font-size: 0.9rem; color: #666; cursor: pointer; transition: all 0.2s; }
        .role-selector .btn-role.active { background: #2e7d32; border-color: #2e7d32; color: #fff; }
        .role-selector .btn-role i { font-size: 1.2rem; display: block; margin-bottom: 0.25rem; }
        
        .back-link { text-align: center; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #eee; }
        .back-link a { color: #2e7d32; text-decoration: none; font-weight: 600; font-size: 0.95rem; display: inline-flex; align-items: center; gap: 0.5rem; }
        .back-link p { color: #888; font-size: 0.85rem; margin-bottom: 0.5rem; }
        
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .animate-login { animation: fadeInUp 0.5s ease forwards; }
        
        @media (max-width: 768px) {
            .login-card { width: 90%; }
            .login-body { padding: 1.5rem; }
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-public">
        <div class="container">
            <a class="navbar-brand" href="<?= base_public() ?>/index.php">
                <i class="bi bi-recycle"></i><span>KampusResik</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarPublic">
                <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarPublic">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="<?= base_public() ?>/index.php"><i class="bi bi-house-door me-1"></i>Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_public() ?>/laporan.php"><i class="bi bi-clipboard-data me-1"></i>Laporan</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_public() ?>/pengaduan.php"><i class="bi bi-megaphone me-1"></i>Pengaduan</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_public() ?>/status.php"><i class="bi bi-search me-1"></i>Cek Status</a></li>
                    <li class="nav-item ms-lg-2"><a href="<?= base_url() ?>/login.php" class="btn btn-login-nav active"><i class="bi bi-box-arrow-in-right me-1"></i>Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="login-wrapper">
        <div class="login-card animate-login">
            <div class="login-header">
                <div class="logo-icon"><i class="bi bi-recycle"></i></div>
                <h4>KampusResik</h4>
                <p>Masuk ke akun Anda</p>
            </div>
            <div class="login-body">
                
                <?php if ($error): ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
                </div>
                <?php endif; ?>
                    <form method="POST" action="" id="loginForm">
                        <!-- Role Selector -->
                        <div class="role-selector">
                            <button type="button" class="btn-role <?= $role === 'petugas' ? 'active' : '' ?>" data-role="petugas">
                                <i class="bi bi-person-badge"></i>Petugas
                            </button>
                            <button type="button" class="btn-role <?= $role === 'admin' ? 'active' : '' ?>" data-role="admin">
                                <i class="bi bi-shield-lock"></i>Admin
                            </button>
                        </div>
                        <input type="hidden" name="role" id="roleInput" value="<?= htmlspecialchars($role) ?>">
                        
                        <!-- Email -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small">Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-envelope text-success"></i></span>
                                <input type="email" class="form-control form-control-lg" id="email" name="email" placeholder="Masukkan email" required>
                            </div>
                        </div>
                        
                        <!-- Password -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-muted small">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-lock text-success"></i></span>
                                <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="Masukkan password" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-login-submit">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                        </button>
                    </form>
                <div class="back-link">
                    <p>Merasa Salah Halaman?</p>
                    <a href="<?= base_public() ?>/index.php">
                        <i class="bi bi-arrow-left"></i>Silahkan Kembali
                    </a>
                </div>
                
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const p = document.getElementById('password');
            const i = this.querySelector('i');
            if (p.type === 'password') { p.type = 'text'; i.classList.replace('bi-eye','bi-eye-slash'); }
            else { p.type = 'password'; i.classList.replace('bi-eye-slash','bi-eye'); }
        });
        document.querySelectorAll('.btn-role').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.btn-role').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('roleInput').value = this.dataset.role;
            });
        });
    </script>
</body>
</html>