<?php /* Looma kaart; ootab muutujat $a */ ?>
<div class="col">
    <div class="card h-100">
        <img src="assets/images/<?= e($a['pilt']) ?>" class="card-img-top" alt="<?= e($a['nimi']) ?>"
             onerror="this.onerror=null;this.src='assets/images/default.png'">
        <div class="card-body">
            <h5 class="card-title"><?= e($a['nimi']) ?></h5>
            <p class="card-text"><?= e($a['liik']) ?>, <?= e($a['sugu']) ?>, <?= (int) $a['vanus'] ?> a<br>
                <?= e($a['luhikirjeldus']) ?></p>
            <a href="animal.php?id=<?= (int) $a['id'] ?>" class="btn btn-primary">Vaata lähemalt</a>
        </div>
    </div>
</div>
