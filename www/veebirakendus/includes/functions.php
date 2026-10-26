<?php
declare(strict_types=1);

// Ühised funktsioonid: andmebaas, sessioon, CSRF, väljundi kaitse, sisselogimine.

$config = require __DIR__ . '/config.php';

const ANIMAL_STATUSES = ['Saadaval', 'Broneeritud', 'Hoolduses', 'Kodus'];
const ANIMAL_SEXES = ['Isane', 'Emane'];
const REQUEST_TOPICS = ['Looma adopteerimine', 'Vabatahtlik töö', 'Annetamine', 'Üldine küsimus'];
const REQUEST_STATUSES = ['Uus', 'Vastatud'];
const USER_ROLES = ['admin', 'toimetaja'];

// Andmebaasi ühendus (PDO, üks ühendus päringu kohta)
function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        global $config;
        $db = $config['db'];
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $db['host'], $db['name']);
        $pdo = new PDO($dsn, $db['user'], $db['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
    return $pdo;
}

// Sessioon: HttpOnly ja SameSite küpsised
function start_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        $https = !empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off';
        session_set_cookie_params([
            'secure' => $https,
            'httponly' => true,
            'samesite' => 'Lax',
            'path' => '/',
        ]);
        session_start();
    }
}

// Väljundi kaitse (XSS)
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// CSRF: vorm peab sisaldama peidetud välja csrf_field()
function csrf_token(): string
{
    start_session();
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

// Kontrollib POST-päringut ja katkestab, kui token ei klapi
function csrf_verify(): void
{
    $sent = $_POST['csrf'] ?? '';
    if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
        http_response_code(403);
        exit('Turvakontroll ebaõnnestus. Laadi leht uuesti.');
    }
}

// Teade järgmisele lehele (Post-Redirect-Get)
function flash_set(string $message, string $type = 'success'): void
{
    start_session();
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function flash_get(): ?array
{
    start_session();
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function current_user(): ?array
{
    start_session();
    return $_SESSION['user'] ?? null;
}

// Nõuab sisselogimist. $loginUrl on relatiivne tee login.php-ni.
function require_login(string $loginUrl): array
{
    $user = current_user();
    if ($user === null) {
        redirect($loginUrl);
    }
    return $user;
}

// Vanus aastates birth_date järgi
function age_years(string $birthDate): int
{
    $birth = new DateTimeImmutable($birthDate);
    return (int) $birth->diff(new DateTimeImmutable('today'))->y;
}

// Vanus kuvamiseks, nt "2 a" või "6 kuud"
function age_label(string $birthDate): string
{
    $birth = new DateTimeImmutable($birthDate);
    $diff = $birth->diff(new DateTimeImmutable('today'));
    if ($diff->y >= 1) {
        return $diff->y . ' a';
    }
    $months = $diff->m + $diff->y * 12;
    $months = max(1, $months);
    return $months . ($months === 1 ? ' kuu' : ' kuud');
}

// Vanusegrupp filtri jaoks: kutsikas (<1 a), noor (1–3 a), täiskasvanu (>3 a)
function age_group_sql(string $group): ?array
{
    return match ($group) {
        'kutsikas' => ['birth_date > DATE_SUB(CURDATE(), INTERVAL 1 YEAR)', []],
        'noor' => ['birth_date <= DATE_SUB(CURDATE(), INTERVAL 1 YEAR) AND birth_date > DATE_SUB(CURDATE(), INTERVAL 3 YEAR)', []],
        'taiskasvanu' => ['birth_date <= DATE_SUB(CURDATE(), INTERVAL 3 YEAR)', []],
        default => null,
    };
}

// Lehekülgede arv ja praegune leht
function page_number(string $param = 'page'): int
{
    return max(1, (int) ($_GET[$param] ?? 1));
}

// Pildi aadress: üleslaaditud fail või vaikimisi kujutis; base on '' või '../'
function animal_image_url(?string $image, string $base): ?string
{
    if ($image === null || $image === '') {
        return null;
    }
    return $base . $image;
}

// Liigid ja tõud vormide valikute jaoks
function species_list(): array
{
    return db()->query('SELECT id, name FROM species ORDER BY name')->fetchAll();
}

function breeds_list(): array
{
    return db()->query('SELECT b.id, b.name, b.species_id, s.name AS species_name
        FROM breeds b JOIN species s ON s.id = b.species_id
        ORDER BY s.name, b.name')->fetchAll();
}

// Lühike staatuse märk (Bootstrap badge)
function status_badge_class(string $status): string
{
    return match ($status) {
        'Saadaval' => 'bg-success',
        'Broneeritud' => 'bg-warning text-dark',
        'Hoolduses' => 'bg-info text-dark',
        'Kodus' => 'bg-secondary',
        default => 'bg-light text-dark',
    };
}
