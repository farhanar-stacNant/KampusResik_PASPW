<?php
/**
 * Auto-prepend configuration for KampusResik FrontEnd.
 * Automatically defines API_BASE_URL based on the environment (local vs production).
 */

if (!defined('API_BASE_URL')) {
    $currentHost = $_SERVER['HTTP_HOST'] ?? '';

    // Deteksi jika diakses secara lokal (localhost / 127.0.0.1)
    if (
        strpos($currentHost, 'localhost') !== false || 
        strpos($currentHost, '127.0.0.1') !== false || 
        strpos($currentHost, 'kampusresik') !== false && strpos($currentHost, 'infinityfreeapp.com') === false
    ) {
        define('API_BASE_URL', 'http://127.0.0.1:8000/api');
    } else {
        // Jika berjalan di production (InfinityFree)
        define('API_BASE_URL', 'https://kampusresiku.infinityfreeapp.com/api');
    }
}
