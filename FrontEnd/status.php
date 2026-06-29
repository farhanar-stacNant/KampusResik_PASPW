<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status - KampusResik</title>
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
                    <a class="nav-link" href="index.php">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="laporan.php">Laporan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pengaduan.php">Pengaduan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="status.php">Status</a>
                </li>

            </ul>
        </div>
    </div>
</nav>

<!-- Page Header -->
<section class="page-header py-4">
    <div class="container-fluid px-3 px-md-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="fw-bold mb-1">Status Laporan Anda</h2>
                <p class="text-muted mb-0">Pantau perkembangan laporan kebersihan yang telah Anda sampaikan.</p>
            </div>
            <div class="col-md-4 mt-3 mt-md-0">
                <div class="input-group">
                    <input type="text" class="form-control" id="searchKode" placeholder="Cari berdasarkan kode laporan..." onkeydown="if(event.key==='Enter') searchByKode()">
                    <button class="btn btn-success" onclick="searchByKode()"><i class="bi bi-search"></i></button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistik -->
<section class="pb-4">
    <div class="container-fluid px-3 px-md-4">
        <div class="row g-3" id="statsRow">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <i class="bi bi-folder stat-icon text-primary"></i>
                            <h3 class="stat-value mb-0" id="statTotal">0</h3>
                            <small class="stat-label">Laporan Dikirim</small>
                        </div>
                        <span class="badge bg-light text-dark">Total</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <i class="bi bi-clock stat-icon text-warning"></i>
                            <h3 class="stat-value mb-0" id="statAktif">00</h3>
                            <small class="stat-label">Dalam Proses</small>
                        </div>
                        <span class="badge bg-warning text-dark">Aktif</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card bg-success text-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <i class="bi bi-check-circle stat-icon text-white-50"></i>
                            <h3 class="stat-value mb-0 text-white" id="statSelesai">00</h3>
                            <small class="stat-label text-white-75">Masalah Teratasi</small>
                        </div>
                        <span class="badge bg-white text-success">Selesai</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Daftar Laporan -->
<section class="pb-5">
    <div class="container-fluid px-3 px-md-4">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Daftar Laporan Terkini</h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-secondary" onclick="loadAll()">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                </div>
                <div id="alertContainer"></div>
                <div class="laporan-list" id="laporanList">
                    <div class="text-center py-5">
                        <div class="spinner-border text-success" role="status"></div>
                        <p class="mt-3 text-muted">Memuat laporan...</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="sticky-top" style="top: 80px; z-index: 1;">
                    <div class="card-clean p-4 mb-4">
                        <h6 class="fw-bold mb-3">Timeline Terakhir</h6>
                        <div class="timeline" id="timelineContainer">
                            <div class="text-muted text-center py-3">
                                <i class="bi bi-clock-history fs-4"></i>
                                <p class="small mb-0 mt-2">Klik laporan untuk melihat timeline</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-clean p-4 bg-success text-white">
                        <h6 class="fw-bold mb-2">Bantu Kampus Tetap Resik!</h6>
                        <p class="small mb-3 opacity-75">Laporan Anda sangat berarti bagi kenyamanan bersama. Terus berikan kontribusi positif.</p>
                        <a href="pengaduan.php" class="btn btn-light btn-sm w-100">
                            <i class="bi bi-plus-lg"></i> Buat Laporan Baru
                        </a>
                    </div>
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
<script>
const statusConfig = {
    'baru': { label: 'Baru', badge: 'bg-secondary', icon: 'bi-send', progress: 25 },
    'diproses': { label: 'Diproses', badge: 'bg-warning', icon: 'bi-arrow-repeat', progress: 50 },
    'selesai': { label: 'Selesai', badge: 'bg-success', icon: 'bi-check-lg', progress: 100 },
    'ditolak': { label: 'Ditolak', badge: 'bg-danger', icon: 'bi-x-circle', progress: 0 }
};
let allReports = [];

function updateStats(list) {
    const total = list.length;
    const aktif = list.filter(r => ['baru', 'diproses'].includes(r.status || '')).length;
    const selesai = list.filter(r => (r.status || '') === 'selesai').length;
    document.getElementById('statTotal').textContent = total;
    document.getElementById('statAktif').textContent = String(aktif).padStart(2, '0');
    document.getElementById('statSelesai').textContent = String(selesai).padStart(2, '0');
}

function renderLaporanList(list) {
    const container = document.getElementById('laporanList');
    allReports = list;
    updateStats(list);
    if (list.length === 0) {
        container.innerHTML = '<div class="text-center py-5"><i class="bi bi-inbox fs-1 text-muted"></i><p class="mt-3 text-muted">Belum ada laporan. <a href="pengaduan.php">Buat laporan baru</a></p></div>';
        return;
    }
    container.innerHTML = list.map(r => {
        const st = r.status || 'dikirim';
        const cfg = statusConfig[st] || statusConfig['baru'];
        const progress = cfg.progress;
        const fotoSrc = r.foto || '../Assets/images/LOGO.png';
        return `<div class="laporan-item" data-kode="${Helpers.escapeHtml(r.kode_laporan || '')}" onclick="showDetail('${Helpers.escapeHtml(r.kode_laporan || '')}')">
            <div class="row g-3 align-items-center">
                <div class="col-auto">
                    <div class="laporan-thumb">
                        <img src="${fotoSrc}" alt="Foto" onerror="this.src='../Assets/images/LOGO.png'">
                    </div>
                </div>
                <div class="col">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <h6 class="laporan-title mb-0">${Helpers.escapeHtml(r.nama_pelapor || 'Anonymous')}</h6>
                        <span class="badge ${cfg.badge} badge-status"><i class="bi ${cfg.icon}"></i> ${cfg.label}</span>
                    </div>
                    <p class="laporan-lokasi mb-2"><i class="bi bi-geo-alt"></i> ${Helpers.escapeHtml(r.lokasi || 'Kampus')}</p>
                    ${st !== 'ditolak' ? `
                    <div class="progress-track">
                        <div class="progress-step ${progress >= 25 ? 'active' : ''}"><span class="step-dot"></span><small>Baru</small></div>
                        <div class="progress-step ${progress >= 50 ? 'active' : ''}"><span class="step-dot"></span><small>Diproses</small></div>
                        <div class="progress-step ${progress >= 100 ? 'active' : ''}"><span class="step-dot"></span><small>Selesai</small></div>
                    </div>` : `<div class="text-danger small"><i class="bi bi-x-circle"></i> Laporan ditolak</div>`}
                </div>
                <div class="col-auto">
                    <a href="detail.php?kode=${encodeURIComponent(r.kode_laporan || '')}" class="btn btn-sm btn-link text-success text-decoration-none" onclick="event.stopPropagation();">Lihat Detail <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>`;
    }).join('');
}

async function loadAll() {
    const container = document.getElementById('laporanList');
    container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-success" role="status"></div><p class="mt-3 text-muted">Memuat laporan...</p></div>';
    try {
        const res = await API.get('/laporan-sampah?limit=50');
        if (res.ok && res.data) {
            const list = res.data.data || res.data;
            renderLaporanList(Array.isArray(list) ? list : []);
        } else {
            renderLaporanList([]);
        }
    } catch (e) {
        container.innerHTML = '<div class="text-center py-5"><i class="bi bi-exclamation-triangle fs-1 text-muted"></i><p class="mt-3 text-muted">Gagal memuat laporan.</p></div>';
    }
}

async function searchByKode() {
    const kode = document.getElementById('searchKode').value.trim();
    const container = document.getElementById('laporanList');
    if (!kode) { loadAll(); return; }
    container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-success" role="status"></div><p class="mt-3 text-muted">Mencari laporan...</p></div>';
    try {
        const res = await API.get('/laporan-sampah/' + encodeURIComponent(kode));
        if (res.ok && res.data && res.data.success && res.data.data) {
            renderLaporanList([res.data.data]);
        } else {
            container.innerHTML = '<div class="text-center py-5"><i class="bi bi-exclamation-circle fs-1 text-muted"></i><p class="mt-3 text-muted">Laporan tidak ditemukan.</p></div>';
            document.getElementById('statTotal').textContent = '0';
            document.getElementById('statAktif').textContent = '00';
            document.getElementById('statSelesai').textContent = '00';
        }
    } catch (e) {
        container.innerHTML = '<div class="text-center py-5"><i class="bi bi-exclamation-circle fs-1 text-muted"></i><p class="mt-3 text-muted">Gagal mencari laporan.</p></div>';
    }
}

async function showDetail(kode) {
    if (!kode) return;
    const timelineContainer = document.getElementById('timelineContainer');
    timelineContainer.innerHTML = '<div class="text-center py-3"><div class="spinner-border spinner-border-sm text-success"></div><p class="small mb-0 mt-2">Memuat...</p></div>';
    try {
        const res = await API.get('/laporan-sampah/' + encodeURIComponent(kode));
        if (res.ok && res.data && res.data.success && res.data.data) {
            const report = res.data.data;
            const timeline = report.timeline || [];
            if (timeline.length > 0) {
                let html = '';
                timeline.forEach((item, index) => {
                    const isLast = index === timeline.length - 1;
                    const statusLabel = item.status.charAt(0).toUpperCase() + item.status.slice(1);
                    const iconMap = { 'baru': 'bi-send', 'diproses': 'bi-arrow-repeat', 'selesai': 'bi-check-lg', 'ditolak': 'bi-x-circle' };
                    const iconClass = iconMap[item.status] || 'bi-circle';
                    html += `<div class="timeline-item ${isLast ? 'active' : ''}">
                        <div class="timeline-icon"><i class="bi ${iconClass}"></i></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">${statusLabel}</h6>
                            <p class="small text-muted mb-1">${Helpers.escapeHtml(item.deskripsi || '')}</p>
                            <small class="text-muted">${Helpers.escapeHtml(item.waktu || '')}</small>
                        </div>
                    </div>`;
                });
                timelineContainer.innerHTML = html;
            } else {
                timelineContainer.innerHTML = '<div class="text-muted text-center py-3"><p class="small mb-0">Belum ada timeline</p></div>';
            }
        } else {
            timelineContainer.innerHTML = '<div class="text-muted text-center py-3"><p class="small mb-0 text-danger">Gagal memuat timeline</p></div>';
        }
    } catch (e) {
        timelineContainer.innerHTML = '<div class="text-muted text-center py-3"><p class="small mb-0 text-danger">Gagal memuat timeline</p></div>';
    }
}

document.addEventListener('DOMContentLoaded', loadAll);
</script>
</body>
</html>
