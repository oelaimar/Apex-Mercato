<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="card" style="max-width:700px;margin:auto;">
    <h2>➕ Add New Team</h2>

    <?php if (!empty($_GET['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
    <?php endif; ?>
    <?php if (!empty($_GET['error'])): ?>
        <div class="alert alert-error"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <form action="../actions/add_team.php" method="POST">
        <div class="form-group">
            <label for="name">Team Name *</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="budget">Team Budget (€) *</label>
            <input type="number" name="budget" id="budget" class="form-control" min="0" required step="1000">
        </div>

        <div class="form-group">
            <label for="manager">Team Manager *</label>
            <input type="text" name="manager" id="manager" class="form-control" required>
        </div>

        <div style="display:flex;gap:1rem;margin-top:1.5rem;">
            <button type="submit" class="btn btn-success">✓ Create Team</button>
            <a href="../admin_dashboard.php" class="btn btn-danger">✗ Cancel</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
