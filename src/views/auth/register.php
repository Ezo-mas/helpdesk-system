<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('/');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');
    
    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
        $error = 'Prašome užpildyti visus laukus';
    } elseif ($password !== $confirm_password) {
        $error = 'Slaptažodžiai nesutampa';
    } elseif (strlen($password) < 6) {
        $error = 'Slaptažodis turi būti bent 6 simbolių ilgio';
    } else {
        try {
            // Check if username exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $error = 'Toks vartotojo vardas jau užimtas';
            } else {
                // Check if email exists
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $error = 'Toks el. paštas jau užregistruotas';
                } else {
                    // Create user
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("
                        INSERT INTO users (username, email, password, full_name, role) 
                        VALUES (?, ?, ?, ?, 'user')
                    ");
                    $stmt->execute([$username, $email, $password_hash, $full_name]);
                    
                    setMessage('Registracija sėkminga! Galite prisijungti.', 'success');
                    redirect('/login');
                }
            }
        } catch (PDOException $e) {
            $error = 'Registracijos klaida. Bandykite dar kartą.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registracija - HelpDesk Sistema</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="icon" href="data:,">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h2>Registracija</h2>
            <p>Sukurkite naują paskyrą</p>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo escape($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="full_name">Vardas ir pavardė</label>
                    <input type="text" 
                           id="full_name" 
                           name="full_name" 
                           class="form-control" 
                           value="<?php echo escape($_POST['full_name'] ?? ''); ?>"
                           required 
                           autofocus>
                </div>
                
                <div class="form-group">
                    <label for="username">Vartotojo vardas</label>
                    <input type="text" 
                           id="username" 
                           name="username" 
                           class="form-control" 
                           value="<?php echo escape($_POST['username'] ?? ''); ?>"
                           required>
                </div>
                
                <div class="form-group">
                    <label for="email">El. paštas</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="form-control" 
                           value="<?php echo escape($_POST['email'] ?? ''); ?>"
                           required>
                </div>
                
                <div class="form-group">
                    <label for="password">Slaptažodis</label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-control" 
                           required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Pakartokite slaptažodį</label>
                    <input type="password" 
                           id="confirm_password" 
                           name="confirm_password" 
                           class="form-control" 
                           required>
                </div>
                
                <button type="submit" class="btn btn-primary">Registruotis</button>
            </form>
            
            <div class="auth-links">
                <p>Jau turite paskyrą? <a href="/login">Prisijungti</a></p>
            </div>
        </div>
    </div>
</body>
</html>