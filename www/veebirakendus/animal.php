<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;

$animal = null;
if ($id > 0) {
    $stmt = db()->prepare("SELECT a.id, a.name, a.sex, a.birth_date, a.size, a.color, a.location,
            a.neutered, a.chipped, a.short_description, a.description, a.status, a.image,
            s.name AS species_name, b.name AS breed_name
        FROM animals a
        JOIN species s ON s.id = a.species_id
        LEFT JOIN breeds b ON b.id = a.breed_id
        WHERE a.id = :id
        LIMIT 1");
    $stmt->execute(['id' => $id]);
    $animal = $stmt->fetch() ?: null;
}

$base = '';
$active = 'animals';

if ($animal === null) {
    http_response_code(404);
    $pageTitle = 'Looma ei leitud';
    require __DIR__ . '/includes/header.php';
    ?>
    <main class="container py-5 text-center">
        <h1 class="fw-bold mb-3">Looma ei leitud</h1>
        <p class="text-muted mb-4">Otsitud looma ei ole olemas või on link vale.</p>
        <a class="btn btn-outline-secondary px-4" href="all_animals.php"><i class="fa-solid fa-arrow-left me-2"></i>Tagasi nimekirja</a>
    </main>
    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = $animal['name'];
$imageUrl = animal_image_url($animal['image'], $base);
$birthDate = (new DateTimeImmutable($animal['birth_date']))->format('d.m.Y');
$yesNo = static fn(int|string $flag): string => ((int) $flag === 1) ? 'Jah' : 'Ei';

require __DIR__ . '/includes/header.php';
?>
    <!-- Animal details -->
    <main class="container px-3 py-4">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Avaleht</a></li>
                <li class="breadcrumb-item"><a href="all_animals.php" class="text-decoration-none">Loomad</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= e($animal['name']) ?></li>
            </ol>
        </nav>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-3 p-lg-4">
                <div class="row g-4 align-items-center">
                    <div class="col-lg-6">
                        <div class="ratio ratio-1x1">
                            <?php if ($imageUrl !== null): ?>
                                <img src="<?= e($imageUrl) ?>" class="rounded object-fit-cover" alt="<?= e($animal['name']) ?>">
                            <?php else: ?>
                                <div class="rounded bg-light d-flex align-items-center justify-content-center text-success">
                                    <i class="fa-solid fa-paw fa-5x" aria-hidden="true"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <h1 class="fw-bold"><?= e($animal['name']) ?></h1>
                        <p class="mb-3"><span class="badge <?= e(status_badge_class($animal['status'])) ?>"><?= e($animal['status']) ?></span></p>

                        <ul class="list-inline text-secondary mb-3">
                            <li class="list-inline-item me-3"><i class="fa-solid fa-paw text-success me-2"></i><?= e($animal['species_name']) ?></li>
                            <li class="list-inline-item me-3"><i class="fa-solid <?= $animal['sex'] === 'Isane' ? 'fa-mars' : 'fa-venus' ?> text-success me-2"></i><?= e($animal['sex']) ?></li>
                            <li class="list-inline-item"><i class="fa-regular fa-calendar text-success me-2"></i><?= e(age_label($animal['birth_date'])) ?></li>
                        </ul>

                        <?php if (!empty($animal['short_description'])): ?>
                            <p class="fw-semibold"><?= e($animal['short_description']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($animal['description'])): ?>
                            <p><?= nl2br(e($animal['description'])) ?></p>
                        <?php endif; ?>

                        <div class="table-responsive">
                            <table class="table table-borderless align-middle mb-4">
                                <tbody>
                                    <tr class="border-bottom">
                                        <th scope="row" class="w-50">Tõug</th>
                                        <td><?= e($animal['breed_name'] ?? 'Segu') ?></td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <th scope="row">Suurus</th>
                                        <td><?= e($animal['size'] ?: '—') ?></td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <th scope="row">Värvus</th>
                                        <td><?= e($animal['color'] ?: '—') ?></td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <th scope="row">Sünniaeg</th>
                                        <td><?= e($birthDate) ?></td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <th scope="row">Asukoht</th>
                                        <td><?= e($animal['location'] ?: '—') ?></td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <th scope="row">Kastreeritud</th>
                                        <td><?= e($yesNo($animal['neutered'])) ?></td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <th scope="row">Kiibistatud</th>
                                        <td><?= e($yesNo($animal['chipped'])) ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <a href="contact.php?animal=<?= (int) $animal['id'] ?>" class="btn btn-success px-4"><i class="fa-solid fa-heart me-2"></i>Soovin <?= e($animal['name']) ?>ga tutvuda</a>
                            <a href="all_animals.php" class="btn btn-outline-secondary px-4"><i class="fa-solid fa-arrow-left me-2"></i>Tagasi nimekirja</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- Animal details end -->
<?php require __DIR__ . '/includes/footer.php'; ?>
