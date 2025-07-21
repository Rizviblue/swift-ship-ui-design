<?php
session_start();
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/auth.php';

// Check if user is logged in, if not redirect to login
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$userRole = $_SESSION['user_role'];
$userName = $_SESSION['user_name'];
$userEmail = $_SESSION['user_email'];

// Redirect based on role
switch ($userRole) {
    case 'admin':
        header('Location: admin/dashboard.php');
        break;
    case 'agent':
        header('Location: agent/dashboard.php');
        break;
    case 'customer':
        header('Location: customer/track.php');
        break;
    default:
        header('Location: login.php');
}
exit();
?>