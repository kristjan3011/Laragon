<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';

$values = ['name' => '', 'email' => '', 'topic' => '', 'message' => ''];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $input = static fn(string $key): string => is_string($_POST[$key] ?? null) ? trim($_POST[$key]) : '';
    $values = [
        'name' => $input('name'),
        'email' => $input('email'),
        'topic' => $input('topic'),
        'message' => $input('message'),
    ];

    $nameLength = mb_strlen($values['name']);
    if ($values['name'] === '') {
        $errors['name'] = 'Palun sisesta oma nimi.';
    } elseif ($nameLength < 2 || $nameLength > 80) {
        $errors['name'] = 'Nimi peab olema 2–80 tähemärki pikk.';
    }

    if ($values['email'] === '' || mb_strlen($values['email']) > 120 || filter_var($values['email'], FILTER_VALIDATE_EMAIL) === false) {
        $errors['email'] = 'Palun sisesta korrektne e-posti aadress.';
    }

    if (!in_array($values['topic'], REQUEST_TOPICS, true)) {
        $errors['topic'] = 'Palun vali teema.';
    }

    $messageLength = mb_strlen($values['message']);
    if ($values['message'] === '') {
        $errors['message'] = 'Palun kirjuta sõnum.';
    } elseif ($messageLength < 10 || $messageLength > 2000) {
        $errors['message'] = 'Sõnum peab olema 10–2000 tähemärki pikk.';
    }

    if ($errors === []) {
        $stmt = db()->prepare("INSERT INTO contact_requests (name, email, topic, message, status)
            VALUES (:name, :email, :topic, :message, :status)");
        $stmt->execute([
            'name' => $values['name'],
            'email' => $values['email'],
            'topic' => $values['topic'],
            'message' => $values['message'],
            'status' => 'Uus',
        ]);
        flash_set('Aitäh! Sinu sõnum on vastu võetud.', 'success');
        redirect('contact.php');
    }
} else {
    // Loomapõhine eelvalik: ?animal=ID
    $animalId = filter_input(INPUT_GET, 'animal', FILTER_VALIDATE_INT) ?: 0;
    if ($animalId > 0) {
        $stmt = db()->prepare('SELECT name FROM animals WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $animalId]);
        $animalName = $stmt->fetchColumn();
        if ($animalName !== false) {
            $values['topic'] = 'Looma adopteerimine';
            $values['message'] = 'Tahaksin tutvuda: ' . $animalName;
        }
    }
}

$flash = flash_get();
$topics = REQUEST_TOPICS;

$base = '';
$active = 'contact';
$pageTitle = 'Kontakt';
require __DIR__ . '/includes/header.php';
?>
    <!-- Contact -->
    <main class="container py-5">
        <div class="mb-5">
            <h1 class="fw-bold display-4">Kontakt</h1>
            <p class="fs-5 text-muted">Võta meiega ühendust – vastame hea meelega kõikidele küsimustele.</p>
        </div>

        <?php if ($flash !== null): ?>
            <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : e($flash['type']) ?> mb-4" role="alert"><?= e($flash['message']) ?></div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Contact information -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4">
                        <h2 class="fw-bold mb-4">Meie kontaktandmed</h2>

                        <div class="d-flex mb-4">
                            <i class="fa-solid fa-location-dot text-success fa-2x fa-fw me-3 mt-1" aria-hidden="true"></i>
                            <div>
                                <h3 class="h5 fw-bold mb-1">Aadress</h3>
                                <p class="mb-0">Varjupaiga tee 1<br>Tallinn 12345</p>
                            </div>
                        </div>

                        <div class="d-flex mb-4">
                            <i class="fa-solid fa-phone text-success fa-2x fa-fw me-3 mt-1" aria-hidden="true"></i>
                            <div>
                                <h3 class="h5 fw-bold mb-1">Telefon</h3>
                                <p class="mb-0">+372 5555 1234<br>E–P 10:00–18:00</p>
                            </div>
                        </div>

                        <div class="d-flex mb-4">
                            <i class="fa-regular fa-envelope text-success fa-2x fa-fw me-3 mt-1" aria-hidden="true"></i>
                            <div>
                                <h3 class="h5 fw-bold mb-1">E-post</h3>
                                <p class="mb-0">info@varjupaik.ee</p>
                            </div>
                        </div>

                        <div class="d-flex mb-4">
                            <i class="fa-regular fa-clock text-success fa-2x fa-fw me-3 mt-1" aria-hidden="true"></i>
                            <div>
                                <h3 class="h5 fw-bold mb-1">Lahtiolekuajad</h3>
                                <p class="mb-0">E–P 10:00–18:00<br>L–P kokkuleppel</p>
                            </div>
                        </div>

                        <h3 class="h5 fw-bold mb-3">
                            <i class="fa-solid fa-share-nodes text-success me-2" aria-hidden="true"></i>
                            Jälgi meid
                        </h3>

                        <div class="d-flex gap-3">
                            <a href="#" class="text-decoration-none" aria-label="Facebook"><i class="fa-brands fa-facebook fa-2x text-primary" aria-hidden="true"></i></a>
                            <a href="#" class="text-decoration-none" aria-label="Instagram"><i class="fa-brands fa-instagram fa-2x" style="color:#E1306C;" aria-hidden="true"></i></a>
                            <a href="#" class="text-decoration-none" aria-label="YouTube"><i class="fa-brands fa-youtube fa-2x text-danger" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact form -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h2 class="fw-bold mb-4">Kirjuta meile</h2>

                        <form method="post" action="contact.php">
                            <?= csrf_field() ?>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-semibold">Sinu nimi</label>
                                    <input type="text" id="name" name="name" class="form-control<?= isset($errors['name']) ? ' is-invalid' : '' ?>"
                                        placeholder="Sinu nimi" value="<?= e($values['name']) ?>" minlength="2" maxlength="80" required>
                                    <div class="invalid-feedback"><?= e($errors['name'] ?? 'Palun sisesta oma nimi.') ?></div>
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-semibold">E-post</label>
                                    <input type="email" id="email" name="email" class="form-control<?= isset($errors['email']) ? ' is-invalid' : '' ?>"
                                        placeholder="Sinu e-post" value="<?= e($values['email']) ?>" maxlength="120" required>
                                    <div class="invalid-feedback"><?= e($errors['email'] ?? 'Palun sisesta korrektne e-posti aadress.') ?></div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="topic" class="form-label fw-semibold">Teema</label>
                                <select id="topic" name="topic" class="form-select<?= isset($errors['topic']) ? ' is-invalid' : '' ?>" required>
                                    <option value="" <?= $values['topic'] === '' ? 'selected' : '' ?>>Vali teema</option>
                                    <?php foreach ($topics as $topic): ?>
                                        <option value="<?= e($topic) ?>" <?= $values['topic'] === $topic ? 'selected' : '' ?>><?= e($topic) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback"><?= e($errors['topic'] ?? 'Palun vali teema.') ?></div>
                            </div>

                            <div class="mb-4">
                                <label for="message" class="form-label fw-semibold">Sõnum</label>
                                <textarea id="message" name="message" class="form-control<?= isset($errors['message']) ? ' is-invalid' : '' ?>" rows="5"
                                    placeholder="Kirjuta oma sõnum siia..." minlength="10" maxlength="2000" required><?= e($values['message']) ?></textarea>
                                <div class="invalid-feedback"><?= e($errors['message'] ?? 'Palun kirjuta sõnum.') ?></div>
                            </div>

                            <button type="submit" class="btn btn-success px-4">
                                <i class="fa-regular fa-paper-plane me-2" aria-hidden="true"></i>Saada sõnum
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- Contact end -->
<?php require __DIR__ . '/includes/footer.php'; ?>
