<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'] ?? 'Guest';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>New Page</title>

  <link rel="stylesheet" href="styles.css">

  <style>
    body {
      font-family: Arial, sans-serif;
      text-align: center;
      background-color: #9e5f5fff;
      padding: 50px;
      color: white;
    }

    button {
      padding: 10px 20px;
      margin: 10px;
      font-size: 16px;
