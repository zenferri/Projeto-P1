<?php
if (!defined('BASE_URL')) {
    $basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
    define('BASE_URL', $basePath === '/' ? '' : $basePath);
}

$pageTitle = $pageTitle ?? 'Singularys';
$pageDescription = $pageDescription ?? 'Servidores virtuais escaláveis, executados no Brasil.';
$user = $_SESSION['user'] ?? null;
$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="color-scheme" content="light">
    <meta name="theme-color" content="#ffffff">
    <title><?= htmlspecialchars($pageTitle) ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            color-scheme: light only;
        }
        html, body {
            background: #fff;
            color: #101C5E;
            font-family: 'Inter', sans-serif;
        }
    </style>

    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
    <?php require __DIR__ . '/navbar.php'; ?>
    
    <?php if ($flashSuccess || $flashError): ?>
        <div class="container mt-3">
            <div class="alert alert-<?= $flashSuccess ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($flashSuccess ?: $flashError) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>

    <main class="main-content">
