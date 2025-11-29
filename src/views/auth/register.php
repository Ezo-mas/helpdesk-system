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
    $full_name = trim($_POST['full_name'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    
    // Validation
    if (empty($username) || empty($email) || empty($full_name) || empty($password)) {
        $error = 'Prašome užpildyti visus laukus';
    } elseif (strlen($password) < 6) {
        $error = 'Slaptažodis turi būti bent 6 simbolių ilgio';
    } elseif ($password !== $password_confirm) {
        $error = 'Slaptažodžiai nesutampa';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Neteisingas el. pašto adresas';
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
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, role) VALUES (?, ?, ?, ?, 'user')");
                    $stmt->execute([$username, $email, $hashed_password, $full_name]);
                    
                    $success = 'Registracija sėkminga! Galite prisijungti.';
                }
            }
        } catch (PDOException $e) {
            $error = 'Registracijos klaida. Bandykite vėliau.';
            if (APP_DEBUG) {
                $error .= ' (' . $e->getMessage() . ')';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registracija - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h2><?php echo SITE_NAME; ?></h2>
            <h3>Registracija</h3>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo escape($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo escape($success); ?>
                    <p><a href="login.php">Eiti į prisijungimą</a></p>
                </div>
            <?php else: ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username">Vartotojo vardas *</label>
                        <input type="text" id="username" name="username" class="form-control" 
                               value="<?php echo escape($_POST['username'] ?? ''); ?>" required autofocus>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">El. paštas *</label>
                        <input type="email" id="email" name="email" class="form-control" 
                               value="<?php echo escape($_POST['email'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="full_name">Vardas ir pavardė *</label>
                        <input type="text" id="full_name" name="full_name" class="form-control" 
                               value="<?php echo escape($_POST['full_name'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Slaptažodis * (mažiausiai 6 simboliai)</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password_confirm">Pakartokite slaptažodį *</label>
                        <input type="password" id="password_confirm" name="password_confirm" class="form-control" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Registruotis</button>
                </form>
                
                <p style="text-align: center; margin-top: 20px;">
                    Jau turite paskyrą? <a href="login.php">Prisijungti</a>
                </p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>