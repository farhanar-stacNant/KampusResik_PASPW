@extends('petugas.layout')

@section('title', 'Detail Laporan - Petugas')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 animate-fade-in">
        <div class="col-12">
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-clipboard-check me-2"></i>Detail Laporan</h4>
            <p class="text-muted mb-0">Kelola status laporan sampah</p>
        </div>
    </div>

    <div class="row mb-3 animate-fade-in">
        <div class="col-12">
            <div class="card card-stat">
                <div class="card-body py-3">
                    <div class="row g-2 align-items-center">
                        <div class="col-6 col-md-4">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control border-start-0" placeholder="Cari lokasi atau kode..." id="searchLaporan">
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <select class="form-select" id="filterStatus">
                                <option value="">Semua Status</option>
                                <option value="dikirim">Dikirim</option>
                                <option value="diterima">Diterima</option>
                                <option value="diproses">Diproses</option>
                                <option value="selesai">Selesai</option>
                                <option value="ditolak">Ditolak</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <input type="date" class="form-control" id="filterTanggal">
                        </div>
                        <div class="col-6 col-md-2">
                            <button class="btn btn-petugas btn-petugas-primary w-100" onclick="filterLaporan()"><i class="bi bi-funnel me-1"></i>Filter</button>
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
                    <div class="table-responsive">
                        <table class="table table-petugas mb-0" id="tabelLaporan">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Lokasi</th>
                                    <th>Kategori</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="laporanBody">
                                <tr><td colspan="6" class="text-center text-muted py-4">Memuat data...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalTitle">Detail Laporan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBody">
                <div class="text-center py-4"><div class="spinner-border text-success"></div><p class="text-muted mt-2">Memuat detail...</p></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const jenisMap = { 'ORG': 'Sampah Organik', 'ANR': 'Sampah Anorganik', 'B3': 'Sampah B3' };
const statusOptions = ['dikirim', 'diterima', 'diproses', 'selesai', 'ditolak'];
const statusLabels = {
    dikirim: 'Dikirim', diterima: 'Diterima', diproses: 'Diproses',
    selesai: 'Selesai', ditolak: 'Ditolak'
};
let laporanData = [];

document.addEventListener('DOMContentLoaded', async function () {
    const res = await API.get('/public/reports?limit=50');
    if (!res.ok) {
        if (res.status === 401) {
            document.getElementById('laporanBody').innerHTML = '<tr><td colspan="6" class="text-center text-warning py-4"><i class="bi bi-exclamation-triangle me-2"></i>Sesi login telah habis. Silakan <a href="/petugas/login" class="alert-link">login ulang</a>.</td></tr>';
        } else if (res.status === 403) {
            document.getElementById('laporanBody').innerHTML = '<tr><td colspan="6" class="text-center text-danger py-4"><i class="bi bi-shield-exclamation me-2"></i>Anda tidak memiliki akses ke data ini.</td></tr>';
        } else {
            document.getElementById('laporanBody').innerHTML = '<tr><td colspan="6" class="text-center text-danger py-4"><i class="bi bi-wifi me-2"></i>Gagal memuat data. Periksa koneksi server.</td></tr>';
        }
        return;
    }
    if (!res.data?.data) {
        document.getElementById('laporanBody').innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data laporan</td></tr>';
        return;
    }
    laporanData = res.data.data;
    renderTable(laporanData);
});

function renderTable(data) {
    const tbody = document.getElementById('laporanBody');
    if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data laporan</td></tr>';
        return;
    }
    tbody.innerHTML = data.map(r => {
        const badge = Helpers.statusBadge(r.status);
        const kategori = (r.kategori_sampah?.nama_kategori || r.kategori?.nama_kategori || jenisMap[r.kategori] || r.kategori || '-');
        return `<tr data-status="${r.status}" data-lokasi="${(r.lokasi || '').toLowerCase()}" data-kode="${(r.kode_laporan || r.kode || '').toLowerCase()}" data-tanggal="${formatDateForFilter(r.created_at)}">
            <td><strong>${Helpers.escapeHtml(r.kode_laporan || r.kode || '#' + r.id)}</strong></td>
            <td><i class="bi bi-geo-alt text-muted me-1"></i>${Helpers.escapeHtml(r.lokasi)}</td>
            <td><span class="badge bg-light text-dark border">${kategori}</span></td>
            <td>
                <select class="form-select form-select-sm status-select" data-id="${r.id}" style="min-width:130px;">
                    ${statusOptions.map(opt =>
                        `<option value="${opt}" ${r.status === opt ? 'selected' : ''}>${statusLabels[opt]}</option>`
                    ).join('')}
                </select>
            </td>
            <td><i class="bi bi-calendar text-muted me-1"></i>${Helpers.formatTanggal(r.created_at)}</td>
            <td>
                <button class="btn btn-sm btn-outline-info" onclick="openDetailModal(${r.id})"><i class="bi bi-eye"></i></button>
            </td>
        </tr>`;
    }).join('');

    document.querySelectorAll('.status-select').forEach(sel => {
        sel.addEventListener('change', async function () {
            const id = this.dataset.id;
            const status = this.value;
            const res = await API.post('/laporan/' + id + '/status', { status });
            if (res.ok) {
                this.closest('tr').dataset.status = status;
                Helpers.showAlert('Status berhasil diperbarui', 'success');
                filterLaporan();
            } else {
                const msg = res.data?.message || 'Gagal memperbarui status';
                Helpers.showAlert(msg, 'danger');
            }
        });
    });
}

function filterLaporan() {
    const search = document.getElementById('searchLaporan').value.toLowerCase();
    const status = document.getElementById('filterStatus').value;
    const tanggal = document.getElementById('filterTanggal').value;
    const rows = document.querySelectorAll('#tabelLaporan tbody tr');
    rows.forEach(row => {
        const rowStatus = row.dataset.status;
        const rowLokasi = row.dataset.lokasi;
        const rowKode = row.dataset.kode;
        const rowTanggal = row.dataset.tanggal || '';
        const matchSearch = !search || rowLokasi.includes(search) || rowKode.includes(search);
        const matchStatus = !status || rowStatus === status;
        const matchTanggal = !tanggal || rowTanggal.startsWith(tanggal);
        row.style.display = (matchSearch && matchStatus && matchTanggal) ? '' : 'none';
    });
}

function formatDateForFilter(dateStr) {
    if (!dateStr) return '';
    return dateStr.split(' ')[0];
}

async function openDetailModal(id) {
    const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('detailModal'));
    document.getElementById('modalTitle').textContent = 'Detail Laporan #' + id;
    document.getElementById('modalBody').innerHTML = '<div class="text-center py-4"><div class="spinner-border text-success"></div><p class="text-muted mt-2">Memuat detail...</p></div>';
    modal.show();

    const res = await API.get('/public/reports/' + id);
    if (!res.ok) {
        document.getElementById('modalBody').innerHTML = '<div class="alert alert-danger">Gagal memuat detail laporan.</div>';
        return;
    }

    const data = res.data?.data || {};
    const badge = Helpers.statusBadge(data.status);
    const kategori = jenisMap[data.jenis_sampah] || jenisMap[data.kategori] || data.kategori || '-';

    let fotoHtml = data.foto
        ? '<img src="' + Helpers.escapeHtml(data.foto) + '" class="img-fluid rounded-3" style="max-height:200px;" alt="Foto Laporan">'
        : '<div class="bg-light rounded-3 p-3 text-center" style="height:180px;"><i class="bi bi-image text-muted" style="font-size:3rem;"></i><p class="text-muted small mt-2">Tidak ada foto</p></div>';

    let timelineHtml = '';
    if (data.timeline && data.timeline.length > 0) {
        timelineHtml = '<div class="mt-3"><label class="text-muted small">Timeline</label><div class="timeline mt-2">';
        data.timeline.forEach(t => {
            timelineHtml += '<div class="timeline-item mb-3 pb-3" style="border-left:3px solid #2e7d32;padding-left:1rem;position:relative;">' +
                '<div style="position:absolute;left:-7px;top:0;width:12px;height:12px;border-radius:50%;background:#2e7d32;border:2px solid #fff;"></div>' +
                '<p class="fw-semibold mb-0">' + Helpers.escapeHtml(t.status) + '</p>' +
                '<p class="text-muted small mb-0">' + Helpers.escapeHtml(t.deskripsi || '') + '</p>' +
                '<p class="text-muted small mb-0"><i class="bi bi-clock me-1"></i>' + Helpers.escapeHtml(t.waktu) + '</p>' +
            '</div>';
        });
        timelineHtml += '</div></div>';
    }

    document.getElementById('modalBody').innerHTML =
        '<div class="mb-3"><label class="text-muted small">Foto Laporan</label><div class="mt-1">' + fotoHtml + '</div></div>' +
        '<div class="mb-2"><label class="text-muted small">Kode Laporan</label><p class="fw-semibold mb-0">' + Helpers.escapeHtml(data.kode) + '</p></div>' +
        '<div class="mb-2"><label class="text-muted small">Lokasi</label><p class="fw-semibold mb-0"><i class="bi bi-geo-alt me-1"></i>' + Helpers.escapeHtml(data.lokasi) + '</p></div>' +
        '<div class="mb-2"><label class="text-muted small">Kategori</label><p class="fw-semibold mb-0">' + kategori + '</p></div>' +
        '<div class="mb-2"><label class="text-muted small">Deskripsi</label><p class="mb-0">' + Helpers.escapeHtml(data.deskripsi) + '</p></div>' +
        '<div class="mb-2"><label class="text-muted small">Status Saat Ini</label><p class="mb-0"><span class="status-badge ' + badge.class + '">' + badge.label + '</span></p></div>' +
        '<div class="mb-2"><label class="text-muted small">Prioritas</label><p class="fw-semibold mb-0">' + (data.prioritas || 'Normal') + '</p></div>' +
        '<div class="mb-2"><label class="text-muted small">Petugas</label><p class="fw-semibold mb-0">' + (data.petugas || 'Belum ditugaskan') + '</p></div>' +
        '<div class="mb-2"><label class="text-muted small">Tanggal Lapor</label><p class="fw-semibold mb-0"><i class="bi bi-calendar me-1"></i>' + Helpers.formatTanggal(data.created_at) + '</p></div>' +
        (data.selesai_at ? '<div class="mb-2"><label class="text-muted small">Selesai</label><p class="fw-semibold mb-0"><i class="bi bi-check-circle me-1"></i>' + Helpers.formatTanggal(data.selesai_at) + '</p></div>' : '') +
        timelineHtml;
}
</script>
@endpush
