<?php
require_once __DIR__ . '/db_connect.php';
$pdo = Database::getInstance()->getConnection();
$transfers = $pdo->query("SELECT * FROM transfers")->fetchAll();
$Players = $pdo->query("SELECT p.nickname, t.name AS team_name, p.market_value
                                        FROM players p
                                        JOIN contracts c ON p.persons_id = c.persons_id
                                        JOIN teames t ON c.team_id = t.id
                                        ORDER BY p.market_value DESC")->fetchAll();
?>
<?php require 'layouts/header.php'; ?>

<div class="card">
    <h2>Dashboard</h2>
    <p>Overview of teams, players and transfers.</p>
    <table>
        <thead>
            <tr>
                <th>Player Nickname</th>
                <th>Team</th>
                <th>Market Value (€)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($Players as $player): ?>
                <tr>
                    <td><?= htmlspecialchars($player['nickname']) ?></td>
                    <td><?= htmlspecialchars($player['team_name']) ?></td>
                    <td class="market-value">€<?= number_format($player['market_value'], 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'){ ?>
<div class="card">
    <h3 style="margin-bottom : 1em;">Quick Actions</h3>
    <a href="/views/form_create_player.php" class="btn btn-primary">Add Player</a>
    <a href="/views/form_create_coach.php" class="btn btn-outline">Add Coach</a>
    <a href="/views/form_create_team.php" class="btn btn-outline">Add Team</a>
    <a href="/views/form_transfer.php" class="btn btn-outline">Make Transfer</a>
</div>
<?php }?>

<?php require 'layouts/footer.php'; ?>