<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - KampusResik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../Assets/css/public.style.css">
    <link rel="icon" type="image/png" href="../Assets/images/LOGO.png">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-public">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">
            <img src="../Assets/images/LOGO.png" alt="KampusResik" height="36">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarPublic" aria-controls="navbarPublic" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarPublic">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="laporan.php">Laporan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pengaduan.php">Pengaduan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="status.php">Status</a>
                </li>

            </ul>
        </div>
    </div>
</nav>

<!-- HERO SECTION -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <h1 class="hero-title mb-4">
                    Selamat Datang, Di<br>
                    <span class="highlight">Kampus Resik!</span>
                </h1>
                <p class="hero-subtitle mb-4">
                    Pantau perkembangan kebersihan lingkungan kampus dan bagikan inspirasi berkelanjutan dari hari-hari. 
                    Kelola laporan sampah dan jaga lingkungan kampus tetap bersih.
                </p>
                <div class="hero-buttons">
                    <a href="pengaduan.php" class="btn-hero-primary">
                        <i class="bi bi-plus-lg"></i> Buat Laporan
                    </a>
                    <a href="laporan.php" class="btn-hero-outline">
                        <i class="bi bi-eye"></i> Lihat Laporan
                    </a>
                    <a href="status.php" class="btn-hero-dark">
                        <i class="bi bi-clipboard-check"></i> Status Laporan
                    </a>
                </div>
            </div>
            
            <div class="col-lg-5 mt-5 mt-lg-0">
                <div class="row g-3" id="heroStats">
                    <div class="col-6">
                        <div class="hero-stat-card">
                            <i class="bi bi-file-earmark-text hero-stat-icon text-success"></i>
                            <div class="hero-stat-value"><span id="statMingguIni">0</span></div>
                            <div class="hero-stat-label">Laporan Minggu Ini</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="hero-stat-card">
                            <i class="bi bi-check-circle hero-stat-icon text-success"></i>
                            <div class="hero-stat-value"><span id="statSelesai">0</span></div>
                            <div class="hero-stat-label">Laporan Selesai</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="hero-stat-card">
                            <div class="hero-stat-value text-success"><span id="statBaru">0</span></div>
                            <div class="hero-stat-label">Laporan Baru</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- STATISTIK KATEGORI -->
<section class="container py-4">
    <h3 class="fw-bold mb-2">Kategori Sampah</h3>
    <p class="text-muted mb-4">Kategori sampah yang tersedia untuk pelaporan</p>
    
    <div class="row g-4" id="kategoriContainer">
        <div class="col-12 text-center py-3">
            <p class="text-muted">Memuat data...</p>
        </div>
    </div>
</section>

<!-- LAPORAN TERBARU -->
<section class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">Laporan Terbaru</h3>
        <a href="laporan.php" class="btn btn-outline-success btn-sm">
            Lihat Semua <i class="bi bi-arrow-right"></i>
        </a>
    </div>
    
    <div class="row g-4" id="recentReports">
        <div class="col-12">
            <div class="text-center py-5">
                <div class="spinner-border text-success" role="status"></div>
                <p class="mt-3 text-muted">Memuat laporan terbaru...</p>
            </div>
        </div>
    </div>
</section>

<!-- CARA PENGGUNAAN -->
<section class="guide-section">
    <div class="container">
        <h3 class="text-center fw-bold mb-5">Cara Menggunakan KampusResik</h3>
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="p-3">
                    <div class="guide-icon-wrapper">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                    <h5 class="guide-title">1. Buat Laporan</h5>
                    <p class="guide-desc">Isi formulir dengan detail masalah kebersihan yang kamu temui di kampus.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3">
                    <div class="guide-icon-wrapper">
                        <i class="bi bi-search"></i>
                    </div>
                    <h5 class="guide-title">2. Pantau Progress</h5>
                    <p class="guide-desc">Tim kampus akan memproses laporanmu. Pantau statusnya secara real-time.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3">
                    <div class="guide-icon-wrapper">
                        <i class="bi bi-check-lg"></i>
                    </div>
                    <h5 class="guide-title">3. Selesai</h5>
                    <p class="guide-desc">Laporan terverifikasi selesai. Kampus menjadi lebih bersih dan nyaman!</p>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="footer-public mt-auto">
    <div class="container-fluid px-3 px-md-4">
        <div class="row align-items-center">
            <div class="col-md-6 mb-2 mb-md-0">
                <span class="footer-brand"><img src="../Assets/images/LOGO.png" alt="" height="24" style="margin-right:8px;vertical-align:middle"> KampusResik</span>
                <p class="footer-text mb-0">Pantau kebersihan kampus bersama.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="index.php" class="footer-link">Beranda</a>
                <a href="laporan.php" class="footer-link">Laporan</a>
                <a href="pengaduan.php" class="footer-link">Pengaduan</a>
            </div>
        </div>
        <hr style="border-color: rgba(255,255,255,0.1); margin: 1rem 0;">
        <div class="text-center">
            <small class="footer-text">&copy; 2026 KampusResik. All rights reserved.</small>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../Assets/js/api.js?v=1.1"></script>
<script src="../Assets/js/helpers.js"></script>
<script src="../Assets/js/public.main.js"></script>
<script>
document.addEventListener('DOMContentLoaded', async function() {
    try {
        const statRes = await API.get('/statistik');
        if (statRes.ok && statRes.data) {
            const s = statRes.data.data || statRes.data;
            document.getElementById('statMingguIni').textContent = (s.laporan_minggu_ini || 0).toLocaleString();
            document.getElementById('statSelesai').textContent = (s.laporan_selesai || 0).toLocaleString();
            document.getElementById('statBaru').textContent = (s.laporan_baru || 0).toLocaleString();
        }
    } catch (e) {
        console.error('Gagal muat statistik', e);
    }

    try {
        const katRes = await API.get('/kategori-sampah');
        const container = document.getElementById('kategoriContainer');
        if (katRes.ok && katRes.data) {
            const list = katRes.data.data || katRes.data;
            if (Array.isArray(list) && list.length > 0) {
                container.innerHTML = list.map(kat => {
                    const risiko = kat.level_risiko || 'rendah';
                    const badgeRisiko = risiko === 'tinggi' ? 'bg-danger' : risiko === 'sedang' ? 'bg-warning text-dark' : 'bg-success';
                    return `<div class="col-md-4">
                        <div class="distribusi-card">
                            <span class="distribusi-badge ${badgeRisiko}">${Helpers.escapeHtml(kat.nama_kategori)}</span>
                            <div class="distribusi-value">${risiko.charAt(0).toUpperCase() + risiko.slice(1)}</div>
                            <div class="distribusi-label">Risiko Kesehatan</div>
                        </div>
                    </div>`;
                }).join('');
            } else {
                container.innerHTML = '<div class="col-12 text-center py-3"><p class="text-muted">Belum ada kategori tersedia.</p></div>';
            }
        } else {
            container.innerHTML = '<div class="col-12 text-center py-3"><p class="text-muted">Gagal memuat kategori.</p></div>';
        }
    } catch (e) {
        document.getElementById('kategoriContainer').innerHTML = '<div class="col-12 text-center py-3"><p class="text-muted">Gagal memuat kategori.</p></div>';
    }

    try {
        const repRes = await API.get('/laporan-sampah?limit=6');
        const container = document.getElementById('recentReports');
        if (repRes.ok && repRes.data) {
            const list = repRes.data.data || repRes.data;
            if (Array.isArray(list) && list.length > 0) {
                container.innerHTML = list.map(r => {
                    const status = r.status || 'baru';
                    const badgeClass = status === 'selesai' ? 'selesai' : status === 'diproses' ? 'diproses' : status === 'ditolak' ? 'ditolak' : 'menunggu';
                    const katNama = r.kategori ? (r.kategori.nama_kategori || 'Lainnya') : 'Lainnya';
                    const desc = r.deskripsi ? (r.deskripsi.length > 100 ? r.deskripsi.substring(0, 100) + '...' : r.deskripsi) : '-';
                    return `<div class="col-md-6 col-lg-4">
                        <div class="laporan-card">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="laporan-badge ${badgeClass}">${status.charAt(0).toUpperCase() + status.slice(1)}</span>
                                    <small class="text-muted">${Helpers.escapeHtml(r.created_at || '-')}</small>
                                </div>
                                <h5 class="laporan-title">${Helpers.escapeHtml(r.nama_pelapor || 'Anonymous')}</h5>
                                <p class="laporan-desc">${Helpers.escapeHtml(desc)}</p>
                                <div class="laporan-meta">
                                    <span class="laporan-location">
                                        <i class="bi bi-geo-alt"></i> ${Helpers.escapeHtml(r.lokasi || 'Kampus')}
                                    </span>
                                    <a href="detail.php?kode=${encodeURIComponent(r.kode_laporan || '')}" class="btn-detail">Detail</a>
                                </div>
                            </div>
                        </div>
                    </div>`;
                }).join('');
            } else {
                container.innerHTML = '<div class="col-12"><div class="text-center py-5"><i class="bi bi-inbox fs-1 text-muted"></i><p class="mt-3 text-muted">Belum ada laporan atau API sedang tidak dapat diakses.</p></div></div>';
            }
        } else {
            container.innerHTML = '<div class="col-12"><div class="text-center py-5"><i class="bi bi-inbox fs-1 text-muted"></i><p class="mt-3 text-muted">Gagal memuat laporan.</p></div></div>';
        }
    } catch (e) {
        document.getElementById('recentReports').innerHTML = '<div class="col-12"><div class="text-center py-5"><i class="bi bi-inbox fs-1 text-muted"></i><p class="mt-3 text-muted">Gagal memuat laporan.</p></div></div>';
    }
});
</script>
</body>
</html>
