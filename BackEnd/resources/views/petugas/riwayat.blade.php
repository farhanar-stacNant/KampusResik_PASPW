@extends('petugas.layout')

@section('title', 'Riwayat - Petugas')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 animate-fade-in">
        <div class="col-12">
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-clock-history me-2"></i>Riwayat Pembersihan</h4>
            <p class="text-muted mb-0">Histori laporan yang telah diselesaikan</p>
        </div>
    </div>

    <div class="row mb-3 animate-fade-in">
        <div class="col-12">
            <div class="card card-stat">
                <div class="card-body py-3">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-8">
                            <label class="text-muted small mb-1">Cari Lokasi</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control border-start-0" placeholder="Nama lokasi..." id="searchLokasi">
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button class="btn btn-petugas btn-petugas-primary w-100" onclick="filterRiwayat()"><i class="bi bi-funnel me-1"></i>Filter</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row animate-fade-in">
        <div class="col-12">
            <div class="card card-stat">
                <div class="card-body">
                    <h5 class="fw-bold mb-4">Histori Tugas</h5>
                    <div class="timeline" id="timelineContainer">
                        <div class="text-center text-muted py-4">Memuat data...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4 animate-fade-in">
        <div class="col-6 col-md-4 mb-3">
            <div class="card card-stat text-center">
                <div class="card-body">
                    <div class="stat-value text-success" id="statTotal">0</div>
                    <div class="stat-label">Total Pembersihan</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 mb-3">
            <div class="card card-stat text-center">
                <div class="card-body">
                    <div class="stat-value text-success" id="statLokasi">0</div>
                    <div class="stat-label">Lokasi Berbeda</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 mb-3">
            <div class="card card-stat text-center">
                <div class="card-body">
                    <div class="stat-value text-success" id="statHari">0</div>
                    <div class="stat-label">Hari Aktif</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const warnaBorder = ['#2e7d32', '#43a047', '#66bb6a', '#00897b', '#1b5e20'];
const jenisMap = { 'ORG': 'Sampah Organik', 'ANR': 'Sampah Anorganik', 'B3': 'Sampah B3' };
let riwayatData = [];

document.addEventListener('DOMContentLoaded', async function () {
    const res = await API.get('/public/reports?limit=50&status=selesai');
    if (!res.ok || !res.data?.data) return;
    riwayatData = res.data.data;
    renderRiwayat(riwayatData);
});

function renderRiwayat(data) {
    const container = document.getElementById('timelineContainer');
    if (data.length === 0) {
        container.innerHTML = '<div class="text-center text-muted py-4">Belum ada riwayat pembersihan</div>';
        return;
    }

    container.innerHTML = data.map((item, index) => {
        const warna = warnaBorder[index % warnaBorder.length];
        const kategori = jenisMap[item.kategori] || item.kategori || '-';
        return `<div class="timeline-item mb-4 pb-4" data-lokasi="${(item.lokasi || '').toLowerCase()}" style="border-left:3px solid ${warna};padding-left:1.5rem;position:relative;">
            <div style="position:absolute;left:-8px;top:0;width:14px;height:14px;border-radius:50%;background:${warna};border:2px solid #fff;box-shadow:0 0 0 3px ${warna}20;"></div>
            <div class="d-flex justify-content-between align-items-start flex-wrap">
                <div>
                    <h6 class="fw-bold mb-1"><i class="bi bi-geo-alt-fill me-1" style="color:${warna}"></i>${Helpers.escapeHtml(item.lokasi)}</h6>
                    <p class="text-muted small mb-1"><i class="bi bi-tag me-1"></i>${kategori}</p>
                    <p class="mb-0 text-dark">${Helpers.escapeHtml(item.deskripsi)}</p>
                </div>
                <div class="text-end mt-2 mt-md-0">
                    <span class="status-badge bg-success mb-1 d-inline-block" style="color:#fff;">Selesai</span>
                    <p class="text-muted small mb-0"><i class="bi bi-calendar me-1"></i>${Helpers.formatTanggal(item.created_at)}</p>
                    ${item.selesai_at ? '<p class="text-muted small mb-0"><i class="bi bi-check-circle me-1"></i>' + Helpers.formatTanggal(item.selesai_at) + '</p>' : ''}
                </div>
            </div>
        </div>`;
    }).join('');

    const total = data.length;
    const lokasiUnik = new Set(data.map(r => r.lokasi).filter(Boolean)).size;
    const hariUnik = new Set(data.map(r => r.created_at ? r.created_at.split('T')[0] : '')).size;

    document.getElementById('statTotal').textContent = total;
    document.getElementById('statLokasi').textContent = lokasiUnik;
    document.getElementById('statHari').textContent = hariUnik;
}

function filterRiwayat() {
    const search = document.getElementById('searchLokasi').value.toLowerCase();
    const items = document.querySelectorAll('.timeline-item');
    items.forEach(item => {
        const lokasi = item.dataset.lokasi;
        item.style.display = (!search || lokasi.includes(search)) ? '' : 'none';
    });
}
</script>
@endpush
