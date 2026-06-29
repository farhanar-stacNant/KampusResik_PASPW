<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - KampusResik</title>
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
                    <a class="nav-link active" href="laporan.php">Laporan</a>
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

<!-- Page Header -->
<section class="page-header py-4">
    <div class="container-fluid px-3 px-md-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="fw-bold mb-1">Laporan Publik</h2>
                <p class="text-muted mb-0">Transparansi pengelolaan kebersihan kampus. Lihat semua laporan yang masuk dan pantau perkembangannya secara langsung.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="pengaduan.php" class="btn-lapor-nav">
                    <i class="bi bi-plus-lg"></i> Buat Laporan Baru
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Filter & Search Section -->
<section class="pb-4">
    <div class="container-fluid px-3 px-md-4">
        <form id="filterForm">
            <div class="row g-3 align-items-center">
                <div class="col-md-8">
                    <div class="search-laporan">
                        <i class="bi bi-search"></i>
                        <input type="search" class="search-laporan-input" id="searchInput"
                               placeholder="Cari berdasarkan lokasi atau jenis masalah..." name="search">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <select class="form-select filter-select" id="kategoriSelect" name="kategori_id">
                            <option value="">Semua Kategori</option>
                        </select>
                        <select class="form-select filter-select" id="statusSelect" name="status">
                            <option value="">Status: Semua</option>
                            <option value="baru">Baru</option>
                            <option value="diproses">Diproses</option>
                            <option value="selesai">Selesai</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary filter-burger" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-sliders"></i> <span class="d-none d-sm-inline">Filter</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end p-3 shadow-lg" style="width: 280px; border-radius: 12px;">
                                <h6 class="fw-bold mb-3">Filter Lanjut</h6>
                                <div class="mb-3">
                                    <label class="form-label small text-muted">Tanggal Mulai</label>
                                    <input type="date" class="form-control form-control-sm" id="tanggalMulai" name="tanggal_mulai">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small text-muted">Tanggal Akhir</label>
                                    <input type="date" class="form-control form-control-sm" id="tanggalAkhir" name="tanggal_akhir">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small text-muted">Urutkan</label>
                                    <select class="form-select form-select-sm" id="sortSelect" name="sort">
                                        <option value="terbaru">Terbaru</option>
                                        <option value="terlama">Terlama</option>
                                        <option value="populer">Terpopuler</option>
                                    </select>
                                </div>
                                <hr class="my-2">
                                <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="resetFilter()">
                                    <i class="bi bi-trash"></i> Bersihkan Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- Grid Laporan -->
<section class="pb-5">
    <div class="container-fluid px-3 px-md-4">
        <div id="alertContainer"></div>
        <div class="row g-4" id="laporanGrid">
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-success" role="status"></div>
                <p class="mt-3 text-muted">Memuat laporan...</p>
            </div>
        </div>
        <nav class="mt-5" id="paginationNav">
            <ul class="pagination justify-content-center" id="paginationUl"></ul>
        </nav>
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
    'baru': { label: 'Baru', badge: 'bg-secondary', icon: 'bi-send' },
    'diproses': { label: 'Diproses', badge: 'bg-warning text-dark', icon: 'bi-arrow-repeat' },
    'selesai': { label: 'Selesai', badge: 'bg-success', icon: 'bi-check-lg' },
    'ditolak': { label: 'Ditolak', badge: 'bg-danger', icon: 'bi-x-circle' }
};
let currentPage = 1;
let lastPage = 1;
let kategoriList = [];

async function loadKategori() {
    try {
        const res = await API.get('/kategori-sampah');
        if (res.ok && res.data) {
            const list = res.data.data || res.data;
            if (Array.isArray(list)) {
                kategoriList = list;
                const sel = document.getElementById('kategoriSelect');
                sel.innerHTML = '<option value="">Semua Kategori</option>' + list.map(k =>
                    `<option value="${k.id}">${Helpers.escapeHtml(k.nama_kategori)}</option>`
                ).join('');
            }
        }
    } catch (e) {
        console.error('Gagal muat kategori', e);
    }
}

async function loadLaporan(page) {
    const params = new URLSearchParams();
    params.set('page', page || 1);
    params.set('limit', 6);
    const search = document.getElementById('searchInput').value.trim();
    const status = document.getElementById('statusSelect').value;
    const kategori = document.getElementById('kategoriSelect').value;
    const tglMulai = document.getElementById('tanggalMulai').value;
    const tglAkhir = document.getElementById('tanggalAkhir').value;
    const sort = document.getElementById('sortSelect').value;
    if (search) params.set('search', search);
    if (status) params.set('status', status);
    if (kategori) params.set('kategori_id', kategori);
    if (tglMulai) params.set('tanggal_mulai', tglMulai);
    if (tglAkhir) params.set('tanggal_akhir', tglAkhir);
    if (sort) params.set('sort', sort);

    const container = document.getElementById('laporanGrid');
    container.innerHTML = '<div class="col-12 text-center py-5"><div class="spinner-border text-success" role="status"></div><p class="mt-3 text-muted">Memuat laporan...</p></div>';

    try {
        const res = await API.get('/laporan-sampah?' + params.toString());
        if (res.ok && res.data) {
            const body = res.data;
            const list = body.data || [];
            currentPage = body.current_page || 1;
            lastPage = body.last_page || 1;

            if (list.length > 0) {
                container.innerHTML = list.map(r => {
                    const st = r.status || 'baru';
                    const cfg = statusConfig[st] || statusConfig['baru'];
                    const katNama = r.kategori ? (r.kategori.nama_kategori || 'Lainnya') : 'Lainnya';
                    const risiko = r.kategori ? (r.kategori.level_risiko || 'rendah') : 'rendah';
                    const desc = r.deskripsi ? (r.deskripsi.length > 100 ? r.deskripsi.substring(0, 100) + '...' : r.deskripsi) : '-';
                    const fotoSrc = r.foto || '../Assets/images/LOGO.png';
                    return `<div class="col-md-6 col-lg-4">
                        <div class="laporan-grid-card">
                            <div class="laporan-grid-img">
                                <img src="${fotoSrc}" alt="Laporan" onerror="this.src='../Assets/images/LOGO.png'">
                                <span class="badge ${cfg.badge} badge-grid">
                                    <i class="bi ${cfg.icon}"></i> ${cfg.label}
                                </span>
                                ${risiko === 'tinggi' ? '<span class="badge bg-danger badge-prioritas">Risiko Tinggi</span>' : ''}
                            </div>
                            <div class="laporan-grid-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="laporan-grid-title">${Helpers.escapeHtml(r.nama_pelapor || 'Anonymous')}</h6>
                                    <small class="text-muted laporan-grid-kode">${Helpers.escapeHtml(r.kode_laporan || '-')}</small>
                                </div>
                                <div class="laporan-grid-meta">
                                    <span><i class="bi bi-geo-alt"></i> ${Helpers.escapeHtml(r.lokasi || '-')}</span>
                                    <span><i class="bi bi-calendar"></i> ${Helpers.escapeHtml(r.created_at || '-')}</span>
                                </div>
                                <p class="laporan-grid-desc">${Helpers.escapeHtml(desc)}</p>
                                <div class="laporan-grid-footer">
                                    <div class="laporan-grid-stats">
                                        <span class="badge bg-light text-dark">${Helpers.escapeHtml(katNama)}</span>
                                        ${risiko === 'tinggi' ? '<span class="badge bg-danger ms-1">Risiko Tinggi</span>' : risiko === 'sedang' ? '<span class="badge bg-warning text-dark ms-1">Risiko Sedang</span>' : ''}
                                    </div>
                                    <a href="detail.php?kode=${encodeURIComponent(r.kode_laporan || '')}" class="btn btn-sm btn-link text-success text-decoration-none fw-semibold">Lihat Detail</a>
                                </div>
                            </div>
                        </div>
                    </div>`;
                }).join('');
            } else {
                container.innerHTML = '<div class="col-12 text-center py-5"><i class="bi bi-inbox fs-1 text-muted"></i><p class="mt-3 text-muted">Belum ada laporan yang sesuai dengan filter.</p></div>';
            }
            renderPagination();
        } else {
            container.innerHTML = '<div class="col-12 text-center py-5"><i class="bi bi-exclamation-triangle fs-1 text-muted"></i><p class="mt-3 text-muted">Gagal memuat laporan.</p></div>';
        }
    } catch (e) {
        container.innerHTML = '<div class="col-12 text-center py-5"><i class="bi bi-exclamation-triangle fs-1 text-muted"></i><p class="mt-3 text-muted">Gagal memuat laporan.</p></div>';
    }
}

function renderPagination() {
    const ul = document.getElementById('paginationUl');
    if (lastPage <= 1) { ul.innerHTML = ''; return; }
    let html = `<li class="page-item ${currentPage <= 1 ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="event.preventDefault(); loadLaporan(${currentPage - 1})"><i class="bi bi-chevron-left"></i></a>
    </li>`;
    for (let i = 1; i <= lastPage; i++) {
        html += `<li class="page-item ${i === currentPage ? 'active' : ''}">
            <a class="page-link" href="#" onclick="event.preventDefault(); loadLaporan(${i})">${i}</a>
        </li>`;
    }
    html += `<li class="page-item ${currentPage >= lastPage ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="event.preventDefault(); loadLaporan(${currentPage + 1})"><i class="bi bi-chevron-right"></i></a>
    </li>`;
    ul.innerHTML = html;
}

function resetFilter() {
    document.getElementById('searchInput').value = '';
    document.getElementById('statusSelect').value = '';
    document.getElementById('kategoriSelect').value = '';
    document.getElementById('tanggalMulai').value = '';
    document.getElementById('tanggalAkhir').value = '';
    document.getElementById('sortSelect').value = 'terbaru';
    loadLaporan(1);
}

document.addEventListener('DOMContentLoaded', function() {
    loadKategori();
    loadLaporan(1);

    document.getElementById('searchInput').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') { e.preventDefault(); loadLaporan(1); }
    });
    document.getElementById('statusSelect').addEventListener('change', function() { loadLaporan(1); });
    document.getElementById('kategoriSelect').addEventListener('change', function() { loadLaporan(1); });
    document.getElementById('sortSelect').addEventListener('change', function() { loadLaporan(1); });
    document.getElementById('tanggalMulai').addEventListener('change', function() { loadLaporan(1); });
    document.getElementById('tanggalAkhir').addEventListener('change', function() { loadLaporan(1); });
});
</script>
</body>
</html>
