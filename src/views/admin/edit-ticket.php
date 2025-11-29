<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

requireLogin();
requireRole(ROLE_ADMIN);

$ticket_id = $_GET['id'] ?? 0;
$error = '';
$success = '';

// Get ticket details
try {
    $stmt = $pdo->prepare("
        SELECT t.*, 
               u.full_name as creator_name,
               u.email as creator_email,
               s.full_name as staff_name
        FROM tickets t
        JOIN users u ON t.user_id = u.id
        LEFT JOIN users s ON t.assigned_to = s.id
        WHERE t.id = ?
    ");
    $stmt->execute([$ticket_id]);
    $ticket = $stmt->fetch();
    
    if (!$ticket) {
        setMessage('Užklausa nerasta', 'danger');
        redirect('/admin/tickets');
    }
} catch (PDOException $e) {
    setMessage('Klaida gaunant užklausą', 'danger');
    redirect('/admin/tickets');
}

// Get all staff members
try {
    $stmt = $pdo->query("SELECT id, full_name FROM users WHERE role IN ('staff', 'admin') ORDER BY full_name");
    $staff_members = $stmt->fetchAll();
} catch (PDOException $e) {
    $staff_members = [];
}

// Handle assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_ticket'])) {
    $assigned_to = $_POST['assigned_to'] === '' ? null : intval($_POST['assigned_to']);
    
    try {
        $stmt = $pdo->prepare("UPDATE tickets SET assigned_to = ? WHERE id = ?");
        $stmt->execute([$assigned_to, $ticket_id]);
        
        setMessage('Užklausa priskirta', 'success');
        redirect('/admin/edit-ticket?id=' . $ticket_id);
    } catch (PDOException $e) {
        $error = 'Klaida priskiriant užklausą';
    }
}

// Handle status change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_status'])) {
    $new_status = $_POST['status'] ?? '';
    
    if (in_array($new_status, ['naujas', 'vykdomas', 'laukiama', 'uždarytas'])) {
        try {
            if ($new_status === 'uždarytas') {
                $stmt = $pdo->prepare("UPDATE tickets SET status = ?, closed_at = NOW() WHERE id = ?");
            } else {
                $stmt = $pdo->prepare("UPDATE tickets SET status = ? WHERE id = ?");
            }
            $stmt->execute([$new_status, $ticket_id]);
            
            setMessage('Būsena pakeista', 'success');
            redirect('/admin/edit-ticket?id=' . $ticket_id);
        } catch (PDOException $e) {
            $error = 'Klaida keičiant būseną';
        }
    }
}

include __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="card">
        <h2>Valdyti užklausą #<?php echo $ticket['id']; ?></h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo escape($error); ?></div>
        <?php endif; ?>
        
        <div style="margin-bottom: 20px;">
            <h3><?php echo escape($ticket['title']); ?></h3>
            <p><strong>Klientas:</strong> <?php echo escape($ticket['creator_name']); ?> (<?php echo escape($ticket['creator_email']); ?>)</p>
            <p><strong>Sukurta:</strong> <?php echo date('Y-m-d H:i', strtotime($ticket['created_at'])); ?></p>
            <p><strong>Būsena:</strong> 
                <span class="badge badge-<?php 
                    echo $ticket['status'] === 'naujas' ? 'new' : 
                        ($ticket['status'] === 'vykdomas' ? 'in-progress' : 
                        ($ticket['status'] === 'laukiama' ? 'waiting' : 'closed')); 
                ?>">
                    <?php echo ucfirst(escape($ticket['status'])); ?>
                </span>
            </p>
            <p><strong>Prioritetas:</strong> 
                <span class="badge priority-<?php echo $ticket['priority']; ?>">
                    <?php echo ucfirst(escape($ticket['priority'])); ?>
                </span>
            </p>
        </div>
        
        <div style="padding: 20px; background: #f8f9fa; border-radius: 6px; margin-bottom: 20px;">
            <h4>Aprašymas:</h4>
            <p><?php echo nl2br(escape($ticket['description'])); ?></p>
        </div>
        
        <!-- Assignment Form -->
        <form method="POST" style="margin-bottom: 20px;">
            <div class="form-group">
                <label for="assigned_to"><strong>Priskirti darbuotojui:</strong></label>
                <div style="display: flex; gap: 10px;">
                    <select name="assigned_to" id="assigned_to" class="form-control" style="max-width: 300px;">
                        <option value="">-- Nepriskirta --</option>
                        <?php foreach ($staff_members as $staff): ?>
                            <option value="<?php echo $staff['id']; ?>" 
                                    <?php echo $ticket['assigned_to'] == $staff['id'] ? 'selected' : ''; ?>>
                                <?php echo escape($staff['full_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="assign_ticket" class="btn btn-primary">Priskirti</button>
                </div>
            </div>
        </form>
        
        <!-- Status Change Form -->
        <form method="POST">
            <div class="form-group">
                <label for="status"><strong>Keisti būseną:</strong></label>
                <div style="display: flex; gap: 10px;">
                    <select name="status" id="status" class="form-control" style="max-width: 300px;">
                        <option value="naujas" <?php echo $ticket['status'] === 'naujas' ? 'selected' : ''; ?>>Naujas</option>
                        <option value="vykdomas" <?php echo $ticket['status'] === 'vykdomas' ? 'selected' : ''; ?>>Vykdomas</option>
                        <option value="laukiama" <?php echo $ticket['status'] === 'laukiama' ? 'selected' : ''; ?>>Laukiama</option>
                        <option value="uždarytas" <?php echo $ticket['status'] === 'uždarytas' ? 'selected' : ''; ?>>Uždarytas</option>
                    </select>
                    <button type="submit" name="change_status" class="btn btn-warning">Pakeisti būseną</button>
                </div>
            </div>
        </form>
    </div>
    
    <a href="/admin/tickets" class="btn btn-secondary">← Grįžti į sąrašą</a>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>