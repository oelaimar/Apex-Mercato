<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>
<!DOCTYPE html>
<html>

<head>
  <title>VERSUS MANAGER</title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>

  <nav class="navbar">
    <a href="/">⚡ Versus Manager</a>

    <div class="navbar-menu">
      <a href="/">Dashboard</a>

      <?php if (!isset($_SESSION['role'])): ?>
        <a href="/auth/login.php">Login</a>
      <?php else: ?>
        <?php if ($_SESSION['role'] === 'admin'): ?>
          <a href="/admin_dashboard.php">Admin</a>
        <?php endif; ?>
        <a href="/journalist_view.php">Analytics</a>
        <a href="/auth/logout.php">Logout</a>
      <?php endif; ?>
    </div>
  </nav>

  <div class="container">