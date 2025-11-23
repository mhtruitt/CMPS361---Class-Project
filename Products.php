<?php
session_start();

// Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Include the database connection
require __DIR__ . '/db_connect.php'; // Make sure this file exists

// Verify $pdo is defined
if (!isset($pdo)) {
    die('Database connection not initialized.');
}

// Pagination
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch products with prepared statement
$stmt = $pdo->prepare("SELECT * FROM products LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Total products for pagination
$total = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_pages = ceil($total / $limit);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Products</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .pagination a {
      margin: 5px;
      padding: 8px 12px;
      text-decoration: none;
      border: 1px solid #1a73e8;
      color: #1a73e8;
    }
    .pagi
