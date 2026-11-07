<?php
require_once 'includes/config.php';

$teemad = ['Huvi looma vastu', 'Broneerimine', 'Lisainfo', 'Muu'];
$vead = [];
$nimi = $email = $teema = $sonum = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nimi  = trim($_POST['nimi'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $teema = trim($_POST['teema'] ?? '');
    $sonum = trim($_POST['sonum'] ?? '');

    if ($nimi === '' || mb_strlen($nimi) > 100) $vead[] = 'Sisesta nimi (kuni 100 märki).';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 100) $vead[] = 'Sisesta korrektne e-posti aadress.';
    if (!in_array($teema, $teemad, true)) $vead[] = 'Vali teema.';
    if ($sonum === '') $vead[] = 'Sisesta sõnum.';

    if (!$vead) {
        // loom_id jääb NULL, staatus 'uus', lisatud täidetakse vaikeväärtusega
        $stmt = $conn->prepare("INSERT INTO paringud (loom_id, nimi, email, teema, sonum, staatus)
                                VALUES (NULL, ?, ?, ?, ?, 'uus')");
        $stmt->bind_param("ssss", $nimi, $email, $teema, $sonum);
        $stmt->execute();
        header('Location: contact.php?saadetud=1');
        exit;
    }
}

$pageTitle = 'Kontakt - Mario Käpakodu';
require_once 'includes/header.php';
?>
<h1 class="mb-4">Kontakt</h1>

<?php if (isset($_GET['saadetud'])): ?>
    <div class="alert alert-success">Sõnum saadeti edukalt.</div>
<?php endif; ?>
<?php if ($vead): ?>
    <div class="alert alert-danger">
        <ul class="mb-0"><?php foreach ($vead as $v): ?><li><?= e($v) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<form action="contact.php" method="post" class="col-lg-6">
    <div class="mb-3">
        <label for="nimi" class="form-label">Nimi</label>
        <input type="text" id="nimi" name="nimi" class="form-control" maxlength="100" required value="<?= e($nimi) ?>">
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">E-post</label>
        <input type="email" id="email" name="email" class="form-control" maxlength="100" required value="<?= e($email) ?>">
    </div>
    <div class="mb-3">
        <label for="teema" class="form-label">Teema</label>
        <select id="teema" name="teema" class="form-select" required>
            <option value="">Vali teema</option>
            <?php foreach ($teemad as $t): ?>
                <option <?= $teema === $t ? 'selected' : '' ?>><?= e($t) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="sonum" class="form-label">Sõnum</label>
        <textarea id="sonum" name="sonum" class="form-control" rows="5" required><?= e($sonum) ?></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Saada sõnum</button>
</form>
<?php require_once 'includes/footer.php'; ?>
