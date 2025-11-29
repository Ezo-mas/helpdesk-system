<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('/');
}

$error = '';
$success = '';

if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
    $success = 'Sėkmingai atsijungėte!';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Prašome užpildyti visus laukus';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                
                redirect('/');
            } else {
                $error = 'Neteisingas vartotojo vardas arba slaptažodis';
            }
        } catch (PDOException $e) {
            $error = 'Prisijungimo klaida. Bandykite dar kartą.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prisijungimas - HelpDesk Sistema</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="icon" href="data:,">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h2>Prisijungimas</h2>
            <p>Prašome prisijungti prie savo paskyros</p>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo escape($success); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo escape($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Vartotojo vardas arba el. paštas</label>
                    <input type="text" 
                           id="username" 
                           name="username" 
                           class="form-control" 
                           value="<?php echo escape($_POST['username'] ?? ''); ?>"
                           required 
                           autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password">Slaptažodis</label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-control" 
                           required>
                </div>
                
                <button type="submit" class="btn btn-primary">Prisijungti</button>
            </form>
            
            <div class="auth-links">
                <p>Neturite paskyros? <a href="/register">Registruotis</a></p>
            </div>
        </div>
    </div>
</body>
</html>