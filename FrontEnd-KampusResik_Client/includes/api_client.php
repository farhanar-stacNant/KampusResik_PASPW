<?php
function getBackendUrlFromEnv() {
    $envPath = __DIR__ . '/../../BackEnd-KampusResik_Api/.env';
    $baseUrl = "http://127.0.0.1:8000/api/";

    if (file_exists($envPath)) {
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $env = [];
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            list($name, $value) = array_pad(explode('=', $line, 2), 2, null);
            if ($name !== null) {
                $env[trim($name)] = trim($value);
            }
        }
        
        if (isset($env['APP_URL'])) {
            $url = trim($env['APP_URL'], '"\'');
            if ($url === 'http://localhost' || $url === 'http://127.0.0.1') {
                $url = 'http://127.0.0.1:8000';
            }
            $baseUrl = rtrim($url, '/') . '/api/';
        }
    }
    return $baseUrl;
}

function callApi($method, $endpoint, $data = null) {
    $baseUrl = getBackendUrlFromEnv();
    $url = $baseUrl . $endpoint;
    $ch = curl_init($url);

    $headers = [
        'Accept: application/json',
        'Content-Type: application/json'
    ];

    if (isset($_SESSION['token'])) {
        $headers[] = 'Authorization: Bearer ' . $_SESSION['token'];
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); 
    
    switch ($method) {
        case 'POST':
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            break;
        case 'PUT':
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            if ($data) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            break;
        case 'DELETE':
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            break;
        case 'GET':
        default:
            // Default adalah GET
            break;
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        return ['status' => 'error', 'message' => 'Koneksi ke server gagal: ' . $error];
    }

    $decodedResponse = json_decode($response, true);

    if ($decodedResponse === null && !empty($response)) {
        return ['status' => 'error', 'message' => 'Gagal membaca response server'];
    }

    return $decodedResponse;
}
?>