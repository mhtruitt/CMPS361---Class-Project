<?php

$config = require __DIR__ . '/config.php';

$cookieParams = session_get_cookie_params();
$cookieLifetime = 0;
session_set_cookie_params([
    'lifetime' => $cookieLifetime,
    'path' => $cookieParams['path'],
    'domain' => $cookieParams['domain'],
    'secure' => $config['session_cookie_secure'],
    'httponly' => $config['session_cookie_httponly'],
    'samesite' => $config['session_cookie_samesite']
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $dbcfg = $config['db'];
    $dsn = sprintf(
        "pgsql:host=%s;port=%s;dbname=%s",
        $dbcfg['host'],
        $dbcfg['port'],
        $dbcfg['dbname']
    );
    $pdo = new PDO($dsn, $dbcfg['user'], $dbcfg['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
}

catch (PDOException $e) {
    die("Database connection failed: " . htmlspecialchars($e->getMessage()));
}
