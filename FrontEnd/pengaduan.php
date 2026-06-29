<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaduan - KampusResik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
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
                    <a class="nav-link active" href="pengaduan.php">Pengaduan</a>
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
        <h2 class="fw-bold mb-1">Form Pengaduan Kebersihan</h2>
        <p class="text-muted mb-0">Bantu kami menjaga kebersihan kampus dengan melaporkan kendala yang Anda temukan.</p>
    </div>
</section>

<section class="form-section pb-5">
    <div class="container-fluid px-3 px-md-4">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-7 col-xl-6">
                <div class="card-clean p-3 p-md-4 p-lg-5">
                    <form id="formPengaduan">
                        <!-- Lokasi -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-uppercase small text-muted mb-2">
                                Lokasi Temuan <span class="text-danger">*</span>
                            </label>
                            <select class="form-control-custom w-100 mb-3" id="lokasiSelect" required>
                                <option value="" disabled selected>Pilih area kampus...</option>
                                <option value="Kampus 2 - FST">Kampus 2 - FST</option>
                                <option value="Kampus 2 - FIK">Kampus 2 - FIK</option>
                                <option value="Kampus 3 - FST">Kampus 3 - FST</option>
                                <option value="Kampus 3 - FIK">Kampus 3 - FIK</option>
                                <option value="Perpustakaan">Perpustakaan</option>
                                <option value="Kantin Kampus - 2">Kantin Kampus - 2</option>
                                <option value="Kantin Kampus - 3">Kantin Kampus - 3</option>
                                <option value="Masjid Kampus - 2">Masjid Kampus - 2</option>
                                <option value="Aula Kampus -2">Aula Kampus -2</option>
                                <option value="Aula Kampus - 3">Aula Kampus - 3</option>
                                <option value="Lapangan Olahraga kampus -2">Lapangan Olahraga kampus -2</option>
                                <option value="Parkiran Utama Kampus - 2">Parkiran Utama Kampus - 2</option>
                                <option value="Parkiran Dosen Kampus - 2">Parkiran Dosen Kampus - 2</option>
                                <option value="Parkiran Utama Kampus - 3">Parkiran Utama Kampus - 3</option>
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

                        <!-- Kategori Sampah -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-uppercase small text-muted mb-2">
                                Kategori Sampah <span class="text-danger">*</span>
                            </label>
                            <select class="form-control-custom w-100" id="kategoriSelect" required>
                                <option value="" disabled selected>Memuat kategori...</option>
                            </select>
                        </div>

                        <!-- Nama Pelapor -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-uppercase small text-muted mb-2">
                                Nama Pelapor <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control-custom w-100" id="namaPelapor" placeholder="Masukkan nama lengkap Anda" required>
                        </div>

                        <!-- Kontak Pelapor -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-uppercase small text-muted mb-2">
                                Kontak <span class="text-muted fw-normal">(Opsional)</span>
                            </label>
                            <input type="text" class="form-control-custom w-100" id="kontakPelapor" placeholder="Nomor telepon atau email">
                        </div>

                        <!-- Deskripsi -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-uppercase small text-muted mb-2">
                                Deskripsi Detail <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control-custom w-100" id="deskripsi" rows="4" placeholder="Ceritakan detail temuan Anda di sini..." required></textarea>
                        </div>

                        <!-- Upload Foto -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-uppercase small text-muted mb-2">
                                Unggah Foto <span class="text-muted fw-normal">(Opsional)</span>
                            </label>
                            <div class="upload-area" id="uploadArea" onclick="document.getElementById('fotoInput').click()">
                                <input type="file" name="foto" id="fotoInput" accept="image/jpeg,image/png" style="display: none;" onchange="handleFileSelect(this)">
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
                            <a href="index.php" class="btn-cancel">Batal</a>
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
                <button type="button" class="btn btn-success w-100 mt-4" data-bs-dismiss="modal" onclick="resetForm()">Tutup</button>
            </div>
        </div>
    </div>
</div>

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
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="../Assets/js/api.js?v=1.1"></script>
<script src="../Assets/js/helpers.js"></script>
<script>
const locationCoords = {
    'Kampus 2 - FST': { lat: -6.2872, lng: 106.8345, zoom: 17 },
    'Kampus 2 - FIK': { lat: -6.2860, lng: 106.8358, zoom: 17 },
    'Kampus 3 - FST': { lat: -6.2950, lng: 106.8400, zoom: 17 },
    'Kampus 3 - FIK': { lat: -6.2965, lng: 106.8415, zoom: 17 },
    'Perpustakaan': { lat: -6.2868, lng: 106.8350, zoom: 18 },
    'Kantin Kampus - 2': { lat: -6.2865, lng: 106.8340, zoom: 18 },
    'Kantin Kampus - 3': { lat: -6.2955, lng: 106.8395, zoom: 18 },
    'Masjid Kampus - 2': { lat: -6.2862, lng: 106.8348, zoom: 18 },
    'Aula Kampus -2': { lat: -6.2869, lng: 106.8352, zoom: 18 },
    'Aula Kampus - 3': { lat: -6.2958, lng: 106.8402, zoom: 18 },
    'Lapangan Olahraga kampus -2': { lat: -6.2875, lng: 106.8335, zoom: 17 },
    'Parkiran Utama Kampus - 2': { lat: -6.2863, lng: 106.8342, zoom: 18 },
    'Parkiran Dosen Kampus - 2': { lat: -6.2867, lng: 106.8355, zoom: 18 },
    'Parkiran Utama Kampus - 3': { lat: -6.2952, lng: 106.8408, zoom: 18 },
};
let map, marker;

function initMap() {
    const defaultLat = -6.2870;
    const defaultLng = 106.8350;
    map = L.map('map').setView([defaultLat, defaultLng], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
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

async function loadKategori() {
    try {
        const res = await API.get('/kategori-sampah');
        const sel = document.getElementById('kategoriSelect');
        if (res.ok && res.data) {
            const list = res.data.data || res.data;
            if (Array.isArray(list) && list.length > 0) {
                sel.innerHTML = '<option value="" disabled selected>Pilih kategori sampah...</option>' +
                    list.map(k =>
                        `<option value="${k.id}" data-risiko="${k.level_risiko || ''}">${Helpers.escapeHtml(k.nama_kategori)} (Risiko: ${Helpers.escapeHtml((k.level_risiko || 'Tidak diketahui').charAt(0).toUpperCase() + (k.level_risiko || 'Tidak diketahui').slice(1))})</option>`
                    ).join('');
            } else {
                sel.innerHTML = '<option value="" disabled>Tidak ada kategori</option>';
            }
        } else {
            sel.innerHTML = '<option value="" disabled>Gagal memuat kategori</option>';
        }
    } catch (e) {
        document.getElementById('kategoriSelect').innerHTML = '<option value="" disabled>Gagal memuat kategori</option>';
    }
}

document.getElementById('formPengaduan').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btnSubmit = document.getElementById('btnSubmit');
    btnSubmit.disabled = true;
    btnSubmit.innerHTML = '<i class="bi bi-hourglass-split"></i> Mengirim...';

    const formData = new FormData();
    formData.append('lokasi', document.getElementById('lokasiSelect').value);
    formData.append('kategori_id', document.getElementById('kategoriSelect').value);
    formData.append('nama_pelapor', document.getElementById('namaPelapor').value);
    formData.append('deskripsi', document.getElementById('deskripsi').value);
    const kontak = document.getElementById('kontakPelapor').value;
    if (kontak) formData.append('kontak_pelapor', kontak);
    const lat = document.getElementById('latitude').value;
    const lng = document.getElementById('longitude').value;
    if (lat && lng) {
        formData.append('latitude', lat);
        formData.append('longitude', lng);
    }
    const foto = document.getElementById('fotoInput').files[0];
    if (foto) formData.append('foto', foto);

    try {
        const res = await API.postFormData('/laporan-sampah', formData);
        if (res.ok && res.data?.data) {
            document.getElementById('kodeLaporan').textContent = res.data.data.kode_laporan;
            const modal = new bootstrap.Modal(document.getElementById('modalSukses'));
            modal.show();
        } else {
            alert('Gagal mengirim laporan: ' + ((res.data?.message || res.data?.error) || 'Terjadi kesalahan'));
        }
    } catch (error) {
        alert('Gagal mengirim laporan. Pastikan koneksi internet stabil.');
        console.error('Error:', error);
    } finally {
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = '<i class="bi bi-send"></i> Kirim Pengaduan';
    }
});

document.getElementById('lokasiSelect').addEventListener('change', function() {
    const val = this.value;
    const coords = locationCoords[val];
    if (coords && map) {
        map.setView([coords.lat, coords.lng], coords.zoom);
        setMarker(coords.lat, coords.lng);
    }
});

document.addEventListener('DOMContentLoaded', function() {
    loadKategori();
    initMap();
});
</script>
</body>
</html>
