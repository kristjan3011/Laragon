<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';

// Kuus viimati lisatud looma, kes ei ole veel kodus
$stmt = db()->prepare("SELECT a.id, a.name, a.sex, a.birth_date, a.short_description, a.image, a.status,
        s.name AS species_name
    FROM animals a
    JOIN species s ON s.id = a.species_id
    WHERE a.status <> :kodus
    ORDER BY a.created_at DESC, a.id DESC
    LIMIT 6");
$stmt->execute(['kodus' => 'Kodus']);
$animals = $stmt->fetchAll();

$base = '';
$active = 'home';
$pageTitle = 'Avaleht';
require __DIR__ . '/includes/header.php';
?>
    <div class="container-fluid">
        <!-- Hero section -->
        <div class="container">
            <div class="hero position-relative">
                <div class="hero-text position-absolute top-50 translate-middle-y">
                    <div class="row p-4">
                        <div class="col-7 p-4">
                            <h1 class="display-4 fw-bold">Iga loom väärib armastust ja kodu</h1>
                            <p class="fs-5 pt-4 pb-4">Meie varjupaigas ootavad paljud toredad loomad oma päriskodu. Tule ja leia endale uus parim sõber!</p>
                            <div class="d-flex gap-2">
                                <a class="btn btn-success p-2 w-100" href="all_animals.php"><i class="fa-solid fa-paw"></i> Vaata loomi</a>
                                <a class="btn btn-outline-success bg-white p-2 w-100" href="#">Kuidas aidata?</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mission section -->
        <div class="container py-5">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <div class="row align-items-center">
                        <div class="col-3 text-center">
                            <img src="assets/images/logo_icon.svg" alt="" class="img-fluid" width="110">
                        </div>
                        <div class="col-9">
                            <h2 class="fw-bold mb-3">Meie missioon</h2>
                            <p class="mb-4">
                                Oleme mittetulundusühing, kes aitab koduta ja hätta sattunud loomi.
                                Meie eesmärk on leida igale loomale soe ja armastav kodu ning
                                tõsta inimeste teadlikkust loomade heaolust.
                            </p>
                            <a class="btn btn-outline-success px-5 py-2" href="#">Loe lähemalt meist</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 border-start ps-5">
                    <div class="row mb-4 align-items-start">
                        <div class="col-2 text-center">
                            <i class="fa-regular fa-heart fa-2xl text-success" aria-hidden="true"></i>
                        </div>
                        <div class="col-10">
                            <p class="fw-bold text-success mb-1">Aitame loomi</p>
                            <p class="mb-0">Hoolitseme, ravime ja pakume turvalist peavarju koduta loomadele.</p>
                        </div>
                    </div>

                    <div class="row mb-4 align-items-start">
                        <div class="col-2 text-center">
                            <i class="fa-solid fa-user-group fa-2xl text-success" aria-hidden="true"></i>
                        </div>
                        <div class="col-10">
                            <p class="fw-bold text-success mb-1">Leiame kodud</p>
                            <p class="mb-0">Otsime igale loomale armastavat kodu ja hoolitseme sobiva pere leidmise eest.</p>
                        </div>
                    </div>

                    <div class="row align-items-start">
                        <div class="col-2 text-center">
                            <i class="fa-solid fa-paw fa-2xl text-success" aria-hidden="true"></i>
                        </div>
                        <div class="col-10">
                            <p class="fw-bold text-success mb-1">Harime ja teavitame</p>
                            <p class="mb-0">Korraldame üritusi ja jagame infot loomade heaolu teemadel.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Animals section -->
        <div class="container py-5 bg-light">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Kodu ootavad loomad</h2>
                <p class="text-muted">Tutvu meie armsate hoolealustega ja leia endale uus sõber.</p>
            </div>

            <?php if ($animals === []): ?>
                <div class="alert alert-info text-center">Hetkel ootavaid loomi ei ole.</div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($animals as $animal): ?>
                        <?php $imageUrl = animal_image_url($animal['image'], $base); ?>
                        <div class="col-sm-6 col-lg-4">
                            <div class="card h-100 shadow-sm border-0">
                                <?php if ($imageUrl !== null): ?>
                                    <img src="<?= e($imageUrl) ?>" class="card-img-top" alt="<?= e($animal['name']) ?>">
                                <?php else: ?>
                                    <div class="card-img-top bg-white d-flex align-items-center justify-content-center text-success" style="height: 240px;">
                                        <i class="fa-solid fa-paw fa-4x" aria-hidden="true"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="card-body">
                                    <h3 class="card-title h5 fw-bold"><?= e($animal['name']) ?></h3>
                                    <p class="small text-muted mb-2"><?= e($animal['species_name']) ?> • <?= e($animal['sex']) ?> • <?= e(age_label($animal['birth_date'])) ?></p>
                                    <p class="card-text text-muted"><?= e($animal['short_description']) ?></p>
                                    <span class="badge <?= e(status_badge_class($animal['status'])) ?> mb-3"><?= e($animal['status']) ?></span>
                                    <div>
                                        <a class="btn btn-outline-success" href="animal.php?id=<?= (int) $animal['id'] ?>">Vaata lähemalt</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="text-center mt-5">
                <a class="btn btn-success px-5 py-2" href="all_animals.php">Vaata kõiki loomi</a>
            </div>
        </div>
        <!-- Animals section end -->
    </div>
<?php require __DIR__ . '/includes/footer.php'; ?>
