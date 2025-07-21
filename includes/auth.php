<?php
require_once 'config/database.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . 'login.php');
        exit();
    }
}

function requireRole($allowedRoles) {
    requireLogin();
    if (!in_array($_SESSION['user_role'], $allowedRoles)) {
        header('Location: ' . BASE_URL . 'unauthorized.php');
        exit();
    }
}

function login($email, $password) {
    global $db;
    
    $query = "SELECT id, name, email, password, role, status, branch_id FROM users WHERE email = ? AND status = 'active'";
    $stmt = $db->prepare($query);
    $stmt->execute([$email]);
    
    if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_branch_id'] = $user['branch_id'];
            $_SESSION['login_time'] = time();
            
            // Update last login
            $updateQuery = "UPDATE users SET last_login = NOW() WHERE id = ?";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->execute([$user['id']]);
            
            return true;
        }
    }
    return false;
}

function logout() {
    session_destroy();
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'],
        'email' => $_SESSION['user_email'],
        'role' => $_SESSION['user_role'],
        'branch_id' => $_SESSION['user_branch_id'] ?? null
    ];
}

function registerUser($name, $email, $password, $phone = null, $address = null) {
    global $db;
    
    // Check if email already exists
    $checkQuery = "SELECT id FROM users WHERE email = ?";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->execute([$email]);
    
    if ($checkStmt->fetch()) {
        return false; // Email already exists
    }
    
    $hashedPassword = password_hash($password, HASH_ALGO);
    
    $query = "INSERT INTO users (name, email, password, role, phone, address, status, created_at) 
              VALUES (?, ?, ?, 'customer', ?, ?, 'active', NOW())";
    $stmt = $db->prepare($query);
    
    return $stmt->execute([$name, $email, $hashedPassword, $phone, $address]);
}
?>