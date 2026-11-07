<?php
require_once '../includes/config.php';
require_once '../includes/animal_helpers.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { header('Location: animals.php'); exit; }

$stmt = $conn->prepare("SELECT * FROM loomad WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$loom = $stmt->get_result()->fetch_assoc();
if (!$loom) { header('Location: animals.php'); exit; }

$opt = animal_options($conn);
$vead = [];
$pilt = $loom['pilt'];
$v = $loom;
$v['sunnikuupaev'] = (string) $loom['sunnikuupaev'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$v, $vead] = animal_collect($opt);
    if ($m = animal_check_image()) $vead[] = $m;

    if (!$vead) {
        $uus = animal_save_image(); // uut pilti ei valitud -> jääb senine
        $stmt = $conn->prepare("UPDATE loomad SET nimi = ?, sugu = ?, vanus = ?, sunnikuupaev = ?, suurus = ?, varvus = ?,
                                asukoht = ?, luhikirjeldus = ?, kirjeldus = ?, pilt = ?, staatus = ?, steriliseeritud = ?,
                                kiibistatud = ?, liik_id = ?, toug_id = ?, tootaja_id = ? WHERE id = ?");
        $p = animal_params($v, $uus ?? $pilt);
        $p[] = $id;
        $stmt->bind_param(ANIMAL_TYPES . 'i', ...$p);
        $stmt->execute();
        header('Location: animals.php?teade=muudetud');
        exit;
    }
}

$pageTitle = 'Muuda looma - Admin';
$submit = 'Salvesta muudatused';
require_once '../includes/admin_header.php';
?>
<h1 class="mb-4">Muuda looma: <?= e($loom['nimi']) ?></h1>
<?php require '../includes/animal_form.php'; ?>
<?php require_once '../includes/admin_footer.php'; ?>
