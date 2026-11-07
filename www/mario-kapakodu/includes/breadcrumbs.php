<?php
// Leivapuru tuleb lehe nimest; üksiklooma vaates loetakse looma nimi andmebaasist.
$crumbs = [];
if ($current === 'all_animals.php') {
    $crumbs = ['Kõik loomad' => null];
} elseif ($current === 'contact.php') {
    $crumbs = ['Kontakt' => null];
} elseif ($current === 'animal.php') {
    $crumbs = ['Kõik loomad' => 'all_animals.php'];
    $bid = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($bid) {
        $bst = $conn->prepare("SELECT nimi FROM loomad WHERE id = ?");
        $bst->bind_param("i", $bid);
        $bst->execute();
        if ($row = $bst->get_result()->fetch_assoc()) {
            $crumbs[$row['nimi']] = null;
        }
    }
}
if ($crumbs): ?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Avaleht</a></li>
        <?php foreach ($crumbs as $label => $href): ?>
            <?php if ($href): ?>
                <li class="breadcrumb-item"><a href="<?= e($href) ?>"><?= e($label) ?></a></li>
            <?php else: ?>
                <li class="breadcrumb-item active" aria-current="page"><?= e($label) ?></li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ol>
</nav>
<?php endif; ?>
