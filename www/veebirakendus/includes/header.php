<?php
// Avaleht/avalik päis. Eeldab muutujaid: $base ('' või '../'), $pageTitle, $active (menüü võti)
require_once __DIR__ . '/functions.php';
start_session();
$user = current_user();
$active = $active ?? '';
$pageTitle = $pageTitle ?? 'Mario Käpakodu';
?>
<!doctype html>
<html lang="et">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?= e($base) ?>assets/css/style.css">
    <title><?= e($pageTitle) ?> | Mario Käpakodu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
</head>

<body>
    <div class="container">
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <a class="navbar-brand" href="<?= e($base) ?>index.php">
                    <img src="<?= e($base) ?>assets/images/logo_big.svg" alt="Mario Käpakodu" width="140">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Ava menüü">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link <?= $active === 'home' ? 'active' : '' ?>" href="<?= e($base) ?>index.php"><strong>Avaleht</strong></a></li>
                        <li class="nav-item"><a class="nav-link <?= $active === 'animals' ? 'active' : '' ?>" href="<?= e($base) ?>all_animals.php"><strong>Loomad</strong></a></li>
                        <li class="nav-item"><a class="nav-link" href="#"><strong>Kuidas aidata</strong></a></li>
                        <li class="nav-item"><a class="nav-link" href="#"><strong>Meist</strong></a></li>
                        <li class="nav-item"><a class="nav-link <?= $active === 'contact' ? 'active' : '' ?>" href="<?= e($base) ?>contact.php"><strong>Kontakt</strong></a></li>
                    </ul>
                    <?php if ($user): ?>
                        <a class="btn btn-outline-success me-2" href="<?= e($base) ?>admin/index.php">Admin</a>
                    <?php else: ?>
                        <a class="btn btn-outline-success me-2" href="<?= e($base) ?>login.php">Logi sisse</a>
                    <?php endif; ?>
                    <a class="btn btn-success" href="#">Anneta <i class="fa-solid fa-heart-circle-check"></i></a>
                </div>
            </div>
        </nav>
    </div>
