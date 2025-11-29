<?php
require_once __DIR__ . '/../src/config/config.php';
require_once __DIR__ . '/../src/config/database.php';

// Get the request URI and clean it
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestUri = rtrim($requestUri, '/');

// Route: Login page
if ($requestUri === '/src/views/auth/login.php' || $requestUri === '/login') {
    require __DIR__ . '/../src/views/auth/login.php';
    exit;
}

// Route: Register page
if ($requestUri === '/src/views/auth/register.php' || $requestUri === '/register') {
    require __DIR__ . '/../src/views/auth/register.php';
    exit;
}

// Route: Logout
if ($requestUri === '/src/views/auth/logout.php' || $requestUri === '/logout') {
    require __DIR__ . '/../src/views/auth/logout.php';
    exit;
}

// Home page
if ($requestUri === '' || $requestUri === '/') {
    if (isLoggedIn()) {
        // Redirect based on role
        if (hasRole(ROLE_ADMIN)) {
            header('Location: /admin/dashboard');
        } elseif (hasRole(ROLE_STAFF)) {
            header('Location: /staff/tickets');
        } else {
            header('Location: /user/my-tickets');
        }
    } else {
        header('Location: /login');
    }
    exit;
}

// === ADMIN ROUTES ===
if (isLoggedIn() && hasRole(ROLE_ADMIN)) {
    if ($requestUri === '/admin/dashboard' || $requestUri === '/src/views/admin/dashboard.php') {
        require __DIR__ . '/../src/views/admin/dashboard.php';
        exit;
    }
    
    if ($requestUri === '/admin/users' || $requestUri === '/src/views/admin/users.php') {
        require __DIR__ . '/../src/views/admin/users.php';
        exit;
    }
    
    if ($requestUri === '/admin/tickets' || $requestUri === '/src/views/admin/tickets.php') {
        require __DIR__ . '/../src/views/staff/tickets.php';
        exit;
    }
    
    if (preg_match('#^/admin/edit-ticket#', $requestUri) || preg_match('#^/src/views/admin/edit-ticket\.php#', $requestUri)) {
        require __DIR__ . '/../src/views/admin/edit-ticket.php';
        exit;
    }
}

// === STAFF ROUTES ===
if (isLoggedIn() && (hasRole(ROLE_STAFF) || hasRole(ROLE_ADMIN))) {
    if ($requestUri === '/staff/tickets' || $requestUri === '/src/views/staff/tickets.php') {
        require __DIR__ . '/../src/views/staff/tickets.php';
        exit;
    }
    
    if (preg_match('#^/staff/ticket/(\d+)$#', $requestUri, $matches) || 
        preg_match('#^/src/views/staff/view-ticket\.php#', $requestUri)) {
        if (isset($matches[1])) {
            $_GET['id'] = $matches[1];
        }
        require __DIR__ . '/../src/views/staff/view-ticket.php';
        exit;
    }
}

// === USER ROUTES ===
if (isLoggedIn()) {
    if ($requestUri === '/user/my-tickets' || $requestUri === '/src/views/user/my-tickets.php') {
        require __DIR__ . '/../src/views/user/my-tickets.php';
        exit;
    }
    
    if ($requestUri === '/user/create-ticket' || $requestUri === '/src/views/user/create-ticket.php') {
        require __DIR__ . '/../src/views/user/create-ticket.php';
        exit;
    }
    
    if (preg_match('#^/user/ticket/(\d+)$#', $requestUri, $matches) || 
        preg_match('#^/src/views/user/view-ticket\.php#', $requestUri)) {
        if (isset($matches[1])) {
            $_GET['id'] = $matches[1];
        }
        require __DIR__ . '/../src/views/user/view-ticket.php';
        exit;
    }
}

// 404 handler
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Puslapis nerastas</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="container" style="margin-top: 50px; text-align: center;">
        <h1>404 - Puslapis nerastas</h1>
        <p>Atsiprašome, bet puslapis, kurio ieškote, nerastas.</p>
        <p><strong>Requested:</strong> <?php echo htmlspecialchars($requestUri); ?></p>
        <a href="/" class="btn btn-primary">Grįžti į pradžią</a>
    </div>
</body>
</html>