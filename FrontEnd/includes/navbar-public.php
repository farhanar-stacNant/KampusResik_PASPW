<nav class="navbar navbar-expand-lg navbar-public">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">
            <i class="bi bi-recycle"></i> KampusResik
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarPublic" aria-controls="navbarPublic" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarPublic">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link <?= ($pageTitle ?? '') === 'Beranda' ? 'active' : '' ?>" href="index.php">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($pageTitle ?? '') === 'Laporan' ? 'active' : '' ?>" href="laporan.php">Laporan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($pageTitle ?? '') === 'Pengaduan' ? 'active' : '' ?>" href="pengaduan.php">Pengaduan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($pageTitle ?? '') === 'Status' ? 'active' : '' ?>" href="status.php">Status</a>
                </li>
                <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                    <a href="../login.php" class="btn-login-nav">Login</a>
                </li>
            </ul>
        </div>
    </div>
</nav>