<?php
session_start();
require_once __DIR__ . '/../autoload.php';

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
    $pseudo = trim($_POST['pseudo']);
    $role = $_POST['role'];
    $marketValue = (float) $_POST['market_value'];
    
    // 2. Recueil des données du contrat
    $teamId = (int) $_POST['team_id'];
    $salary = (float) $_POST['salary'];
    $endDate = $_POST['end_date'];
    $buyoutClause = !empty($_POST['buyout_clause']) ? (float) $_POST['buyout_clause'] : null;

    // 3. Création de l'objet Player (pour validation via le constructeur/setters)
    // Note: contractId est null pour l'instant car non créé
    $player = new Player(
        $name,
        $email,
        $nationality,
        $pseudo,
        $role,
        $marketValue,
        $salary
    );

    // 4. Insertion dans la table 'persons'
    $sqlPerson = "INSERT INTO persons (type, name, email, nationality) 
                  VALUES ('player', :name, :email, :nationality)";
    $stmtPerson = $pdo->prepare($sqlPerson);
    $stmtPerson->execute([
        ':name' => $player->getName(),
        ':email' => $player->getEmail(),
        ':nationality' => $player->getNationality()
    ]);
    
    $personId = (int) $pdo->lastInsertId();

    // 5. Insertion dans la table 'players'
    $sqlPlayer = "INSERT INTO players (persons_id, nickname, role, market_value) 
                  VALUES (:persons_id, :nickname, :role, :market_value)";
    $stmtPlayer = $pdo->prepare($sqlPlayer);
    $stmtPlayer->execute([
        ':persons_id' => $personId,
        ':nickname' => $player->getPseudo(),
        ':role' => $player->getRole(),
        ':market_value' => $player->getMarketValue()
    ]);

    // 6. Création et insertion du contrat
    $contract = new Contract(
        $teamId,
        $salary,
        $endDate,
        $buyoutClause
    );

    // Validation du contrat
    if (!$contract->isActive()) {
        throw new Exception("La date de fin du contrat doit être dans le futur");
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
    
    $_SESSION['message'] = "✓ Joueur '{$player->getPseudo()}' créé avec succès au sein de l'équipe!";
    $_SESSION['message_type'] = "success";
    
} catch (InvalidArgumentException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    $_SESSION['message'] = "❌ Erreur de validation: " . $e->getMessage();
    $_SESSION['message_type'] = "error";
} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    if ($e->getCode() == 23000) {
        $_SESSION['message'] = "❌ Cet email est déjà utilisé ou erreur d'intégrité";
    } else {
        $_SESSION['message'] = "❌ Erreur de base de données: " . $e->getMessage();
    }
    $_SESSION['message_type'] = "error";
    error_log("Erreur création joueur: " . $e->getMessage());
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    $_SESSION['message'] = "❌ Erreur: " . $e->getMessage();
    $_SESSION['message_type'] = "error";
}

header('Location: ../admin_dashboard.php');
exit;