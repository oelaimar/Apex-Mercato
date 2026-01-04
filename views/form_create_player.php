<?php
require_once __DIR__ . '/../layouts/header.php';
require_once  __DIR__ . '/../db_connect.php';

// Fetch teams
$pdo = Database::getInstance()->getConnection();
$teams = $pdo->query("SELECT * FROM teames ORDER BY name")->fetchAll();
$roles = ['Toplaner', 'Jungler', 'Midlaner', 'ADC', 'Support'];
?>

<div class="card" style="max-width:900px;margin:auto;">
    <h2>➕ Add New Player</h2>

    <?php if (!empty($_GET['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
    <?php endif; ?>

    <?php if (!empty($_GET['error'])): ?>
        <div class="alert alert-error"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <form action="../../actions/add_player.php" method="POST">
        <div class="grid-2">
            <div class="form-group">
                <label for="name">Full Name *</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="nickname">In-Game Nickname *</label>
                <input type="text" name="nickname" id="nickname" class="form-control" required minlength="3">
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="nationality">Nationality *</label>
                <input type="text" name="nationality" id="nationality" class="form-control" required>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label for="role">Role *</label>
                <select name="role" id="role" class="form-control" required>
                    <option value="">-- Select Role --</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= $role ?>"><?= $role ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="market_value">Market Value (€) *</label>
                <input type="number" name="market_value" id="market_value" class="form-control" required min="0" step="1000">
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label for="team_id">Team *</label>
                <select name="team_id" id="team_id" class="form-control" required>
                    <option value="">-- Select Team --</option>
                    <?php foreach ($teams as $team): ?>
                        <option value="<?= $team['id'] ?>"><?= htmlspecialchars($team['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="salary">Monthly Salary (€) *</label>
                <input type="number" name="salary" id="salary" class="form-control" required min="0" step="100">
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label for="start_date">Contract Start Date *</label>
                <input type="date" name="start_date" id="start_date" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="end_date">Contract End Date *</label>
                <input type="date" name="end_date" id="end_date" class="form-control" required>
            </div>
        </div>

        <div class="form-group">
            <label for="buyout_clause">Buyout Clause (€) - Optional</label>
            <input type="number" name="buyout_clause" id="buyout_clause" class="form-control" min="0" step="1000">
        </div>

        <div style="display:flex;gap:1rem;margin-top:1.5rem;">
            <button type="submit" class="btn btn-success">✓ Create Player</button>
            <a href="../../admin_dashboard.php" class="btn btn-danger">✗ Cancel</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
