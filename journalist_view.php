<?php
require_once __DIR__ . '/autoload.php';
require 'auth/auth.php';
requireRole(['journalist','admin']);

$pdo = Database::getInstance()->getConnection();

// Market Insights
$topValued = $pdo->query("SELECT p.nickname, p.market_value, t.name as team_name 
                          FROM players p 
                          LEFT JOIN contracts c ON p.persons_id = c.persons_id 
                          LEFT JOIN teames t ON c.team_id = t.id 
                          ORDER BY p.market_value DESC LIMIT 10")->fetchAll();

$recentTransfers = $pdo->query("SELECT tr.*, ps.name, p.nickname, t_dep.name as dep_team, t_arr.name as arr_team 
                                FROM transfers tr 
                                JOIN persons ps ON tr.persons_id = ps.id 
                                LEFT JOIN players p ON ps.id = p.persons_id 
                                LEFT JOIN teames t_dep ON tr.departure_team_id = t_dep.id 
                                LEFT JOIN teames t_arr ON tr.arrival_team_id = t_arr.id 
                                ORDER BY tr.created_at DESC LIMIT 8")->fetchAll();

require 'layouts/header.php';
?>

<div class="dashboard-header">
    <h1>Market Insights & Analytics</h1>
    <p class="text-muted">Real-time data for journalists and market analysts.</p>
</div>

<div class="dashboard-grid">
    <div class="card">
        <h2>🔥 Market Leaders (Top 10)</h2>
        <table>
            <thead>
                <tr>
                    <th>Player</th>
                    <th>Current Club</th>
                    <th>Market Value</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($topValued as $player): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($player['nickname'] ?: 'Unknown') ?></strong></td>
                    <td><?= htmlspecialchars($player['team_name'] ?: 'Free Agent') ?></td>
                    <td class="market-value">€<?= number_format($player['market_value'], 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="card">
        <h2>🔄 Recent Deal Flow</h2>
        <div class="transfer-list">
            <?php if (empty($recentTransfers)): ?>
                <p>No recent transfers recorded.</p>
            <?php else: ?>
                <?php foreach ($recentTransfers as $tr): ?>
                <div class="transfer-item">
                    <div class="transfer-header">
                        <span class="badge badge-success"><?= strtoupper($tr['status']) ?></span>
                        <span class="text-muted"><?= date('M d, Y', strtotime($tr['created_at'])) ?></span>
                    </div>
                    <p>
                        <strong><?= htmlspecialchars($tr['nickname'] ?: $tr['name']) ?></strong><br>
                        <small><?= htmlspecialchars($tr['dep_team'] ?: 'None') ?> ➔ <?= htmlspecialchars($tr['arr_team'] ?: 'None') ?></small>
                    </p>
                    <p class="market-value">€<?= number_format($tr['amount'], 0, ',', '.') ?></p>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.transfer-item {
    padding: 1rem 0;
    border-bottom: 1px solid #eee;
}
.transfer-item:last-child { border-bottom: none; }
.transfer-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}
.text-muted { color: #666; font-size: 0.85rem; }
</style>

<?php require 'layouts/footer.php'; ?>
