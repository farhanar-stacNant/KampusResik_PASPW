<?php
// Header + Sub-nav Icon + Sidebar untuk Petugas
// Require: api-config.php, auth.php
require_once __DIR__ . '/auth.php';

$user = get_user_data();
$nama = $user['nama'] ?? $user['name'] ?? 'Petugas';

$nav_items = [
    ['id' => 'index', 'label' => 'Dashboard', 'icon' => 'bi-speedometer2', 'file' => 'index.php'],
    ['id' => 'detail-laporan', 'label' => 'Detail Laporan', 'icon' => 'bi-clipboard-check', 'file' => 'detail-laporan.php'],
    ['id' => 'riwayat', 'label' => 'Riwayat', 'icon' => 'bi-clock-history', 'file' => 'riwayat.php'],
    ['id' => 'pengaturan', 'label' => 'Pengaturan', 'icon' => 'bi-gear', 'file' => 'pengaturan.php'],
];
$current = current_page();
?>

<!-- Header -->
<header class="petugas-header" id="petugasHeader">
    <div class="header-brand">
        <i class="bi bi-recycle"></i>
        <span>KampusResik</span>
    </div>
    <button class="burger-btn" id="burgerToggle" type="button" aria-label="Menu">
        <i class="bi bi-list" id="burgerIcon"></i>
        <span id="burgerText" class="menu-label">Menu</span>
    </button>
</header>

<!-- Sub-nav Icon -->
<nav class="subnav-icon" id="subnavIcon">
    <?php foreach ($nav_items as $item): 
        $active = ($current === $item['id']) ? 'active' : '';
        $url = base_petugas() . '/' . $item['file'];
    ?>
    <a href="<?= $url ?>" class="subnav-icon-item <?= $active ?>" title="<?= $item['label'] ?>">
        <i class="bi <?= $item['icon'] ?>"></i>
    </a>
    <?php endforeach; ?>
</nav>

<!-- Sidebar Kanan -->
<aside class="petugas-sidebar" id="petugasSidebar">
    <div class="sidebar-content">
        <div class="sidebar-nav">
            <?php foreach ($nav_items as $item): 
                $active = ($current === $item['id']) ? 'active' : '';
                $url = base_petugas() . '/' . $item['file'];
            ?>
            <a href="<?= $url ?>" class="sidebar-item <?= $active ?>">
                <i class="bi <?= $item['icon'] ?>"></i>
                <span><?= $item['label'] ?></span>
            </a>
            <?php endforeach; ?>
        </div>
        <div class="sidebar-footer">
            <div class="user-info">
                <small>Masuk sebagai</small>
                <strong><?= htmlspecialchars($nama) ?></strong>
            </div>
            <a href="<?= base_url() ?>/includes/auth.php?action=logout" class="btn-logout">
                <i class="bi bi-box-arrow-right"></i> Keluar
            </a>
        </div>
    </div>
</aside>