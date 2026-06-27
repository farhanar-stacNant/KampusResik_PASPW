<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

if (!isset($pageTitle)) $pageTitle = 'Petugas - KampusResik';

$user = get_user_data();
$nama = $user['nama'] ?? $user['name'] ?? 'Petugas';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= assets_url() ?>/css/petugas.style.css">
</head>
<body>