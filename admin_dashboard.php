<?php 
require 'auth/auth.php';
require_once __DIR__ . '/autoload.php';
requireRole('admin');

$pdo = Database::getInstance()->getConnection();

// Statistics
$playerCount = $pdo->query("SELECT COUNT(*) FROM players")->fetchColumn();
$coachCount = $pdo->query("SELECT COUNT(*) FROM coaches")->fetchColumn();
$teamCount = $pdo->query("SELECT COUNT(*) FROM teames")->fetchColumn();
$totalMarketValue = $pdo->query("SELECT SUM(market_value) FROM players")->fetchColumn();

// Recent Players
$recentPlayers = $pdo->query("SELECT p.persons_id, p.nickname, ps.name, p.market_value, t.name as team_name 
                             FROM players p 
                             JOIN persons ps ON p.persons_id = ps.id 
                             LEFT JOIN contracts c ON ps.id = c.persons_id 
                             LEFT JOIN teames t ON c.team_id = t.id 
                             ORDER BY ps.id DESC")->fetchAll();

// Teams
$teams = $pdo->query("SELECT * FROM teames ORDER BY budget DESC")->fetchAll();

require 'layouts/header.php';
?>

<div class="dashboard-header">
    <h1>Admin Console</h1>
    <div class="quick-actions">
        <a href="views/form_create_player.php" class="btn btn-primary">Add Player</a>
        <a href="views/form_create_coach.php" class="btn btn-outline">Add Coach</a>
        <a href="views/form_create_team.php" class="btn btn-outline">Add Team</a>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <h3>Players</h3>
        <p class="stat-value"><?= $playerCount ?></p>
    </div>
    <div class="stat-card">
        <h3>Coaches</h3>
        <p class="stat-value"><?= $coachCount ?></p>
    </div>
    <div class="stat-card">
        <h3>Teams</h3>
        <p class="stat-value"><?= $teamCount ?></p>
    </div>
    <div class="stat-card">
        <h3>Market Size</h3>
        <p class="stat-value">€<?= number_format($totalMarketValue / 1000000, 1) ?>M</p>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;gap:1rem;flex-wrap:wrap;">
            <h2>Recent Talent</h2>
            <div class="filters" style="display:flex;gap:0.5rem;align-items:center;">
                <input type="text" id="playerSearch" placeholder="Search by name..." class="form-control" style="width:200px;margin-bottom:0;">
                <select id="teamFilter" class="form-control" style="width:150px;margin-bottom:0;">
                    <option value="">All Teams</option>
                    <?php foreach ($teams as $t): ?>
                        <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Team</th>
                    <th>Value</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="playersTableBody">
                <tr>
                    <td colspan="4" style="text-align:center;padding:2rem;">⌛ Loading players...</td>
                </tr>
            </tbody>
        </table>
        <div id="paginationContainer" class="pagination-container"></div>
    </div>

    <div class="card">
        <h2>Clubs Financials</h2>
        <table>
            <thead>
                <tr>
                    <th>Team</th>
                    <th>Budget</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($teams as $t): ?>
                <tr>
                    <td><?= htmlspecialchars($t['name']) ?></td>
                    <td class="market-value">€<?= number_format($t['budget'], 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require 'layouts/footer.php'; ?>
<script src="assets/js/filter_players.js"></script>
