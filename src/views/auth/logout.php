<?php
require_once __DIR__ . '/../../config/config.php';

// Destroy all session data
session_unset();
session_destroy();

// Redirect to login page with a message
header('Location: /src/views/auth/login.php?logout=success');
exit();
?>