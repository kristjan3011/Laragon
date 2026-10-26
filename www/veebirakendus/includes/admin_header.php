<?php
// Admini päis ja menüü. Eeldab muutujaid: $pageTitle, $adminActive. Nõuab sisselogimist.
require_once __DIR__ . '/functions.php';
start_session();
$user = require_login('../login.php');
$adminActive = $adminActive ?? '';
$pageTitle = $pageTitle ?? 'Admin';
$flash = flash_get();
?>
<!doctype html>
<html lang="et">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | Admin | Mario Käpakodu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg bg-white border-bottom shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php"><img src="../assets/images/logo_big.svg" alt="Mario Käpakodu" height="50"></a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminMenu" aria-label="Ava menüü" aria-controls="adminMenu" aria-expanded="false"><span class="navbar-toggler-icon"></span></button>

            <div class="collapse navbar-collapse" id="adminMenu">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link <?= $adminActive === 'home' ? 'active' : '' ?>" href="index.php"><i class="fa-solid fa-house me-1"></i> Avaleht</a></li>
                    <li class="nav-item"><a class="nav-link <?= $adminActive === 'animals' ? 'active' : '' ?>" href="animals.php"><i class="fa-solid fa-paw me-1"></i> Loomad</a></li>
                    <li class="nav-item"><a class="nav-link <?= $adminActive === 'animal_form' ? 'active' : '' ?>" href="animal_form.php"><i class="fa-solid fa-plus me-1"></i> Lisa loom</a></li>
                    <li class="nav-item"><a class="nav-link <?= $adminActive === 'species' ? 'active' : '' ?>" href="species.php"><i class="fa-solid fa-tags me-1"></i> Liigid ja tõud</a></li>
                    <li class="nav-item"><a class="nav-link <?= $adminActive === 'users' ? 'active' : '' ?>" href="users.php"><i class="fa-solid fa-users me-1"></i> Kasutajad</a></li>
                    <li class="nav-item"><a class="nav-link <?= $adminActive === 'requests' ? 'active' : '' ?>" href="requests.php"><i class="fa-regular fa-envelope me-1"></i> Päringud</a></li>
                </ul>

                <span class="navbar-text me-3"><i class="fa-solid fa-user-shield text-success me-1"></i> <?= e($user['name']) ?></span>
                <form method="post" action="logout.php" class="d-inline">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-outline-danger"><i class="fa-solid fa-right-from-bracket me-2"></i> Logi välja</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="container py-5">
        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : 'success' ?> alert-dismissible fade show" role="alert">
                <?= e($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Sulge"></button>
            </div>
        <?php endif; ?>
