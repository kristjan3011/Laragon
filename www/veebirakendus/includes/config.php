<?php
// Rakenduse seaded. Andmebaasi andmed saab üle kirjutada keskkonnamuutujatega (DB_HOST, DB_NAME, DB_USER, DB_PASS).
return [
    'db' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'name' => getenv('DB_NAME') ?: 'mario_kapakodu',
        'user' => getenv('DB_USER') ?: 'root',
        'pass' => getenv('DB_PASS') ?: '',
    ],
    'per_page_public' => 8,
    'per_page_admin' => 10,
    'upload_dir' => dirname(__DIR__) . '/assets/uploads',
    'upload_url' => 'assets/uploads',
    'upload_max_bytes' => 5 * 1024 * 1024,
    'upload_mimes' => [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
    ],
];
