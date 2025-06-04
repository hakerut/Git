<?php
require_once __DIR__ . '/helpers.php';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Auktion Plattform</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="/"><img src="/assets/logo.png" alt="Logo" class="logo"></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 header-nav">
                <li class="nav-item"><a class="nav-link<?= ($_SERVER['SCRIPT_NAME'] == "/index.php" ? ' active' : '') ?>" href="/">Startseite</a></li>
                <li class="nav-item"><a class="nav-link<?= ($_SERVER['SCRIPT_NAME'] == "/auktion_erstellen.php" ? ' active' : '') ?>" href="/auktion_erstellen.php">Auktion erstellen</a></li>
                <?php if (is_logged_in()): ?>
                    <li class="nav-item"><a class="nav-link<?= ($_SERVER['SCRIPT_NAME'] == "/meine_auktionen.php" ? ' active' : '') ?>" href="/meine_auktionen.php">Meine Auktionen</a></li>
                    <li class="nav-item"><a class="nav-link" href="/logout.php">Abmelden</a></li>
                    <?php if (is_admin()): ?>
                        <li class="nav-item"><a class="nav-link" href="/admin/index.php">Admin</a></li>
                    <?php endif; ?>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link<?= ($_SERVER['SCRIPT_NAME'] == "/login.php" ? ' active' : '') ?>" href="/login.php">Anmelden</a></li>
                    <li class="nav-item"><a class="nav-link<?= ($_SERVER['SCRIPT_NAME'] == "/register.php" ? ' active' : '') ?>" href="/register.php">Registrieren</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<div class="container mt-4">
<?= flash() ?>