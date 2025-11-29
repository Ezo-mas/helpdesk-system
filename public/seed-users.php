<?php
require_once __DIR__ . '/../src/config/config.php';
require_once __DIR__ . '/../src/config/database.php';

// Generate fresh password hash
$password_hash = password_hash('password123', PASSWORD_DEFAULT);

try {
    // Update all existing users with the fresh hash
    $stmt = $pdo->prepare("UPDATE users SET password = ?");
    $stmt->execute([$password_hash]);
    
    echo " All user passwords updated successfully!<br>";
    echo " All accounts now use password: <strong>password123</strong><br><br>";
    
    // Show all users
    $stmt = $pdo->query("SELECT id, username, email, role FROM users");
    $users = $stmt->fetchAll();
    
    echo "<h3>Test Accounts:</h3>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Password</th></tr>";
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>{$user['id']}</td>";
        echo "<td>{$user['username']}</td>";
        echo "<td>{$user['email']}</td>";
        echo "<td>{$user['role']}</td>";
        echo "<td>password123</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<br><br><a href='/login'>Go to Login Page</a>";
    
} catch (PDOException $e) {
    echo " Error: " . $e->getMessage();
}
?>