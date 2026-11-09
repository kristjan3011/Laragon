<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';

// Küpsiste nõusolek: seab, muudab ja kustutab küpsise CONSENT_COOKIE (ainult POST + CSRF)
start_session();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}
csrf_verify();

$action = is_string($_POST['action'] ?? null) ? $_POST['action'] : '';
$cookie = [
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Lax',
    'secure' => !empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off',
];

if ($action === 'accept' || $action === 'reject') {
    setcookie(CONSENT_COOKIE, $action === 'accept' ? 'jah' : 'ei', $cookie + ['expires' => time() + 365 * 86400]);
} elseif ($action === 'reset') {
    setcookie(CONSENT_COOKIE, '', $cookie + ['expires' => time() - 3600]);
}

redirect('index.php');
