<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require __DIR__ . '/db_connect.php';

if (!isset($pdo)) {
    die('Database connection not initialized.');
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = $_POST['price'] ?? '';

    if ($name === '' || $description === '' || !is_numeric($price) || $price < 0) {
        die('Invalid input.');
    }

    $stmt = $pdo->prepare("INSERT INTO products (name, description, price) VALUES (:name, :description, :price)");
    $stmt->execute([
        ':name' => $name,
        ':description' => $description,
        ':price' => $price
    ]);

    header("Location: products.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Add Product</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Add New Product</h1>
<form method="post">
  <label>Product Name:</label><br>
  <input type="text" name="name" required><br><br>

  <label>Description:</label><br>
  <textarea name="description" required></textarea><br><br>

  <label>Price ($):</label><br>
  <input type="number" step="0.01" name="price" required><br><br>

  <button type="submit">Add Product</button>
</form>
</body>
</html>
