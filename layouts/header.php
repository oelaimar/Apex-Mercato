<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
  <title>VERSUS MANAGER</title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<nav class="navbar">
  <a href="/index.php">⚡ Versus Manager</a>

  <div class="navbar-menu">
    <a href="/index.php">Dashboard</a>

    <?php if ($_SESSION['role'] ?? '' === 'admin'): ?>
      <a href="/admin_dashboard.php">Admin</a>
    <?php endif; ?>

    <a href="/journaliste_view.php">Analytics</a>

    <?php if (!isset($_SESSION['role'])): ?>
      <a href="/auth/login.php">Login</a>
    <?php else: ?>
      <a href="/auth/logout.php">Logout</a>
    <?php endif; ?>
  </div>
</nav>

<div class="container">
