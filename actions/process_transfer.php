<?php
session_start();
require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/../auth/auth.php';
requireRole('admin');

$pdo = Database::getInstance()->getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../admin_dashboard.php');
    exit;
}

try {
    $pdo->beginTransaction();

    $playerId = (int) $_POST['player_id'];
    $arrivalTeamId = (int) $_POST['arrival_team'];
    $amount = !empty($_POST['amount']) ? (float) $_POST['amount'] : 0.0;

    $stmtCurrent = $pdo->prepare("SELECT team_id FROM contracts WHERE persons_id = :player_id LIMIT 1");
    $stmtCurrent->execute([':player_id' => $playerId]);
    $currentContract = $stmtCurrent->fetch();

    if (!$currentContract) {
        throw new Exception("The player does not have an active contract.");
    }

    $departureTeamId = (int) $currentContract['team_id'];

    if ($departureTeamId === $arrivalTeamId) {
        throw new Exception("Departure and arrival teams cannot be the same.");
    }

    $stmtArrival = $pdo->prepare("SELECT * FROM teames WHERE id = :id");
    $stmtArrival->execute([':id' => $arrivalTeamId]);
    $arrivalTeamData = $stmtArrival->fetch();

    if (!$arrivalTeamData) {
        throw new Exception("Arrival team not found.");
    }

    if ($arrivalTeamData['budget'] < $amount) {
        throw new Exception("The arrival team does not have enough budget for this transfer.");
    }

    $stmtUpdateArrival = $pdo->prepare("UPDATE teames SET budget = budget - :amount WHERE id = :id");
    $stmtUpdateArrival->execute([':amount' => $amount, ':id' => $arrivalTeamId]);

    if ($departureTeamId) {
        $stmtUpdateDeparture = $pdo->prepare("UPDATE teames SET budget = budget + :amount WHERE id = :id");
        $stmtUpdateDeparture->execute([':amount' => $amount, ':id' => $departureTeamId]);
    }

    $stmtUpdateContract = $pdo->prepare("UPDATE contracts SET team_id = :team_id WHERE persons_id = :player_id");
    $stmtUpdateContract->execute([':team_id' => $arrivalTeamId, ':player_id' => $playerId]);

    $reference = 'TRF-' . strtoupper(bin2hex(random_bytes(4)));
    $sqlTransfer = "INSERT INTO transfers (reference, persons_id, departure_team_id, arrival_team_id, amount, status) 
                    VALUES (:reference, :player_id, :departure_id, :arrival_id, :amount, 'completed')";
    
    $stmtTransfer = $pdo->prepare($sqlTransfer);
    $stmtTransfer->execute([
        ':reference' => $reference,
        ':player_id' => $playerId,
        ':departure_id' => $departureTeamId,
        ':arrival_id' => $arrivalTeamId,
        ':amount' => $amount
    ]);

    $pdo->commit();

    $_SESSION['message'] = "✓ Transfer successful! Reference: $reference";
    $_SESSION['message_type'] = "success";
    header('Location: ../admin_dashboard.php');
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    $_SESSION['message'] = "❌ Transfer failed: " . $e->getMessage();
    $_SESSION['message_type'] = "error";
    header('Location: ../views/form_transfer.php?error=' . urlencode($e->getMessage()));
    exit;
}
