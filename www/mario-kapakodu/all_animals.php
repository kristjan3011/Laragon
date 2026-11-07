<?php
$pageTitle = 'Kõik loomad - Mario Käpakodu';
require_once 'includes/header.php';

$otsi  = trim($_GET['otsi'] ?? '');
$liik  = filter_input(INPUT_GET, 'liik', FILTER_VALIDATE_INT) ?: 0;
$sugu  = in_array($_GET['sugu'] ?? '', ['isane', 'emane'], true) ? $_GET['sugu'] : '';
$vanus = in_array($_GET['vanus'] ?? '', ['1', '2', '3'], true) ? $_GET['vanus'] : '';

$sql = "SELECT l.id, l.nimi, l.sugu, l.vanus, l.luhikirjeldus, l.pilt, li.nimetus AS liik
        FROM loomad l JOIN liigid li ON li.id = l.liik_id WHERE 1=1";
$types = '';
$params = [];
if ($otsi !== '') { $sql .= " AND l.nimi LIKE ?"; $types .= 's'; $params[] = '%' . $otsi . '%'; }
if ($liik)        { $sql .= " AND l.liik_id = ?"; $types .= 'i'; $params[] = $liik; }
if ($sugu !== '') { $sql .= " AND l.sugu = ?";    $types .= 's'; $params[] = $sugu; }
// Vanuse vahemikud sünnikuupäeva järgi (kindlad tingimused, kasutaja sisendit SQL-i ei panda)
if ($vanus === '1') $sql .= " AND l.sunnikuupaev > DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
if ($vanus === '2') $sql .= " AND l.sunnikuupaev <= DATE_SUB(CURDATE(), INTERVAL 1 YEAR) AND l.sunnikuupaev > DATE_SUB(CURDATE(), INTERVAL 5 YEAR)";
if ($vanus === '3') $sql .= " AND l.sunnikuupaev <= DATE_SUB(CURDATE(), INTERVAL 5 YEAR)";
$sql .= " ORDER BY l.nimi";

$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();
$liigid = $conn->query("SELECT id, nimetus FROM liigid ORDER BY nimetus");
?>
<h1 class="mb-4">Kõik loomad</h1>
<form method="get" action="all_animals.php" class="row g-2 mb-4">
    <div class="col-md-3">
        <input type="text" name="otsi" class="form-control" placeholder="Otsi nime järgi" value="<?= e($otsi) ?>">
    </div>
    <div class="col-md-2">
        <select name="liik" class="form-select">
            <option value="">Kõik liigid</option>
            <?php while ($li = $liigid->fetch_assoc()): ?>
                <option value="<?= (int) $li['id'] ?>" <?= $liik === (int) $li['id'] ? 'selected' : '' ?>><?= e($li['nimetus']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="col-md-2">
        <select name="sugu" class="form-select">
            <option value="">Mõlemad sood</option>
            <option value="isane" <?= $sugu === 'isane' ? 'selected' : '' ?>>Isane</option>
            <option value="emane" <?= $sugu === 'emane' ? 'selected' : '' ?>>Emane</option>
        </select>
    </div>
    <div class="col-md-3">
        <select name="vanus" class="form-select">
            <option value="">Kõik vanused</option>
            <option value="1" <?= $vanus === '1' ? 'selected' : '' ?>>Alla 1 aasta</option>
            <option value="2" <?= $vanus === '2' ? 'selected' : '' ?>>1-5 aastat</option>
            <option value="3" <?= $vanus === '3' ? 'selected' : '' ?>>Üle 5 aasta</option>
        </select>
    </div>
    <div class="col-md-2 d-grid">
        <button type="submit" class="btn btn-primary">Otsi</button>
    </div>
</form>

<?php if ($res->num_rows === 0): ?>
    <div class="alert alert-info">Sobivaid loomi ei leitud.</div>
<?php else: ?>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
        <?php while ($a = $res->fetch_assoc()) include 'includes/card.php'; ?>
    </div>
<?php endif; ?>
<?php require_once 'includes/footer.php'; ?>
