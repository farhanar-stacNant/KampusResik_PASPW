<?php
/**
 * KampusResik - Helper Functions
 * File: FrontEnd/includes/functions.php
 * Require: api-config.php (harus sudah di-load)
 */

// --- FETCH API UNIVERSAL ---
function fetch_api($endpoint, $method = 'GET', $data = null, $token = null) {
    $url = API_BASE_URL . $endpoint;
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $headers = ['Content-Type: application/json', 'Accept: application/json', 'X-Api-Token: ' . API_SECRET_TOKEN];
    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    return [
        'code' => $http_code,
        'body' => json_decode($response, true),
        'error' => $error
    ];
}

// --- FETCH API MULTIPART (untuk upload file) ---
function fetch_api_multipart($endpoint, $fields, $files = [], $token = null) {
    $url = API_BASE_URL . $endpoint;
    
    $boundary = '----WebKitFormBoundary' . uniqid();
    $body = '';
    
    foreach ($fields as $name => $value) {
        $body .= "--{$boundary}\r\n";
        $body .= "Content-Disposition: form-data; name=\"{$name}\"\r\n\r\n";
        $body .= "{$value}\r\n";
    }
    
    foreach ($files as $name => $filePath) {
        if (!file_exists($filePath)) continue;
        $fileName = basename($filePath);
        $fileType = mime_content_type($filePath);
        $fileContent = file_get_contents($filePath);
        
        $body .= "--{$boundary}\r\n";
        $body .= "Content-Disposition: form-data; name=\"{$name}\"; filename=\"{$fileName}\"\r\n";
        $body .= "Content-Type: {$fileType}\r\n\r\n";
        $body .= $fileContent . "\r\n";
    }
    $body .= "--{$boundary}--\r\n";
    
    $headers = ["Content-Type: multipart/form-data; boundary={$boundary}", 'X-Api-Token: ' . API_SECRET_TOKEN];
    if ($token) $headers[] = 'Authorization: Bearer ' . $token;
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return ['code' => $http_code, 'body' => json_decode($response, true)];
}

// --- GET USER DATA DARI API ---
function get_user_data($token = null) {
    if (!$token) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $token = $_SESSION['user_token'] ?? null;
    }
    if (!$token) return [];
    
    $result = fetch_api('/auth/me', 'GET', null, $token);
    return ($result['code'] === 200) ? ($result['body']['data'] ?? []) : [];
}

// --- FORMAT TANGGAL INDONESIA ---
function format_tanggal($date) {
    if (!$date) return '-';
    $bulan = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    $d = date('d', strtotime($date));
    $m = $bulan[(int)date('m', strtotime($date))];
    $y = date('Y', strtotime($date));
    return "$d $m $y";
}

// --- STATUS BADGE HELPER ---
function status_badge($status) {
    $map = [
        'dikirim'  => ['label' => 'Dikirim',  'class' => 'bg-secondary'],
        'diterima' => ['label' => 'Diterima', 'class' => 'bg-info'],
        'diproses' => ['label' => 'Diproses', 'class' => 'bg-warning text-dark'],
        'selesai'  => ['label' => 'Selesai',  'class' => 'bg-success'],
        'ditolak'  => ['label' => 'Ditolak',  'class' => 'bg-danger'],
    ];
    return $map[$status] ?? ['label' => ucfirst($status), 'class' => 'bg-secondary'];
}

// --- KATEGORI SAMPAH LABEL ---
function kategori_label($jenis) {
    $map = ['ORG' => 'Organik', 'ANR' => 'Anorganik', 'B3' => 'B3'];
    return $map[$jenis] ?? 'Lainnya';
}

// --- RISIKO BADGE HELPER ---
function risiko_badge($level) {
    $map = [
        'rendah' => ['label' => 'Rendah', 'class' => 'bg-success'],
        'sedang' => ['label' => 'Sedang', 'class' => 'bg-warning text-dark'],
        'tinggi' => ['label' => 'Tinggi', 'class' => 'bg-danger'],
    ];
    return $map[$level] ?? ['label' => ucfirst($level), 'class' => 'bg-secondary'];
}