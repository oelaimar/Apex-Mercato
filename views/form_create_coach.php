<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../db_connect.php';

$styles = ['Aggressive', 'Defensive', 'Supportive', 'Strategic'];
?>

<div class="card" style="max-width:700px;margin:auto;">
    <h2>➕ Add New Coach</h2>

    <?php if (!empty($_GET['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
    <?php endif; ?>
    <?php if (!empty($_GET['error'])): ?>
        <div class="alert alert-error"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <form action="../../actions/add_coach.php" method="POST">
        <div class="grid-2">
            <div class="form-group">
                <label for="name">Full Name *</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label for="nationality">Nationality *</label>
                <input type="text" name="nationality" id="nationality" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="style">Coaching Style *</label>
                <select name="style" id="style" class="form-control" required>
                    <option value="">-- Select Style --</option>
                    <?php foreach ($styles as $style): ?>
                        <option value="<?= $style ?>"><?= $style ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="experience">Years of Experience *</label>
            <input type="number" name="experience" id="experience" class="form-control" min="0" required>
        </div>

        <div style="display:flex;gap:1rem;margin-top:1.5rem;">
            <button type="submit" class="btn btn-success">✓ Create Coach</button>
            <a href="../../admin_dashboard.php" class="btn btn-danger">✗ Cancel</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
