<?php /* Looma vorm; ootab muutujaid $v (väärtused), $opt (rippmenüüd), $vead, $submit ja valikuliselt $pilt (olemasolev pilt) */ ?>
<?php if ($vead): ?>
    <div class="alert alert-danger">
        <ul class="mb-0"><?php foreach ($vead as $m): ?><li><?= e($m) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>
<form method="post" enctype="multipart/form-data" class="row g-3">
    <div class="col-md-6">
        <label for="nimi" class="form-label">Nimi</label>
        <input type="text" id="nimi" name="nimi" class="form-control" maxlength="100" required value="<?= e($v['nimi']) ?>">
    </div>
    <div class="col-md-6">
        <label for="liik_id" class="form-label">Liik</label>
        <select id="liik_id" name="liik_id" class="form-select" required>
            <option value="">Vali liik</option>
            <?php foreach ($opt['liigid'] as $o): ?>
                <option value="<?= (int) $o['id'] ?>" <?= (int) $v['liik_id'] === (int) $o['id'] ? 'selected' : '' ?>><?= e($o['nimetus']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6">
        <label for="toug_id" class="form-label">Tõug</label>
        <select id="toug_id" name="toug_id" class="form-select" required>
            <option value="">Vali tõug</option>
            <?php foreach ($opt['liigid'] as $l): ?>
                <optgroup label="<?= e($l['nimetus']) ?>">
                <?php foreach ($opt['toud'] as $o): if ($o['liik_id'] == $l['id']): ?>
                    <option value="<?= (int) $o['id'] ?>" <?= (int) $v['toug_id'] === (int) $o['id'] ? 'selected' : '' ?>><?= e($o['nimetus']) ?></option>
                <?php endif; endforeach; ?>
                </optgroup>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6">
        <label for="tootaja_id" class="form-label">Vastutav töötaja</label>
        <select id="tootaja_id" name="tootaja_id" class="form-select" required>
            <option value="">Vali töötaja</option>
            <?php foreach ($opt['tootajad'] as $o): ?>
                <option value="<?= (int) $o['id'] ?>" <?= (int) $v['tootaja_id'] === (int) $o['id'] ? 'selected' : '' ?>><?= e($o['nimi']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3">
        <label for="sugu" class="form-label">Sugu</label>
        <select id="sugu" name="sugu" class="form-select" required>
            <option value="">Vali</option>
            <?php foreach ($opt['sugu'] as $o): ?><option <?= $v['sugu'] === $o ? 'selected' : '' ?>><?= e($o) ?></option><?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3">
        <label for="vanus" class="form-label">Vanus (aastat)</label>
        <input type="number" id="vanus" name="vanus" class="form-control" min="0" max="40" required value="<?= e($v['vanus']) ?>">
    </div>
    <div class="col-md-3">
        <label for="sunnikuupaev" class="form-label">Sünnikuupäev</label>
        <input type="date" id="sunnikuupaev" name="sunnikuupaev" class="form-control" value="<?= e($v['sunnikuupaev']) ?>">
    </div>
    <div class="col-md-3">
        <label for="suurus" class="form-label">Suurus</label>
        <select id="suurus" name="suurus" class="form-select" required>
            <option value="">Vali</option>
            <?php foreach ($opt['suurus'] as $o): ?><option <?= $v['suurus'] === $o ? 'selected' : '' ?>><?= e($o) ?></option><?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-4">
        <label for="varvus" class="form-label">Värvus</label>
        <input type="text" id="varvus" name="varvus" class="form-control" maxlength="100" required value="<?= e($v['varvus']) ?>">
    </div>
    <div class="col-md-4">
        <label for="asukoht" class="form-label">Asukoht</label>
        <input type="text" id="asukoht" name="asukoht" class="form-control" maxlength="100" required value="<?= e($v['asukoht']) ?>">
    </div>
    <div class="col-md-4">
        <label for="staatus" class="form-label">Staatus</label>
        <select id="staatus" name="staatus" class="form-select" required>
            <?php foreach ($opt['staatus'] as $o): ?><option <?= $v['staatus'] === $o ? 'selected' : '' ?>><?= e($o) ?></option><?php endforeach; ?>
        </select>
    </div>
    <div class="col-12">
        <label for="luhikirjeldus" class="form-label">Lühikirjeldus</label>
        <input type="text" id="luhikirjeldus" name="luhikirjeldus" class="form-control" maxlength="255" required value="<?= e($v['luhikirjeldus']) ?>">
    </div>
    <div class="col-12">
        <label for="kirjeldus" class="form-label">Kirjeldus</label>
        <textarea id="kirjeldus" name="kirjeldus" class="form-control" rows="5" required><?= e($v['kirjeldus']) ?></textarea>
    </div>
    <div class="col-12">
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="steriliseeritud" name="steriliseeritud" value="1" <?= $v['steriliseeritud'] ? 'checked' : '' ?>>
            <label class="form-check-label" for="steriliseeritud">Steriliseeritud</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="kiibistatud" name="kiibistatud" value="1" <?= $v['kiibistatud'] ? 'checked' : '' ?>>
            <label class="form-check-label" for="kiibistatud">Kiibistatud</label>
        </div>
    </div>
    <div class="col-12">
        <?php if (!empty($pilt)): ?>
            <img src="../assets/images/<?= e($pilt) ?>" alt="Praegune pilt" class="img-thumbnail mb-2 d-block" style="max-height:120px"
                 onerror="this.onerror=null;this.src='../assets/images/default.png'">
        <?php endif; ?>
        <label for="pilt" class="form-label">Pilt (JPG või PNG, mittekohustuslik)</label>
        <input type="file" id="pilt" name="pilt" class="form-control" accept=".jpg,.jpeg,.png,image/jpeg,image/png">
    </div>
    <div class="col-12">
        <button type="submit" class="btn btn-primary"><?= e($submit) ?></button>
        <a href="animals.php" class="btn btn-outline-secondary">Tühista</a>
    </div>
</form>
