<?php
// Application-wide configuration settings
define('APP_NAME', 'HelpDesk System');
define('APP_ENV', 'development');
define('APP_DEBUG', true);
define('APP_URL', 'http://localhost:8000');

// Database configuration
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'helpdesk');
define('DB_USER', 'root');
define('DB_PASS', 'password');

// User roles
define('ROLE_ADMIN', 'admin');
define('ROLE_SUPPORT', 'support');
define('ROLE_USER', 'user');
?>