<?php

require __DIR__ . '/init.php';
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}
$registered = isset($_GET['registered']);
$errors = $_SESSION['login_errors'] ?? [];
unset($_SESSION['login_errors']);
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Login</title></head>
<body>
<h2>Login</h2>
<?php if ($registered): ?>
    <div style="color:green;">Registration successful — please log in.</div>
<?php endif; ?>
<?php if ($errors): ?>
    <div style="color:red;"><ul><?php foreach($errors as $err) echo "<li>" . htmlspecialchars($err) . "</li>"; ?></ul></div>
<?php endif; ?>
<form method="post" action="authenticate.php">
    <label>Username: <input name="username" required></label><br>
    <label>Password: <input name="password" type="password" required></label><br>
    <input type="hidden" name="csrf" value="<?=htmlspecialchars($_SESSION['csrf'])?>">
    <button type="submit">Login</button>
</form>
<p><a href="register.php">Register</a></p>
</body>
</html>
