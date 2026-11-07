<?php
require_once '../includes/config.php';

$perPage = 10;

// Kustutamine (POST, ettevalmistatud päring, PRG)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $teade = 'viga';
    if ($id) {
        try {
            $stmt = $conn->prepare("DELETE FROM loomad WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            if ($stmt->affected_rows > 0) $teade = 'kustutatud';
        } catch (mysqli_sql_exception $ex) { // nt võõrvõti: loomaga on seotud andmeid
        }
    }
    header("Location: animals.php?teade=$teade");
    exit;
}

// Lehekülgede kaupa kuvamine
$total = (int) $conn->query("SELECT COUNT(*) AS kokku FROM loomad")->fetch_assoc()['kokku'];
$pages = max(1, (int) ceil($total / $perPage));
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, ['options' => ['default' => 1, 'min_range' => 1]]);
$page = min($page, $pages); // liiga suur lehekülg -> viimane
$offset = ($page - 1) * $perPage;

$stmt = $conn->prepare("SELECT loomad.id, loomad.nimi, loomad.pilt, loomad.sugu, loomad.vanus, loomad.staatus, loomad.lisatud, liigid.nimetus AS liik
                        FROM loomad
                        LEFT JOIN liigid ON loomad.liik_id = liigid.id
                        ORDER BY loomad.id DESC
                        LIMIT ? OFFSET ?");
$stmt->bind_param("ii", $perPage, $offset);
$stmt->execute();
$res = $stmt->get_result();

$teated = ['lisatud' => ['success', 'Loom lisati.'], 'muudetud' => ['success', 'Looma andmed salvestati.'],
           'kustutatud' => ['success', 'Loom kustutati.'], 'viga' => ['danger', 'Toiming ebaõnnestus (loom puudub või on seotud andmetega).']];
$teade = $teated[$_GET['teade'] ?? ''] ?? null;
$badge = ['saadaval' => 'success', 'broneeritud' => 'warning', 'hoolduses' => 'secondary'];

$pageTitle = 'Loomad - Admin';
require_once '../includes/admin_header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0">Loomad</h1>
    <a href="animal_add.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Lisa loom</a>
</div>
<?php if ($teade): ?><div class="alert alert-<?= $teade[0] ?>"><?= e($teade[1]) ?></div><?php endif; ?>

<div class="table-responsive">
<table class="table table-striped align-middle">
    <thead><tr><th>ID</th><th>Pilt</th><th>Nimi</th><th>Liik</th><th>Sugu</th><th>Vanus</th><th>Staatus</th><th>Lisatud</th><th class="text-end">Tegevused</th></tr></thead>
    <tbody>
    <?php while ($a = $res->fetch_assoc()): ?>
        <tr>
            <td><?= (int) $a['id'] ?></td>
            <td><img src="../assets/images/<?= e($a['pilt']) ?>" alt="<?= e($a['nimi']) ?>" width="48" height="48" style="object-fit:cover"
                     onerror="this.onerror=null;this.src='../assets/images/default.png'"></td>
            <td><?= e($a['nimi']) ?></td>
            <td><?= e($a['liik']) ?></td>
            <td><?= e($a['sugu']) ?></td>
            <td><?= (int) $a['vanus'] ?></td>
            <td><span class="badge text-bg-<?= $badge[$a['staatus']] ?? 'light' ?>"><?= e($a['staatus']) ?></span></td>
            <td><?= e($a['lisatud']) ?></td>
            <td class="text-end text-nowrap">
                <a href="../animal.php?id=<?= (int) $a['id'] ?>" class="btn btn-sm btn-outline-primary">Vaata</a>
                <a href="animal_edit.php?id=<?= (int) $a['id'] ?>" class="btn btn-sm btn-outline-secondary">Muuda</a>
                <form action="animals.php" method="post" class="d-inline"
                      onsubmit="return confirm(<?= e(json_encode('Kas oled kindel, et soovid looma ' . $a['nimi'] . ' kustutada?', JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP)) ?>);">
                    <input type="hidden" name="id" value="<?= (int) $a['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-outline-danger">Kustuta</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
</div>

<nav aria-label="Lehekülgede navigatsioon">
    <ul class="pagination justify-content-center">
        <li class="page-item<?= $page <= 1 ? ' disabled' : '' ?>"><a class="page-link" href="?page=<?= $page - 1 ?>">Eelmine</a></li>
        <?php for ($i = 1; $i <= $pages; $i++): ?>
            <li class="page-item<?= $i === $page ? ' active' : '' ?>"><a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a></li>
        <?php endfor; ?>
        <li class="page-item<?= $page >= $pages ? ' disabled' : '' ?>"><a class="page-link" href="?page=<?= $page + 1 ?>">Järgmine</a></li>
    </ul>
</nav>
<p class="text-center text-muted">Kokku <?= $total ?> looma</p>
<?php require_once '../includes/admin_footer.php'; ?>
