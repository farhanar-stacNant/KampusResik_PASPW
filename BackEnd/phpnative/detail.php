<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Laporan - KampusResik</title>
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
                    <a class="nav-link" href="status.php">Status</a>
                </li>

            </ul>
        </div>
    </div>
</nav>

<section class="page-header py-4">
    <div class="container-fluid px-3 px-md-4">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="fw-bold mb-1">Detail Laporan</h2>
                <p class="text-muted mb-0">Informasi lengkap dan riwayat status laporan.</p>
            </div>
            <div class="col-auto">
                <a href="laporan.php" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</section>

<section class="pb-5">
    <div class="container-fluid px-3 px-md-4">
        <div id="alertContainer"></div>
        <div id="detailContent" class="row g-4">
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-success" role="status"></div>
                <p class="mt-3 text-muted">Memuat detail laporan...</p>
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
    'baru': { label: 'Baru', badge: 'bg-secondary', icon: 'bi-send' },
    'diproses': { label: 'Diproses', badge: 'bg-warning text-dark', icon: 'bi-arrow-repeat' },
    'selesai': { label: 'Selesai', badge: 'bg-success', icon: 'bi-check-lg' },
    'ditolak': { label: 'Ditolak', badge: 'bg-danger', icon: 'bi-x-circle' }
};

async function loadDetail() {
    const params = new URLSearchParams(window.location.search);
    const kode = params.get('kode');
    const container = document.getElementById('detailContent');

    if (!kode) {
        container.innerHTML = '<div class="col-12"><div class="alert alert-warning">Kode laporan tidak ditemukan. <a href="laporan.php">Kembali ke daftar</a></div></div>';
        return;
    }

    try {
        const res = await API.get('/laporan-sampah/' + encodeURIComponent(kode));
        if (res.ok && res.data && res.data.success && res.data.data) {
            const report = res.data.data;
            const timeline = report.timeline || [];
            const config = statusConfig[report.status || 'baru'] || statusConfig['baru'];
            const risiko = report.kategori ? (report.kategori.level_risiko || 'rendah') : 'rendah';
            const katNama = report.kategori ? (report.kategori.nama_kategori || 'Lainnya') : 'Lainnya';

            let risikoBadge = '';
            if (risiko === 'tinggi') risikoBadge = '<span class="badge bg-danger">Risiko Tinggi</span>';
            else if (risiko === 'sedang') risikoBadge = '<span class="badge bg-warning text-dark">Risiko Sedang</span>';
            else risikoBadge = '<span class="badge bg-success">Risiko Rendah</span>';

            let fotoHtml = '';
            if (report.foto) {
                fotoHtml = `<div class="mb-4"><img src="${Helpers.escapeHtml(report.foto)}" alt="Foto Laporan" class="img-fluid rounded" style="max-height: 400px; width: 100%; object-fit: cover;" onerror="this.style.display='none'"></div>`;
            }

            let googleMapsLink = '';
            if (report.latitude && report.longitude) {
                googleMapsLink = `<a href="https://www.google.com/maps?q=${report.latitude},${report.longitude}" target="_blank" class="small text-success"><i class="bi bi-box-arrow-up-right"></i> Lihat di Google Maps</a>`;
            }

            let catatanHtml = '';
            if (report.catatan_petugas) {
                catatanHtml = `<div class="alert alert-info"><label class="fw-bold small text-uppercase mb-1"><i class="bi bi-info-circle"></i> Catatan Petugas</label><p class="mb-0">${Helpers.escapeHtml(report.catatan_petugas)}</p></div>`;
            }

            let timelineHtml = '';
            if (timeline.length > 0) {
                timelineHtml = timeline.map((item, index) => {
                    const isLast = index === timeline.length - 1;
                    const statusLabel = item.status.charAt(0).toUpperCase() + item.status.slice(1);
                    const iconMap = { 'baru': 'bi-send', 'diproses': 'bi-arrow-repeat', 'selesai': 'bi-check-lg', 'ditolak': 'bi-x-circle' };
                    const iconClass = iconMap[item.status] || 'bi-circle';
                    return `<div class="timeline-item ${isLast ? 'active' : ''}">
                        <div class="timeline-icon"><i class="bi ${iconClass}"></i></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">${statusLabel}</h6>
                            <p class="small text-muted mb-1">${Helpers.escapeHtml(item.deskripsi || '')}</p>
                            <small class="text-muted">${Helpers.escapeHtml(item.waktu || '')}</small>
                        </div>
                    </div>`;
                }).join('');
            } else {
                timelineHtml = '<div class="text-muted text-center py-4"><i class="bi bi-clock-history fs-2"></i><p class="small mb-0 mt-2">Belum ada timeline</p></div>';
            }

            container.innerHTML = `
            <div class="col-lg-8">
                <div class="card-clean p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="badge ${config.badge} mb-2"><i class="bi ${config.icon}"></i> ${config.label}</span>
                            <h4 class="fw-bold mb-1">Laporan ${Helpers.escapeHtml(report.kode_laporan || '-')}</h4>
                            <small class="text-muted">Oleh: ${Helpers.escapeHtml(report.nama_pelapor || '-')}</small>
                        </div>
                        ${risiko === 'tinggi' ? '<span class="badge bg-danger">Risiko Tinggi</span>' : risiko === 'sedang' ? '<span class="badge bg-warning text-dark">Risiko Sedang</span>' : ''}
                    </div>
                    ${fotoHtml}
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase">Lokasi</label>
                            <p class="mb-0"><i class="bi bi-geo-alt text-success"></i> ${Helpers.escapeHtml(report.lokasi || '-')}</p>
                            ${googleMapsLink}
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase">Kategori</label>
                            <p class="mb-0">
                                <span class="badge bg-light text-dark">${Helpers.escapeHtml(katNama)}</span>
                                ${risikoBadge}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase">Tanggal Lapor</label>
                            <p class="mb-0"><i class="bi bi-calendar text-success"></i> ${Helpers.escapeHtml(report.created_at || '-')}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase">Kontak</label>
                            <p class="mb-0"><i class="bi bi-telephone text-success"></i> ${Helpers.escapeHtml(report.kontak_pelapor || '-')}</p>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="text-muted small text-uppercase">Deskripsi</label>
                        <p class="mb-0" style="white-space: pre-wrap;">${Helpers.escapeHtml(report.deskripsi || '-')}</p>
                    </div>
                    ${catatanHtml}
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card-clean p-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-clock-history"></i> Timeline</h5>
                    <div class="timeline">${timelineHtml}</div>
                </div>
                <div class="card-clean p-4 bg-success text-white mt-4">
                    <h6 class="fw-bold mb-2">Pantau Status Laporan</h6>
                    <p class="small mb-3 opacity-75">Simpan kode laporan untuk memantau status kapan saja.</p>
                    <div class="bg-white bg-opacity-20 rounded p-2 text-center">
                        <small class="text-black">Kode Laporan</small>
                        <div class="text-black fw-bold fs-5">${Helpers.escapeHtml(report.kode_laporan || '-')}</div>
                    </div>
                </div>
            </div>`;
        } else {
            container.innerHTML = '<div class="col-12"><div class="alert alert-warning">Laporan tidak ditemukan. <a href="laporan.php">Kembali ke daftar</a></div></div>';
        }
    } catch (e) {
        container.innerHTML = '<div class="col-12"><div class="alert alert-warning">Gagal memuat detail laporan. <a href="laporan.php">Kembali ke daftar</a></div></div>';
    }
}

document.addEventListener('DOMContentLoaded', loadDetail);
</script>
</body>
</html>
