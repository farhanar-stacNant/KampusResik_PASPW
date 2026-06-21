<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$current = basename($_SERVER['PHP_SELF']);
$role = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : ''; 
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
    :root {
        --deepsea-dark: #0f172a;
        --deepsea-medium: #1e293b;
        --teal-primary: #0d9488;
        --sidebar-width: 260px;
    }

    body { margin: 0; font-family: sans-serif; display: flex; transition: all 0.3s ease; }

    #sidebar {
        width: var(--sidebar-width);
        min-width: var(--sidebar-width);
        background: linear-gradient(180deg, var(--deepsea-dark), var(--deepsea-medium));
        color: #fff; height: 100vh; z-index: 1000;
        transition: all 0.3s ease;
        padding-top: 20px;
    }

    #sidebar.active { width: 80px; min-width: 80px; }
    #sidebar.active .hide-text, #sidebar.active .sidebar-header small { display: none !important; }
    #sidebar.active .sidebar-menu ul li a { justify-content: center; padding: 12px 0; }
    #sidebar.active .sidebar-menu ul li a i { margin-right: 0; font-size: 1.5rem; }

    #sidebarCollapseGlobal {
        position: fixed; top: 15px; left: 15px; z-index: 1060;
        background-color: var(--deepsea-dark); color: #fff;
        border-radius: 8px; border: none; padding: 8px 12px; cursor: pointer;
    }

    .sidebar-menu { padding: 20px 0; }
    .sidebar-menu ul { list-style: none; padding: 0; margin: 0; }
    .sidebar-menu ul li a {
        padding: 12px 24px; display: flex; align-items: center;
        color: rgba(255, 255, 255, 0.7); text-decoration: none; border-left: 4px solid transparent;
    }
    .sidebar-menu ul li a:hover, .sidebar-menu ul li.active a { color: #fff; background: rgba(255, 255, 255, 0.05); border-left-color: var(--teal-primary); }
    .sidebar-menu ul li a i { margin-right: 12px; font-size: 1.2rem; }

    @media (max-width: 768px) {
        #sidebar { position: fixed; left: -260px; }
        #sidebar.active { left: 0; }
    }
</style>

<button type="button" id="sidebarCollapseGlobal"><i class="bi bi-list"></i></button>

<nav id="sidebar">
    <div class="sidebar-header d-flex align-items-center gap-3 px-3 mb-4">
        <i class="bi bi-recycle fs-3" style="color: var(--teal-primary);"></i>
        <div class="hide-text">
            <h6 class="mb-0">Kampus Resik</h6>
            <small class="text-white-50"><?= ucfirst($role) ?> Panel</small>
        </div>
    </div>
    
    <div class="sidebar-menu">
        <ul>
            <?php if ($role === 'admin'): ?>
                <li class="<?= ($current == 'dashboard.php') ? 'active' : '' ?>"><a href="dashboard.php"><i class="bi bi-grid-fill"></i> <span class="hide-text">Dashboard</span></a></li>
                <li class="<?= ($current == 'laporan.php') ? 'active' : '' ?>"><a href="laporan.php"><i class="bi bi-file-earmark-text"></i> <span class="hide-text">Laporan Masuk</span></a></li>
                <li class="<?= ($current == 'kategori.php') ? 'active' : '' ?>"><a href="kategori.php"><i class="bi bi-tags"></i> <span class="hide-text">Kategori</span></a></li>
                <li class="<?= ($current == 'proses.php') ? 'active' : '' ?>"><a href="proses.php"><i class="bi bi-gear-wide-connected"></i> <span class="hide-text">Proses</span></a></li>
                <li class="<?= ($current == 'rekap_laporan.php') ? 'active' : '' ?>"><a href="rekap_laporan.php"><i class="bi bi-archive"></i> <span class="hide-text">Rekap Laporan</span></a></li>
                <li class="<?= ($current == 'user_management.php') ? 'active' : '' ?>"><a href="user_management.php"><i class="bi bi-people"></i> <span class="hide-text">User</span></a></li>
                <li class="<?= ($current == 'pengaturan.php') ? 'active' : '' ?>"><a href="pengaturan.php"><i class="bi bi-gear"></i> <span class="hide-text">Pengaturan</span></a></li>
            <?php elseif ($role === 'petugas'): ?>
                <li class="<?= ($current == 'dashboard.php') ? 'active' : '' ?>"><a href="dashboard.php"><i class="bi bi-grid-fill"></i> <span class="hide-text">Dashboard</span></a></li>
                <li class="<?= ($current == 'riwayat.php') ? 'active' : '' ?>"><a href="riwayat.php"><i class="bi bi-clock-history"></i> <span class="hide-text">Riwayat</span></a></li>
                <li class="<?= ($current == 'detail.php') ? 'active' : '' ?>"><a href="detail.php"><i class="bi bi-info-circle"></i> <span class="hide-text">Detail</span></a></li>
                <li class="<?= ($current == 'pengaturan.php') ? 'active' : '' ?>"><a href="pengaturan.php"><i class="bi bi-gear"></i> <span class="hide-text">Pengaturan</span></a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<script>
    const sidebar = document.getElementById('sidebar');
    const btn = document.getElementById('sidebarCollapseGlobal');
    btn.addEventListener('click', () => { sidebar.classList.toggle('active'); });
</script>