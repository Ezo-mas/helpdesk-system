<?php
require_once '../src/config/config.php';
require_once '../src/config/database.php';
require_once '../src/controllers/AuthController.php';
require_once '../src/controllers/TicketController.php';
require_once '../src/controllers/AdminController.php';
require_once '../src/controllers/UserController.php';

// Start session
session_start();

// Routing logic
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Simple routing
if ($requestUri === '/login' && $requestMethod === 'GET') {
    require '../src/views/auth/login.php';
} elseif ($requestUri === '/register' && $requestMethod === 'GET') {
    require '../src/views/auth/register.php';
} elseif ($requestUri === '/dashboard' && $requestMethod === 'GET') {
    (new AdminController())->dashboard();
} elseif ($requestUri === '/tickets' && $requestMethod === 'GET') {
    (new TicketController())->index();
} elseif (preg_match('/^\/my-tickets$/', $requestUri) && $requestMethod === 'GET') {
    (new UserController())->myTickets();
} else {
    // Default to home or 404
    http_response_code(404);
    echo "404 Not Found";
}
?>