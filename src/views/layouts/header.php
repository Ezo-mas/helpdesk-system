<?php
require_once __DIR__ . '/../../config/config.php';

$currentUser = null;
if (isLoggedIn()) {
    $currentUser = getCurrentUser();
}

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <a href="/" style="color: white; text-decoration: none;"><?php echo SITE_NAME; ?></a>
            </div>
            
            <?php if ($currentUser): ?>
                <nav>
                    <ul>
                        <?php if (hasRole(ROLE_ADMIN)): ?>
                            <li><a href="/src/views/admin/dashboard.php" class="<?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">Valdymo skydas</a></li>
                            <li><a href="/src/views/admin/users.php">Vartotojai</a></li>
                            <li><a href="/src/views/admin/tickets.php">Visos užklausos</a></li>
                        <?php elseif (hasRole(ROLE_STAFF)): ?>
                            <li><a href="/src/views/staff/tickets.php" class="<?php echo $currentPage === 'tickets.php' ? 'active' : ''; ?>">Mano užklausos</a></li>
                        <?php else: ?>
                            <li><a href="/src/views/user/my-tickets.php" class="<?php echo $currentPage === 'my-tickets.php' ? 'active' : ''; ?>">Mano užklausos</a></li>
                            <li><a href="/src/views/user/create-ticket.php">Nauja užklausa</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
                
                <div class="user-info">
                    <span class="username"><?php echo escape($currentUser['full_name'] ?? $currentUser['username']); ?></span>
                    <span class="role-badge">
                        <?php 
                        if (hasRole(ROLE_ADMIN)) echo 'Administratorius';
                        elseif (hasRole(ROLE_STAFF)) echo 'Darbuotojas';
                        else echo 'Vartotojas';
                        ?>
                    </span>
                    <a href="/src/views/auth/logout.php" class="btn btn-sm btn-danger">Atsijungti</a>
                </div>
            <?php else: ?>
                <nav>
                    <ul>
                        <li><a href="/src/views/auth/login.php">Prisijungti</a></li>
                        <li><a href="/src/views/auth/register.php">Registruotis</a></li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </header>
    
    <?php
    // Display flash messages
    $message = getMessage();
    if ($message):
    ?>
        <div class="container" style="margin-top: 20px;">
            <div class="alert alert-<?php echo $message['type']; ?>">
                <?php echo escape($message['message']); ?>
            </div>
        </div>
    <?php endif; ?>