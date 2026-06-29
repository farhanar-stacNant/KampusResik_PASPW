<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - KampusResik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/assets/css/admin.style.css">
    <link rel="icon" type="image/png" href="/images/LOGO.png">
    @stack('styles')
</head>
<body>
    <div class="admin-wrapper">
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <div class="admin-mobile-header">
            <button class="mobile-toggle" id="mobileToggle" aria-label="Toggle sidebar">
                <i class="bi bi-list" style="font-size: 1.6rem;"></i>
            </button>
            <div class="mobile-brand">
                <img src="/images/LOGO.png" alt="KampusResik" height="28">
            </div>
            <div></div>
        </div>

        <aside class="admin-sidebar" id="adminSidebar">
            <button class="sidebar-close-btn" id="sidebarCloseBtn" aria-label="Close sidebar">
                <i class="bi bi-x-lg"></i>
            </button>
            <div class="sidebar-brand">
                <img src="/images/LOGO.png" alt="KampusResik" height="36">
                <div class="brand-text">
                    <span class="brand-title">KampusResik</span>
                    <span class="brand-subtitle">Admin Panel</span>
                </div>
            </div>
            <nav class="sidebar-nav">
                <a href="/admin/dashboard" class="nav-item {{ request()->is('admin/dashboard*') ? 'active' : '' }}">
                    <span class="nav-label">Dashboard</span>
                    <i class="bi bi-grid-1x2 nav-icon"></i>
                </a>
                <a href="/admin/laporan" class="nav-item {{ request()->is('admin/laporan*') ? 'active' : '' }}">
                    <span class="nav-label">Laporan Masuk</span>
                    <i class="bi bi-inbox nav-icon"></i>
                </a>
                <a href="/admin/pemantauan" class="nav-item {{ request()->is('admin/pemantauan*') ? 'active' : '' }}">
                    <span class="nav-label">Pemantauan</span>
                    <i class="bi bi-calendar-check nav-icon"></i>
                </a>
                <a href="/admin/kategori" class="nav-item {{ request()->is('admin/kategori*') ? 'active' : '' }}">
                    <span class="nav-label">Kategori & Petugas</span>
                    <i class="bi bi-tags nav-icon"></i>
                </a>
                <a href="/admin/rekap" class="nav-item {{ request()->is('admin/rekap*') ? 'active' : '' }}">
                    <span class="nav-label">Rekap Laporan</span>
                    <i class="bi bi-file-earmark-bar-graph nav-icon"></i>
                </a>
                <a href="/admin/pengaturan" class="nav-item {{ request()->is('admin/pengaturan*') ? 'active' : '' }}">
                    <span class="nav-label">Pengaturan</span>
                    <i class="bi bi-gear nav-icon"></i>
                </a>
            </nav>
            <div class="sidebar-footer">
                <a href="/admin/pengaturan" class="admin-profile">
                    <div class="profile-avatar">
                        <i class="bi bi-person-fill" id="sidebarAvatarIcon"></i>
                    </div>
                    <div class="profile-info">
                        <span class="profile-name" id="sidebarProfileName">Admin</span>
                        <span class="profile-role">Administrator</span>
                    </div>
                </a>
            </div>
        </aside>

        <main class="admin-main">
            <div id="alertContainer"></div>
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/api.js"></script>
    <script src="/assets/js/auth.js"></script>
    <script src="/assets/js/helpers.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (!Auth.requireAdminOrKoordinator()) return;

            const user = Auth.getUser();
            if (user.name) {
                document.getElementById('sidebarProfileName').textContent = user.name;
            }
            if (user.role) {
                const roleLabel = document.querySelector('.profile-role');
                if (roleLabel) {
                    const labels = { admin: 'Administrator', koordinator: 'Koordinator' };
                    roleLabel.textContent = labels[user.role] || user.role;
                }
            }

            // Hide admin-only nav items for koordinator
            if (Auth.isKoordinator()) {
                document.querySelectorAll('.nav-item').forEach(el => {
                    const href = el.getAttribute('href');
                    if (href && !href.includes('/dashboard') && !href.includes('/rekap') && !href.includes('/pengaturan')) {
                        el.style.display = 'none';
                    }
                });
            }

            const toggle = document.getElementById('mobileToggle');
            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const closeBtn = document.getElementById('sidebarCloseBtn');

            function closeSidebar() {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('show');
            }

            function openSidebar() {
                sidebar.classList.add('mobile-open');
                overlay.classList.add('show');
            }

            if (toggle && sidebar && overlay) {
                toggle.addEventListener('click', function () {
                    if (sidebar.classList.contains('mobile-open')) {
                        closeSidebar();
                    } else {
                        openSidebar();
                    }
                });
                overlay.addEventListener('click', closeSidebar);
                if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
            }

            // Close sidebar on nav click (mobile)
            document.querySelectorAll('.nav-item').forEach(item => {
                item.addEventListener('click', function () {
                    if (window.innerWidth < 992) closeSidebar();
                });
            });
        });

        window.logoutAdmin = async function () {
            const res = await API.post('/auth/logout');
            Auth.logout();
        };
    </script>
    @stack('scripts')
</body>
</html>
