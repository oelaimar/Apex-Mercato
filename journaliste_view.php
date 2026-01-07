<?php
require_once __DIR__ . '/autoload.php';
require 'auth/auth.php';
requireRole(['journalist','admin']);
require 'layouts/header.php';
?>

<div class="card">
  <h2>Market Analytics</h2>
  <p>Read-only market insights.</p>
</div>
