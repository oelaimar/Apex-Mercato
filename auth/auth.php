<?php
session_start();

function requireRole(string|array $roles):void{ 
    if(!isset($_SESSION['role'])){
        // Not logged in
        header('location: auth/login.php?error=you+need+to+log+in');
    }
    
    if (!is_array($roles)) {
        $roles = [$roles];
    }

    if (!in_array($_SESSION['role'], $roles)) {
        // Logged in but not allowed
        switch ($_SESSION['role']) {
        case 'admin':
            header('Location: /admin_dashboard.php');
            break;
        case 'journalist':
            header('Location: /journalist_view.php');
            break;
        default:
            header('Location: login.php');
    }
    exit;
    }

}
