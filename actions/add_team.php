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
    $name = trim($_POST['name']);
    $budget = (float) $_POST['budget'];
    $manager = trim($_POST['manager']);

    $team = new Team($name, $budget, $manager);

    $sql = "INSERT INTO teames (name, budget, manager) VALUES (:name, :budget, :manager)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':name'    => $team->getName(),
        ':budget'  => $team->getBudget(),
        ':manager' => $team->getManagerName()
    ]);

    $_SESSION['message'] = "✓ Team '{$team->getName()}' Successfully created!";
    $_SESSION['message_type'] = "success";
    header('Location: ../admin_dashboard.php');
    exit;

} catch (InvalidArgumentException $e) {
    header('Location: ../views/form_create_team.php?error=' . urlencode($e->getMessage()));
    exit;
} catch (PDOException $e) {
    $errorMsg = ($e->getCode() == 23000) ? "The Team name is already registered" : $e->getMessage();
    header('Location: ../views/form_create_team.php?error=' . urlencode($errorMsg));
    exit;
} catch (Exception $e) {
    header('Location: ../views/form_create_team.php?error=' . urlencode($e->getMessage()));
    exit;
}
