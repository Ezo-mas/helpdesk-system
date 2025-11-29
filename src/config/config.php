<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Application-wide configuration settings
define('APP_NAME', 'HelpDesk Sistema');
define('APP_ENV', 'development');
define('APP_DEBUG', true);
define('APP_URL', 'http://localhost');

// Database configuration
define('DB_HOST', 'mysql'); // Changed to 'mysql' for Docker, use 'localhost' for local Apache
define('DB_PORT', '3306');
define('DB_NAME', 'helpdesk');
define('DB_USER', 'root');
define('DB_PASS', 'password');

// Laiko zona
date_default_timezone_set('Europe/Vilnius');

// Sistemos nustatymai
define('SITE_NAME', 'HelpDesk Sistema');
define('BASE_URL', 'http://localhost');

// Užklausų būsenos
define('STATUS_NEW', 'naujas');
define('STATUS_IN_PROGRESS', 'vykdomas');
define('STATUS_WAITING', 'laukiama');
define('STATUS_CLOSED', 'uždarytas');

// Prioritetai
define('PRIORITY_LOW', 'žemas');
define('PRIORITY_MEDIUM', 'vidutinis');
define('PRIORITY_HIGH', 'aukštas');

// Rolės (User roles)
define('ROLE_ADMIN', 'admin');
define('ROLE_STAFF', 'staff');
define('ROLE_SUPPORT', 'staff'); // Alias for compatibility
define('ROLE_USER', 'user');

// Pagalbinės funkcijos
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /login');
        exit();
    }
}

function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

function requireRole($role) {
    requireLogin();
    if (!hasRole($role)) {
        header('Location: /index.php');
        exit();
    }
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function setMessage($message, $type = 'info') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
}

function getMessage() {
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        $type = $_SESSION['message_type'] ?? 'info';
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}

function escape($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    require_once __DIR__ . '/database.php';
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}
?>