<?php
if (!isset($currentUser)) {
    $currentUser = getCurrentUser();
}
?>
<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'HelpDesk Sistema'; ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="icon" href="data:,">
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <a href="/" style="color: white; text-decoration: none;">
                    <?php echo SITE_NAME; ?>
                </a>
            </div>
            
            <nav>
                <ul>
                    <?php if (hasRole(ROLE_ADMIN)): ?>
                        <li><a href="/admin/dashboard" <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/dashboard') !== false) ? 'class="active"' : ''; ?>>Dashboard</a></li>
                        <li><a href="/admin/users" <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/users') !== false) ? 'class="active"' : ''; ?>>Vartotojai</a></li>
                        <li><a href="/admin/tickets" <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/tickets') !== false) ? 'class="active"' : ''; ?>>Užklausos</a></li>
                    <?php elseif (hasRole(ROLE_STAFF)): ?>
                        <li><a href="/staff/tickets" <?php echo (strpos($_SERVER['REQUEST_URI'], '/staff/tickets') !== false) ? 'class="active"' : ''; ?>>Užklausos</a></li>
                    <?php else: ?>
                        <li><a href="/user/my-tickets" <?php echo (strpos($_SERVER['REQUEST_URI'], '/user/my-tickets') !== false) ? 'class="active"' : ''; ?>>Mano užklausos</a></li>
                        <li><a href="/user/create-ticket" <?php echo (strpos($_SERVER['REQUEST_URI'], '/user/create-ticket') !== false) ? 'class="active"' : ''; ?>>Sukurti užklausą</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            
            <div class="user-info">
                <span class="username"><?php echo escape($currentUser['full_name']); ?></span>
                <span class="role-badge"><?php echo ucfirst(escape($currentUser['role'])); ?></span>
                <a href="/logout" class="btn btn-sm btn-secondary">Atsijungti</a>
            </div>
        </div>
    </header>

    <main>