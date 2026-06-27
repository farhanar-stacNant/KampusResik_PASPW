<?php
require_once '../includes/api-config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if (!function_exists('fetch_api')) {
    function fetch_api($endpoint, $method = 'GET', $data = null, $token = null) {
        $url = rtrim(API_BASE_URL, '/') . '/' . ltrim($endpoint, '/');
        $ch = curl_init($url);
        $headers = ['Accept: application/json'];
        if ($token) $headers[] = 'Authorization: Bearer ' . $token;
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            $headers[] = 'Content-Type: application/json';
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ['code' => $httpCode, 'body' => json_decode($response, true)];
    }
}

require_petugas();

$pageTitle = 'Detail Laporan - Petugas';
$token = get_auth_token();

function mapStatusClass($status) {
    return match($status) {
        'dikirim' => 'status-belum',
        'diterima' => 'status-proses',
        'diproses' => 'status-proses',
        'selesai' => 'status-selesai',
        'ditolak' => 'status-tertunda',
        default => 'status-belum',
    };
}
function mapStatusLabel($status) {
    return match($status) {
        'dikirim' => 'Dikirim',
        'diterima' => 'Diterima',
        'diproses' => 'Diproses',
        'selesai' => 'Selesai',
        'ditolak' => 'Ditolak',
        default => ucfirst($status),
    };
}
function mapJenisLabel($jenis) {
    return match($jenis) {
        'ORG' => 'Sampah Organik',
        'ANR' => 'Sampah Anorganik',
        'B3' => 'Sampah B3',
        default => $jenis,
    };
}

$status_options = ['dikirim', 'diterima', 'diproses', 'selesai', 'ditolak'];

// Fetch laporan dari API
$laporan_list = [];
try {
    $result = fetch_api('/public/reports?limit=50', 'GET', null, $token);
    if ($result['code'] === 200 && isset($result['body']['data'])) {
        $laporan_list = $result['body']['data'];
    }
} catch (Exception $e) {
    $laporan_list = [];
}

include '../includes/head-petugas.php';
include '../includes/navbar-petugas.php';
?>

<main class="main-content">
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
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                                    <input type="text" class="form-control border-start-0" placeholder="Cari lokasi atau kode..." id="searchLaporan">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="filterStatus">
                                    <option value="">Semua Status</option>
                                    <?php foreach ($status_options as $opt): ?>
                                    <option value="<?= $opt ?>"><?= mapStatusLabel($opt) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="date" class="form-control" id="filterTanggal">
                            </div>
                            <div class="col-md-2">
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
                                <tbody>
                                    <?php if (empty($laporan_list)): ?>
                                    <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data laporan</td></tr>
                                    <?php else: ?>
                                    <?php foreach ($laporan_list as $lap): 
                                        $status = $lap['status'] ?? 'dikirim';
                                        $status_class = mapStatusClass($status);
                                    ?>
                                    <tr data-status="<?= $status ?>" data-lokasi="<?= strtolower(htmlspecialchars($lap['lokasi'] ?? '')) ?>" data-kode="<?= strtolower(htmlspecialchars($lap['kode'] ?? '')) ?>">
                                        <td><strong><?= htmlspecialchars($lap['kode'] ?? '#'.$lap['id']) ?></strong></td>
                                        <td><i class="bi bi-geo-alt text-muted me-1"></i><?= htmlspecialchars($lap['lokasi'] ?? '-') ?></td>
                                        <td><span class="badge bg-light text-dark border"><?= mapJenisLabel($lap['kategori'] ?? '-') ?></span></td>
                                        <td>
                                            <select class="form-select form-select-sm status-select" data-id="<?= $lap['id'] ?>" style="min-width: 130px;">
                                                <?php foreach ($status_options as $opt): ?>
                                                <option value="<?= $opt ?>" <?= ($status === $opt) ? 'selected' : '' ?>><?= mapStatusLabel($opt) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td><i class="bi bi-calendar text-muted me-1"></i><?= htmlspecialchars($lap['created_at'] ?? '-') ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-info" onclick="openDetailModal(<?= $lap['id'] ?>)"><i class="bi bi-eye"></i></button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal Detail Laporan (Dinamis) -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalTitle">Detail Laporan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBody">
                <div class="text-center py-4"><div class="spinner-border text-primary"></div><p class="text-muted mt-2">Memuat detail...</p></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
const API_BASE = '<?= rtrim(API_BASE_URL, '/') ?>';
const TOKEN = '<?= addslashes($token ?? '') ?>';

function filterLaporan() {
    const search = document.getElementById('searchLaporan').value.toLowerCase();
    const status = document.getElementById('filterStatus').value;
    const tanggal = document.getElementById('filterTanggal').value;
    const rows = document.querySelectorAll('#tabelLaporan tbody tr');
    rows.forEach(row => {
        const rowStatus = row.dataset.status;
        const rowLokasi = row.dataset.lokasi;
        const rowKode = row.dataset.kode;
        const matchSearch = !search || rowLokasi.includes(search) || rowKode.includes(search);
        const matchStatus = !status || rowStatus === status;
        row.style.display = (matchSearch && matchStatus) ? '' : 'none';
    });
}

// Update status via AJAX
document.querySelectorAll('.status-select').forEach(sel => {
    sel.addEventListener('change', async function() {
        const id = this.dataset.id;
        const status = this.value;
        try {
            const res = await fetch(`${API_BASE}/laporan/${id}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': 'Bearer <?= $token ?>',
                    'X-Api-Token': '<?= API_SECRET_TOKEN ?>'
                },
                body: JSON.stringify({ status: status })
            });
            const data = await res.json();
            if (res.ok) {
                this.closest('tr').dataset.status = status;
                filterLaporan();
                alert('Status berhasil diperbarui');
            } else {
                alert(data.message || 'Gagal memperbarui status');
            }
        } catch (e) {
            alert('Terjadi kesalahan jaringan');
        }
    });
});

async function openDetailModal(id) {
    const modal = new bootstrap.Modal(document.getElementById('detailModal'));
    document.getElementById('modalTitle').textContent = 'Detail Laporan #' + id;
    document.getElementById('modalBody').innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary"></div><p class="text-muted mt-2">Memuat detail...</p></div>';
    modal.show();
    
    try {
        const res = await fetch(`${API_BASE}/public/reports/${id}`, {
            headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + TOKEN }
        });
        const result = await res.json();
        const data = result.data || {};
        
        const statusMap = {
            'dikirim': ['status-belum','Dikirim'], 'diterima': ['status-proses','Diterima'],
            'diproses': ['status-proses','Diproses'], 'selesai': ['status-selesai','Selesai'],
            'ditolak': ['status-tertunda','Ditolak']
        };
        const [statusClass, statusLabel] = statusMap[data.status] || ['status-belum', data.status];
        const jenisMap = { 'ORG': 'Sampah Organik', 'ANR': 'Sampah Anorganik', 'B3': 'Sampah B3' };
        const jenisLabel = jenisMap[data.jenis_sampah] || jenisMap[data.kategori] || (data.kategori || '-');
        
        let fotoHtml = data.foto 
            ? `<img src="${data.foto}" class="img-fluid rounded-3" style="max-height:200px;" alt="Foto Laporan">`
            : `<div class="bg-light rounded-3 p-3 text-center" style="height:180px;"><i class="bi bi-image text-muted" style="font-size:3rem;"></i><p class="text-muted small mt-2">Tidak ada foto</p></div>`;
        
        let timelineHtml = '';
        if (data.timeline && data.timeline.length > 0) {
            timelineHtml = '<div class="mt-3"><label class="text-muted small">Timeline</label><div class="timeline mt-2">';
            data.timeline.forEach(t => {
                timelineHtml += `<div class="timeline-item mb-3 pb-3" style="border-left:3px solid #1565C0;padding-left:1rem;position:relative;">
                    <div style="position:absolute;left:-7px;top:0;width:12px;height:12px;border-radius:50%;background:#1565C0;border:2px solid #fff;"></div>
                    <p class="fw-semibold mb-0">${t.status}</p>
                    <p class="text-muted small mb-0">${t.deskripsi || ''}</p>
                    <p class="text-muted small mb-0"><i class="bi bi-clock me-1"></i>${t.waktu}</p>
                </div>`;
            });
            timelineHtml += '</div></div>';
        }
        
        document.getElementById('modalBody').innerHTML = `
            <div class="mb-3"><label class="text-muted small">Foto Laporan</label><div class="mt-1">${fotoHtml}</div></div>
            <div class="mb-2"><label class="text-muted small">Kode Laporan</label><p class="fw-semibold mb-0">${data.kode || '-'}</p></div>
            <div class="mb-2"><label class="text-muted small">Lokasi</label><p class="fw-semibold mb-0"><i class="bi bi-geo-alt me-1"></i>${data.lokasi || '-'}</p></div>
            <div class="mb-2"><label class="text-muted small">Kategori</label><p class="fw-semibold mb-0">${jenisLabel}</p></div>
            <div class="mb-2"><label class="text-muted small">Deskripsi</label><p class="mb-0">${data.deskripsi || '-'}</p></div>
            <div class="mb-2"><label class="text-muted small">Status Saat Ini</label><p class="mb-0"><span class="status-badge ${statusClass}">${statusLabel}</span></p></div>
            <div class="mb-2"><label class="text-muted small">Prioritas</label><p class="fw-semibold mb-0">${data.prioritas || 'Normal'}</p></div>
            <div class="mb-2"><label class="text-muted small">Petugas</label><p class="fw-semibold mb-0">${data.petugas || 'Belum ditugaskan'}</p></div>
            <div class="mb-2"><label class="text-muted small">Tanggal Lapor</label><p class="fw-semibold mb-0"><i class="bi bi-calendar me-1"></i>${data.created_at || '-'}</p></div>
            ${data.selesai_at ? `<div class="mb-2"><label class="text-muted small">Selesai</label><p class="fw-semibold mb-0"><i class="bi bi-check-circle me-1"></i>${data.selesai_at}</p></div>` : ''}
            ${timelineHtml}
        `;
    } catch (e) {
        document.getElementById('modalBody').innerHTML = '<div class="alert alert-danger">Gagal memuat detail laporan.</div>';
    }
}
</script>

<?php include '../includes/footer-petugas.php'; ?>