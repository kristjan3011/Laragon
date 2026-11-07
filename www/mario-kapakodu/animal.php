<?php
require_once 'includes/config.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: index.php');
    exit;
}

$stmt = $conn->prepare("SELECT l.*, li.nimetus AS liik, t.nimetus AS toug
                        FROM loomad l
                        JOIN liigid li ON li.id = l.liik_id
                        LEFT JOIN toud t ON t.id = l.toug_id
                        WHERE l.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$a = $stmt->get_result()->fetch_assoc();
if (!$a) {
    header('Location: index.php');
    exit;
}

$pageTitle = $a['nimi'] . ' - Mario Käpakodu';
require_once 'includes/header.php';
$jahei = fn($v) => $v ? 'Jah' : 'Ei';
?>
<div class="row g-4">
    <div class="col-md-5">
        <img src="assets/images/<?= e($a['pilt']) ?>" class="img-fluid rounded" alt="<?= e($a['nimi']) ?>"
             onerror="this.onerror=null;this.src='assets/images/default.png'">
    </div>
    <div class="col-md-7">
        <h1><?= e($a['nimi']) ?></h1>
        <p class="lead"><?= e($a['luhikirjeldus']) ?></p>
        <table class="table">
            <tr><th>Liik</th><td><?= e($a['liik']) ?></td></tr>
            <tr><th>Tõug</th><td><?= e($a['toug'] ?? '-') ?></td></tr>
            <tr><th>Sugu</th><td><?= e($a['sugu']) ?></td></tr>
            <tr><th>Vanus</th><td><?= (int) $a['vanus'] ?> a</td></tr>
            <tr><th>Sünnikuupäev</th><td><?= e($a['sunnikuupaev'] ?? '-') ?></td></tr>
            <tr><th>Suurus</th><td><?= e($a['suurus']) ?></td></tr>
            <tr><th>Värvus</th><td><?= e($a['varvus']) ?></td></tr>
            <tr><th>Asukoht</th><td><?= e($a['asukoht']) ?></td></tr>
            <tr><th>Staatus</th><td><?= e($a['staatus']) ?></td></tr>
            <tr><th>Steriliseeritud</th><td><?= $jahei($a['steriliseeritud']) ?></td></tr>
            <tr><th>Kiibistatud</th><td><?= $jahei($a['kiibistatud']) ?></td></tr>
        </table>
        <p><?= nl2br(e($a['kirjeldus'])) ?></p>
        <a href="contact.php" class="btn btn-primary">Võta ühendust</a>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>
