<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';

start_session();
require_login('../login.php');

// POST-käsitlus (PRG): enne päise väljastamist
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $pdo = db();
    $action = (string) ($_POST['action'] ?? '');

    $positiveId = static fn(mixed $v): int|false => filter_var($v, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

    if ($action === 'add_species') {
        $name = trim((string) ($_POST['name'] ?? ''));
        $len = mb_strlen($name);
        if ($len < 2 || $len > 50) {
            $_SESSION['form_state'] = ['form' => 'add_species', 'errors' => ['name' => 'Liigi nimi peab olema 2–50 märki.'], 'old' => ['name' => $name]];
            flash_set('Liigi lisamine ebaõnnestus. Paranda vead.', 'error');
        } else {
            try {
                $pdo->prepare('INSERT INTO species (name) VALUES (?)')->execute([$name]);
                flash_set('Liik „' . $name . '“ lisati.');
            } catch (PDOException $ex) {
                if ($ex->getCode() !== '23000') {
                    throw $ex;
                }
                $_SESSION['form_state'] = ['form' => 'add_species', 'errors' => ['name' => 'Selline liik on juba olemas.'], 'old' => ['name' => $name]];
                flash_set('Selline liik on juba olemas.', 'error');
            }
        }
        redirect('species.php');
    }

    if ($action === 'delete_species') {
        $id = $positiveId($_POST['id'] ?? '');
        if ($id === false) {
            flash_set('Vigane liik.', 'error');
            redirect('species.php');
        }
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM animals WHERE species_id = ?');
        $stmt->execute([$id]);
        if ((int) $stmt->fetchColumn() > 0) {
            flash_set('Liiki ei saa kustutada, sest sellega on seotud loomad.', 'error');
        } else {
            try {
                $del = $pdo->prepare('DELETE FROM species WHERE id = ?');
                $del->execute([$id]);
                if ($del->rowCount() > 0) {
                    flash_set('Liik kustutati koos selle tõugudega.');
                } else {
                    flash_set('Liiki ei leitud.', 'error');
                }
            } catch (PDOException $ex) {
                // Samal ajal lisati liigile loom (viitevõtme rike)
                flash_set('Liiki ei saa kustutada, sest sellega on seotud loomad.', 'error');
            }
        }
        redirect('species.php');
    }

    if ($action === 'add_breed') {
        $speciesRaw = (string) ($_POST['species_id'] ?? '');
        $speciesId = $positiveId($speciesRaw);
        $name = trim((string) ($_POST['name'] ?? ''));
        $errors = [];

        if ($speciesId === false) {
            $errors['species_id'] = 'Vali liik.';
        } else {
            $chk = $pdo->prepare('SELECT COUNT(*) FROM species WHERE id = ?');
            $chk->execute([$speciesId]);
            if ((int) $chk->fetchColumn() === 0) {
                $errors['species_id'] = 'Valitud liiki ei leitud.';
            }
        }
        $len = mb_strlen($name);
        if ($len < 2 || $len > 80) {
            $errors['name'] = 'Tõu nimi peab olema 2–80 märki.';
        }

        if ($errors === []) {
            try {
                $pdo->prepare('INSERT INTO breeds (species_id, name) VALUES (?, ?)')->execute([$speciesId, $name]);
                flash_set('Tõug „' . $name . '“ lisati.');
            } catch (PDOException $ex) {
                if ($ex->getCode() !== '23000') {
                    throw $ex;
                }
                $errors['name'] = 'Selline tõug on sellel liigil juba olemas.';
            }
        }

        if ($errors !== []) {
            $_SESSION['form_state'] = ['form' => 'add_breed', 'errors' => $errors, 'old' => ['species_id' => $speciesRaw, 'name' => $name]];
            flash_set('Tõugu ei lisatud. Paranda vead.', 'error');
        }
        redirect('species.php');
    }

    if ($action === 'delete_breed') {
        $id = $positiveId($_POST['id'] ?? '');
        if ($id === false) {
            flash_set('Vigane tõug.', 'error');
            redirect('species.php');
        }
        // Looma tõu viide nullitakse FK-reegli (ON DELETE SET NULL) järgi
        $del = $pdo->prepare('DELETE FROM breeds WHERE id = ?');
        $del->execute([$id]);
        if ($del->rowCount() > 0) {
            flash_set('Tõug kustutati.');
        } else {
            flash_set('Tõugu ei leitud.', 'error');
        }
        redirect('species.php');
    }

    flash_set('Tundmatu tegevus.', 'error');
    redirect('species.php');
}

// Vormi olek eelmisest POST-ist (vead ja sisestatud väärtused)
$formState = $_SESSION['form_state'] ?? null;
unset($_SESSION['form_state']);
$speciesErrors = [];
$speciesOld = ['name' => ''];
$breedErrors = [];
$breedOld = ['species_id' => '', 'name' => ''];
if (is_array($formState)) {
    if (($formState['form'] ?? '') === 'add_species') {
        $speciesErrors = $formState['errors'];
        $speciesOld = array_merge($speciesOld, $formState['old']);
    } elseif (($formState['form'] ?? '') === 'add_breed') {
        $breedErrors = $formState['errors'];
        $breedOld = array_merge($breedOld, $formState['old']);
    }
}

$adminActive = 'species';
$pageTitle = 'Liigid ja tõud';
require __DIR__ . '/../includes/admin_header.php';

$speciesRows = db()->query('SELECT s.id, s.name, COUNT(a.id) AS animal_count
    FROM species s LEFT JOIN animals a ON a.species_id = s.id
    GROUP BY s.id, s.name ORDER BY s.name')->fetchAll();
$breeds = breeds_list();
$speciesOptions = species_list();
?>

<h1 class="h3 mb-4"><i class="fa-solid fa-tags text-success me-2"></i>Liigid ja tõud</h1>

<div class="row g-4">
    <!-- Liigid -->
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom-0 pt-3">
                <h2 class="h5 mb-0"><i class="fa-solid fa-paw text-success me-2"></i>Liigid</h2>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Nimi</th>
                                <th scope="col">Loomi</th>
                                <th scope="col" class="text-end">Tegevused</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($speciesRows === []): ?>
                                <tr><td colspan="3" class="text-center text-muted py-4">Liike pole veel lisatud.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($speciesRows as $row): ?>
                                <tr>
                                    <td><?= e($row['name']) ?></td>
                                    <td><span class="badge bg-secondary"><?= (int) $row['animal_count'] ?></span></td>
                                    <td class="text-end">
                                        <form method="post" action="species.php" class="d-inline"
                                            onsubmit="return confirm('Kustutada liik? Selle tõud kustutatakse ka.')">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="action" value="delete_species">
                                            <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Kustuta liik" aria-label="Kustuta liik <?= e($row['name']) ?>">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <hr>

                <form method="post" action="species.php" novalidate>
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="add_species">
                    <label for="species_name" class="form-label fw-semibold">Uus liik</label>
                    <input type="text" id="species_name" name="name" maxlength="50" required
                        class="form-control<?= isset($speciesErrors['name']) ? ' is-invalid' : '' ?>"
                        value="<?= e($speciesOld['name']) ?>" placeholder="nt Jänes">
                    <div class="invalid-feedback"><?= e($speciesErrors['name'] ?? '') ?></div>
                    <button type="submit" class="btn btn-success mt-3"><i class="fa-solid fa-plus me-1"></i> Lisa liik</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Tõud -->
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom-0 pt-3">
                <h2 class="h5 mb-0"><i class="fa-solid fa-dog text-success me-2"></i>Tõud</h2>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Liik</th>
                                <th scope="col">Tõug</th>
                                <th scope="col" class="text-end">Tegevused</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($breeds === []): ?>
                                <tr><td colspan="3" class="text-center text-muted py-4">Tõugu pole veel lisatud.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($breeds as $breed): ?>
                                <tr>
                                    <td><?= e($breed['species_name']) ?></td>
                                    <td><?= e($breed['name']) ?></td>
                                    <td class="text-end">
                                        <form method="post" action="species.php" class="d-inline"
                                            onsubmit="return confirm('Kustutada tõug? Selle tõuga loomadel jääb tõug määramata.')">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="action" value="delete_breed">
                                            <input type="hidden" name="id" value="<?= (int) $breed['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Kustuta tõug" aria-label="Kustuta tõug <?= e($breed['name']) ?>">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <hr>

                <form method="post" action="species.php" novalidate>
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="add_breed">
                    <div class="mb-3">
                        <label for="breed_species" class="form-label fw-semibold">Liik</label>
                        <select id="breed_species" name="species_id" required
                            class="form-select<?= isset($breedErrors['species_id']) ? ' is-invalid' : '' ?>">
                            <option value="">Vali liik…</option>
                            <?php foreach ($speciesOptions as $sp): ?>
                                <option value="<?= (int) $sp['id'] ?>" <?= (string) $sp['id'] === (string) $breedOld['species_id'] ? 'selected' : '' ?>>
                                    <?= e($sp['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback"><?= e($breedErrors['species_id'] ?? '') ?></div>
                    </div>
                    <div class="mb-3">
                        <label for="breed_name" class="form-label fw-semibold">Tõu nimi</label>
                        <input type="text" id="breed_name" name="name" maxlength="80" required
                            class="form-control<?= isset($breedErrors['name']) ? ' is-invalid' : '' ?>"
                            value="<?= e($breedOld['name']) ?>" placeholder="nt Labrador">
                        <div class="invalid-feedback"><?= e($breedErrors['name'] ?? '') ?></div>
                    </div>
                    <button type="submit" class="btn btn-success" <?= $speciesOptions === [] ? 'disabled' : '' ?>>
                        <i class="fa-solid fa-plus me-1"></i> Lisa tõug
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
