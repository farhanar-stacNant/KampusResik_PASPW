@extends('admin.layout')

@section('title', 'Rekap Laporan')
@section('content')
<div class="container-fluid">
    <div class="admin-header animate-in">
        <div>
            <h4>Rekap Laporan</h4>
            <p>Lihat dan export rekap laporan sampah berdasarkan filter.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-admin btn-admin-outline" onclick="exportExcel()">
                <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
            </button>
            <button class="btn btn-admin btn-admin-outline" onclick="exportPDF()">
                <i class="bi bi-file-earmark-pdf me-2"></i>Export PDF
            </button>
        </div>
    </div>

    <div class="row g-3 mb-4" id="statCards"></div>

    <div class="admin-card mb-3 mb-md-4 animate-in">
        <div class="card-body py-3 px-3 px-md-4">
            <form id="filterForm" class="row g-2 g-md-3 align-items-end" onsubmit="event.preventDefault(); loadReports();">
                <div class="col-6 col-md-3">
                    <label class="form-label fw-semibold">Dari Tanggal</label>
                    <input type="date" class="form-control" id="filterDateFrom">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label fw-semibold">Sampai Tanggal</label>
                    <input type="date" class="form-control" id="filterDateTo">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label fw-semibold">Kategori</label>
                    <select class="form-select" id="filterKategori">
                        <option value="">Semua Kategori</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label fw-semibold">Status</label>
                    <select class="form-select" id="filterStatus">
                        <option value="">Semua</option>
                        <option value="baru">Baru</option>
                        <option value="diterima">Diterima</option>
                        <option value="diproses">Diproses</option>
                        <option value="selesai">Selesai</option>
                        <option value="ditolak">Ditolak</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label fw-semibold">Level Risiko</label>
                    <select class="form-select" id="filterRisiko">
                        <option value="">Semua</option>
                        <option value="rendah">Rendah</option>
                        <option value="sedang">Sedang</option>
                        <option value="tinggi">Tinggi</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <button type="submit" class="btn btn-admin btn-admin-primary w-100">
                        <i class="bi bi-funnel me-2"></i>Filter
                    </button>
                </div>
                <div class="col-6 col-md-3">
                    <button type="button" class="btn btn-admin btn-admin-outline w-100" onclick="resetFilter()">
                        <i class="bi bi-arrow-counterclockwise me-2"></i>Reset
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="admin-card animate-in">
        <div class="card-body p-0 p-md-4">
            <div class="table-responsive" style="overflow-x:auto;-webkit-overflow-scrolling:touch;max-width:100%">
                <table class="admin-table mb-0" style="width:100%">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Pelapor</th>
                            <th>Kategori</th>
                            <th>Risiko</th>
                            <th>Lokasi</th>
                            <th>Tanggal</th>
                            <th>Status</th>
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
                <p>Tidak ada laporan ditemukan.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const fallbackStats = [
    { label: 'Total Laporan', value: '13', icon: 'bi-clipboard-data', color: 'blue', trend: 'Semua waktu', trend_up: true },
    { label: 'Menunggu', value: '5', icon: 'bi-clock', color: 'orange', trend: 'Perlu ditindak', trend_up: false },
    { label: 'Diproses', value: '3', icon: 'bi-arrow-repeat', color: 'green', trend: 'Sedang dikerjakan', trend_up: true },
    { label: 'Selesai', value: '5', icon: 'bi-check-circle', color: 'green', trend: 'Selesai semua', trend_up: true },
];

async function loadStats() {
    const res = await API.get('/admin/statistics');
    const container = document.getElementById('statCards');
    let list;
    if (res.ok) {
        list = res.body.data?.list ?? res.body.data ?? [];
    }
    const stats = (list && list.length) ? list : fallbackStats;
    container.innerHTML = stats.map(s => {
        const trendIcon = s.trend_up !== false ? '↗' : '⚠';
        const trendClass = s.trend_up !== false ? 'up' : 'down';
        return `
            <div class="col-6 col-xl-3 animate-in">
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

async function loadKategoriFilter() {
    const res = await API.get('/admin/categories');
    const select = document.getElementById('filterKategori');
    if (res.ok) {
        const list = Array.isArray(res.body.data) ? res.body.data : [];
        list.forEach(k => {
            select.innerHTML += `<option value="${k.id}">${Helpers.escapeHtml(k.nama || k.name)}</option>`;
        });
    }
}

async function loadReports() {
    const tbody = document.getElementById('reportBody');
    const loading = document.getElementById('loadingTable');
    const empty = document.getElementById('emptyTable');
    tbody.innerHTML = '';
    loading.classList.remove('d-none');
    empty.classList.add('d-none');

    const params = new URLSearchParams({ limit: '50' });
    const from = document.getElementById('filterDateFrom').value;
    const to = document.getElementById('filterDateTo').value;
    const kategori = document.getElementById('filterKategori').value;
    const status = document.getElementById('filterStatus').value;
    const risiko = document.getElementById('filterRisiko').value;
    if (from) params.set('from', from);
    if (to) params.set('to', to);
    if (kategori) params.set('kategori_id', kategori);
    if (status) params.set('status', status);
    if (risiko) params.set('level_risiko', risiko);

    const res = await API.get(`/admin/rekap?${params.toString()}`);
    loading.classList.add('d-none');

    if (res.ok) {
        const reports = Array.isArray(res.body.data) ? res.body.data : (res.body.data?.data ?? []);
        if (!reports.length) {
            empty.classList.remove('d-none');
            return;
        }
        tbody.innerHTML = reports.map(r => {
            const badge = Helpers.statusBadge(r.status);
            const level = r.level_risiko || r.kategori?.level_risiko || 'rendah';
            const levelBadge = `<span class="badge bg-${level === 'tinggi' ? 'danger' : level === 'sedang' ? 'warning text-dark' : 'success'}">${level.charAt(0).toUpperCase() + level.slice(1)}</span>`;
            return `
                <tr>
                    <td><strong style="font-size:0.85rem">${Helpers.escapeHtml(r.kode_laporan || '-')}</strong></td>
                    <td style="font-size:0.85rem">${Helpers.escapeHtml(r.nama_pelapor || r.nama || '-')}</td>
                    <td style="font-size:0.85rem">${Helpers.escapeHtml(r.kategori?.nama || r.kategori || '-')}</td>
                    <td style="font-size:0.8rem">${levelBadge}</td>
                    <td style="font-size:0.85rem">${Helpers.escapeHtml(r.lokasi || '-')}</td>
                    <td style="font-size:0.85rem">${Helpers.formatTanggal(r.created_at || r.tanggal)}</td>
                    <td><span class="badge-status badge-${r.status}" style="font-size:0.75rem;padding:0.3rem 0.6rem">${badge.label}</span></td>
                </tr>
            `;
        }).join('');
    } else {
        empty.classList.remove('d-none');
    }
}

function resetFilter() {
    document.getElementById('filterForm').reset();
    loadReports();
}

async function exportExcel() {
    const params = getFilterParams();
    const res = await fetch(`${API.BASE_URL}/admin/export/excel?${params.toString()}`, {
        headers: API.getHeaders()
    });
    if (!res.ok) return;
    const blob = await res.blob();
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'rekap-laporan-' + new Date().toISOString().slice(0,10) + '.csv';
    document.body.appendChild(a);
    a.click();
    a.remove();
    window.URL.revokeObjectURL(url);
}

async function exportPDF() {
    const params = getFilterParams();
    const res = await fetch(`${API.BASE_URL}/admin/export/pdf?${params.toString()}`, {
        headers: API.getHeaders()
    });
    if (!res.ok) return;
    const html = await res.text();
    const w = window.open('', '_blank');
    if (w) {
        w.document.write(html);
        w.document.close();
    }
}

function getFilterParams() {
    const params = new URLSearchParams();
    const from = document.getElementById('filterDateFrom').value;
    const to = document.getElementById('filterDateTo').value;
    const kategori = document.getElementById('filterKategori').value;
    const status = document.getElementById('filterStatus').value;
    const risiko = document.getElementById('filterRisiko').value;
    if (from) params.set('from', from);
    if (to) params.set('to', to);
    if (kategori) params.set('kategori_id', kategori);
    if (status) params.set('status', status);
    if (risiko) params.set('level_risiko', risiko);
    return params;
}

document.addEventListener('DOMContentLoaded', () => {
    loadStats();
    loadKategoriFilter();
    loadReports();
});
</script>
@endpush
