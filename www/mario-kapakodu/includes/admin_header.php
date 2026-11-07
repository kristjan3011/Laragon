<?php
require_once __DIR__ . '/config.php';
$current = basename($_SERVER['SCRIPT_NAME']);
$nav = ['index.php' => ['Töölaud', 'speedometer2'], 'animals.php' => ['Loomad', 'list-ul'], 'animal_add.php' => ['Lisa loom', 'plus-circle']];
?>
<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle ?? 'Admin - Mario Käpakodu') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">Käpakodu admin</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-expanded="false" aria-label="Menüü">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav me-auto">
                <?php foreach ($nav as $url => [$label, $icon]): ?>
                    <li class="nav-item">
                        <a class="nav-link<?= $url === $current ? ' active' : '' ?>" href="<?= e($url) ?>"><i class="bi bi-<?= e($icon) ?>"></i> <?= e($label) ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <a class="btn btn-outline-light btn-sm" href="../index.php">Avalik sait</a>
        </div>
    </div>
</nav>
<div class="container mt-4">
