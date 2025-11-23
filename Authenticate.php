<?php

require __DIR__ . '/init.php';

// Ensure $pdo exists
if (!isset($pdo)) {
    $dsn = 'mysql:host=localhost;dbname=test;charset=utf8mb4';
    $user = 'dbuser';
    $pass = 'dbpass';
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    $pdo = new PDO($dsn, $user, $pass, $options);
}

// Safely check request method
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: login.php');
    exit;
}

$errors = [];

// CSRF validation
if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
    $errors[] = 'Invalid CSRF token.';
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    $errors[] = 'Missing username or password.';
}

if ($errors) {
    $_SESSION['login_errors'] = $errors;
    header('Location: login.php');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE username = :u LIMIT 1");
    $stmt->execute([':u' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['logged_in_at'] = time();
        header('Location: welcome.php');
        exit;
    } else {
        $_SESSION['login_errors'] = ['Invalid username or password.'];
        header('Location: login.php');
        exit;
    }
} catch (Exception $e) {
    $_SESSION['login_errors'] = ['Login failed due to server error.'];
    header('Location: login.php');
    exit;
}
