<?php
if (!defined('API_BASE_URL')) {
    define('API_BASE_URL', 'http://127.0.0.1:8000/api');
}
if (!defined('API_SECRET_TOKEN')) {
    define('API_SECRET_TOKEN', 'KampusResik_Secret_Token_2026');
}

function api_get_contents($url) {
    $options = [
        'http' => [
            'header' => "X-Api-Token: " . API_SECRET_TOKEN . "\r\n"
        ]
    ];
    $context = stream_context_create($options);
    return @file_get_contents($url, false, $context);
}
function base_url() {
    static $base_url = null;
    if ($base_url !== null) return $base_url;
    
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $script = $_SERVER['PHP_SELF'];
    $dir = dirname($script);
    $base = $protocol . '://' . $host;
    if ($dir !== '/') {
        $parts = explode('/', trim($dir, '/'));
        $frontendIndex = array_search('FrontEnd', $parts);
        if ($frontendIndex !== false) {
            $base .= '/' . implode('/', array_slice($parts, 0, $frontendIndex + 1));
        } else {
            $base .= '/' . implode('/', array_slice($parts, 0, -1));
        }
    }
    
    $base_url = rtrim($base, '/');
    return $base_url;
}

// Helper untuk sub-folder
function base_public()  { return base_url() . '/Public'; }
function base_admin()   { return base_url() . '/Admin'; }
function base_petugas() { return base_url() . '/Petugas'; }
function base_assets()  { return base_url() . '/Assets'; }

// Helper untuk URL assets
function assets_url() {
    return base_assets();
}

// Helper: Halaman aktif
function current_page() {
    return basename($_SERVER['PHP_SELF'], '.php');
}

// Helper: Redirect
function redirect($path) {
    header('Location: ' . $path);
    exit();
}