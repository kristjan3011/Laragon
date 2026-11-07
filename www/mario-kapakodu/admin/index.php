<?php
require_once '../includes/config.php';

// Statistika (päringutes pole kasutaja sisendit, seega pole ettevalmistatud päringut vaja)
$stat = $conn->query("SELECT COUNT(*) AS kokku,
                             SUM(staatus = 'saadaval') AS saadaval,
                             SUM(staatus = 'broneeritud') AS broneeritud,
                             SUM(staatus = 'hoolduses') AS hoolduses
                      FROM loomad")->fetch_assoc();
$paringuid = (int) $conn->query("SELECT COUNT(*) AS kokku FROM paringud")->fetch_assoc()['kokku'];

$loomad = $conn->query("SELECT loomad.nimi, loomad.vanus, loomad.staatus, liigid.nimetus AS liik
                        FROM loomad
                        LEFT JOIN liigid ON loomad.liik_id = liigid.id
                        ORDER BY loomad.id DESC
                        LIMIT 4");
$sonumid = $conn->query("SELECT nimi, teema, staatus FROM paringud ORDER BY id DESC LIMIT 4");

$cards = [['Loomi kokku', $stat['kokku'], 'primary'], ['Saadaval', $stat['saadaval'], 'success'],
          ['Broneeritud', $stat['broneeritud'], 'warning'], ['Hoolduses', $stat['hoolduses'], 'secondary'],
          ['Päringuid', $paringuid, 'info']];

$pageTitle = 'Töölaud - Admin';
require_once '../includes/admin_header.php';
?>
<h1 class="mb-4">Töölaud</h1>

<div class="row row-cols-2 row-cols-md-5 g-3 mb-4">
    <?php foreach ($cards as [$label, $value, $color]): ?>
        <div class="col">
            <div class="card text-bg-<?= $color ?> h-100">
                <div class="card-body">
                    <div class="fs-2 fw-bold"><?= (int) $value ?></div>
                    <div><?= e($label) ?></div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <h2 class="h5">Viimati lisatud loomad</h2>
        <table class="table table-sm">
            <thead><tr><th>Nimi</th><th>Liik</th><th>Vanus</th><th>Staatus</th></tr></thead>
            <tbody>
            <?php while ($a = $loomad->fetch_assoc()): ?>
                <tr><td><?= e($a['nimi']) ?></td><td><?= e($a['liik']) ?></td><td><?= (int) $a['vanus'] ?></td><td><?= e($a['staatus']) ?></td></tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        <a href="animals.php">Kõik loomad</a>
    </div>
    <div class="col-lg-6">
        <h2 class="h5">Viimased sõnumid</h2>
        <table class="table table-sm">
            <thead><tr><th>Nimi</th><th>Teema</th><th>Staatus</th></tr></thead>
            <tbody>
            <?php while ($s = $sonumid->fetch_assoc()): ?>
                <tr><td><?= e($s['nimi']) ?></td><td><?= e($s['teema']) ?></td><td><?= e($s['staatus']) ?></td></tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once '../includes/admin_footer.php'; ?>
