<?php
/**
 * KampusResik - Auth & Session Handler
 * File: FrontEnd/includes/auth.php
 * Require: api-config.php, functions.php
 */

// Start session jika belum
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/api-config.php';

// --- TOKEN MANAGEMENT ---
function set_auth_token($token, $user = null) {
    $_SESSION['user_token'] = $token;
    if ($user) {
        $_SESSION['user_role'] = $user['role'] ?? null;
        $_SESSION['user_id'] = $user['id'] ?? null;
        $_SESSION['user_name'] = $user['nama'] ?? $user['name'] ?? null;
        $_SESSION['user_email'] = $user['email'] ?? null;
    }
}

function get_auth_token() {
    return $_SESSION['user_token'] ?? null;
}

function clear_auth() {
    $_SESSION = [];
    session_unset();
    session_destroy();
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
}

// --- ROLE CHECK ---
function get_user_role() {
    return $_SESSION['user_role'] ?? null;
}

function is_logged_in() {
    return !empty(get_auth_token());
}

function is_admin() {
    return is_logged_in() && get_user_role() === 'admin';
}

function is_petugas() {
    return is_logged_in() && get_user_role() === 'petugas';
}

// --- MIDDLEWARE / GUARD ---
function require_login() {
    if (!is_logged_in()) {
        redirect(base_url() . '/login.php');
    }
}

function require_admin() {
    require_login();
    if (!is_admin()) {
        clear_auth();
        redirect(base_url() . '/login.php?error=unauthorized');
    }
}

function require_petugas() {
    require_login();
    if (!is_petugas()) {
        clear_auth();
        redirect(base_url() . '/login.php?error=unauthorized');
    }
}

// --- LOGOUT HANDLER ---
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    clear_auth();
    redirect(base_url() . '/login.php');
}