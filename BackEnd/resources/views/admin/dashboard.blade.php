@extends('admin.layout')

@section('title', 'Dashboard')
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.min.css">
@endpush

@section('content')
<div class="container-fluid">
    <div class="admin-header animate-in">
        <div>
            <h4>Ringkasan Statistik</h4>
            <p>Pantau kebersihan dan pengelolaan sampah kampus secara real-time.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-admin btn-admin-outline" onclick="document.getElementById('tren-mingguan').scrollIntoView({behavior:'smooth'})">
                <i class="bi bi-calendar3 me-2"></i>Minggu Ini
            </button>
            <a href="/admin/rekap" class="btn btn-admin btn-admin-outline"><i class="bi bi-file-excel me-2"></i>Export Laporan</a>
        </div>
    </div>

    <div id="risikoAlert" class="alert alert-danger d-none align-items-center mb-4 animate-in" role="alert" style="border-left: 5px solid #dc3545;">
        <div class="d-flex align-items-center gap-3">
            <i class="bi bi-exclamation-triangle-fill fs-3"></i>
            <div>
                <strong class="d-block">Peringatan Risiko Tinggi!</strong>
                <span>Terdapat <strong class="risiko-count">0</strong> laporan dengan risiko tinggi yang perlu segera ditindaklanjuti.</span>
            </div>
            <a href="/admin/laporan?level_risiko=tinggi" class="btn btn-outline-danger btn-sm ms-auto">Lihat Laporan <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
    </div>

    <div class="row g-3 mb-4" id="statCards"></div>

    <div class="row g-4">
        <div class="col-lg-8 animate-in">
            <div class="chart-container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div id="tren-mingguan">
                        <h5 class="fw-bold mb-0">Tren Laporan Mingguan</h5>
                    </div>
                    <div class="d-flex gap-3">
                        <span class="d-flex align-items-center gap-2" style="font-size:0.85rem">
                            <span style="width:12px;height:12px;background:var(--adm-primary);border-radius:3px;display:inline-block"></span>
                            Minggu Ini
                        </span>
                        <span class="d-flex align-items-center gap-2" style="font-size:0.85rem">
                            <span style="width:12px;height:12px;background:#90a4ae;border-radius:3px;display:inline-block"></span>
                            Minggu Lalu
                        </span>
                    </div>
                </div>
                <canvas id="weeklyChart" height="220"></canvas>
            </div>
        </div>

        <div class="col-lg-4 animate-in">
            <div class="admin-card h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-4">Kategori Sampah Terpopuler</h5>
                    <div id="popularCategories"></div>
                    <div class="mt-4 p-3 rounded-3" style="background:linear-gradient(135deg,#2e7d32,#388e3c);color:#fff">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-lightbulb" style="font-size:1.5rem"></i>
                            <div>
                                <p class="mb-0 fw-semibold" style="font-size:.9rem">Tips Pengelolaan</p>
                                <p class="mb-0" style="font-size:.8rem;opacity:.9">Pastikan area TPS3R selalu bersih untuk mencegah penumpukan lalat dan bau tidak sedap.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
let weeklyChart = null;

const fallbackStats = [
    { label: 'Total Laporan', value: '13', icon: 'bi-clipboard-data', color: 'blue', trend: '+12%', trend_up: true },
    { label: 'Laporan Masuk', value: '5', icon: 'bi-inbox', color: 'orange', trend: 'Perlu Tindak', trend_up: false },
    { label: 'Sedang Diproses', value: '3', icon: 'bi-arrow-repeat', color: 'green', trend: '+5%', trend_up: true },
    { label: 'Selesai Hari Ini', value: '5', icon: 'bi-check-circle', color: 'green', trend: '+8%', trend_up: true },
];

const fallbackChart = [
    { hari: 'Sen', minggu_ini: 2, minggu_lalu: 1 },
    { hari: 'Sel', minggu_ini: 3, minggu_lalu: 2 },
    { hari: 'Rab', minggu_ini: 1, minggu_lalu: 2 },
    { hari: 'Kam', minggu_ini: 4, minggu_lalu: 3 },
    { hari: 'Jum', minggu_ini: 2, minggu_lalu: 2 },
    { hari: 'Sab', minggu_ini: 1, minggu_lalu: 1 },
    { hari: 'Min', minggu_ini: 0, minggu_lalu: 0 },
];

const fallbackKategori = [
    { nama: 'Sampah Organik', persen: 35, warna: '#2e7d32' },
    { nama: 'Sampah Plastik', persen: 25, warna: '#388e3c' },
    { nama: 'Sampah Medis', persen: 15, warna: '#1b5e20' },
    { nama: 'Sampah B3', persen: 15, warna: '#00897b' },
    { nama: 'Sampah Kertas', persen: 10, warna: '#90a4ae' },
];

async function loadDashboard() {
    const [statsRes, chartRes, catRes] = await Promise.all([
        API.get('/admin/statistics'),
        API.get('/admin/weekly-chart'),
        API.get('/admin/popular-categories'),
    ]);

    const statsList = statsRes.ok ? (statsRes.body.data?.list ?? statsRes.body.data ?? []) : fallbackStats;
    const chartData = chartRes.ok ? (chartRes.body.data ?? []) : fallbackChart;
    const kategori = catRes.ok ? (catRes.body.data ?? []) : fallbackKategori;

    renderStats(statsList.length ? statsList : fallbackStats);

    const risikoTinggi = statsRes.body.data?.laporan_risiko_tinggi ?? 0;
    const risikoAlert = document.getElementById('risikoAlert');
    if (risikoAlert && risikoTinggi > 0) {
        risikoAlert.classList.remove('d-none');
        risikoAlert.querySelector('.risiko-count').textContent = risikoTinggi;
    }

    renderChart(chartData.length ? chartData : fallbackChart);
    renderKategori(kategori.length ? kategori : fallbackKategori);
}

function renderStats(stats) {
    const container = document.getElementById('statCards');
    container.innerHTML = stats.map(s => {
        const trendIcon = s.trend_up !== false ? '↗' : '⚠';
        const trendClass = s.trend_up !== false ? 'up' : 'down';
        return `
            <div class="col-6 col-xl-3 animate-in">
                <div class="admin-card">
                    <div class="card-body">
                        <div class="stat-card">
                            <div class="stat-icon ${s.color || 'blue'}">
                                <i class="bi ${s.icon || 'bi-clipboard-data'}"></i>
                            </div>
                            <div class="stat-info">
                                <h3>${s.value ?? 0}</h3>
                                <p>${s.label || ''}</p>
                                <span class="stat-trend ${trendClass}">${trendIcon} ${s.trend || '0%'}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

function renderChart(data) {
    if (weeklyChart) weeklyChart.destroy();
    const ctx = document.getElementById('weeklyChart').getContext('2d');
    weeklyChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(d => d.hari),
            datasets: [
                {
                    label: 'Minggu Lalu',
                    data: data.map(d => d.minggu_lalu),
                    backgroundColor: '#90a4ae',
                    borderRadius: 6,
                    borderSkipped: false,
                },
                {
                    label: 'Minggu Ini',
                    data: data.map(d => d.minggu_ini),
                    backgroundColor: 'rgba(46,125,50,.85)',
                    borderRadius: 6,
                    borderSkipped: false,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,.04)' } },
                x: { grid: { display: false } }
            }
        }
    });
}

function renderKategori(data) {
    const container = document.getElementById('popularCategories');
    if (!data.length) {
        container.innerHTML = `<div class="text-center text-muted py-5">
            <i class="bi bi-inbox fs-1 d-block mb-3 opacity-25"></i>
            <p>Belum ada data kategori</p>
            <small>Data akan muncul setelah ada laporan masuk</small>
        </div>`;
        return;
    }
    container.innerHTML = data.map(k => `
        <div class="mb-3">
            <div class="d-flex justify-content-between mb-1">
                <span class="fw-semibold" style="font-size:.9rem">${Helpers.escapeHtml(k.nama)}</span>
                <span class="fw-bold" style="font-size:.9rem">${k.persen}%</span>
            </div>
            <div class="progress-admin">
                <div class="progress-bar" style="width:${k.persen}%;                background:${k.warna || '#2e7d32'}"></div>
            </div>
        </div>
    `).join('');
}

document.addEventListener('DOMContentLoaded', loadDashboard);
</script>
@endpush
