<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';

$adminActive = 'home';
$pageTitle = 'Avaleht';
require __DIR__ . '/../includes/admin_header.php';

$pdo = db();

// Statistika
$stats = $pdo->query("SELECT
        COUNT(*) AS total,
        COALESCE(SUM(status = 'Saadaval'), 0) AS available,
        COALESCE(SUM(status = 'Broneeritud'), 0) AS reserved
    FROM animals")->fetch();

$newMessages = (int) $pdo->query("SELECT COUNT(*) FROM contact_requests WHERE status = 'Uus'")->fetchColumn();

// Viimased 5 looma
$latestAnimals = $pdo->query("SELECT a.name, a.birth_date, a.status, s.name AS species_name
    FROM animals a
    JOIN species s ON s.id = a.species_id
    ORDER BY a.created_at DESC, a.id DESC
    LIMIT 5")->fetchAll();

// Viimased 5 sõnumit
$latestRequests = $pdo->query("SELECT name, topic, status
    FROM contact_requests
    ORDER BY created_at DESC, id DESC
    LIMIT 5")->fetchAll();

$statCards = [
    ['icon' => 'fa-paw', 'value' => (int) $stats['total'], 'label' => 'Looma', 'solid' => true],
    ['icon' => 'fa-house', 'value' => (int) $stats['available'], 'label' => 'Koduta', 'solid' => true],
    ['icon' => 'fa-heart', 'value' => (int) $stats['reserved'], 'label' => 'Broneeritud', 'solid' => false],
    ['icon' => 'fa-envelope', 'value' => $newMessages, 'label' => 'Uusi sõnumeid', 'solid' => false],
];
?>
<h1 class="fw-bold mb-2">Administraatori avaleht</h1>
<p class="text-muted mb-5">Ülevaade varjupaiga tegevusest.</p>

<!-- Statistika -->
<div class="row g-4 mb-5">
    <?php foreach ($statCards as $card): ?>
        <div class="col-6 col-lg-3">
            <div class="card text-center shadow-sm border-0 h-100">
                <div class="card-body">
                    <i class="<?= $card['solid'] ? 'fa-solid' : 'fa-regular' ?> <?= e($card['icon']) ?> fa-3x text-success mb-3" aria-hidden="true"></i>
                    <h2 class="fw-bold"><?= e($card['value']) ?></h2>
                    <p class="mb-0"><?= e($card['label']) ?></p>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-4">
    <!-- Viimati lisatud loomad -->
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="d-flex justify-content-between align-items-center px-3 py-3 border-bottom">
                <h2 class="h5 mb-0">Viimati lisatud loomad</h2>
                <a href="animals.php" class="btn btn-sm btn-outline-secondary">Kõik loomad</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr><th scope="col">Nimi</th><th scope="col">Liik</th><th scope="col">Vanus</th><th scope="col">Staatus</th></tr>
                    </thead>
                    <tbody>
                        <?php if (!$latestAnimals): ?>
                            <tr><td colspan="4" class="text-muted">Loomi pole veel lisatud.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($latestAnimals as $animal): ?>
                            <tr>
                                <td><?= e($animal['name']) ?></td>
                                <td><?= e($animal['species_name']) ?></td>
                                <td><?= e(age_label($animal['birth_date'])) ?></td>
                                <td><span class="badge <?= e(status_badge_class($animal['status'])) ?>"><?= e($animal['status']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Viimased sõnumid -->
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="d-flex justify-content-between align-items-center px-3 py-3 border-bottom">
                <h2 class="h5 mb-0">Viimased sõnumid</h2>
                <a href="requests.php" class="btn btn-sm btn-outline-secondary">Kõik päringud</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr><th scope="col">Nimi</th><th scope="col">Teema</th><th scope="col">Staatus</th></tr>
                    </thead>
                    <tbody>
                        <?php if (!$latestRequests): ?>
                            <tr><td colspan="3" class="text-muted">Sõnumeid pole veel saabunud.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($latestRequests as $req): ?>
                            <tr>
                                <td><?= e($req['name']) ?></td>
                                <td><?= e($req['topic']) ?></td>
                                <td>
                                    <?php if ($req['status'] === 'Uus'): ?>
                                        <span class="badge bg-primary">Uus</span>
                                    <?php else: ?>
                                        <span class="badge bg-success"><?= e($req['status']) ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
