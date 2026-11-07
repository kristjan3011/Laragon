<?php
require_once '../includes/config.php';
require_once '../includes/animal_helpers.php';

$opt = animal_options($conn);
$vead = [];
$v = ['nimi' => '', 'liik_id' => 0, 'toug_id' => 0, 'tootaja_id' => 0, 'sugu' => '', 'vanus' => '', 'sunnikuupaev' => '',
      'suurus' => '', 'varvus' => '', 'asukoht' => '', 'luhikirjeldus' => '', 'kirjeldus' => '',
      'staatus' => 'saadaval', 'steriliseeritud' => 0, 'kiibistatud' => 0];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$v, $vead] = animal_collect($opt);
    if ($m = animal_check_image()) $vead[] = $m;

    if (!$vead) {
        $pilt = animal_save_image() ?? 'default.png';
        $stmt = $conn->prepare("INSERT INTO loomad (nimi, sugu, vanus, sunnikuupaev, suurus, varvus, asukoht, luhikirjeldus,
                                kirjeldus, pilt, staatus, steriliseeritud, kiibistatud, liik_id, toug_id, tootaja_id)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $p = animal_params($v, $pilt);
        $stmt->bind_param(ANIMAL_TYPES, ...$p);
        $stmt->execute();
        header('Location: animals.php?teade=lisatud');
        exit;
    }
}

$pageTitle = 'Lisa loom - Admin';
$submit = 'Lisa loom';
require_once '../includes/admin_header.php';
?>
<h1 class="mb-4">Lisa uus loom</h1>
<?php require '../includes/animal_form.php'; ?>
<?php require_once '../includes/admin_footer.php'; ?>
