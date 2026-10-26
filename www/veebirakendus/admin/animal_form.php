<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';

// Üleslaaditud pildi eemaldamine (ainult assets/uploads kaustast)
if (!function_exists('remove_uploaded_image')) {
    function remove_uploaded_image(?string $image): void
    {
        global $config;
        if ($image === null || $image === '' || !str_starts_with($image, $config['upload_url'] . '/')) {
            return;
        }
        $base = realpath($config['upload_dir']);
        $file = realpath(dirname(__DIR__) . '/' . $image);
        if ($base === false || $file === false || !is_file($file)) {
            return;
        }
        if (!str_starts_with($file, $base . DIRECTORY_SEPARATOR)) {
            return;
        }
        unlink($file);
    }
}

// Lähtepunkt: ID GET-parameetrist (muutmine) või uus loom
$id = null;
$notFound = false;
if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($id === false) {
        $id = null;
        $notFound = true;
    }
}

$pdo = db();
$animal = null;
if ($id !== null) {
    $stmt = $pdo->prepare('SELECT * FROM animals WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $animal = $stmt->fetch() ?: null;
    if ($animal === null) {
        $notFound = true;
    }
}

$species = species_list();
$breeds = breeds_list();

// Lubatud liigid ja tõud (tõug peab kuuluma valitud liigile)
$speciesIds = array_map(fn($s) => (int) $s['id'], $species);
$breedSpecies = [];
foreach ($breeds as $b) {
    $breedSpecies[(int) $b['id']] = (int) $b['species_id'];
}

$errors = [];
$isEdit = $animal !== null;

if ($notFound && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(404);
}

// Vormi väärtused
$values = [
    'name' => '',
    'species_id' => '',
    'breed_id' => '',
    'sex' => '',
    'status' => 'Saadaval',
    'birth_date' => '',
    'location' => '',
    'size' => '',
    'color' => '',
    'short_description' => '',
    'description' => '',
    'neutered' => '',
    'chipped' => '',
];

if ($animal !== null) {
    $values = [
        'name' => (string) $animal['name'],
        'species_id' => (string) $animal['species_id'],
        'breed_id' => (string) ($animal['breed_id'] ?? ''),
        'sex' => (string) $animal['sex'],
        'status' => (string) $animal['status'],
        'birth_date' => (string) $animal['birth_date'],
        'location' => (string) ($animal['location'] ?? ''),
        'size' => (string) ($animal['size'] ?? ''),
        'color' => (string) ($animal['color'] ?? ''),
        'short_description' => (string) $animal['short_description'],
        'description' => (string) ($animal['description'] ?? ''),
        'neutered' => $animal['neutered'] ? '1' : '',
        'chipped' => $animal['chipped'] ? '1' : '',
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    if ($notFound) {
        flash_set('Looma ei leitud.', 'error');
        redirect('animals.php');
    }

    $str = fn(string $key): string => is_string($_POST[$key] ?? null) ? trim($_POST[$key]) : '';

    $values = [
        'name' => $str('name'),
        'species_id' => $str('species_id'),
        'breed_id' => $str('breed_id'),
        'sex' => $str('sex'),
        'status' => $str('status'),
        'birth_date' => $str('birth_date'),
        'location' => $str('location'),
        'size' => $str('size'),
        'color' => $str('color'),
        'short_description' => $str('short_description'),
        'description' => $str('description'),
        'neutered' => isset($_POST['neutered']) ? '1' : '',
        'chipped' => isset($_POST['chipped']) ? '1' : '',
    ];

    // Valideerimine
    if ($values['name'] === '') {
        $errors['name'] = 'Palun sisesta looma nimi.';
    } elseif (mb_strlen($values['name']) > 80) {
        $errors['name'] = 'Nimi on liiga pikk (max 80 tähemärki).';
    }

    $speciesId = filter_var($values['species_id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($speciesId === false || !in_array($speciesId, $speciesIds, true)) {
        $errors['species_id'] = 'Palun vali liik.';
    }

    // Tõug on valikuline; kui see ei kuulu valitud liigile, jäetakse see tühjaks
    $breedId = null;
    if ($values['breed_id'] !== '') {
        $bid = filter_var($values['breed_id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($bid !== false && isset($breedSpecies[$bid]) && $breedSpecies[$bid] === $speciesId) {
            $breedId = $bid;
        }
    }

    if (!in_array($values['sex'], ANIMAL_SEXES, true)) {
        $errors['sex'] = 'Palun vali sugu.';
    }
    if (!in_array($values['status'], ANIMAL_STATUSES, true)) {
        $errors['status'] = 'Palun vali staatus.';
    }

    $birthDate = DateTimeImmutable::createFromFormat('!Y-m-d', $values['birth_date']);
    if ($values['birth_date'] === '') {
        $errors['birth_date'] = 'Palun sisesta sünnikuupäev.';
    } elseif ($birthDate === false || $birthDate->format('Y-m-d') !== $values['birth_date']) {
        $errors['birth_date'] = 'Sünnikuupäev on vigane.';
    } elseif ($birthDate > new DateTimeImmutable('today')) {
        $errors['birth_date'] = 'Sünnikuupäev ei saa olla tulevikus.';
    }

    if (mb_strlen($values['location']) > 120) {
        $errors['location'] = 'Asukoht on liiga pikk (max 120 tähemärki).';
    }
    if (mb_strlen($values['size']) > 40) {
        $errors['size'] = 'Suurus on liiga pikk (max 40 tähemärki).';
    }
    if (mb_strlen($values['color']) > 80) {
        $errors['color'] = 'Värvus on liiga pikk (max 80 tähemärki).';
    }

    if ($values['short_description'] === '') {
        $errors['short_description'] = 'Palun sisesta lühikirjeldus.';
    } elseif (mb_strlen($values['short_description']) > 255) {
        $errors['short_description'] = 'Lühikirjeldus on liiga pikk (max 255 tähemärki).';
    }
    if (mb_strlen($values['description']) > 5000) {
        $errors['description'] = 'Pikk kirjeldus on liiga pikk (max 5000 tähemärki).';
    }

    // Pildi kontroll
    $upload = null;
    $file = $_FILES['photo'] ?? null;
    if (is_array($file) && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            $errors['photo'] = 'Pildi üleslaadimine ebaõnnestus.';
        } elseif ((int) $file['size'] > $config['upload_max_bytes']) {
            $errors['photo'] = 'Pilt on liiga suur (max ' . (int) ($config['upload_max_bytes'] / 1048576) . ' MB).';
        } elseif (!is_uploaded_file($file['tmp_name'])) {
            $errors['photo'] = 'Pildi üleslaadimine ebaõnnestus.';
        } else {
            $mime = null;
            if (function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                if ($finfo !== false) {
                    $mime = finfo_file($finfo, $file['tmp_name']) ?: null;
                }
            }
            if ($mime === null) {
                $info = @getimagesize($file['tmp_name']);
                $mime = is_array($info) ? ($info['mime'] ?? null) : null;
            }
            if ($mime === null || !isset($config['upload_mimes'][$mime])) {
                $errors['photo'] = 'Lubatud on ainult JPG ja PNG pildid.';
            } else {
                $upload = ['tmp' => $file['tmp_name'], 'ext' => $config['upload_mimes'][$mime]];
            }
        }
    }

    // Salvestamine
    if (!$errors) {
        $newImage = null;

        if ($upload !== null) {
            $dir = $config['upload_dir'];
            if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
                $errors['photo'] = 'Üleslaadimise kaust ei ole kättesaadav.';
            } else {
                $name = bin2hex(random_bytes(16)) . '.' . $upload['ext'];
                if (move_uploaded_file($upload['tmp'], $dir . DIRECTORY_SEPARATOR . $name)) {
                    $newImage = $config['upload_url'] . '/' . $name;
                } else {
                    $errors['photo'] = 'Pildi salvestamine ebaõnnestus.';
                }
            }
        }

        if (!$errors) {
            $params = [
                ':name' => $values['name'],
                ':species_id' => $speciesId,
                ':breed_id' => $breedId,
                ':sex' => $values['sex'],
                ':birth_date' => $values['birth_date'],
                ':size' => $values['size'] === '' ? null : $values['size'],
                ':color' => $values['color'] === '' ? null : $values['color'],
                ':location' => $values['location'] === '' ? null : $values['location'],
                ':neutered' => $values['neutered'] === '1' ? 1 : 0,
                ':chipped' => $values['chipped'] === '1' ? 1 : 0,
                ':short_description' => $values['short_description'],
                ':description' => $values['description'] === '' ? null : $values['description'],
                ':status' => $values['status'],
            ];

            try {
                if ($isEdit) {
                    $params[':image'] = $newImage ?? $animal['image'];
                    $sql = 'UPDATE animals SET name = :name, species_id = :species_id, breed_id = :breed_id, sex = :sex,
                            birth_date = :birth_date, size = :size, color = :color, location = :location,
                            neutered = :neutered, chipped = :chipped, short_description = :short_description,
                            description = :description, status = :status, image = :image
                            WHERE id = :id';
                    $params[':id'] = $id;
                    $pdo->prepare($sql)->execute($params);
                    if ($newImage !== null) {
                        remove_uploaded_image($animal['image']);
                    }
                } else {
                    $params[':image'] = $newImage;
                    $sql = 'INSERT INTO animals (name, species_id, breed_id, sex, birth_date, size, color, location,
                            neutered, chipped, short_description, description, status, image)
                            VALUES (:name, :species_id, :breed_id, :sex, :birth_date, :size, :color, :location,
                            :neutered, :chipped, :short_description, :description, :status, :image)';
                    $pdo->prepare($sql)->execute($params);
                }
            } catch (Throwable $ex) {
                remove_uploaded_image($newImage);
                throw $ex;
            }

            flash_set('Loom salvestatud.', 'success');
            redirect('animals.php');
        }
    }
}

$adminActive = 'animal_form';
$pageTitle = $isEdit ? 'Muuda looma' : 'Lisa uus loom';
require __DIR__ . '/../includes/admin_header.php';

if ($notFound) {
    ?>
    <div class="alert alert-danger">Looma ei leitud. Loom on võib-olla kustutatud.</div>
    <a href="animals.php" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-2"></i>Tagasi loomade juurde</a>
    <?php
    require __DIR__ . '/../includes/admin_footer.php';
    exit;
}

$invalid = fn(string $field): string => isset($errors[$field]) ? ' is-invalid' : '';
$feedback = fn(string $field): string => isset($errors[$field])
    ? '<div class="invalid-feedback">' . e($errors[$field]) . '</div>'
    : '';

$formAction = 'animal_form.php' . ($isEdit ? '?id=' . (int) $id : '');
$previewUrl = $animal !== null ? animal_image_url($animal['image'], '../') : null;
$todayIso = date('Y-m-d');
?>
<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
        <h1 class="fw-bold mb-1"><?= e($pageTitle) ?></h1>
        <p class="text-muted mb-0"><?= $isEdit ? 'Muuda looma andmeid ja salvesta muudatused.' : 'Sisesta looma andmed ja lisa ta varjupaiga nimekirja.' ?></p>
    </div>
    <a href="animals.php" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-2"></i>Tagasi loomade juurde</a>
</div>

<form method="post" action="<?= e($formAction) ?>" enctype="multipart/form-data" novalidate>
    <?= csrf_field() ?>
    <div class="row g-4">
        <!-- Vasak veerg -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-3">Looma põhiandmed</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">Nimi <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" class="form-control<?= $invalid('name') ?>" maxlength="80" value="<?= e($values['name']) ?>" placeholder="Sisesta looma nimi">
                            <?= $feedback('name') ?>
                        </div>
                        <div class="col-md-6">
                            <label for="species_id" class="form-label fw-semibold">Liik <span class="text-danger">*</span></label>
                            <select id="species_id" name="species_id" class="form-select<?= $invalid('species_id') ?>">
                                <option value="">Vali liik</option>
                                <?php foreach ($species as $sp): ?>
                                    <option value="<?= e($sp['id']) ?>" <?= (string) $sp['id'] === $values['species_id'] ? 'selected' : '' ?>><?= e($sp['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?= $feedback('species_id') ?>
                        </div>
                        <div class="col-md-6">
                            <label for="breed_id" class="form-label fw-semibold">Tõug</label>
                            <select id="breed_id" name="breed_id" class="form-select<?= $invalid('breed_id') ?>">
                                <option value="">Segu / teadmata</option>
                                <?php
                                $currentSpeciesName = null;
                                foreach ($breeds as $b):
                                    if ($currentSpeciesName !== $b['species_name']):
                                        if ($currentSpeciesName !== null) {
                                            echo '</optgroup>';
                                        }
                                        $currentSpeciesName = $b['species_name'];
                                        echo '<optgroup label="' . e($currentSpeciesName) . '">';
                                    endif;
                                ?>
                                    <option value="<?= e($b['id']) ?>" <?= (string) $b['id'] === $values['breed_id'] ? 'selected' : '' ?>><?= e($b['name']) ?></option>
                                <?php endforeach;
                                if ($currentSpeciesName !== null) {
                                    echo '</optgroup>';
                                }
                                ?>
                            </select>
                            <?= $feedback('breed_id') ?>
                        </div>
                        <div class="col-md-6">
                            <label for="sex" class="form-label fw-semibold">Sugu <span class="text-danger">*</span></label>
                            <select id="sex" name="sex" class="form-select<?= $invalid('sex') ?>">
                                <option value="">Vali sugu</option>
                                <?php foreach (ANIMAL_SEXES as $sx): ?>
                                    <option value="<?= e($sx) ?>" <?= $sx === $values['sex'] ? 'selected' : '' ?>><?= e($sx) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?= $feedback('sex') ?>
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label fw-semibold">Staatus <span class="text-danger">*</span></label>
                            <select id="status" name="status" class="form-select<?= $invalid('status') ?>">
                                <?php foreach (ANIMAL_STATUSES as $st): ?>
                                    <option value="<?= e($st) ?>" <?= $st === $values['status'] ? 'selected' : '' ?>><?= e($st) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?= $feedback('status') ?>
                        </div>
                        <div class="col-md-6">
                            <label for="birth_date" class="form-label fw-semibold">Sünnikuupäev <span class="text-danger">*</span></label>
                            <input type="date" id="birth_date" name="birth_date" class="form-control<?= $invalid('birth_date') ?>" max="<?= e($todayIso) ?>" value="<?= e($values['birth_date']) ?>">
                            <?= $feedback('birth_date') ?>
                        </div>
                        <div class="col-md-6">
                            <label for="location" class="form-label fw-semibold">Asukoht</label>
                            <input type="text" id="location" name="location" class="form-control<?= $invalid('location') ?>" maxlength="120" value="<?= e($values['location']) ?>" placeholder="Näiteks Haapsalu varjupaik">
                            <?= $feedback('location') ?>
                        </div>
                        <div class="col-md-6">
                            <label for="size" class="form-label fw-semibold">Suurus</label>
                            <input type="text" id="size" name="size" class="form-control<?= $invalid('size') ?>" maxlength="40" value="<?= e($values['size']) ?>" placeholder="Näiteks Keskmine (15–25 kg)">
                            <?= $feedback('size') ?>
                        </div>
                        <div class="col-md-6">
                            <label for="color" class="form-label fw-semibold">Värvus</label>
                            <input type="text" id="color" name="color" class="form-control<?= $invalid('color') ?>" maxlength="80" value="<?= e($values['color']) ?>" placeholder="Näiteks must ja valge">
                            <?= $feedback('color') ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-3">Kirjeldus</h2>
                    <div class="mb-3">
                        <label for="short_description" class="form-label fw-semibold">Lühikirjeldus <span class="text-danger">*</span></label>
                        <input type="text" id="short_description" name="short_description" class="form-control<?= $invalid('short_description') ?>" maxlength="255" value="<?= e($values['short_description']) ?>" placeholder="Lühike tutvustus loomakaardi jaoks">
                        <?= $feedback('short_description') ?>
                    </div>
                    <div class="mb-0">
                        <label for="description" class="form-label fw-semibold">Pikem kirjeldus</label>
                        <textarea id="description" name="description" class="form-control<?= $invalid('description') ?>" rows="8" placeholder="Kirjelda looma iseloomu, käitumist ja vajadusi"><?= e($values['description']) ?></textarea>
                        <?= $feedback('description') ?>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-3">Terviseandmed</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="neutered" name="neutered" value="1" <?= $values['neutered'] === '1' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="neutered">Loom on steriliseeritud või kastreeritud</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="chipped" name="chipped" value="1" <?= $values['chipped'] === '1' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="chipped">Loom on kiibistatud</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Parem veerg -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-3">Looma pilt</h2>
                    <?php if ($previewUrl !== null): ?>
                        <div class="border rounded-3 text-center p-2 mb-3 bg-light">
                            <img src="<?= e($previewUrl) ?>" alt="<?= e($values['name']) ?>" class="img-fluid rounded" style="max-height: 220px; object-fit: cover;">
                        </div>
                    <?php else: ?>
                        <div class="bg-light border rounded-3 text-center text-muted p-4 mb-3">
                            <i class="fa-regular fa-image fa-3x mb-2" aria-hidden="true"></i>
                            <p class="mb-0">Vali loomale sobiv pilt.</p>
                        </div>
                    <?php endif; ?>
                    <label for="photo" class="form-label fw-semibold">Laadi pilt üles</label>
                    <input type="file" id="photo" name="photo" class="form-control<?= $invalid('photo') ?>" accept="image/jpeg,image/png">
                    <?= $feedback('photo') ?>
                    <div class="form-text">JPG või PNG, max 5 MB.</div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-3">Salvestamine</h2>
                    <p class="text-muted">Kontrolli enne salvestamist, et kõik looma andmed oleksid õiged.</p>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success"><i class="fa-solid fa-floppy-disk me-2"></i>Salvesta loom</button>
                        <a href="animals.php" class="btn btn-outline-secondary">Tühista</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
