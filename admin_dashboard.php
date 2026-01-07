<?php 
require 'auth/auth.php';
require_once __DIR__ . '/autoload.php';
requirerole('admin');
require 'layouts/header.php';
?>

<div class="card">
  <h2>Administration</h2>
  <p>Manage all platform resources.</p>
</div>
