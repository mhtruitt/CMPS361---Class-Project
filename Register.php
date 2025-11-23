<?php

require __DIR__ . '/init.php';

// Make sure $pdo exists. Example initialization if missing:
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

// CSRF token
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$errors = [];

// Use null coalescing to safely check REQUEST_METHOD
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
        $errors[] = 'Invalid CSRF token.';
    }

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $errors[] = 'Username and password required.';
    } elseif (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }

    if (!$errors) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (:u, :h)");
            $stmt->execute([':u' => $username, ':h' => $hash]);
            header('Location: login.php?registered=1');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') { // Unique violation in MySQL
                $errors[] = 'Username already taken.';
            } else {
                $errors[] = 'Registration failed.';
            }
        }
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Register</title></head>
<body>
<h2>Register</h2>
<?php if ($errors): ?>
    <div style="color:red;">
        <ul>
        <?php foreach($errors as $err): ?>
            <li><?=htmlspecialchars($err)?></li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
<form method="post" action="">
    <label>Username: <input name="username" required></label><br>
    <label>Password: <input name="password" type="password" required minlength="8"></label><br>
    <input type="hidden" name="csrf" value="<?=htmlspecialchars($_SESSION['csrf'])?>">
    <button type="submit">Register</button>
</form>
<p><a href="login.php">Login</a></p>
</body>
</html>

