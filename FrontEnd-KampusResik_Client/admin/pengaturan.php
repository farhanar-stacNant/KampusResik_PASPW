<?php session_start(); 
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['token'])) {
    header('Location: ../login.php');
    exit;
}

if (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin') {
    header("Location: ../" . $_SESSION['role'] . "/dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Admin - KampusResik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .settings-card { border-radius: 1rem; border: none; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/navbarPrivate.php'; ?>
    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h3 fw-bold mb-0">Pengaturan Admin</h2>
                    <a href="../logout.php" class="btn btn-danger btn-sm">Logout</a>
                </div>

                <div class="card settings-card p-4 p-md-5">
                    <h4 class="fw-bold mb-4">Pengaturan Profil</h4>
                    <form action="proses_pengaturan.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap</label>
                            <input type="text" name="name" value="<?= htmlspecialchars($_SESSION['user']['name'] ?? '') ?>" class="form-control py-2">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Password Baru</label>
                            <input type="password" name="password" placeholder="Kosongkan jika tidak ingin diubah" class="form-control py-2">
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold" style="background-color: #0d9488; border: none;">
                            Simpan Perubahan
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>