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
                $_SESSION['full_name'] = $user['full_name'];
                
                setMessage('Sėkmingai prisijungėte!', 'success');
                redirect('/');
            } else {
                $error = 'Neteisingas vartotojo vardas arba slaptažodis';
            }
        } catch (PDOException $e) {
            $error = 'Prisijungimo klaida. Bandykite vėliau.';
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
    <title>Prisijungimas - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h2><?php echo SITE_NAME; ?></h2>
            <h3>Prisijungimas</h3>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo escape($success); ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo escape($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Vartotojo vardas arba el. paštas</label>
                    <input type="text" id="username" name="username" class="form-control" 
                           value="<?php echo escape($_POST['username'] ?? ''); ?>" required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password">Slaptažodis</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Prisijungti</button>
            </form>
            
            <p style="text-align: center; margin-top: 20px;">
                Neturite paskyros? <a href="register.php">Registruotis</a>
            </p>
            
            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
                <p style="font-size: 12px; color: #7f8c8d; text-align: center;">
                    <strong>Testiniai prisijungimo duomenys:</strong><br>
                    Admin: admin / password123<br>
                    Darbuotojas: darbuotojas1 / password123<br>
                    Vartotojas: vartotojas1 / password123
                </p>
            </div>
        </div>
    </div>
</body>
</html>