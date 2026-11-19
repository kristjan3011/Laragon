<?php
declare(strict_types=1);

// Varasem õppeversioon on arhiiv. Päring suunatakse terviklikku rakendusse.
// POST-andmeid ei edastata ning eri andmebaaside ID-sid ei kasutata uuesti.
$target = '../veebirakendus/all_animals.php';
$query = [];
$search = $_GET['q'] ?? $_GET['otsi'] ?? '';
if (is_string($search) && trim($search) !== '') {
    $query['q'] = mb_substr(trim($search), 0, 80);
}
$sex = $_GET['sex'] ?? $_GET['sugu'] ?? '';
$sexes = ['isane' => 'Isane', 'emane' => 'Emane', 'Isane' => 'Isane', 'Emane' => 'Emane'];
if (is_string($sex) && isset($sexes[$sex])) {
    $query['sex'] = $sexes[$sex];
}
$age = $_GET['age'] ?? '';
if (is_string($age) && in_array($age, ['kutsikas', 'noor', 'taiskasvanu'], true)) {
    $query['age'] = $age;
}
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($page !== false && $page !== null) {
    $query['page'] = $page;
}
if ($query !== []) {
    $target .= '?' . http_build_query($query);
}
header('Location: ' . $target, true, 303);
exit;
