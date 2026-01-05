<?php
session_start();

// Hardcoded credentials
$USERS = [
    'admin' => ['password' => 'admin', 'role' => 'admin'],
    'journalist' => ['password' => 'journalist', 'role' => 'journalist'],
];

// Get form input
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (isset($USERS[$username]) && $USERS[$username]['password'] === $password) {
    $_SESSION['username'] = $username;
    $_SESSION['role'] = $USERS[$username]['role'];

    // Redirect based on role
    if ($_SESSION['role'] === 'admin') {
        header('Location: ../admin_dashboard.php');
        exit;
    } elseif ($_SESSION['role'] === 'journalist') {
        header('Location: ../journaliste_view.php');
        exit;
    }

} else {
    // Invalid credentials
    header('Location: login.php?error=Invalid+username+or+password');
    exit;
}
