<?php
date_default_timezone_set('Europe/Tallinn');

$conn = new mysqli(
    getenv('DB_HOST') ?: 'localhost',
    getenv('DB_USER') ?: 'kapakodu_app',
    getenv('DB_PASS') ?: 'kapakodu_app_pw',
    getenv('DB_NAME') ?: 'kapakodu'
);
$conn->set_charset('utf8mb4');

function e($s) { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); }
