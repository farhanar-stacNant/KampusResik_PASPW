<?php
session_set_cookie_params(['path' => '/']);
session_start();

require_once __DIR__ . '/includes/api_client.php';

// Inisialisasi variabel pesan agar tidak muncul Warning
$pesanGagal = "";

// Jika sudah ada token, langsung arahkan ke dashboard
if (isset($_SESSION['token']) && !empty($_SESSION['role'])) {
    header("Location: " . $_SESSION['role'] . "/dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resData = callApi('POST', 'login', ['email' => $_POST['email'], 'password' => $_POST['password']]);
    
    if (isset($resData['data']['token'])) {
        session_regenerate_id(true); // Keamanan sesi
        $_SESSION['token'] = $resData['data']['token'];
        $_SESSION['user'] = $resData['data']['user'];
        $_SESSION['role'] = strtolower($resData['data']['user']['role']); // 'admin' atau 'petugas'
        
        session_write_close();
        header("Location: " . $_SESSION['role'] . "/dashboard.php");
        exit;
    } else {
        $pesanGagal = $resData['message'] ?? "Login gagal. Cek kredensial Anda.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - KampusResik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --primary-green: #0d9488; }
        body { background-color: #e6f2f1; font-size: clamp(0.85rem, 1vw, 1rem); }
        .login-card { border-radius: 1.5rem; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .btn-green { background-color: #065f57; color: white; border: none; transition: 0.3s; }
        .btn-green:hover { background-color: #044741; color: white; }
        .form-control { border-radius: 0.75rem; padding: 0.75rem; }
    </style>
</head>
<body>

    <?php include __DIR__ . '/includes/navbarPublic.php'; ?>

    <div class="container d-flex align-items-center justify-content-center min-vh-100 p-3">
        <div class="card login-card p-4 p-md-5 w-100" style="max-width: 450px;">
            <div class="text-center mb-4">
                <h2 class="fw-bold" style="color: var(--primary-green);">Login</h2>
                <p class="text-muted small">Silakan masuk untuk mengelola kebersihan kampus.</p>
            </div>
            
            <?php if ($pesanGagal): ?>
                <div class="alert alert-danger small text-center"><?= htmlspecialchars($pesanGagal) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Email Administrator/Petugas</label>
                    <input type="email" name="email" required class="form-control" placeholder="nama@kampusresik.id">
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-bold">Kata Sandi</label>
                    <input type="password" name="password" required class="form-control" placeholder="••••••••">
                </div>
                <button type="submit" class="btn btn-green w-100 py-2 fw-bold shadow-sm">Masuk &rarr;</button>
            </form>
            
            <div class="text-center mt-4">
                <a href="#" class="text-decoration-none small text-muted">Lupa Sandi?</a>
            </div>
            <hr class="my-4">
            <p class="text-center small text-muted">Bantuan teknis atau kendala akses? <a href="#" class="text-decoration-none">Hubungi IT Support</a></p>
        </div>
    </div>
</body>
</html>