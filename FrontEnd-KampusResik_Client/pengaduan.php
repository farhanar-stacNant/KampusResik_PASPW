<?php
$apiKategoriUrl = "http://127.0.0.1:8000/api/kategori";
$apiKirimUrl    = "http://127.0.0.1:8000/api/pengaduan";
$kategoriList   = [];

$resKategori = @file_get_contents($apiKategoriUrl);
if ($resKategori) {
    $resData = json_decode($resKategori, true);
    if (isset($resData['status']) && $resData['status'] == 'success') {
        $kategoriList = $resData['data'];
    }
}

$pesanSukses = ""; $pesanGagal = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['foto_sebelum']) && $_FILES['foto_sebelum']['error'] === UPLOAD_ERR_OK) {
        $postData = [
            'kategori_id'       => $_POST['kategori_id'],
            'nama_pelapor'      => $_POST['nama_pelapor'] ?? 'Anonim',
            'latitude'          => $_POST['latitude'],
            'longitude'         => $_POST['longitude'],
            'deskripsi_singkat' => $_POST['deskripsi_singkat'],
            'foto_sebelum'      => new CURLFile($_FILES['foto_sebelum']['tmp_name'], $_FILES['foto_sebelum']['type'], $_FILES['foto_sebelum']['name'])
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiKirimUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            $result = json_decode($response, true);
            if (isset($result['status']) && $result['status'] === 'success') {
                $pesanSukses = "Laporan Anda berhasil dikirim!";
            } else {
                $pesanGagal = $result['message'] ?? "Gagal mengirim laporan.";
            }
        }
    } else {
        $pesanGagal = "Wajib mengunggah foto bukti fisik sampah.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporkan Sampah - KampusResik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Leaflet Map CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body { background-color: #f8f9fa; }
        .form-card { border-radius: 1rem; border: none; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075); }
        .btn-gradient { background: linear-gradient(to right, #0d9488, #0891b2); color: white; border: none; }
        .btn-gradient:hover { background: linear-gradient(to right, #0f766e, #0e7490); color: white; }
        #map { height: 320px; border-radius: 0.75rem; margin-bottom: 1.5rem; border: 1px solid #ced4da; }
    </style>
</head>
<body>
    <?php include 'includes/navbarPublic.php'; ?>
    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card form-card p-4 p-md-5">
                    <h1 class="h3 fw-bold mb-2">Form Pengaduan Temuan Sampah</h1>
                    <p class="text-muted mb-4">Bantu kami menjaga kebersihan kampus dengan melaporkan titik penumpukan sampah liar di sekitar Anda.</p>

                    <?php if ($pesanSukses): ?>
                        <div class="alert alert-success"><?= $pesanSukses ?></div>
                    <?php endif; ?>
                    <?php if ($pesanGagal): ?>
                        <div class="alert alert-danger"><?= $pesanGagal ?></div>
                    <?php endif; ?>

                    <form action="pengaduan.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Pelapor</label>
                            <input type="text" name="nama_pelapor" class="form-control" placeholder="Contoh: Anonim">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jenis / Kategori Sampah <span class="text-danger">*</span></label>
                            <select name="kategori_id" required class="form-select">
                                <option value="">-- Pilih Jenis Sampah --</option>
                                <?php foreach ($kategoriList as $kat): ?>
                                    <option value="<?= $kat['id'] ?>"><?= htmlspecialchars($kat['nama_kategori']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Leaflet Interactive Map -->
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <label class="form-label fw-semibold mb-0">Tentukan Lokasi Sampah di Peta <span class="text-danger">*</span></label>
                            <button type="button" onclick="getLokasiGPS()" class="btn btn-sm btn-outline-secondary py-1">
                                📍 Deteksi GPS Saya
                            </button>
                        </div>
                        <div id="map"></div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Latitude <span class="text-danger">*</span></label>
                                <input type="text" name="latitude" id="lat" required class="form-control" placeholder="-7.xxxxxx">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Longitude <span class="text-danger">*</span></label>
                                <input type="text" name="longitude" id="lng" required class="form-control" placeholder="112.xxxxxx">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deskripsi Singkat <span class="text-danger">*</span></label>
                            <textarea name="deskripsi_singkat" rows="3" required class="form-control" placeholder="Jelaskan kondisi dan patokan tempat..."></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Foto Bukti Fisik <span class="text-danger">*</span></label>
                            <input type="file" name="foto_sebelum" accept="image/*" required class="form-control">
                        </div>

                        <button type="submit" class="btn btn-gradient w-100 py-3 fw-bold">Kirim Pengaduan Sekarang</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Leaflet Map JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        var map, marker;
        var defaultLat = -7.250445;
        var defaultLng = 112.768845;

        document.addEventListener("DOMContentLoaded", function() {
            // Initialize Map
            map = L.map('map').setView([defaultLat, defaultLng], 14);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Add Draggable Marker
            marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

            // Event on marker dragend
            marker.on('dragend', function(e) {
                var position = marker.getLatLng();
                updateInputs(position.lat, position.lng);
            });

            // Event on map click
            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                updateInputs(e.latlng.lat, e.latlng.lng);
            });
        });

        function updateInputs(lat, lng) {
            document.getElementById('lat').value = lat.toFixed(6);
            document.getElementById('lng').value = lng.toFixed(6);
        }

        function getLokasiGPS() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(pos) {
                    var lat = pos.coords.latitude;
                    var lng = pos.coords.longitude;
                    
                    map.setView([lat, lng], 16);
                    marker.setLatLng([lat, lng]);
                    updateInputs(lat, lng);
                }, function() {
                    alert("Gagal mendeteksi lokasi GPS Anda.");
                });
            } else {
                alert("Geolocation tidak didukung oleh browser Anda.");
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>