@extends('admin.layout')

@section('title', 'Manajemen Laporan')
@section('content')
<div class="container-fluid px-2 px-md-3">
    <div class="admin-header animate-in">
        <div>
            <h4>Manajemen Laporan</h4>
            <p>Kelola semua laporan sampah yang masuk dari masyarakat.</p>
        </div>
        <div>
            <button class="btn btn-admin btn-admin-outline" onclick="refreshTable()">
                <i class="bi bi-arrow-clockwise me-2"></i>Refresh
            </button>
        </div>
    </div>

    <div class="row g-2 g-md-3 mb-3 mb-md-4" id="statCards"></div>

    <div class="admin-card mb-3 mb-md-4 animate-in">
        <div class="card-body py-3 px-3 px-md-4">
            <div class="row g-2 align-items-end">
                <div class="col-6 col-md-4 col-lg-3">
                    <label class="fw-semibold mb-1" style="font-size:.8rem">Filter Risiko:</label>
                    <select class="form-select form-select-sm" id="filterRisiko" onchange="applyFilter()">
                        <option value="">Semua</option>
                        <option value="rendah">Rendah</option>
                        <option value="sedang">Sedang</option>
                        <option value="tinggi">Tinggi</option>
                    </select>
                </div>
                <div class="col-6 col-md-4 col-lg-3">
                    <label class="fw-semibold mb-1" style="font-size:.8rem">Status:</label>
                    <select class="form-select form-select-sm" id="filterStatus" onchange="applyFilter()">
                        <option value="">Semua</option>
                        <option value="baru">Baru</option>
                        <option value="dikirim">Dikirim</option>
                        <option value="diterima">Diterima</option>
                        <option value="diproses">Diproses</option>
                        <option value="selesai">Selesai</option>
                        <option value="ditolak">Ditolak</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="admin-card animate-in">
        <div class="card-body p-0 p-md-4">
            <div class="table-responsive" style="overflow-x:auto;-webkit-overflow-scrolling:touch;max-width:100%">
                <table class="admin-table mb-0" id="reportTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Pelapor</th>
                            <th>Foto</th>
                            <th>Lokasi</th>
                            <th>Kategori</th>
                            <th>Risiko</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="reportBody"></tbody>
                </table>
            </div>
            <div id="loadingTable" class="text-center py-4 py-md-5 text-muted">
                <div class="spinner-border spinner-border-sm me-2" role="status"></div>Memuat data...
            </div>
            <div id="emptyTable" class="text-center py-4 py-md-5 text-muted d-none">
                <i class="bi bi-inbox fs-1 d-block mb-3 opacity-25"></i>
                <p>Belum ada laporan.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const fallbackStats = [
    { label: 'Menunggu', value: '5', icon: 'bi-clock', color: 'orange', trend: 'Perlu Ditindak', trend_up: false },
    { label: 'Diproses', value: '3', icon: 'bi-arrow-repeat', color: 'green', trend: 'Sedang dikerjakan', trend_up: true },
    { label: 'Selesai', value: '4', icon: 'bi-check-circle', color: 'green', trend: 'Hari ini: 2', trend_up: true },
];

async function loadData() {
    const statsRes = await API.get('/admin/statistics');
    const statsList = statsRes.ok ? (statsRes.body.data?.list ?? statsRes.body.data ?? []) : [];
    renderStats(statsList.length ? statsList : fallbackStats);

    const params = new URLSearchParams();
    const level = document.getElementById('filterRisiko').value;
    const status = document.getElementById('filterStatus').value;
    if (level) params.set('level_risiko', level);
    if (status) params.set('status', status);
    params.set('limit', '50');

    const res = await API.get('/admin/reports?' + params.toString());
    if (res.ok) {
        const reports = Array.isArray(res.body.data) ? res.body.data : (res.body.data?.data ?? []);
        renderTable(reports);
    } else {
        if (res.status === 401) {
            Helpers.showAlert('Sesi login telah habis. Silakan login ulang.', 'warning');
        } else if (res.status === 403) {
            Helpers.showAlert('Anda tidak memiliki akses ke data ini.', 'danger');
        } else {
            Helpers.showAlert('Gagal memuat data: ' + (res.data?.message || 'Koneksi ke server terputus'), 'danger');
        }
        renderTable([]);
    }
}

function applyFilter() {
    document.getElementById('loadingTable').classList.remove('d-none');
    document.getElementById('reportBody').innerHTML = '';
    document.getElementById('emptyTable').classList.add('d-none');
    loadData();
}

function renderStats(stats) {
    const container = document.getElementById('statCards');
    container.innerHTML = stats.map(s => {
        const trendIcon = s.trend_up !== false ? '↗' : '⚠';
        const trendClass = s.trend_up !== false ? 'up' : 'down';
        return `
            <div class="col-6 col-md-4 animate-in">
                <div class="admin-card h-100">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center gap-3" style="flex-direction:row">
                            <div class="stat-icon ${s.color || 'blue'}" style="width:44px;height:44px;font-size:1.3rem;margin:0;">
                                <i class="bi ${s.icon || 'bi-clipboard-data'}"></i>
                            </div>
                            <div class="stat-info" style="text-align:left">
                                <h3 style="font-size:1.4rem;margin:0;line-height:1.2">${s.value ?? 0}</h3>
                                <p style="font-size:0.75rem;margin:0;line-height:1.3">${s.label || ''}</p>
                                <span class="stat-trend ${trendClass}" style="font-size:0.7rem">${trendIcon} ${s.trend || ''}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

function renderTable(reports) {
    const tbody = document.getElementById('reportBody');
    const loading = document.getElementById('loadingTable');
    const empty = document.getElementById('emptyTable');
    loading.classList.add('d-none');

    if (!reports.length) {
        empty.classList.remove('d-none');
        tbody.innerHTML = '';
        return;
    }
    empty.classList.add('d-none');

    tbody.innerHTML = reports.map(r => {
        const badge = Helpers.statusBadge(r.status);
        const level = r.level_risiko || r.kategori?.level_risiko || 'rendah';
        const levelBadge = `<span class="badge bg-${level === 'tinggi' ? 'danger' : level === 'sedang' ? 'warning text-dark' : 'success'}">${level.charAt(0).toUpperCase() + level.slice(1)}</span>`;
        const fotoHtml = r.foto
            ? `<img src="${r.foto}" alt="foto" style="width:40px;height:40px;object-fit:cover;border-radius:6px">`
            : `<span class="text-muted">-</span>`;
        return `
            <tr>
                <td><strong style="font-size:0.85rem">${Helpers.escapeHtml(r.kode_laporan || '-')}</strong></td>
                <td style="font-size:0.85rem">${Helpers.escapeHtml(r.nama_pelapor || r.nama || '-')}</td>
                <td>${fotoHtml}</td>
                <td style="font-size:0.85rem">${Helpers.escapeHtml(r.lokasi || '-')}</td>
                <td style="font-size:0.85rem">${Helpers.escapeHtml(r.kategori?.nama || r.kategori || '-')}</td>
                <td style="font-size:0.8rem">${levelBadge}</td>
                <td>
                    <select class="form-select status-select" data-id="${r.id}" onchange="updateStatus(this, ${r.id})" style="min-width:95px;font-size:0.75rem;padding:0.2rem 1.2rem 0.2rem 0.4rem">
                        <option value="baru" ${r.status === 'baru' ? 'selected' : ''}>Baru</option>
                        <option value="dikirim" ${r.status === 'dikirim' ? 'selected' : ''}>Dikirim</option>
                        <option value="diterima" ${r.status === 'diterima' ? 'selected' : ''}>Diterima</option>
                        <option value="diproses" ${r.status === 'diproses' ? 'selected' : ''}>Diproses</option>
                        <option value="selesai" ${r.status === 'selesai' ? 'selected' : ''}>Selesai</option>
                        <option value="ditolak" ${r.status === 'ditolak' ? 'selected' : ''}>Ditolak</option>
                    </select>
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-success" onclick="alert('Detail laporan: ${r.kode_laporan}')" style="padding:0.25rem 0.4rem;font-size:0.75rem">
                        <i class="bi bi-eye"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

async function updateStatus(el, id) {
    const status = el.value;
    const res = await API.post(`/admin/reports/${id}/status`, { status });
    if (res.ok) {
        Helpers.showAlert('Status berhasil diperbarui!', 'success');
    } else {
        Helpers.showAlert('Gagal memperbarui status.', 'danger');
        el.value = el.querySelector(`option[selected]`)?.value || 'baru';
    }
}

function refreshTable() {
    document.getElementById('loadingTable').classList.remove('d-none');
    document.getElementById('reportBody').innerHTML = '';
    document.getElementById('emptyTable').classList.add('d-none');
    loadData();
}

document.addEventListener('DOMContentLoaded', loadData);
</script>
@endpush
