<?php

$host = '127.0.0.1';
$port = '5432';
$dbname = 'auth_demo';
$user = 'auth_user';
$pass = 'change_this_password';
$plainPassword = 'TestPass123!';
$passwordHash = password_hash($plainPassword, PASSWORD_DEFAULT);

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (:username, :password_hash)");
    $stmt->execute([
        ':username' => 'alice',
        ':password_hash' => $passwordHash
    ]);

    echo "Test user 'alice' inserted successfully.\n";
    echo "   Username: alice\n";
    echo "   Password: $plainPassword\n";
}

catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
    exit;
}
