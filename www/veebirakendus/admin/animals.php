<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';

// Kustutatud või asendatud üleslaaditud pildi eemaldamine (ainult assets/uploads kaustast)
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

// Filtrid GET-parameetritest (valideeritud)
$q = is_string($_GET['q'] ?? null) ? trim($_GET['q']) : '';
$q = mb_substr($q, 0, 100);

$speciesFilter = filter_var(is_string($_GET['species'] ?? null) ? $_GET['species'] : null,FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$speciesFilter = $speciesFilter === false ? '' : (string) $speciesFilter;

$statusFilter = is_string($_GET['status'] ?? null) ? $_GET['status'] : '';
if (!in_array($statusFilter, ANIMAL_STATUSES, true)) {
    $statusFilter = '';
}

$ageFilter = is_string($_GET['age'] ?? null) ? $_GET['age'] : '';
if (age_group_sql($ageFilter) === null) {
    $ageFilter = '';
}

$filters = array_filter([
    'q' => $q,
    'species' => $speciesFilter,
    'status' => $statusFilter,
    'age' => $ageFilter,
], fn($v) => $v !== '');

$page = page_number();
$returnQuery = http_build_query(array_merge($filters, ['page' => $page]));

// Kustutamine (POST, Post-Redirect-Get)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    if (($_POST['action'] ?? '') === 'delete') {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($id === false) {
            flash_set('Vigane looma number.', 'error');
        } else {
            $stmt = db()->prepare('SELECT image FROM animals WHERE id = :id');
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch();
            if ($row === false) {
                flash_set('Looma ei leitud.', 'error');
            } else {
                $del = db()->prepare('DELETE FROM animals WHERE id = :id');
                $del->execute([':id' => $id]);
                remove_uploaded_image($row['image']);
                flash_set('Loom kustutatud.', 'success');
            }
        }
    }

    redirect('animals.php' . ($returnQuery !== '' ? '?' . $returnQuery : ''));
}

// Päringu ehitamine
$where = [];
$params = [];

if ($q !== '') {
    $like = '%' . addcslashes($q, '%_\\') . '%';
    $where[] = '(a.name LIKE :q1 OR s.name LIKE :q2 OR a.short_description LIKE :q3)';
    $params[':q1'] = $like;
    $params[':q2'] = $like;
    $params[':q3'] = $like;
}
if ($speciesFilter !== '') {
    $where[] = 'a.species_id = :species';
    $params[':species'] = (int) $speciesFilter;
}
if ($statusFilter !== '') {
    $where[] = 'a.status = :status';
    $params[':status'] = $statusFilter;
}
if ($ageFilter !== '') {
    $ageSql = age_group_sql($ageFilter);
    $where[] = '(' . $ageSql[0] . ')';
}

$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$pdo = db();
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM animals a JOIN species s ON s.id = a.species_id $whereSql");
$countStmt->execute($params);
$total = (int) $countStmt->fetchColumn();

$perPage = (int) $config['per_page_admin'];
$pages = max(1, (int) ceil($total / $perPage));
$page = min($page, $pages);
$offset = ($page - 1) * $perPage;

$listStmt = $pdo->prepare("SELECT a.id, a.name, a.sex, a.birth_date, a.status, a.image, a.created_at, s.name AS species_name
    FROM animals a
    JOIN species s ON s.id = a.species_id
    $whereSql
    ORDER BY a.created_at DESC, a.id DESC
    LIMIT {$perPage} OFFSET {$offset}");
$listStmt->execute($params);
$animals = $listStmt->fetchAll();

$species = species_list();
$adminActive = 'animals';
$pageTitle = 'Loomad';
require __DIR__ . '/../includes/admin_header.php';

$from = $total === 0 ? 0 : $offset + 1;
$to = min($offset + $perPage, $total);

$ageOptions = [
    'kutsikas' => 'Kutsikas / Kassipoeg',
    'noor' => 'Noor',
    'taiskasvanu' => 'Täiskasvanu',
];

// Lehekülje link säilitab filtrid
$pageUrl = fn(int $n) => 'animals.php?' . http_build_query(array_merge($filters, ['page' => $n]));
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="fw-bold mb-1">Loomad</h1>
        <p class="text-muted mb-0">Halda varjupaiga loomi: vaata, lisa, muuda või kustuta.</p>
    </div>
    <a href="animal_form.php" class="btn btn-success"><i class="fa-solid fa-plus me-2"></i>Lisa uus loom</a>
</div>

<!-- Otsing ja filter -->
<form method="get" action="animals.php" class="card shadow-sm border-0 p-3 mb-4" role="search">
    <div class="row g-3">
        <div class="col-12 col-lg-4">
            <label for="q" class="form-label fw-semibold">Otsi looma</label>
            <div class="input-group">
                <input type="search" id="q" name="q" class="form-control" value="<?= e($q) ?>" placeholder="Otsi nime, liigi või kirjelduse järgi...">
                <button type="submit" class="btn btn-outline-secondary" aria-label="Otsi"><i class="fa-solid fa-magnifying-glass"></i></button>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <label for="species" class="form-label fw-semibold">Liik</label>
            <select id="species" name="species" class="form-select">
                <option value="">Kõik liigid</option>
                <?php foreach ($species as $sp): ?>
                    <option value="<?= e($sp['id']) ?>" <?= (string) $sp['id'] === $speciesFilter ? 'selected' : '' ?>><?= e($sp['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-6 col-lg-3">
            <label for="status" class="form-label fw-semibold">Staatus</label>
            <select id="status" name="status" class="form-select">
                <option value="">Kõik staatused</option>
                <?php foreach (ANIMAL_STATUSES as $st): ?>
                    <option value="<?= e($st) ?>" <?= $st === $statusFilter ? 'selected' : '' ?>><?= e($st) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-6 col-lg-3">
            <label for="age" class="form-label fw-semibold">Vanus</label>
            <select id="age" name="age" class="form-select">
                <option value="">Kõik</option>
                <?php foreach ($ageOptions as $val => $label): ?>
                    <option value="<?= e($val) ?>" <?= $val === $ageFilter ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="d-flex flex-wrap justify-content-end gap-2 mt-3">
        <a href="animals.php" class="btn btn-outline-secondary"><i class="fa-solid fa-rotate-left me-2"></i>Tühista filtrid</a>
        <button type="submit" class="btn btn-success"><i class="fa-solid fa-filter me-2"></i>Rakenda filtrid</button>
    </div>
</form>

<!-- Loomade tabel -->
<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table align-middle table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th scope="col">Pilt</th>
                    <th scope="col">Nimi</th>
                    <th scope="col">Liik</th>
                    <th scope="col">Sugu</th>
                    <th scope="col">Vanus</th>
                    <th scope="col">Staatus</th>
                    <th scope="col">Lisatud</th>
                    <th scope="col" class="text-end">Tegevused</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$animals): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">Filtritele vastavaid loomi ei leitud.</td></tr>
                <?php endif; ?>
                <?php foreach ($animals as $a): ?>
                    <?php $imageUrl = animal_image_url($a['image'], '../'); ?>
                    <tr>
                        <td>
                            <?php if ($imageUrl !== null): ?>
                                <img src="<?= e($imageUrl) ?>" alt="<?= e($a['name']) ?>" width="60" height="60" class="rounded" style="object-fit: cover;">
                            <?php else: ?>
                                <div class="rounded bg-light text-muted d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                    <i class="fa-solid fa-paw" aria-hidden="true"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="fw-bold"><?= e($a['name']) ?></td>
                        <td><?= e($a['species_name']) ?></td>
                        <td><?= e($a['sex']) ?></td>
                        <td><?= e(age_label($a['birth_date'])) ?></td>
                        <td><span class="badge <?= e(status_badge_class($a['status'])) ?>"><?= e($a['status']) ?></span></td>
                        <td><?= e((new DateTimeImmutable($a['created_at']))->format('d.m.Y')) ?></td>
                        <td class="text-end text-nowrap">
                            <a href="../animal.php?id=<?= e($a['id']) ?>" class="btn btn-sm btn-outline-secondary" aria-label="Vaata: <?= e($a['name']) ?>" title="Vaata"><i class="fa-solid fa-eye"></i></a>
                            <a href="animal_form.php?id=<?= e($a['id']) ?>" class="btn btn-sm btn-outline-primary" aria-label="Muuda: <?= e($a['name']) ?>" title="Muuda"><i class="fa-solid fa-pen"></i></a>
                            <form method="post" action="animals.php<?= $returnQuery !== '' ? '?' . e($returnQuery) : '' ?>" class="d-inline" onsubmit="return confirm('Kas kustutada see loom?');">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= e($a['id']) ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger" aria-label="Kustuta: <?= e($a['name']) ?>" title="Kustuta"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 p-3 border-top">
        <p class="small text-muted mb-0">
            <?php if ($total === 0): ?>
                Kokku 0 loomi.
            <?php else: ?>
                Kuvatakse <?= e($from) ?>–<?= e($to) ?> kokku <?= e($total) ?> loomi.
            <?php endif; ?>
        </p>
        <?php if ($pages > 1): ?>
            <nav aria-label="Lehekülgede vahetus">
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= e($pageUrl(max(1, $page - 1))) ?>" aria-label="Eelmine leht">&laquo;</a>
                    </li>
                    <?php for ($n = 1; $n <= $pages; $n++): ?>
                        <li class="page-item <?= $n === $page ? 'active' : '' ?>" <?= $n === $page ? 'aria-current="page"' : '' ?>>
                            <a class="page-link" href="<?= e($pageUrl($n)) ?>"><?= e($n) ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $page >= $pages ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= e($pageUrl(min($pages, $page + 1))) ?>" aria-label="Järgmine leht">&raquo;</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
