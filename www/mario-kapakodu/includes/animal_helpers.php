<?php
// Looma lisamise/muutmise ühised abifunktsioonid (Ülesanded 13 ja 14).
const PILT_KAUST = __DIR__ . '/../assets/images/';
const ANIMAL_TYPES = 'ssissssssssiiiii'; // bind_param tüübid, vt animal_params()

// ENUM-veeru lubatud väärtused andmebaasist (andmebaas on tõe allikas).
function enum_values(mysqli $conn, string $col): array {
    $r = $conn->query("SELECT COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'loomad' AND COLUMN_NAME = '$col'")->fetch_row();
    preg_match_all("/'([^']*)'/u", $r[0] ?? '', $m);
    return $m[1];
}

// Rippmenüüde andmed andmebaasist.
function animal_options(mysqli $conn): array {
    return [
        'sugu' => enum_values($conn, 'sugu'), 'suurus' => enum_values($conn, 'suurus'), 'staatus' => enum_values($conn, 'staatus'),
        'liigid'   => $conn->query("SELECT id, nimetus FROM liigid ORDER BY nimetus")->fetch_all(MYSQLI_ASSOC),
        'toud'     => $conn->query("SELECT id, liik_id, nimetus FROM toud ORDER BY nimetus")->fetch_all(MYSQLI_ASSOC),
        'tootajad' => $conn->query("SELECT id, nimi FROM tootajad ORDER BY nimi")->fetch_all(MYSQLI_ASSOC),
    ];
}

// Loeb POST-andmed ja kontrollib neid. Tagastab [väärtused, vead].
function animal_collect(array $opt): array {
    $t = fn($k) => trim((string) ($_POST[$k] ?? ''));
    $v = [
        'nimi' => $t('nimi'), 'liik_id' => (int) $t('liik_id'), 'toug_id' => (int) $t('toug_id'),
        'tootaja_id' => (int) $t('tootaja_id'), 'sugu' => $t('sugu'), 'vanus' => $t('vanus'),
        'sunnikuupaev' => $t('sunnikuupaev'), 'suurus' => $t('suurus'), 'varvus' => $t('varvus'),
        'asukoht' => $t('asukoht'), 'luhikirjeldus' => $t('luhikirjeldus'), 'kirjeldus' => $t('kirjeldus'),
        'staatus' => $t('staatus'), 'steriliseeritud' => isset($_POST['steriliseeritud']) ? 1 : 0,
        'kiibistatud' => isset($_POST['kiibistatud']) ? 1 : 0,
    ];
    $vead = [];
    $max = ['nimi' => 100, 'varvus' => 100, 'asukoht' => 100, 'luhikirjeldus' => 255];
    $nimed = ['nimi' => 'Nimi', 'varvus' => 'Värvus', 'asukoht' => 'Asukoht', 'luhikirjeldus' => 'Lühikirjeldus', 'kirjeldus' => 'Kirjeldus'];
    foreach ($nimed as $k => $label) {
        if ($v[$k] === '') $vead[] = "$label on kohustuslik.";
        elseif (isset($max[$k]) && mb_strlen($v[$k]) > $max[$k]) $vead[] = "$label on liiga pikk (kuni $max[$k] märki).";
    }
    if (!in_array($v['liik_id'], array_map('intval', array_column($opt['liigid'], 'id')), true)) $vead[] = 'Vali liik.';
    $toug = array_values(array_filter($opt['toud'], fn($r) => (int) $r['id'] === $v['toug_id']));
    if (!$toug || (int) $toug[0]['liik_id'] !== $v['liik_id']) $vead[] = 'Vali valitud liigile sobiv tõug.';
    if (!in_array($v['tootaja_id'], array_map('intval', array_column($opt['tootajad'], 'id')), true)) $vead[] = 'Vali töötaja.';
    if (!in_array($v['sugu'], $opt['sugu'], true)) $vead[] = 'Vali sugu.';
    if (!in_array($v['suurus'], $opt['suurus'], true)) $vead[] = 'Vali suurus.';
    if (!in_array($v['staatus'], $opt['staatus'], true)) $vead[] = 'Vali staatus.';
    if (filter_var($v['vanus'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 40]]) === false) $vead[] = 'Vanus peab olema täisarv 0-40.';
    if ($v['sunnikuupaev'] !== '') {
        $d = DateTime::createFromFormat('Y-m-d', $v['sunnikuupaev']);
        if (!$d || $d->format('Y-m-d') !== $v['sunnikuupaev']) $vead[] = 'Sünnikuupäev on vigane.';
    }
    return [$v, $vead];
}

// Kontrollib üleslaaditud pilti; tagastab veateate või null (ka siis, kui faili ei valitud).
function animal_check_image(): ?string {
    $f = $_FILES['pilt'] ?? null;
    if (!$f || $f['error'] === UPLOAD_ERR_NO_FILE) return null;
    if ($f['error'] !== UPLOAD_ERR_OK) return 'Pildi üleslaadimine ebaõnnestus.';
    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    $type = @getimagesize($f['tmp_name'])[2] ?? 0;
    if (!in_array($ext, ['jpg', 'jpeg', 'png'], true) || !in_array($type, [IMAGETYPE_JPEG, IMAGETYPE_PNG], true)) return 'Pilt peab olema JPG või PNG.';
    if ($f['size'] > 2 * 1024 * 1024) return 'Pilt on liiga suur (kuni 2 MB).';
    return null;
}

// Salvestab pildi kausta assets/images ajatempliga; tagastab faili nime või null.
function animal_save_image(): ?string {
    $f = $_FILES['pilt'] ?? null;
    if (!$f || $f['error'] !== UPLOAD_ERR_OK) return null;
    $safe = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($f['name'])); // täpitähed jms failinimes -> "_"
    $name = time() . '_' . $safe;
    return move_uploaded_file($f['tmp_name'], PILT_KAUST . $name) ? $name : null;
}

// Päringu andmed bind_param jaoks (INSERT ja UPDATE kasutavad sama järjekorda).
function animal_params(array $v, string $pilt): array {
    return [$v['nimi'], $v['sugu'], (int) $v['vanus'], $v['sunnikuupaev'] === '' ? null : $v['sunnikuupaev'],
        $v['suurus'], $v['varvus'], $v['asukoht'], $v['luhikirjeldus'], $v['kirjeldus'], $pilt, $v['staatus'],
        $v['steriliseeritud'], $v['kiibistatud'], $v['liik_id'], $v['toug_id'], $v['tootaja_id']];
}
