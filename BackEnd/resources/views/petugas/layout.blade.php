<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Petugas Panel') - KampusResik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/assets/css/petugas.style.css">
    <link rel="icon" type="image/png" href="/images/LOGO.png">
    @stack('styles')
</head>
<body>
    <div id="alertContainer"></div>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <header class="petugas-header">
        <div class="header-brand">
            <img src="/images/LOGO.png" alt="KampusResik" height="32">
        </div>
        <button class="burger-btn" id="burgerToggle" aria-label="Toggle menu">
            <i class="bi bi-list" id="burgerIcon"></i>
            <span class="menu-label d-none" id="burgerText">Tutup</span>
        </button>
    </header>

    <aside class="petugas-sidebar" id="petugasSidebar">
        <div class="sidebar-content">
            <nav class="sidebar-nav">
                <a href="/petugas/dashboard" class="sidebar-item {{ request()->is('petugas/dashboard*') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2"></i> Dashboard
                </a>
                <a href="/petugas/laporan" class="sidebar-item {{ request()->is('petugas/laporan*') ? 'active' : '' }}">
                    <i class="bi bi-clipboard-check"></i> Detail Laporan
                </a>
                <a href="/petugas/riwayat" class="sidebar-item {{ request()->is('petugas/riwayat*') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i> Riwayat
                </a>
                <a href="/petugas/pengaturan" class="sidebar-item {{ request()->is('petugas/pengaturan*') ? 'active' : '' }}">
                    <i class="bi bi-gear"></i> Pengaturan
                </a>
            </nav>
            <div class="sidebar-footer">
                <div class="user-info">
                    <small>Login sebagai</small>
                    <strong id="sidebarUserName">Petugas</strong>
                </div>
                <button class="btn-logout" id="btnLogout"><i class="bi bi-box-arrow-right"></i> Keluar</button>
            </div>
        </div>
    </aside>

    <main class="main-content">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/api.js"></script>
    <script src="/assets/js/auth.js"></script>
    <script src="/assets/js/helpers.js"></script>
    <script src="/assets/js/petugas-main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (!Auth.requirePetugas()) return;

            const user = Auth.getUser();
            if (user.name || user.nama) {
                document.getElementById('sidebarUserName').textContent = user.name || user.nama;
            }

            const btnLogout = document.getElementById('btnLogout');
            if (btnLogout) {
                btnLogout.addEventListener('click', async function () {
                    if (!confirm('Apakah Anda yakin ingin keluar?')) return;
                    await API.post('/auth/logout');
                    Auth.logout();
                });
            }

            let touchStartX = 0;
            let touchEndX = 0;
            const sidebar = document.getElementById('petugasSidebar');
            const overlay = document.getElementById('sidebarOverlay');

            document.addEventListener('touchstart', function (e) {
                touchStartX = e.changedTouches[0].screenX;
            }, { passive: true });

            document.addEventListener('touchend', function (e) {
                touchEndX = e.changedTouches[0].screenX;
                const swipeDistance = touchStartX - touchEndX;
                if (Math.abs(swipeDistance) > 80) {
                    if (swipeDistance > 0 && sidebar.classList.contains('open')) {
                        sidebar.classList.remove('open');
                        overlay.classList.remove('show');
                        document.querySelector('.main-content')?.classList.remove('sidebar-open');
                    } else if (swipeDistance < 0 && !sidebar.classList.contains('open')) {
                        sidebar.classList.add('open');
                        overlay.classList.add('show');
                        document.querySelector('.main-content')?.classList.add('sidebar-open');
                    }
                }
            }, { passive: true });
        });
    </script>
    @stack('scripts')
</body>
</html>
