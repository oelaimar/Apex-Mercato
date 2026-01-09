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

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $nationality = trim($_POST['nationality']);
    $nickname = trim($_POST['nickname']);
    $role = $_POST['role'];
    $marketValue = (float) $_POST['market_value'];
    
    $teamId = (int) $_POST['team_id'];
    $salary = (float) $_POST['salary'];
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    $buyoutClause = !empty($_POST['buyout_clause']) ? (float) $_POST['buyout_clause'] : null;

    $player = new Player(
        $name,
        $email,
        $nationality,
        $nickname,
        $role,
        $marketValue,
        $salary
    );

    $sqlPerson = "INSERT INTO persons (type, name, email, nationality) 
                  VALUES ('player', :name, :email, :nationality)";
    $stmtPerson = $pdo->prepare($sqlPerson);
    $stmtPerson->execute([
        ':name' => $player->getName(),
        ':email' => $player->getEmail(),
        ':nationality' => $player->getNationality()
    ]);
    
    $personId = (int) $pdo->lastInsertId();

    $sqlPlayer = "INSERT INTO players (persons_id, nickname, role, market_value) 
                  VALUES (:persons_id, :nickname, :role, :market_value)";
    $stmtPlayer = $pdo->prepare($sqlPlayer);
    $stmtPlayer->execute([
        ':persons_id' => $personId,
        ':nickname' => $player->getPseudo(),
        ':role' => $player->getRole(),
        ':market_value' => $player->getMarketValue()
    ]);

    $contract = new Contract(
        $teamId,
        $salary,
        $endDate,
        $buyoutClause,
        $startDate
    );

    if (!$contract->isActive()) {
        throw new Exception("The contract end date must be a future date.");
    }

    $sqlContract = "INSERT INTO contracts (uuid, persons_id, team_id, salary, buyout, end_date)
                    VALUES (:uuid, :persons_id, :team_id, :salary, :buyout, :end_date)";
    
    $stmtContract = $pdo->prepare($sqlContract);
    $stmtContract->execute([
        ':uuid' => $contract->uuid,
        ':persons_id' => $personId,
        ':team_id' => $teamId,
        ':salary' => $salary,
        ':buyout' => $buyoutClause,
        ':end_date' => $endDate
    ]);

    $pdo->commit();
    
    $_SESSION['message'] = "✓ player '{$player->getPseudo()}' Successfully created!";
    $_SESSION['message_type'] = "success";
    header('Location: ../admin_dashboard.php');
    exit;
    
} catch (InvalidArgumentException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    $_SESSION['message'] = "❌ Validation failed: " . $e->getMessage();
    $_SESSION['message_type'] = "error";
    header('Location: ../views/form_create_player.php?error=' . urlencode($e->getMessage()));
    exit;
} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    $errorMsg = ($e->getCode() == 23000) ? "The email address is already registered" : $e->getMessage();
    $_SESSION['message'] = "❌ " . $errorMsg;
    $_SESSION['message_type'] = "error";
    header('Location: ../views/form_create_player.php?error=' . urlencode($errorMsg));
    exit;
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    $_SESSION['message'] = "❌ " . $e->getMessage();
    $_SESSION['message_type'] = "error";
    header('Location: ../views/form_create_player.php?error=' . urlencode($e->getMessage()));
    exit;
}