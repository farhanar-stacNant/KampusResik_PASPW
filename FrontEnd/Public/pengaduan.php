<?php
$pageTitle = 'Pengaduan';
require_once __DIR__ . '/../includes/api-config.php';
require_once __DIR__ . '/../includes/head-public.php';
require_once __DIR__ . '/../includes/navbar-public.php';

$kategoriSampah = [
    ['value' => 'ORG', 'label' => 'Organik', 'icon' => 'bi-tree'],
    ['value' => 'ANR', 'label' => 'Anorganik', 'icon' => 'bi-box'],
    ['value' => 'B3', 'label' => 'B3 (Berbahaya & Beracun)', 'icon' => 'bi-exclamation-triangle']
];

$areaKampus = [
    'Kampus 2 - FST',
    'Kampus 2 - FIK',
    'Kampus 3 - FST',
    'Kampus 3 - FIK',
    'Perpustakaan',
    'Kantin Kampus - 2',
    'Kantin Kampus - 3',
    'Masjid Kampus - 2',
    'Aula Kampus -2',
    'Aula Kampus - 3',
    'Lapangan Olahraga kampus -2',
    'Parkiran Utama Kampus - 2',
    'Parkiran Dosen Kampus - 2',
    'Parkiran Utama Kampus - 3',
];
?>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<section class="page-header py-4">
    <div class="container-fluid px-3 px-md-4">
        <h2 class="fw-bold mb-1">Form Pengaduan Kebersihan</h2>
        <p class="text-muted mb-0">Bantu kami menjaga kebersihan kampus dengan melaporkan kendala yang Anda temukan.</p>
    </div>
</section>

<section class="form-section pb-5">
    <div class="container-fluid px-3 px-md-4">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-7 col-xl-6">
                <div class="card-clean p-3 p-md-4 p-lg-5">
                    <form id="formPengaduan" enctype="multipart/form-data" onsubmit="return submitForm(event)">
                        
                        <!-- Lokasi -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-uppercase small text-muted mb-2">
                                Lokasi Temuan <span class="text-danger">*</span>
                            </label>
                            <select class="form-control-custom w-100 mb-3" name="lokasi" id="lokasiSelect" required>
                                <option value="" disabled selected>Pilih area kampus...</option>
                                <?php foreach ($areaKampus as $area): ?>
                                    <option value="<?= htmlspecialchars($area) ?>"><?= htmlspecialchars($area) ?></option>
                                <?php endforeach; ?>
                            </select>
                            
                            <div class="mb-2 d-flex justify-content-between align-items-center">
                                <label class="form-label fw-semibold text-uppercase small text-muted mb-0">Pilih Lokasi di Peta</label>
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="getCurrentLocation()">
                                    <i class="bi bi-geo-alt"></i> Cari Lokasi
                                </button>
                            </div>
                            <div id="map" style="height: 250px; border-radius: 12px; border: 1px solid #E2E8F0;"></div>
                            <input type="hidden" name="latitude" id="latitude">
                            <input type="hidden" name="longitude" id="longitude">
                        </div>

                        <!-- Jenis Sampah -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-uppercase small text-muted mb-2">
                                Jenis Sampah <span class="text-danger">*</span>
                            </label>
                            <select class="form-control-custom w-100" name="jenis_sampah" id="jenisSampah" required>
                                <option value="" disabled selected>Pilih jenis laporan...</option>
                                <?php foreach ($kategoriSampah as $kat): ?>
                                    <option value="<?= $kat['value'] ?>"><?= $kat['label'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Deskripsi -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-uppercase small text-muted mb-2">
                                Deskripsi Detail <span class="text-muted fw-normal">(Opsional)</span>
                            </label>
                            <textarea class="form-control-custom w-100" name="deskripsi" rows="4" placeholder="Ceritakan detail temuan Anda di sini..."></textarea>
                        </div>

                        <!-- Nomor WA -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-uppercase small text-muted mb-2">
                                Nomor WhatsApp <span class="text-muted fw-normal">(Opsional)</span>
                            </label>
                            <div class="input-group-custom">
                                <span class="input-icon">
                                    <i class="bi bi-whatsapp text-success"></i>
                                </span>
                                <input type="tel" class="form-control-with-icon" name="no_wa" id="noWa" 
                                       placeholder="08xx-xxxx-xxxx atau +62xxx" 
                                       pattern="[0-9+\s-]+">
                            </div>
                            <small class="text-muted">Isi jika ingin menerima notifikasi saat laporan selesai</small>
                        </div>

                        <!-- Upload Foto -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-uppercase small text-muted mb-2">
                                Unggah Foto <span class="text-danger">*</span>
                            </label>
                            <div class="upload-area" id="uploadArea" onclick="document.getElementById('fotoInput').click()">
                                <input type="file" name="foto" id="fotoInput" accept="image/jpeg,image/png" 
                                       style="display: none;" required onchange="handleFileSelect(this)">
                                <div class="upload-content" id="uploadContent">
                                    <i class="bi bi-camera fs-1 text-muted"></i>
                                    <p class="mb-1 mt-2">Klik untuk ambil foto atau pilih dari galeri</p>
                                    <small class="text-muted">Maksimum size: 1MB</small>
                                </div>
                                <div class="upload-preview d-none" id="uploadPreview">
                                    <img id="previewImg" src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                                    <button type="button" class="btn btn-sm btn-danger mt-2" onclick="clearFile(event)">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </div>
                            </div>
                            <small class="text-danger d-none" id="fileError">File terlalu besar! Maksimum 1MB.</small>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-3">
                            <button type="submit" class="btn-submit flex-fill" id="btnSubmit">
                                <i class="bi bi-send"></i> Kirim Pengaduan
                            </button>
                            <a href="index.php" class="btn-cancel">
                                Batal
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Sukses -->
<div class="modal fade" id="modalSukses" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-body text-center p-5">
                <div class="mb-3">
                    <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="bi bi-check-lg text-success fs-1"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-2">Laporan Telah Dikirim!</h4>
                <p class="text-muted mb-4">Simpan kode berikut untuk memantau status laporan Anda.</p>
                
                <div class="bg-light rounded-3 p-3 mb-3">
                    <small class="text-muted text-uppercase">Kode Laporan</small>
                    <div class="d-flex align-items-center justify-content-center gap-2 mt-1">
                        <h3 class="fw-bold text-success mb-0" id="kodeLaporan">-</h3>
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="copyKode()" id="btnCopy">
                            <i class="bi bi-copy"></i>
                        </button>
                    </div>
                </div>
                
                <p class="small text-muted mb-0">Kode juga bisa dilihat di halaman <a href="status.php">Status Laporan</a></p>
                
                <button type="button" class="btn btn-success w-100 mt-4" data-bs-dismiss="modal" onclick="resetForm()">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
let map, marker;

function initMap() {
    const defaultLat = -6.2088;
    const defaultLng = 106.8456;
    
    map = L.map('map').setView([defaultLat, defaultLng], 15);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);
    
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                map.setView([lat, lng], 16);
                setMarker(lat, lng);
            },
            () => setMarker(defaultLat, defaultLng)
        );
    } else {
        setMarker(defaultLat, defaultLng);
    }
    
    map.on('click', (e) => setMarker(e.latlng.lat, e.latlng.lng));
}

function setMarker(lat, lng) {
    if (marker) map.removeLayer(marker);
    marker = L.marker([lat, lng]).addTo(map);
    document.getElementById('latitude').value = lat;
    document.getElementById('longitude').value = lng;
}

function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((position) => {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            map.setView([lat, lng], 16);
            setMarker(lat, lng);
        }, () => {
            alert('Tidak dapat mengakses lokasi. Pastikan izin lokasi diaktifkan.');
        });
    } else {
        alert('Browser tidak mendukung geolocation.');
    }
}

function handleFileSelect(input) {
    const file = input.files[0];
    const maxSize = 1024 * 1024;
    
    if (file) {
        if (file.size > maxSize) {
            document.getElementById('fileError').classList.remove('d-none');
            input.value = '';
            return;
        }
        
        document.getElementById('fileError').classList.add('d-none');
        
        const reader = new FileReader();
        reader.onload = (e) => {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('uploadContent').classList.add('d-none');
            document.getElementById('uploadPreview').classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    }
}

function clearFile(e) {
    e.stopPropagation();
    document.getElementById('fotoInput').value = '';
    document.getElementById('uploadContent').classList.remove('d-none');
    document.getElementById('uploadPreview').classList.add('d-none');
    document.getElementById('fileError').classList.add('d-none');
}

function copyKode() {
    const kode = document.getElementById('kodeLaporan').textContent;
    navigator.clipboard.writeText(kode).then(() => {
        const btn = document.getElementById('btnCopy');
        btn.innerHTML = '<i class="bi bi-check"></i>';
        btn.classList.replace('btn-outline-success', 'btn-success');
        setTimeout(() => {
            btn.innerHTML = '<i class="bi bi-copy"></i>';
            btn.classList.replace('btn-success', 'btn-outline-success');
        }, 2000);
    }).catch(() => {
        // Fallback
        const textarea = document.createElement('textarea');
        textarea.value = kode;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
    });
}

function resetForm() {
    document.getElementById('formPengaduan').reset();
    document.getElementById('uploadContent').classList.remove('d-none');
    document.getElementById('uploadPreview').classList.add('d-none');
    document.getElementById('fileError').classList.add('d-none');
    if (marker) map.removeLayer(marker);
    document.getElementById('latitude').value = '';
    document.getElementById('longitude').value = '';
}

async function submitForm(e) {
    e.preventDefault();
    
    const btnSubmit = document.getElementById('btnSubmit');
    btnSubmit.disabled = true;
    btnSubmit.innerHTML = '<i class="bi bi-hourglass-split"></i> Mengirim...';
    
    const form = e.target;
    const formData = new FormData(form);
    
    try {
        const response = await fetch('<?= API_BASE_URL ?>/public/reports', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-Api-Token': '<?= API_SECRET_TOKEN ?>'
            },
            credentials: 'omit',
            body: formData
        });
        
        const result = await response.json();
        
        if (response.ok && result.data) {
            document.getElementById('kodeLaporan').textContent = result.data.kode;
            const modal = new bootstrap.Modal(document.getElementById('modalSukses'));
            modal.show();
        } else {
            alert('Gagal mengirim laporan: ' + (result.message || 'Terjadi kesalahan'));
        }
    } catch (error) {
        alert('Gagal mengirim laporan. Pastikan koneksi internet stabil.');
        console.error('Error:', error);
    } finally {
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = '<i class="bi bi-send"></i> Kirim Pengaduan';
    }
    
    return false;
}

document.addEventListener('DOMContentLoaded', initMap);
</script>
<?php require_once __DIR__ . '/../includes/footer-public.php'; ?>