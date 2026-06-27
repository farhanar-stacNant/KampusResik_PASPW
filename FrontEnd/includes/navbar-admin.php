<?php
// Sidebar Admin (Kiri, Permanen)
// Require: api-config.php, auth.php
require_once __DIR__ . '/auth.php';

$user = get_user_data();
$nama = $user['nama'] ?? $user['name'] ?? 'Admin';

$nav_items = [
    ['id' => 'index', 'label' => 'Dashboard', 'icon' => 'bi-speedometer2', 'file' => 'index.php'],
    ['id' => 'manajemen-laporan', 'label' => 'Laporan Masuk', 'icon' => 'bi-clipboard-data', 'file' => 'manajemen-laporan.php'],
    ['id' => 'pemantauan-penugasan', 'label' => 'Pemantauan', 'icon' => 'bi-calendar-week', 'file' => 'pemantauan-penugasan.php'],
    ['id' => 'manajemen-kategori', 'label' => 'Kategori & Petugas', 'icon' => 'bi-folder-plus', 'file' => 'manajemen-kategori.php'],
    ['id' => 'rekap-laporan', 'label' => 'Rekap Laporan', 'icon' => 'bi-journal-text', 'file' => 'rekap-laporan.php'],
    ['id' => 'pengaturan', 'label' => 'Pengaturan', 'icon' => 'bi-gear', 'file' => 'pengaturan.php'],
];
$current = current_page();
?>

<!-- Mobile Header -->
<header class="admin-mobile-header">
    <button class="mobile-toggle" id="sidebarToggle" type="button">
        <i class="bi bi-list"></i>
    </button>
    <span class="mobile-brand"><i class="bi bi-recycle"></i> KampusResik</span>
</header>

<!-- Sidebar Kiri -->
<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-brand">
        <i class="bi bi-recycle"></i>
        <div class="brand-text">
            <span class="brand-title">Kampus Resik</span>
            <span class="brand-subtitle">Admin Panel</span>
        </div>
    </div>
    
    <nav class="sidebar-nav">
        <?php foreach ($nav_items as $item): 
            $active = ($current === $item['id']) ? 'active' : '';
            $url = base_admin() . '/' . $item['file'];
        ?>
        <a href="<?= $url ?>" class="nav-item <?= $active ?>">
            <span class="nav-label"><?= $item['label'] ?></span>
            <i class="bi <?= $item['icon'] ?> nav-icon"></i>
        </a>
        <?php endforeach; ?>
    </nav>
    
    <div class="sidebar-footer">
        <a href="<?= base_admin() ?>/pengaturan.php" class="admin-profile">
            <div class="profile-avatar">
                <i class="bi bi-person-circle"></i>
            </div>
            <div class="profile-info">
                <span class="profile-name"><?= htmlspecialchars($nama) ?></span>
                <span class="profile-role">Koordinator</span>
            </div>
        </a>
    </div>
</aside>

<!-- Overlay Mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>