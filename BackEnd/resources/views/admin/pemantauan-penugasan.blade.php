@extends('admin.layout')

@section('title', 'Pemantauan & Penugasan')
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.min.css">
@endpush

@section('content')
<div class="container-fluid">
    <div class="admin-header animate-in">
        <div>
            <h4>Pemantauan & Penugasan</h4>
            <p>Pantau progress kebersihan dan jadwal petugas lapangan.</p>
        </div>
        <div>
            <button class="btn btn-admin btn-admin-primary" data-bs-toggle="modal" data-bs-target="#jadwalModal">
                <i class="bi bi-plus-lg"></i>Tambah Jadwal
            </button>
        </div>
    </div>

    <div class="row g-3 mb-4" id="progressCards"></div>

    <div class="row g-4 mb-4">
        <div class="col-12 animate-in">
            <div class="admin-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0"><i class="bi bi-calendar3 me-2"></i>Kalender Penugasan</h5>
                        <div class="d-flex gap-2 align-items-center">
                            <button class="btn btn-sm btn-outline-secondary" onclick="navigateMonth(-1)">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <strong id="monthYearLabel" class="mx-2" style="min-width:140px;text-align:center"></strong>
                            <button class="btn btn-sm btn-outline-secondary" onclick="navigateMonth(1)">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                    <div class="calendar-wrapper" id="calendarWrapper"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4" id="chart-progress-section">
        <div class="col-12 animate-in">
            <div class="chart-container">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                    <h5 class="fw-bold mb-0"><i class="bi bi-graph-up me-2"></i>Progress Harian</h5>
                    <div class="d-flex gap-2 flex-fill flex-md-grow-0 justify-content-end">
                        <select class="form-select form-select-sm" id="monthSelect" style="width:auto;min-width:80px"></select>
                        <select class="form-select form-select-sm" id="yearSelect" style="width:auto;min-width:85px"></select>
                        <button class="btn btn-sm btn-admin btn-admin-primary px-3" onclick="loadProgressChart()" title="Cari">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
                <canvas id="progressChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="jadwalModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Tambah Jadwal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tanggal</label>
                    <input type="date" class="form-control" id="jadwalTanggal">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Petugas</label>
                    <select class="form-select" id="jadwalPetugas"></select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Lokasi</label>
                    <input type="text" class="form-control" id="jadwalLokasi" placeholder="Area kampus">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Keterangan</label>
                    <textarea class="form-control" id="jadwalKeterangan" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-admin btn-admin-outline" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-admin btn-admin-primary" onclick="simpanJadwal()">Simpan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();
let progressChartInstance = null;
const dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

const fallbackProgress = [
    { label: 'Total Tugas', value: '12', icon: 'bi-list-task', color: 'blue', trend: 'Bulan ini', trend_up: true },
    { label: 'Selesai', value: '8', icon: 'bi-check-circle', color: 'green', trend: '66.7%', trend_up: true },
    { label: 'Dalam Progress', value: '3', icon: 'bi-arrow-repeat', color: 'orange', trend: '25%', trend_up: true },
    { label: 'Belum Dimulai', value: '1', icon: 'bi-clock', color: 'red', trend: '8.3%', trend_up: false },
];

const fallbackJadwal = [];

async function loadProgressCards() {
    const res = await API.get('/admin/progress');
    const container = document.getElementById('progressCards');
    let list;
    if (res.ok) {
        list = Array.isArray(res.body.data) ? res.body.data : (res.body.data?.list ?? fallbackProgress);
    } else {
        list = fallbackProgress;
    }
    container.innerHTML = (list.length ? list : fallbackProgress).map(s => `
        <div class="col-6 col-xl-3 animate-in">
            <div class="admin-card h-100">
                <div class="card-body py-3 px-3">
                    <div class="stat-card" style="padding:0;gap:0.75rem">
                        <div class="stat-icon ${s.color || 'blue'}" style="width:44px;height:44px;font-size:1.3rem">
                            <i class="bi ${s.icon || 'bi-clipboard-data'}"></i>
                        </div>
                        <div class="stat-info">
                            <h3 style="font-size:1.4rem">${s.value ?? 0}</h3>
                            <p style="font-size:0.75rem">${s.label || ''}</p>
                            <span class="stat-trend ${s.trend_up !== false ? 'up' : 'down'}" style="font-size:0.7rem">${s.trend || ''}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

async function loadCalendar() {
    const res = await API.get('/admin/jadwal');
    const jadwal = res.ok ? (Array.isArray(res.body.data) ? res.body.data : []) : [];
    renderCalendar(jadwal);
}

function renderCalendar(jadwal) {
    const label = document.getElementById('monthYearLabel');
    const monthName = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    label.textContent = `${monthName[currentMonth]} ${currentYear}`;

    const firstDay = new Date(currentYear, currentMonth, 1).getDay();
    const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
    const today = new Date();

    let html = `<div class="calendar-header-row">`;
    dayNames.forEach(d => { html += `<div class="calendar-header-cell">${d}</div>`; });
    html += `</div><div class="calendar-days-grid">`;

    for (let i = 0; i < firstDay; i++) {
        html += `<div class="calendar-day-cell empty"></div>`;
    }

    for (let d = 1; d <= daysInMonth; d++) {
        const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
        const isToday = today.getFullYear() === currentYear && today.getMonth() === currentMonth && today.getDate() === d;
        const dayJadwal = jadwal.filter(j => j.tanggal === dateStr || j.date === dateStr);
        const hasProgress = dayJadwal.length > 0;

        let cls = 'calendar-day-cell';
        if (isToday) cls += ' today';
        if (hasProgress) cls += ' has-progress';

        let petugasHtml = '';
        if (dayJadwal.length) {
            dayJadwal.forEach(j => {
                const nama = j.petugas_nama || j.petugas_name || j.nama_petugas || j.nama || '';
                petugasHtml += `<div class="petugas-name" title="${Helpers.escapeHtml(nama)}">${Helpers.escapeHtml(nama)}</div>`;
            });
        }

        html += `
            <div class="${cls}" onclick="alert('${dateStr}')">
                <span class="day-number">${d}</span>
                ${petugasHtml}
            </div>
        `;
    }

    html += `</div>`;
    document.getElementById('calendarWrapper').innerHTML = html;
}

function navigateMonth(delta) {
    currentMonth += delta;
    if (currentMonth > 11) { currentMonth = 0; currentYear++; }
    if (currentMonth < 0) { currentMonth = 11; currentYear--; }
    loadCalendar();
}

async function loadProgressChart() {
    const bulan = document.getElementById('monthSelect').value;
    const tahun = document.getElementById('yearSelect').value;
    const res = await API.get(`/admin/progress-harian?bulan=${bulan}&tahun=${tahun}`);

    if (progressChartInstance) progressChartInstance.destroy();
    const ctx = document.getElementById('progressChart').getContext('2d');

    let labels = [], dataLaporan = [], dataSelesai = [];
    if (res.ok && Array.isArray(res.body.data)) {
        res.body.data.forEach(item => {
            labels.push(item.tanggal || item.date || item.hari || '-');
            dataLaporan.push(item.total_masuk || item.total || item.laporan || 0);
            dataSelesai.push(item.total_selesai || item.selesai || item.completed || 0);
        });
    } else {
        const daysInMonth = new Date(tahun, bulan, 0).getDate();
        for (let d = 1; d <= Math.min(daysInMonth, 7); d++) {
            labels.push(`${d}`);
            dataLaporan.push(Math.floor(Math.random() * 5) + 1);
            dataSelesai.push(Math.floor(Math.random() * 4));
        }
    }

    progressChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Total Laporan',
                    data: dataLaporan,
                    borderColor: '#2e7d32',
                    backgroundColor: 'rgba(46,125,50,0.1)',
                    fill: true,
                    tension: .4,
                },
                {
                    label: 'Selesai',
                    data: dataSelesai,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16,185,129,0.1)',
                    fill: true,
                    tension: .4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'top' } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,.04)' } },
                x: { grid: { display: false } }
            }
        }
    });
}

async function loadPetugasDropdown() {
    const res = await API.get('/admin/petugas');
    const select = document.getElementById('jadwalPetugas');
    if (res.ok) {
        const list = Array.isArray(res.body.data) ? res.body.data : [];
        select.innerHTML = list.map(p =>
            `<option value="${p.id}">${Helpers.escapeHtml(p.nama || p.name)}</option>`
        ).join('');
    } else {
        select.innerHTML = '<option value="">Tidak ada petugas</option>';
    }
}

async function simpanJadwal() {
    const tanggal = document.getElementById('jadwalTanggal').value;
    const petugas_id = document.getElementById('jadwalPetugas').value;
    const lokasi = document.getElementById('jadwalLokasi').value.trim();
    const keterangan = document.getElementById('jadwalKeterangan').value.trim();
    if (!tanggal || !petugas_id) {
        Helpers.showAlert('Tanggal dan petugas harus diisi.', 'warning');
        return;
    }
    const res = await API.post('/admin/jadwal', { tanggal, petugas_id, lokasi, keterangan });
    if (res.ok) {
        Helpers.showAlert('Jadwal berhasil ditambahkan!', 'success');
        bootstrap.Modal.getInstance(document.getElementById('jadwalModal')).hide();
        document.getElementById('jadwalTanggal').value = '';
        document.getElementById('jadwalPetugas').selectedIndex = 0;
        document.getElementById('jadwalLokasi').value = '';
        document.getElementById('jadwalKeterangan').value = '';
        loadCalendar();
    } else {
        const msg = res.body?.message || 'Gagal menyimpan jadwal.';
        Helpers.showAlert(msg, 'danger');
    }
}

function initMonthYearSelects() {
    const monthSelect = document.getElementById('monthSelect');
    const yearSelect = document.getElementById('yearSelect');
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    months.forEach((m, i) => {
        monthSelect.innerHTML += `<option value="${i + 1}" ${i === new Date().getMonth() ? 'selected' : ''}>${m}</option>`;
    });
    for (let y = new Date().getFullYear() - 2; y <= new Date().getFullYear() + 1; y++) {
        yearSelect.innerHTML += `<option value="${y}" ${y === new Date().getFullYear() ? 'selected' : ''}>${y}</option>`;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    loadProgressCards();
    loadCalendar();
    initMonthYearSelects();
    loadProgressChart();
    loadPetugasDropdown();
});
</script>
@endpush
