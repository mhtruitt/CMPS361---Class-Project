<?php

require __DIR__ . '/init.php';

// Clear session data
$_SESSION = [];

// Delete the session cookie if cookies are used
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    $sessionName = session_name();
    if ($sessionName) { // only call setcookie if $sessionName is a string
        setcookie(
            $sessionName,
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
}

// Destroy the session
session_destroy();

// Redirect to login page
header('Location: login.php?logged_out=1');
exit;
