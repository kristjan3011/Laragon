<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';

start_session();
if (current_user() !== null) {
    redirect('admin/index.php');
}

$email = '';
$loginError = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $email = is_string($_POST['email'] ?? null) ? trim($_POST['email']) : '';
    $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';

    // Lihtne piirang: kuni 5 ebaõnnestunud katset 5 minuti jooksul
    $maxAttempts = 5;
    $window = 300;
    $now = time();
    $attempts = array_values(array_filter(
        is_array($_SESSION['login_attempts'] ?? null) ? $_SESSION['login_attempts'] : [],
        static fn($t) => is_int($t) && $t > $now - $window
    ));
    $throttled = count($attempts) >= $maxAttempts;

    $user = null;
    if (!$throttled) {
        // Fikseeritud bcrypt räsi, et tundmatu e-posti korral kuluks sama aeg
        $dummyHash = '$2y$10$oLYOncJPKx9KG19tqV6tbeulOcew/XNaYCGZmqaItKfypN00IHeOa';
        $row = false;
        if ($email !== '' && $password !== '' && mb_strlen($email) <= 120 && filter_var($email, FILTER_VALIDATE_EMAIL) !== false) {
            $stmt = db()->prepare('SELECT id, name, email, password_hash, role FROM users WHERE email = :email LIMIT 1');
            $stmt->execute(['email' => $email]);
            $row = $stmt->fetch();
        }
        if (strlen($password) <= 72) {
            $ok = password_verify($password, $row ? $row['password_hash'] : $dummyHash);
            if ($row && $ok) {
                $user = $row;
            }
        }
    }

    if ($user !== null) {
        unset($_SESSION['login_attempts']);
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];
        redirect('admin/index.php');
    }

    if ($throttled) {
        $loginError = 'Liiga palju katseid. Proovi mõne minuti pärast.';
    } else {
        $attempts[] = $now;
        $loginError = 'Vale e-post või parool.';
    }
    $_SESSION['login_attempts'] = $attempts;
}

$flash = $loginError === null ? flash_get() : null;

$base = '';
$active = '';
$pageTitle = 'Logi sisse';
require __DIR__ . '/includes/header.php';
?>
    <!-- Login -->
    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <img src="assets/images/logo_icon.svg" alt="" width="80">
                            <h1 class="fw-bold mt-3">Logi sisse</h1>
                            <p class="text-muted">Logi sisse, et hallata kontaktpäringuid<br>ja loomade andmeid.</p>
                        </div>

                        <?php if ($flash !== null): ?>
                            <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : e($flash['type']) ?>" role="alert"><?= e($flash['message']) ?></div>
                        <?php endif; ?>
                        <?php if ($loginError !== null): ?>
                            <div class="alert alert-danger" role="alert"><?= e($loginError) ?></div>
                        <?php endif; ?>

                        <form method="post" action="login.php">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">E-post</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-regular fa-envelope" aria-hidden="true"></i></span>
                                    <input type="email" id="email" name="email" class="form-control" placeholder="Sinu e-post"
                                        value="<?= e($email) ?>" maxlength="120" autocomplete="username" required>
                                    <div class="invalid-feedback">Palun sisesta korrektne e-posti aadress.</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">Parool</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-lock" aria-hidden="true"></i></span>
                                    <input type="password" id="password" name="password" class="form-control" placeholder="Sinu parool"
                                        autocomplete="current-password" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword" aria-label="Näita parooli">
                                        <i class="fa-regular fa-eye" aria-hidden="true"></i>
                                    </button>
                                    <div class="invalid-feedback">Palun sisesta parool.</div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end align-items-center mb-4">
                                <a href="#" class="text-success text-decoration-none">Unustasid parooli?</a>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-success">Logi sisse</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- Login end -->
    <script>
        // Parooli näitamine/peitmine
        (function () {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.getElementById('togglePassword');
            if (!passwordInput || !toggleButton) return;
            toggleButton.addEventListener('click', () => {
                const show = passwordInput.type === 'password';
                passwordInput.type = show ? 'text' : 'password';
                toggleButton.setAttribute('aria-label', show ? 'Peida parool' : 'Näita parooli');
                toggleButton.querySelector('i').className = show ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
            });
        })();
    </script>
<?php require __DIR__ . '/includes/footer.php'; ?>
