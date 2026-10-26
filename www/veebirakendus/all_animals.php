<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';

// Filtriparameetrid (GET)
$q = is_string($_GET['q'] ?? null) ? mb_substr(trim($_GET['q']), 0, 80) : '';
$speciesId = filter_input(INPUT_GET, 'species', FILTER_VALIDATE_INT) ?: 0;
$age = is_string($_GET['age'] ?? null) ? $_GET['age'] : '';
$sex = is_string($_GET['sex'] ?? null) ? $_GET['sex'] : '';
if (!in_array($sex, ANIMAL_SEXES, true)) {
    $sex = '';
}
$ageCondition = age_group_sql($age); // null, kui vanusegrupp on tundmatu

// Päringu tingimused; kodus olevaid loomi ei näidata
$where = ["a.status <> 'Kodus'"];
$params = [];
if ($q !== '') {
    $where[] = 'a.name LIKE :q';
    $params['q'] = '%' . addcslashes($q, '%_\\') . '%';
}
if ($speciesId > 0) {
    $where[] = 'a.species_id = :species';
    $params['species'] = $speciesId;
}
if ($ageCondition !== null) {
    $where[] = '(' . $ageCondition[0] . ')';
}
if ($sex !== '') {
    $where[] = 'a.sex = :sex';
    $params['sex'] = $sex;
}
$whereSql = implode(' AND ', $where);

$perPage = (int) $config['per_page_public'];
$countStmt = db()->prepare("SELECT COUNT(*) FROM animals a WHERE $whereSql");
$countStmt->execute($params);
$total = (int) $countStmt->fetchColumn();
$pages = max(1, (int) ceil($total / $perPage));
$page = min(page_number(), $pages);
$offset = ($page - 1) * $perPage;

$stmt = db()->prepare("SELECT a.id, a.name, a.sex, a.birth_date, a.short_description, a.image, a.status,
        s.name AS species_name
    FROM animals a
    JOIN species s ON s.id = a.species_id
    WHERE $whereSql
    ORDER BY a.created_at DESC, a.id DESC
    LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$animals = $stmt->fetchAll();

// Säilitame filtrid lehekülgede linkides
$filters = array_filter([
    'q' => $q,
    'species' => $speciesId > 0 ? $speciesId : '',
    'age' => $ageCondition !== null ? $age : '',
    'sex' => $sex,
], static fn($value) => $value !== '' && $value !== null);
$pageUrl = static fn(int $n): string => 'all_animals.php?' . http_build_query($filters + ['page' => $n]);

$speciesOptions = species_list();

$base = '';
$active = 'animals';
$pageTitle = 'Kõik loomad';
require __DIR__ . '/includes/header.php';
?>
    <!-- Animals page -->
    <main class="container py-5">
        <div class="text-center mb-5">
            <h1 class="fw-bold">Kõik loomad</h1>
            <p class="text-muted">Tutvu meie hoolealustega ja leia endale uus parim sõber.</p>
        </div>

        <!-- Filter -->
        <form class="row g-3 mb-5" method="get" action="all_animals.php" role="search">
            <div class="col-lg-4">
                <label for="search" class="visually-hidden">Otsi nime järgi</label>
                <input type="search" id="search" name="q" class="form-control" value="<?= e($q) ?>" maxlength="80" placeholder="Otsi looma nime järgi...">
            </div>

            <div class="col-lg-2">
                <label for="species" class="visually-hidden">Liik</label>
                <select id="species" name="species" class="form-select">
                    <option value="">Kõik liigid</option>
                    <?php foreach ($speciesOptions as $sp): ?>
                        <option value="<?= (int) $sp['id'] ?>" <?= $speciesId === (int) $sp['id'] ? 'selected' : '' ?>><?= e($sp['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-lg-2">
                <label for="age" class="visually-hidden">Vanus</label>
                <select id="age" name="age" class="form-select">
                    <option value="">Kõik vanused</option>
                    <option value="kutsikas" <?= $age === 'kutsikas' ? 'selected' : '' ?>>Kutsikas / Kassipoeg</option>
                    <option value="noor" <?= $age === 'noor' ? 'selected' : '' ?>>Noor</option>
                    <option value="taiskasvanu" <?= $age === 'taiskasvanu' ? 'selected' : '' ?>>Täiskasvanu</option>
                </select>
            </div>

            <div class="col-lg-2">
                <label for="sex" class="visually-hidden">Sugu</label>
                <select id="sex" name="sex" class="form-select">
                    <option value="">Kõik sood</option>
                    <option value="Isane" <?= $sex === 'Isane' ? 'selected' : '' ?>>Isane</option>
                    <option value="Emane" <?= $sex === 'Emane' ? 'selected' : '' ?>>Emane</option>
                </select>
            </div>

            <div class="col-lg-2 d-grid">
                <button type="submit" class="btn btn-success">Filtreeri</button>
            </div>
        </form>

        <?php if ($animals === []): ?>
            <div class="alert alert-info text-center">Loomi ei leitud.</div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($animals as $animal): ?>
                    <?php $imageUrl = animal_image_url($animal['image'], $base); ?>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card h-100 shadow-sm border-0">
                            <?php if ($imageUrl !== null): ?>
                                <img src="<?= e($imageUrl) ?>" class="card-img-top" alt="<?= e($animal['name']) ?>">
                            <?php else: ?>
                                <div class="card-img-top bg-white d-flex align-items-center justify-content-center text-success" style="height: 200px;">
                                    <i class="fa-solid fa-paw fa-3x" aria-hidden="true"></i>
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <h2 class="card-title h5 fw-bold"><?= e($animal['name']) ?></h2>
                                <p class="small text-muted mb-2"><?= e($animal['species_name']) ?> • <?= e($animal['sex']) ?> • <?= e(age_label($animal['birth_date'])) ?></p>
                                <p class="card-text"><?= e($animal['short_description']) ?></p>
                                <span class="badge <?= e(status_badge_class($animal['status'])) ?> mb-3"><?= e($animal['status']) ?></span>
                                <div><a class="btn btn-outline-success" href="animal.php?id=<?= (int) $animal['id'] ?>">Vaata lähemalt</a></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($pages > 1): ?>
            <!-- Pagination -->
            <nav aria-label="Lehekülgede vahetus" class="mt-5">
                <ul class="pagination justify-content-center flex-wrap">
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <?php if ($page <= 1): ?>
                            <span class="page-link">Eelmine</span>
                        <?php else: ?>
                            <a class="page-link" href="<?= e($pageUrl($page - 1)) ?>">Eelmine</a>
                        <?php endif; ?>
                    </li>
                    <?php for ($n = 1; $n <= $pages; $n++): ?>
                        <li class="page-item <?= $n === $page ? 'active' : '' ?>" <?= $n === $page ? 'aria-current="page"' : '' ?>>
                            <a class="page-link" href="<?= e($pageUrl($n)) ?>"><?= $n ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $page >= $pages ? 'disabled' : '' ?>">
                        <?php if ($page >= $pages): ?>
                            <span class="page-link">Järgmine</span>
                        <?php else: ?>
                            <a class="page-link" href="<?= e($pageUrl($page + 1)) ?>">Järgmine</a>
                        <?php endif; ?>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </main>
    <!-- Animals page end -->
<?php require __DIR__ . '/includes/footer.php'; ?>
