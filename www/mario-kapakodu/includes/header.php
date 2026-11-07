<?php
require_once __DIR__ . '/config.php';
$current = basename($_SERVER['SCRIPT_NAME']);
$menu = $conn->query("SELECT nimetus, url, ikoon FROM menu WHERE aktiivne = 1 ORDER BY jarjekord");
?>
<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle ?? 'Mario Käpakodu') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container">
        <a class="navbar-brand" href="index.php">Mario Käpakodu</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-expanded="false" aria-label="Menüü">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav ms-auto">
                <?php while ($m = $menu->fetch_assoc()): ?>
                    <li class="nav-item">
                        <a class="nav-link<?= $m['url'] === $current ? ' active' : '' ?>" href="<?= e($m['url']) ?>">
                            <?php if ($m['ikoon']): ?><i class="bi bi-<?= e($m['ikoon']) ?>"></i><?php endif; ?>
                            <?= e($m['nimetus']) ?>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
    </div>
</nav>
<div class="container mt-3">
<?php include __DIR__ . '/breadcrumbs.php'; ?>
