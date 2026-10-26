<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';

// Sessiooni lõpetamine: tühjenda andmed, kustuta küpsis ja hävita sessioon
start_session();

// Väljalogimine ainult POST-iga ja CSRF-tokeniga (GET ei tee midagi)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}
csrf_verify();

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', [
        'expires' => time() - 42000,
        'path' => $params['path'],
        'domain' => $params['domain'],
        'secure' => $params['secure'],
        'httponly' => true,
        'samesite' => $params['samesite'] !== '' ? $params['samesite'] : 'Lax',
    ]);
}

session_destroy();

redirect('../login.php');
