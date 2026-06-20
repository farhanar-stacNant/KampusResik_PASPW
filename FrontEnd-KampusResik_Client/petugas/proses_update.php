<?php
session_start();
if (!isset($_SESSION['token'])) {
    header('Location: ../login.php');
    exit;
}

$id = $_POST['id'] ?? null;
if (!$id) {
    header('Location: riwayat.php');
    exit;
}

$status = $_POST['status'] ?? 'diproses';

// Prepare API URL
$url = "http://127.0.0.1:8000/api/petugas/update-status/" . $id;

$postData = [
    'status' => $status,
];

// If there's an uploaded file
if ($status === 'selesai' && isset($_FILES['foto_bukti']) && $_FILES['foto_bukti']['error'] === UPLOAD_ERR_OK) {
    $postData['foto_sesudah'] = new CURLFile(
        $_FILES['foto_bukti']['tmp_name'],
        $_FILES['foto_bukti']['type'],
        $_FILES['foto_bukti']['name']
    );
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $_SESSION['token'],
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Redirect back to riwayat.php
header('Location: riwayat.php');
exit;
