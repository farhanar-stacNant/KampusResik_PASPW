@extends('petugas.layout')

@section('title', 'Dashboard - Petugas')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 animate-fade-in">
        <div class="col-12">
            <h4 class="fw-bold text-dark mb-1" id="welcomeMessage">Selamat Datang, Petugas!</h4>
            <p class="text-muted mb-0">Berikut ringkasan tugas Anda hari ini</p>
        </div>
    </div>

    <div class="row g-3 mb-4" id="statCards">
        <div class="col-6 col-lg-3 animate-fade-in">
            <div class="card card-stat h-100">
                <div class="card-body">
                    <div class="stat-icon" style="background:#e8f5e9;color:#2e7d32;"><i class="bi bi-clipboard-check"></i></div>
                    <div class="stat-value" id="statTotal">0</div>
                    <div class="stat-label">Total Laporan</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 animate-fade-in">
            <div class="card card-stat h-100">
                <div class="card-body">
                    <div class="stat-icon" style="background:#e8f5e9;color:#2e7d32;"><i class="bi bi-arrow-repeat"></i></div>
                    <div class="stat-value" id="statDiproses">0</div>
                    <div class="stat-label">Diproses</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 animate-fade-in">
            <div class="card card-stat h-100">
                <div class="card-body">
                    <div class="stat-icon" style="background:#e8f5e9;color:#2e7d32;"><i class="bi bi-check-circle"></i></div>
                    <div class="stat-value" id="statSelesai">0</div>
                    <div class="stat-label">Selesai</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 animate-fade-in">
            <div class="card card-stat h-100">
                <div class="card-body">
                    <div class="stat-icon" style="background:#fff3e0;color:#e65100;"><i class="bi bi-send"></i></div>
                    <div class="stat-value" id="statDikirim">0</div>
                    <div class="stat-label">Dikirim</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row animate-fade-in">
        <div class="col-12">
            <div class="card card-stat">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0"><i class="bi bi-geo-alt-fill text-success me-2"></i>Lokasi Tugas Hari Ini</h5>
                        <a href="/petugas/laporan" class="btn btn-sm btn-petugas btn-petugas-primary">Lihat Semua <i class="bi bi-arrow-right ms-1"></i></a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-petugas mb-0" id="activeTasksTable">
                            <thead><tr><th>Lokasi</th><th>Kategori</th><th>Status</th><th>Aksi</th></tr></thead>
                            <tbody id="activeTasksBody">
                                <tr><td colspan="4" class="text-center text-muted py-4">Memuat data...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', async function () {
    const user = Auth.getUser();
    const nama = user.name || user.nama || 'Petugas';
    document.getElementById('welcomeMessage').textContent = 'Selamat Datang, ' + nama + '!';

    const res = await API.get('/public/reports?limit=50');
    if (!res.ok || !res.data?.data) return;

    const reports = res.data.data;
    let total = reports.length, diproses = 0, selesai = 0, dikirim = 0;

    reports.forEach(r => {
        const s = r.status || '';
        if (s === 'diproses' || s === 'diterima') diproses++;
        else if (s === 'selesai') selesai++;
        else if (s === 'dikirim') dikirim++;
    });

    document.getElementById('statTotal').textContent = total;
    document.getElementById('statDiproses').textContent = diproses;
    document.getElementById('statSelesai').textContent = selesai;
    document.getElementById('statDikirim').textContent = dikirim;

    const activeTasks = reports.filter(r => (r.status || '') !== 'selesai').slice(0, 5);
    const tbody = document.getElementById('activeTasksBody');

    if (activeTasks.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">Tidak ada tugas aktif</td></tr>';
        return;
    }

    const jenisMap = { 'ORG': 'Sampah Organik', 'ANR': 'Sampah Anorganik', 'B3': 'Sampah B3' };

    tbody.innerHTML = activeTasks.map(r => {
        const badge = Helpers.statusBadge(r.status);
        const kategori = r.kategori?.nama_kategori || jenisMap[r.kategori] || r.kategori || '-';
        return `<tr>
            <td><i class="bi bi-geo-alt text-muted me-2"></i>${Helpers.escapeHtml(r.lokasi)}</td>
            <td><span class="badge bg-light text-dark border">${kategori}</span></td>
            <td><span class="status-badge ${badge.class}">${badge.label}</span></td>
            <td><a href="/petugas/laporan" class="btn btn-sm btn-outline-success"><i class="bi bi-pencil-square"></i></a></td>
        </tr>`;
    }).join('');
});
</script>
@endpush
