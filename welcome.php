<?php

require __DIR__ . '/init.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$timeout = 30 * 60;

$loggedInAt = 0;
if (isset($_SESSION['logged_in_at']) && is_numeric($_SESSION['logged_in_at'])) {
    $loggedInAt = (int)$_SESSION['logged_in_at'];
}

if ($loggedInAt > 0 && (time() - $loggedInAt) > $timeout) {
    session_unset();
    session_destroy();
    header('Location: login.php?timeout=1');
    exit;
}

$username = 'User';
if (isset($_SESSION['username']) && is_scalar($_SESSION['username'])) {
    $username = (string)$_SESSION['username'];
}

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome</title>
</head>
<body>
<h2>Welcome</h2>
<p>Hello, <?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?> — you are logged in.</p>
<p><a href="logout.php">Logout</a></p>
</body>
</html>
