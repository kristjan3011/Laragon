<?php
declare(strict_types=1);

// Varasem õppeversioon on arhiiv. Päring suunatakse terviklikku rakendusse.
// POST-andmeid ei edastata ning eri andmebaaside ID-sid ei kasutata uuesti.
$target = '../veebirakendus/index.php';
header('Location: ' . $target, true, 303);
exit;
