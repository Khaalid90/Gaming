<?php
$pageTitle = $pageTitle ?? 'GameHub';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle) ?> | GameHub</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <script src="assets/js/app.js" defer></script>
</head>
<body>
<header class="site-header">
  <a class="brand" href="index.php">GAME<span>HUB</span></a>
  <nav>
    <a href="index.php">Home</a>
    <?php if (is_admin()): ?>
      <a href="admin.php">Dashboard</a><a href="logout.php">Logout</a>
    <?php else: ?>
      <a href="login.php">Admin</a>
    <?php endif; ?>
  </nav>
</header>
<main class="container">
