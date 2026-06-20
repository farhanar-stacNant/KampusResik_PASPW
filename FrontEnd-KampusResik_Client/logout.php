<?php
session_start();

if (isset($_SESSION['token'])) {
    require_once 'includes/api_client.php';
    
    $ch = curl_init("http://127.0.0.1:8000/api/logout");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $_SESSION['token'],
        'Accept: application/json'
    ]);
    curl_exec($ch);
    curl_close($ch);
}

$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();
header('Location: index.php');
exit;