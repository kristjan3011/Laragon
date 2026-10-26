<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';

start_session();
$me = require_login('../login.php');

// Kasutajate haldus on ainult administraatorile
if (($me['role'] ?? '') !== 'admin') {
    flash_set('Sul pole selleks õigust.', 'error');
    redirect('index.php');
}

$pdo = db();
$myId = (int) ($me['id'] ?? 0);

// POST-käsitlus (PRG): enne päise väljastamist
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = (string) ($_POST['action'] ?? '');
    $idRaw = (string) ($_POST['id'] ?? '');
    $id = $idRaw === '' ? null : filter_var($idRaw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

    if ($action === 'save') {
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $role = (string) ($_POST['role'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $errors = [];

        if ($idRaw !== '' && $id === false) {
            flash_set('Vigane kasutaja.', 'error');
            redirect('users.php');
        }

        $existing = null;
        if ($id !== null) {
            $stmt = $pdo->prepare('SELECT id, role FROM users WHERE id = ?');
            $stmt->execute([$id]);
            $existing = $stmt->fetch() ?: null;
            if ($existing === null) {
                flash_set('Kasutajat ei leitud.', 'error');
                redirect('users.php');
            }
        }

        $nameLen = mb_strlen($name);
        if ($nameLen < 2 || $nameLen > 80) {
            $errors['name'] = 'Nimi peab olema 2–80 tähemärki.';
        }
        if ($email === '' || mb_strlen($email) > 120 || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors['email'] = 'Sisesta korrektne e-posti aadress (kuni 120 tähemärki).';
        }
        if (!in_array($role, USER_ROLES, true)) {
            $errors['role'] = 'Vali roll.';
        }
        $passwordLen = mb_strlen($password);
        if ($existing === null) {
            if ($passwordLen < 8) {
                $errors['password'] = 'Parool peab olema vähemalt 8 tähemärki.';
            }
        } elseif ($password !== '' && $passwordLen < 8) {
            $errors['password'] = 'Uus parool peab olema vähemalt 8 tähemärki (või jäta tühjaks).';
        }
        // password_hash (bcrypt) kasutab ainult esimesi 72 baiti
        if (!isset($errors['password']) && strlen($password) > 72) {
            $errors['password'] = 'Parool võib olla kuni 72 baiti pikk.';
        }

        if ($errors === []) {
            $successMsg = '';
            try {
                $pdo->beginTransaction();
                if ($existing === null) {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $pdo->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)')
                        ->execute([$name, $email, $hash, $role]);
                    $successMsg = 'Kasutaja „' . $name . '“ lisati.';
                } else {
                    // Lukusta administraatorite read ja muudetav kasutaja (race-safe valvur)
                    $stmt = $pdo->prepare("SELECT id, role FROM users WHERE role = 'admin' OR id = ? ORDER BY id FOR UPDATE");
                    $stmt->execute([$id]);
                    $adminCount = 0;
                    $currentRole = null;
                    foreach ($stmt->fetchAll() as $row) {
                        if ($row['role'] === 'admin') {
                            $adminCount++;
                        }
                        if ((int) $row['id'] === $id) {
                            $currentRole = $row['role'];
                        }
                    }
                    if ($currentRole === null) {
                        $pdo->rollBack();
                        flash_set('Kasutajat ei leitud.', 'error');
                        redirect('users.php');
                    }
                    // Viimase administraatori degradeerimise keeld (ka iseenda puhul)
                    if ($currentRole === 'admin' && $role !== 'admin' && $adminCount <= 1) {
                        $pdo->rollBack();
                        $errors['role'] = 'Viimast administraatorit ei saa muuta toimetajaks.';
                        throw new RuntimeException('last-admin');
                    }
                    if ($password !== '') {
                        $hash = password_hash($password, PASSWORD_DEFAULT);
                        $pdo->prepare('UPDATE users SET name = ?, email = ?, role = ?, password_hash = ? WHERE id = ?')
                            ->execute([$name, $email, $role, $hash, $id]);
                    } else {
                        $pdo->prepare('UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?')
                            ->execute([$name, $email, $role, $id]);
                    }
                    $successMsg = 'Kasutaja andmed salvestati.';
                }
                $pdo->commit();
                if ($existing !== null && $id === $myId) {
                    start_session();
                    $_SESSION['user']['name'] = $name;
                    $_SESSION['user']['email'] = $email;
                    $_SESSION['user']['role'] = $role;
                }
                flash_set($successMsg);
            } catch (RuntimeException $ex) {
                if ($ex->getMessage() !== 'last-admin') {
                    throw $ex;
                }
            } catch (PDOException $ex) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                if ($ex->getCode() !== '23000') {
                    throw $ex;
                }
                $errors['email'] = 'See e-posti aadress on juba kasutusel.';
            }
        }

        if ($errors !== []) {
            // Parooli ei jäeta vormi väärtustesse
            $_SESSION['form_state'] = [
                'id' => $id,
                'errors' => $errors,
                'old' => ['name' => $name, 'email' => $email, 'role' => $role],
            ];
            flash_set('Salvestamine ebaõnnestus. Paranda vead.', 'error');
            redirect($id !== null ? 'users.php?id=' . $id : 'users.php');
        }
        redirect('users.php');
    }

    if ($action === 'delete') {
        if ($id === null || $id === false) {
            flash_set('Vigane kasutaja.', 'error');
            redirect('users.php');
        }
        if ($id === $myId) {
            flash_set('Iseennast ei saa kustutada.', 'error');
            redirect('users.php');
        }
        try {
            $pdo->beginTransaction();
            // Lukusta administraatorite read ja kustutatav kasutaja (race-safe valvur)
            $stmt = $pdo->prepare("SELECT id, role FROM users WHERE role = 'admin' OR id = ? ORDER BY id FOR UPDATE");
            $stmt->execute([$id]);
            $adminCount = 0;
            $targetRole = null;
            foreach ($stmt->fetchAll() as $row) {
                if ($row['role'] === 'admin') {
                    $adminCount++;
                }
                if ((int) $row['id'] === $id) {
                    $targetRole = $row['role'];
                }
            }
            if ($targetRole === null) {
                $pdo->rollBack();
                flash_set('Kasutajat ei leitud.', 'error');
                redirect('users.php');
            }
            if ($targetRole === 'admin' && $adminCount <= 1) {
                $pdo->rollBack();
                flash_set('Viimast administraatorit ei saa kustutada.', 'error');
                redirect('users.php');
            }
            $pdo->prepare('DELETE FROM users WHERE id = ?')->execute([$id]);
            $pdo->commit();
        } catch (Throwable $ex) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $ex;
        }
        flash_set('Kasutaja kustutati.');
        redirect('users.php');
    }

    flash_set('Tundmatu tegevus.', 'error');
    redirect('users.php');
}

// Muutmise režiim: ?id=N
$editId = null;
$editUser = null;
if (isset($_GET['id']) && $_GET['id'] !== '') {
    $editId = filter_var($_GET['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($editId === false) {
        flash_set('Vigane kasutaja.', 'error');
        redirect('users.php');
    }
    $stmt = $pdo->prepare('SELECT id, name, email, role FROM users WHERE id = ?');
    $stmt->execute([$editId]);
    $editUser = $stmt->fetch() ?: null;
    if ($editUser === null) {
        flash_set('Kasutajat ei leitud.', 'error');
        redirect('users.php');
    }
}

// Vormi olek eelmisest POST-ist
$formState = $_SESSION['form_state'] ?? null;
unset($_SESSION['form_state']);
$errors = [];
$values = ['name' => '', 'email' => '', 'role' => 'toimetaja'];
if ($editUser !== null) {
    $values = ['name' => $editUser['name'], 'email' => $editUser['email'], 'role' => $editUser['role']];
}
if (is_array($formState) && (($formState['id'] ?? null) === $editId)) {
    $errors = $formState['errors'];
    $values = array_merge($values, $formState['old']);
}

$adminActive = 'users';
$pageTitle = 'Kasutajad';
require __DIR__ . '/../includes/admin_header.php';

$users = $pdo->query('SELECT id, name, email, role, created_at FROM users ORDER BY name')->fetchAll();
$isEditing = $editUser !== null;
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <h1 class="h3 mb-0"><i class="fa-solid fa-users text-success me-2"></i>Kasutajad</h1>
    <?php if ($isEditing): ?>
        <a href="users.php" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-plus me-1"></i> Uus kasutaja</a>
    <?php endif; ?>
</div>

<div class="row g-4">
    <!-- Kasutajate tabel -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Nimi</th>
                            <th scope="col">E-post</th>
                            <th scope="col">Roll</th>
                            <th scope="col">Lisatud</th>
                            <th scope="col" class="text-end">Tegevused</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($users === []): ?>
                            <tr><td colspan="5" class="text-center text-muted py-4">Kasutajaid pole.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($users as $u): ?>
                            <?php $isSelf = (int) $u['id'] === $myId; ?>
                            <tr<?= (int) $u['id'] === $editId ? ' class="table-success"' : '' ?>>
                                <td>
                                    <?= e($u['name']) ?>
                                    <?php if ($isSelf): ?><span class="badge bg-light text-dark border ms-1">sina</span><?php endif; ?>
                                </td>
                                <td><?= e($u['email']) ?></td>
                                <td>
                                    <span class="badge <?= $u['role'] === 'admin' ? 'bg-danger' : 'bg-secondary' ?>"><?= e($u['role'] === 'admin' ? 'Administraator' : 'Toimetaja') ?></span>
                                </td>
                                <td><?= e(date('d.m.Y', strtotime((string) $u['created_at']))) ?></td>
                                <td class="text-end text-nowrap">
                                    <a href="users.php?id=<?= (int) $u['id'] ?>" class="btn btn-sm btn-outline-primary" title="Muuda" aria-label="Muuda kasutajat <?= e($u['name']) ?>">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form method="post" action="users.php" class="d-inline"
                                        onsubmit="return confirm('Kustutada kasutaja? Seda ei saa tagasi võtta.')">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Kustuta" aria-label="Kustuta kasutaja <?= e($u['name']) ?>"
                                            <?= $isSelf ? 'disabled' : '' ?>>
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Lisamise / muutmise vorm -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 pt-3">
                <h2 class="h5 mb-0">
                    <i class="fa-solid <?= $isEditing ? 'fa-user-pen' : 'fa-user-plus' ?> text-success me-2"></i>
                    <?= $isEditing ? 'Muuda kasutajat' : 'Lisa kasutaja' ?>
                </h2>
            </div>
            <div class="card-body">
                <form method="post" action="users.php" novalidate>
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="save">
                    <?php if ($isEditing): ?>
                        <input type="hidden" name="id" value="<?= (int) $editUser['id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="user_name" class="form-label fw-semibold">Nimi</label>
                        <input type="text" id="user_name" name="name" maxlength="80" required
                            class="form-control<?= isset($errors['name']) ? ' is-invalid' : '' ?>"
                            value="<?= e($values['name']) ?>">
                        <div class="invalid-feedback"><?= e($errors['name'] ?? '') ?></div>
                    </div>

                    <div class="mb-3">
                        <label for="user_email" class="form-label fw-semibold">E-post</label>
                        <input type="email" id="user_email" name="email" maxlength="120" required
                            class="form-control<?= isset($errors['email']) ? ' is-invalid' : '' ?>"
                            value="<?= e($values['email']) ?>">
                        <div class="invalid-feedback"><?= e($errors['email'] ?? '') ?></div>
                    </div>

                    <div class="mb-3">
                        <label for="user_role" class="form-label fw-semibold">Roll</label>
                        <select id="user_role" name="role" class="form-select<?= isset($errors['role']) ? ' is-invalid' : '' ?>">
                            <?php foreach (USER_ROLES as $r): ?>
                                <option value="<?= e($r) ?>" <?= $values['role'] === $r ? 'selected' : '' ?>><?= e($r === 'admin' ? 'Administraator' : 'Toimetaja') ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback"><?= e($errors['role'] ?? '') ?></div>
                    </div>

                    <div class="mb-3">
                        <label for="user_password" class="form-label fw-semibold">Parool</label>
                        <input type="password" id="user_password" name="password" minlength="8" maxlength="72" autocomplete="new-password"
                            class="form-control<?= isset($errors['password']) ? ' is-invalid' : '' ?>"
                            <?= $isEditing ? '' : 'required' ?>>
                        <div class="invalid-feedback"><?= e($errors['password'] ?? '') ?></div>
                        <div class="form-text">
                            <?= $isEditing ? 'Jäta tühjaks, kui parooli ei muudeta. 8–72 tähemärki.' : '8–72 tähemärki.' ?>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success">
                        <i class="fa-solid fa-floppy-disk me-1"></i> <?= $isEditing ? 'Salvesta muudatused' : 'Lisa kasutaja' ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
