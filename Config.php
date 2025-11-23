<?php

return [
    'db' => [
        'host' => '127.0.0.1',
        'port' => '5432',
        'dbname' => 'auth_demo',
        'user' => 'auth_user',
        'pass' => 'change_this_password'
    ],

    'session_cookie_secure' => true,
    'session_cookie_httponly' => true,
    'session_cookie_samesite' => 'Lax',
];
