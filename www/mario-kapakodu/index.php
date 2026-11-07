<?php
$pageTitle = 'Mario Käpakodu';
require_once 'includes/header.php';

$res = $conn->query("SELECT l.id, l.nimi, l.sugu, l.vanus, l.luhikirjeldus, l.pilt, li.nimetus AS liik
                     FROM loomad l JOIN liigid li ON li.id = l.liik_id
                     ORDER BY l.id DESC LIMIT 4");
?>
<h1 class="mb-4">Uued pereliikmed</h1>
<div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
    <?php while ($a = $res->fetch_assoc()) include 'includes/card.php'; ?>
</div>
<?php require_once 'includes/footer.php'; ?>
