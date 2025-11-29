<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

// Require user to be logged in
requireLogin();

// Only regular users can create tickets (staff and admin can too, but this is user view)
if (!hasRole(ROLE_USER) && !hasRole(ROLE_STAFF) && !hasRole(ROLE_ADMIN)) {
    redirect('/');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $priority = $_POST['priority'] ?? 'vidutinis';
    
    // Validation
    if (empty($title) || empty($description)) {
        $error = 'Prašome užpildyti visus laukus';
    } elseif (strlen($title) < 5) {
        $error = 'Pavadinimas turi būti bent 5 simbolių ilgio';
    } elseif (strlen($description) < 10) {
        $error = 'Aprašymas turi būti bent 10 simbolių ilgio';
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO tickets (user_id, title, description, priority, status) 
                VALUES (?, ?, ?, ?, 'naujas')
            ");
            $stmt->execute([$_SESSION['user_id'], $title, $description, $priority]);
            
            setMessage('Užklausa sėkmingai pateikta!', 'success');
            redirect('/user/my-tickets');
        } catch (PDOException $e) {
            $error = 'Klaida kuriant užklausą. Bandykite vėliau.';
            if (APP_DEBUG) {
                $error .= ' (' . $e->getMessage() . ')';
            }
        }
    }
}

include __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Nauja užklausa</h2>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo escape($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo escape($success); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="title">Pavadinimas *</label>
                <input type="text" id="title" name="title" class="form-control" 
                       value="<?php echo escape($_POST['title'] ?? ''); ?>" 
                       placeholder="Trumpas problemos aprašymas" required>
            </div>
            
            <div class="form-group">
                <label for="priority">Prioritetas *</label>
                <select id="priority" name="priority" class="form-control" required>
                    <option value="žemas" <?php echo (($_POST['priority'] ?? '') === 'žemas') ? 'selected' : ''; ?>>Žemas</option>
                    <option value="vidutinis" <?php echo (($_POST['priority'] ?? 'vidutinis') === 'vidutinis') ? 'selected' : ''; ?>>Vidutinis</option>
                    <option value="aukštas" <?php echo (($_POST['priority'] ?? '') === 'aukštas') ? 'selected' : ''; ?>>Aukštas</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="description">Detalus aprašymas *</label>
                <textarea id="description" name="description" class="form-control" 
                          placeholder="Aprašykite problemą kuo išsamiau..." 
                          required><?php echo escape($_POST['description'] ?? ''); ?></textarea>
            </div>
            
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary">Pateikti užklausą</button>
                <a href="/user/my-tickets" class="btn btn-secondary">Atšaukti</a>
            </div>
        </form>
    </div>
    
    <div class="card">
        <h3>Patarimai</h3>
        <ul>
            <li>Aprašykite problemą kuo išsamiau</li>
            <li>Nurodykite, kokių veiksmų atlikote prieš atsiradant problemai</li>
            <li>Jei įmanoma, pridėkite ekrano kopijas ar klaidos pranešimus</li>
            <li>Tiksliai nurodykite, kokio rezultato tikitės</li>
        </ul>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>