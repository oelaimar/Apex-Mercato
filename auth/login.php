<?php require '../layouts/header.php'; ?>

<div class="card" style="max-width:400px;margin:auto;">
  <h2>Login</h2>
  
    <?php if (!empty($_GET['error'])): ?>
        <div class="alert alert-error"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

  <form method="POST" action="process_login.php">
    <div class="form-group">
      <input name="username" placeholder="Username" required>
    </div>

    <div class="form-group">
      <input type="password" name="password" placeholder="Password" required>
    </div>

    <button class="btn btn-primary">Login</button>
  </form>
</div>
