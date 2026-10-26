<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';

start_session();
require_login('../login.php');

$pdo = db();

// Filtri ja lehe väärtused (valideeritud)
$statusFilter = in_array($_GET['status'] ?? '', REQUEST_STATUSES, true) ? (string) $_GET['status'] : '';

// POST-käsitlus (PRG): enne päise väljastamist
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = (string) ($_POST['action'] ?? '');
    $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $postStatus = in_array($_POST['status'] ?? '', REQUEST_STATUSES, true) ? (string) $_POST['status'] : '';
    $postPage = max(1, (int) ($_POST['page'] ?? 1));

    $query = http_build_query(array_filter([
        'status' => $postStatus,
        'page' => $postPage > 1 ? $postPage : null,
    ], static fn($v) => $v !== '' && $v !== null));
    $back = 'requests.php' . ($query !== '' ? '?' . $query : '');

    if ($id === false) {
        flash_set('Vigane päring.', 'error');
        redirect($back);
    }

    if ($action === 'toggle') {
        $stmt = $pdo->prepare("UPDATE contact_requests
            SET status = CASE WHEN status = 'Uus' THEN 'Vastatud' ELSE 'Uus' END
            WHERE id = ?");
        $stmt->execute([$id]);
        if ($stmt->rowCount() > 0) {
            flash_set('Päringu staatus muudeti.');
        } else {
            flash_set('Päringut ei leitud.', 'error');
        }
        redirect($back);
    }

    if ($action === 'delete') {
        $stmt = $pdo->prepare('DELETE FROM contact_requests WHERE id = ?');
        $stmt->execute([$id]);
        if ($stmt->rowCount() > 0) {
            flash_set('Päring kustutati.');
        } else {
            flash_set('Päringut ei leitud.', 'error');
        }
        redirect($back);
    }

    flash_set('Tundmatu tegevus.', 'error');
    redirect($back);
}

$adminActive = 'requests';
$pageTitle = 'Päringud';
require __DIR__ . '/../includes/admin_header.php';

$perPage = max(1, (int) $config['per_page_admin']);

// Kokku ja lehekülgede arv
$where = $statusFilter !== '' ? 'WHERE status = :status' : '';
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM contact_requests $where");
if ($statusFilter !== '') {
    $countStmt->bindValue(':status', $statusFilter);
}
$countStmt->execute();
$total = (int) $countStmt->fetchColumn();

$totalPages = max(1, (int) ceil($total / $perPage));
$page = min(page_number(), $totalPages);
$offset = ($page - 1) * $perPage;

$listStmt = $pdo->prepare("SELECT id, name, email, topic, message, status, created_at
    FROM contact_requests $where
    ORDER BY created_at DESC, id DESC
    LIMIT :limit OFFSET :offset");
if ($statusFilter !== '') {
    $listStmt->bindValue(':status', $statusFilter);
}
$listStmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$listStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$listStmt->execute();
$rows = $listStmt->fetchAll();

// Lingid säilitavad filtri
function requests_url(string $status, int $page): string
{
    $params = [];
    if ($status !== '') {
        $params['status'] = $status;
    }
    if ($page > 1) {
        $params['page'] = $page;
    }
    return 'requests.php' . ($params ? '?' . http_build_query($params) : '');
}

$filters = ['' => 'Kõik', 'Uus' => 'Uus', 'Vastatud' => 'Vastatud'];
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <h1 class="h3 mb-0"><i class="fa-regular fa-envelope text-success me-2"></i>Päringud</h1>
    <div class="btn-group" role="group" aria-label="Staatuse filter">
        <?php foreach ($filters as $value => $label): ?>
            <a href="<?= e(requests_url($value, 1)) ?>"
                class="btn btn-sm <?= $statusFilter === $value ? 'btn-success' : 'btn-outline-success' ?>"><?= e($label) ?></a>
        <?php endforeach; ?>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body pb-0">
        <p class="text-muted mb-3">Kokku <?= $total ?> päringut<?= $statusFilter !== '' ? ' (staatus: ' . e($statusFilter) . ')' : '' ?></p>
    </div>
    <div class="table-responsive">
        <table class="table align-middle table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th scope="col">Nimi</th>
                    <th scope="col">E-post</th>
                    <th scope="col">Teema</th>
                    <th scope="col" style="min-width: 220px;">Sõnum</th>
                    <th scope="col">Saadud</th>
                    <th scope="col">Staatus</th>
                    <th scope="col" class="text-end">Tegevused</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($rows === []): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">Päringuid ei leitud.</td></tr>
                <?php endif; ?>
                <?php foreach ($rows as $r): ?>
                    <?php
                    $message = (string) $r['message'];
                    $isLong = mb_strlen($message) > 120;
                    $preview = $isLong ? mb_substr($message, 0, 120) . '…' : $message;
                    $isNew = $r['status'] === 'Uus';
                    ?>
                    <tr>
                        <td><?= e($r['name']) ?></td>
                        <td><a href="mailto:<?= e($r['email']) ?>"><?= e($r['email']) ?></a></td>
                        <td><?= e($r['topic']) ?></td>
                        <td>
                            <?php if ($isLong): ?>
                                <details>
                                    <summary class="small" style="cursor: pointer;"><?= e($preview) ?></summary>
                                    <div class="small mt-2 text-break" style="white-space: pre-wrap;"><?= e($message) ?></div>
                                </details>
                            <?php else: ?>
                                <span class="small" style="white-space: pre-wrap;"><?= e($message) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-nowrap"><?= e(date('d.m.Y H:i', strtotime((string) $r['created_at']))) ?></td>
                        <td>
                            <span class="badge <?= $isNew ? 'bg-primary' : 'bg-success' ?>"><?= e($r['status']) ?></span>
                        </td>
                        <td class="text-end text-nowrap">
                            <form method="post" action="requests.php" class="d-inline">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="toggle">
                                <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                                <input type="hidden" name="status" value="<?= e($statusFilter) ?>">
                                <input type="hidden" name="page" value="<?= $page ?>">
                                <button type="submit" class="btn btn-sm <?= $isNew ? 'btn-outline-success' : 'btn-outline-secondary' ?>"
                                    title="<?= $isNew ? 'Märgi vastatuks' : 'Märgi uueks' ?>"
                                    aria-label="<?= $isNew ? 'Märgi vastatuks' : 'Märgi uueks' ?>">
                                    <i class="fa-solid <?= $isNew ? 'fa-check' : 'fa-rotate-left' ?>"></i>
                                </button>
                            </form>
                            <form method="post" action="requests.php" class="d-inline"
                                onsubmit="return confirm('Kustutada päring? Seda ei saa tagasi võtta.')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                                <input type="hidden" name="status" value="<?= e($statusFilter) ?>">
                                <input type="hidden" name="page" value="<?= $page ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Kustuta" aria-label="Kustuta päring">
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

<?php if ($totalPages > 1): ?>
    <nav class="mt-4" aria-label="Lehekülje navigeerimine">
        <ul class="pagination justify-content-center flex-wrap">
            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= e(requests_url($statusFilter, $page - 1)) ?>">&laquo; Eelmine</a>
            </li>
            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                    <a class="page-link" href="<?= e(requests_url($statusFilter, $p)) ?>"><?= $p ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= e(requests_url($statusFilter, $page + 1)) ?>">Järgmine &raquo;</a>
            </li>
        </ul>
    </nav>
<?php endif; ?>

<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
