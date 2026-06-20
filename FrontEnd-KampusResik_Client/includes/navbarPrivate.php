<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Mendapatkan nama file saat ini
$current = basename($_SERVER['PHP_SELF']);
$role = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : ''; 
$nama_user = $_SESSION['user']['name'] ?? 'Pengguna';

// Tentukan base path untuk link agar tidak error saat di-include di folder berbeda
$base_path = ($role === 'admin') ? '../admin/' : '../petugas/';
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
    :root {
        --deepsea-dark: #0f172a;
        --deepsea-medium: #1e293b;
        --teal-primary: #0d9488;
        --body-bg: #f8fafc;
    }

    body { margin: 0; overflow-x: hidden; background-color: var(--body-bg); font-family: sans-serif; }

    /* Sidebar */
    #sidebar {
        min-width: 260px; max-width: 260px;
        background: linear-gradient(180deg, var(--deepsea-dark), var(--deepsea-medium));
        color: #fff; transition: all 0.3s ease;
        position: fixed; top: 0; left: 0; height: 100vh; z-index: 1000;
        margin-left: -260px; padding-top: 80px;
    }
    #sidebar.active { margin-left: 0; }

    /* Burger */
    #sidebarCollapseGlobal {
        position: fixed; top: 20px; left: 20px; z-index: 1060;
        background-color: var(--deepsea-dark) !important;
        color: #ffffff; border-radius: 12px !important;
        border: 1px solid rgba(255,255,255,0.1);
        width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;
    }

    /* Menu Styling */
    .sidebar-menu { padding: 20px 0; flex-grow: 1; }
    .sidebar-menu ul { list-style: none; padding: 0; margin: 0; }
    .sidebar-menu ul li a {
        padding: 12px 24px; display: flex; align-items: center;
        color: rgba(255, 255, 255, 0.7); text-decoration: none;
        border-left: 4px solid transparent; transition: all 0.3s;
    }
    .sidebar-menu ul li a:hover, .sidebar-menu ul li.active a {
        color: #fff; background: rgba(255, 255, 255, 0.05); border-left-color: var(--teal-primary);
    }
    .sidebar-menu ul li a i { margin-right: 12px; }

    @media (min-width: 769px) {
        #sidebar { margin-left: 0; }
        #sidebarCollapseGlobal { display: none !important; }
    }
</style>

<button type="button" id="sidebarCollapseGlobal" class="btn shadow d-md-none">
    <i class="bi bi-list fs-4"></i>
</button>

<nav id="sidebar">
    <div>
        <div class="sidebar-header d-flex align-items-center gap-3 px-3">
            <div class="p-2 rounded-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background-color: rgba(13, 148, 136, 0.2);">
                <i class="bi bi-recycle fs-4" style="color: var(--teal-primary);"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-0 text-white">Kampus Resik</h6>
                <small class="text-white-50" style="font-size: 0.75rem;"><?= ucfirst($role) ?> Panel</small>
            </div>
        </div>
        
        <div class="sidebar-menu">
            <ul>
                <?php if ($role === 'admin'): ?>
                    <li class="<?= ($current == 'dashboard.php') ? 'active' : '' ?>"><a href="dashboard.php"><i class="bi bi-grid-fill"></i>Dashboard</a></li>
                    <li class="<?= ($current == 'laporan.php') ? 'active' : '' ?>"><a href="laporan.php"><i class="bi bi-file-earmark-text"></i>Laporan Masuk</a></li>
                    <li class="<?= ($current == 'kategori.php') ? 'active' : '' ?>"><a href="kategori.php"><i class="bi bi-tags"></i>Kategori</a></li>
                    <li class="<?= ($current == 'proses.php') ? 'active' : '' ?>"><a href="proses.php"><i class="bi bi-gear-wide-connected"></i>Proses</a></li>
                    <li class="<?= ($current == 'rekap_laporan.php') ? 'active' : '' ?>"><a href="rekap_laporan.php"><i class="bi bi-archive"></i>Rekap Laporan</a></li>
                    <li class="<?= ($current == 'user_management.php') ? 'active' : '' ?>"><a href="user_management.php"><i class="bi bi-people"></i>User</a></li>
                    <li class="<?= ($current == 'pengaturan.php') ? 'active' : '' ?>"><a href="pengaturan.php"><i class="bi bi-gear"></i>Pengaturan</a></li>
                <?php elseif ($role === 'petugas'): ?>
                    <li class="<?= ($current == 'dashboard.php') ? 'active' : '' ?>"><a href="dashboard.php"><i class="bi bi-grid-fill"></i>Dashboard</a></li>
                    <li class="<?= ($current == 'riwayat.php') ? 'active' : '' ?>"><a href="riwayat.php"><i class="bi bi-clock-history"></i>Riwayat</a></li>
                    <li class="<?= ($current == 'detail.php') ? 'active' : '' ?>"><a href="detail.php"><i class="bi bi-info-circle"></i>Detail</a></li>
                    <li class="<?= ($current == 'pengaturan.php') ? 'active' : '' ?>"><a href="pengaturan.php"><i class="bi bi-gear"></i>Pengaturan</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    
    <div class="sidebar-footer p-3">
        <a href="../pengaduan.php" class="btn text-white w-100 py-2 mb-3 fw-semibold" style="background-color: var(--teal-primary); border-radius: 8px;">
            <i class="bi bi-plus-lg"></i> Laporan Baru
        </a>
        <form action="../logout.php" method="POST" class="m-0">
            <button type="submit" class="btn btn-link text-muted p-0 text-decoration-none d-flex align-items-center gap-2 small">
                <i class="bi bi-box-arrow-left"></i> Keluar
            </button>
        </form>
    </div>
</nav>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const sidebar = document.getElementById('sidebar');
        const collapseBtn = document.getElementById('sidebarCollapseGlobal');
        collapseBtn.addEventListener('click', (e) => { e.stopPropagation(); sidebar.classList.toggle('active'); });
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768 && !sidebar.contains(e.target) && !collapseBtn.contains(e.target)) sidebar.classList.remove('active');
        });
    });
</script>