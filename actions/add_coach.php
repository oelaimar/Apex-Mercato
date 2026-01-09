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
    $styleCoaching = trim($_POST['style_coaching']);
    $yearsExperience = (int) $_POST['years_experience'];
    
    $teamId = (int) $_POST['team_id'];
    $salary = (float) $_POST['salary'];
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    $buyoutClause = !empty($_POST['buyout_clause']) ? (float) $_POST['buyout_clause'] : null;

    $coach = new Coach(
        $name,
        $email,
        $nationality,
        $styleCoaching,
        $yearsExperience,
        $salary
    );

    $sqlPerson = "INSERT INTO persons (type, name, email, nationality) 
                  VALUES ('coach', :name, :email, :nationality)";
    $stmtPerson = $pdo->prepare($sqlPerson);
    $stmtPerson->execute([
        ':name' => $coach->getName(),
        ':email' => $coach->getEmail(),
        ':nationality' => $coach->getNationality()
    ]);
    
    $personId = (int) $pdo->lastInsertId();

    $sqlCoach = "INSERT INTO coaches (persons_id, coaching_style, years_of_experience) 
                  VALUES (:persons_id, :coaching_style, :years_of_experience)";
    $stmtCoach = $pdo->prepare($sqlCoach);
    $stmtCoach->execute([
        ':persons_id' => $personId,
        ':coaching_style' => $coach->getCoachingStyle(),
        ':years_of_experience' => $coach->getYearsExperience()
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
    
    $_SESSION['message'] = "✓ coach '{$coach->getName()}' Successfully created!";
    $_SESSION['message_type'] = "success";
    header('Location: ../admin_dashboard.php');
    exit;
    
} catch (InvalidArgumentException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    $_SESSION['message'] = "❌ Validation failed: " . $e->getMessage();
    $_SESSION['message_type'] = "error";
    header('Location: ../views/form_create_coach.php?error=' . urlencode($e->getMessage()));
    exit;
} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    $errorMsg = ($e->getCode() == 23000) ? "The email address is already registered" : $e->getMessage();
    $_SESSION['message'] = "❌ " . $errorMsg;
    $_SESSION['message_type'] = "error";
    header('Location: ../views/form_create_coach.php?error=' . urlencode($errorMsg));
    exit;
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    $_SESSION['message'] = "❌ " . $e->getMessage();
    $_SESSION['message_type'] = "error";
    header('Location: ../views/form_create_coach.php?error=' . urlencode($e->getMessage()));
    exit;
}
