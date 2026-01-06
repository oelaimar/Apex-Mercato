<?php
require_once __DIR__ . '/../layouts/header.php';
require_once  __DIR__ . '/../autoload.php';

// Fetch players and teams
$pdo = Database::getInstance()->getConnection();
$players = $pdo->query("SELECT id, name, nickname FROM persons INNER JOIN players ON persons.id = players.persons_id ORDER BY nickname")->fetchAll();
$teams = $pdo->query("SELECT * FROM teames ORDER BY name")->fetchAll();
?>

<div class="card" style="max-width:800px;margin:auto;">
    <h2>🔄 Create Transfer</h2>

    <?php if (!empty($_GET['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
    <?php endif; ?>
    <?php if (!empty($_GET['error'])): ?>
        <div class="alert alert-error"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <form action="../../actions/process_transfer.php" method="POST">
        <div class="form-group">
            <label for="player_id">Player *</label>
            <select name="player_id" id="player_id" class="form-control" required>
                <option value="">-- Select Player --</option>
                <?php foreach ($players as $player): ?>
                    <option value="<?= $player['id'] ?>"><?= htmlspecialchars($player['nickname'] . ' (' . $player['name'] . ')') ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label for="departure_team">Departure Team</label>
                <select name="departure_team" id="departure_team" class="form-control">
                    <option value="">-- Select Departure Team --</option>
                    <?php foreach ($teams as $team): ?>
                        <option value="<?= $team['id'] ?>"><?= htmlspecialchars($team['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="arrival_team">Arrival Team *</label>
                <select name="arrival_team" id="arrival_team" class="form-control" required>
                    <option value="">-- Select Arrival Team --</option>
                    <?php foreach ($teams as $team): ?>
                        <option value="<?= $team['id'] ?>"><?= htmlspecialchars($team['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="amount">Transfer Amount (€) - Optional</label>
            <input type="number" name="amount" id="amount" class="form-control" min="0" step="1000">
        </div>

        <div style="display:flex;gap:1rem;margin-top:1.5rem;">
            <button type="submit" class="btn btn-success">✓ Execute Transfer</button>
            <a href="../../admin_dashboard.php" class="btn btn-danger">✗ Cancel</a>
        </div>
    </form>
</div>

<?php require  __DIR__ . '/../layouts/footer.php'; ?>
