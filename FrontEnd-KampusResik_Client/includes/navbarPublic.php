<style>
    :root {
        --nav-font-size: calc(0.9rem + 0.3vw); 
    }
    .navbar { transition: background 0.3s ease; }
    .nav-link { 
        font-size: var(--nav-font-size) !important;
        color: rgba(255, 255, 255, 0.85) !important;
        transition: all 0.2s ease-in-out;
        padding: 0.5rem 1rem !important;
    }
    .nav-link:hover, .nav-link.active { 
        color: #ffffff !important; 
        font-weight: 600;
    }
    @media (min-width: 992px) { .nav-link.active { border-bottom: 2px solid #ffffff; }}
    .login-link { font-size: calc(var(--nav-font-size) * 0.9) !important; }
    .bg-grad-nav { background: linear-gradient(90deg, #0d9488, #0891b2); }
    .navbar-collapse {
        background: linear-gradient(90deg, #0d9488, #0891b2);
        padding: 10px;
        border-radius: 0 0 8px 8px;
    }
</style>

<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$current = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm mb-4 bg-grad-nav">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold" href="index.php" style="font-size: calc(var(--nav-font-size) * 1.2);">KampusResik</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link <?= ($current == 'index.php') ? 'active' : '' ?>" href="index.php">Beranda</a></li>
                <li class="nav-item"><a class="nav-link <?= ($current == 'status.php') ? 'active' : '' ?>" href="status.php">Status</a></li>
                <li class="nav-item"><a class="nav-link <?= ($current == 'laporan.php') ? 'active' : '' ?>" href="laporan.php">Laporan</a></li>
                <li class="nav-item"><a class="nav-link <?= ($current == 'pengaduan.php') ? 'active' : '' ?>" href="pengaduan.php">Pengaduan</a></li>
            </ul>

            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link login-link <?= ($current == 'login.php') ? 'active' : '' ?>" href="login.php">Login Admin/Petugas</a>
                </li>
            </ul>
        </div>
    </div>
</nav>