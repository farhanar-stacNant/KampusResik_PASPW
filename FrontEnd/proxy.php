<?php
// proxy.php - Menghindari CORS dengan proxy server-to-server
header('Content-Type: application/json');

$API_BASE = 'https://kampusresiks.gt.tc/api';

// Ambil endpoint dari query string
$endpoint = isset($_GET['endpoint']) ? trim($_GET['endpoint']) : '';

if (empty($endpoint)) {
    http_response_code(400);
    echo json_encode([
        'error' => 'Endpoint tidak boleh kosong',
        'info' => 'Gunakan format: proxy.php?endpoint=kategori-sampah'
    ]);
    exit;
}

// Bangun URL tujuan
$url = $API_BASE . '/' . ltrim($endpoint, '/');

// Forward query parameters (kecuali 'endpoint')
$params = $_GET;
unset($params['endpoint']);
if (!empty($params)) {
    $url .= '?' . http_build_query($params);
}

// Tentukan method
$method = $_SERVER['REQUEST_METHOD'];

// Siapkan headers untuk forward
$headers = ['Accept: application/json'];

// Forward Authorization header jika ada
if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    $headers[] = 'Authorization: ' . $_SERVER['HTTP_AUTHORIZATION'];
}

// Siapkan cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

if ($method === 'POST') {
    curl_setopt($ch, CURLOPT_POST, true);
    
    // Periksa apakah request berupa multipart/form-data (mengandung files atau $_POST)
    if (!empty($_FILES) || !empty($_POST)) {
        $postData = $_POST;
        foreach ($_FILES as $key => $file) {
            if ($file['error'] === UPLOAD_ERR_OK) {
                // Gunakan CURLFile untuk upload file via PHP cURL
                $postData[$key] = new CURLFile($file['tmp_name'], $file['type'], $file['name']);
            }
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    } else {
        // Jika raw JSON payload
        $input = file_get_contents('php://input');
        if (!empty($input)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
            $headers[] = 'Content-Type: application/json';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
    }
} elseif ($method === 'PUT') {
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    $input = file_get_contents('php://input');
    if (!empty($input)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
} elseif ($method === 'DELETE') {
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
}

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

// Tambahkan Logging Sementara untuk Debugging
$logData = [
    'time' => date('Y-m-d H:i:s'),
    'url' => $url,
    'method' => $method,
    'post_fields' => $_POST,
    'files' => array_keys($_FILES),
    'response_code' => $httpCode,
    'response' => json_decode($response, true) ?: $response,
    'error' => $error
];
file_put_contents('proxy_debug.log', json_encode($logData, JSON_PRETTY_PRINT) . "\n", FILE_APPEND);

if ($error) {
    http_response_code(500);
    echo json_encode(['error' => 'Proxy error: ' . $error]);
    exit;
}

http_response_code($httpCode);
echo $response;
