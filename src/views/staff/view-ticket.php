<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

requireLogin();
requireRole(ROLE_STAFF);

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
        LEFT JOIN users u ON t.user_id = u.id
        LEFT JOIN users s ON t.assigned_to = s.id
        WHERE t.id = ? AND t.assigned_to = ?
    ");
    $stmt->execute([$ticket_id, $_SESSION['user_id']]);
    $ticket = $stmt->fetch();
    
    if (!$ticket) {
        setMessage('Užklausa nerasta arba neturite teisės ją peržiūrėti', 'danger');
        redirect('/src/views/staff/tickets.php');
    }
} catch (PDOException $e) {
    setMessage('Klaida gaunant užklausą', 'danger');
    redirect('/src/views/staff/tickets.php');
}

// Get comments
try {
    $stmt = $pdo->prepare("
        SELECT c.*, u.full_name, u.role
        FROM comments c
        JOIN users u ON c.user_id = u.id
        WHERE c.ticket_id = ?
        ORDER BY c.created_at ASC
    ");
    $stmt->execute([$ticket_id]);
    $comments = $stmt->fetchAll();
} catch (PDOException $e) {
    $comments = [];
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
            redirect('/src/views/staff/view-ticket.php?id=' . $ticket_id);
        } catch (PDOException $e) {
            $error = 'Klaida keičiant būseną';
        }
    }
}

// Handle new comment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    $comment = trim($_POST['comment'] ?? '');
    $is_internal = isset($_POST['is_internal']) ? 1 : 0;
    
    if (empty($comment)) {
        $error = 'Komentaras negali būti tuščias';
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO comments (ticket_id, user_id, comment, is_internal) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$ticket_id, $_SESSION['user_id'], $comment, $is_internal]);
            
            setMessage('Komentaras pridėtas', 'success');
            redirect('/src/views/staff/view-ticket.php?id=' . $ticket_id);
        } catch (PDOException $e) {
            $error = 'Klaida pridedant komentarą';
        }
    }
}

include __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px;">
            <div>
                <h2>Užklausa #<?php echo $ticket['id']; ?>: <?php echo escape($ticket['title']); ?></h2>
                <p style="color: #7f8c8d; margin-top: 5px;">
                    <strong>Klientas:</strong> <?php echo escape($ticket['creator_name']); ?> 
                    (<?php echo escape($ticket['creator_email']); ?>)<br>
                    <strong>Sukurta:</strong> <?php echo date('Y-m-d H:i', strtotime($ticket['created_at'])); ?>
                </p>
            </div>
            <div style="text-align: right;">
                <span class="badge priority-<?php echo $ticket['priority']; ?>">
                    Prioritetas: <?php echo ucfirst(escape($ticket['priority'])); ?>
                </span>
            </div>
        </div>
        
        <div style="padding: 20px; background: #f8f9fa; border-radius: 6px; margin-bottom: 20px;">
            <h4>Aprašymas:</h4>
            <p><?php echo nl2br(escape($ticket['description'])); ?></p>
        </div>
        
        <!-- Status Change Form -->
        <form method="POST" style="margin-top: 20px;">
            <div class="form-group">
                <label for="status"><strong>Keisti būseną:</strong></label>
                <div style="display: flex; gap: 10px;">
                    <select name="status" id="status" class="form-control" style="max-width: 300px;">
                        <option value="naujas" <?php echo $ticket['status'] === 'naujas' ? 'selected' : ''; ?>>Naujas</option>
                        <option value="vykdomas" <?php echo $ticket['status'] === 'vykdomas' ? 'selected' : ''; ?>>Vykdomas</option>
                        <option value="laukiama" <?php echo $ticket['status'] === 'laukiama' ? 'selected' : ''; ?>>Laukiama</option>
                        <option value="uždarytas" <?php echo $ticket['status'] === 'uždarytas' ? 'selected' : ''; ?>>Uždarytas</option>
                    </select>
                    <button type="submit" name="change_status" class="btn btn-primary">Pakeisti</button>
                </div>
            </div>
        </form>
        
        <?php if ($ticket['status'] === 'uždarytas'): ?>
            <div style="margin-top: 20px; padding: 15px; background: #d4edda; border-left: 4px solid #28a745; border-radius: 4px;">
                <strong>Užklausa uždaryta:</strong> <?php echo date('Y-m-d H:i', strtotime($ticket['closed_at'])); ?>
                <?php if ($ticket['rating']): ?>
                    <p style="margin-top: 10px;">
                        <strong>Kliento įvertinimas:</strong> 
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span style="color: <?php echo $i <= $ticket['rating'] ? '#f39c12' : '#ddd'; ?>; font-size: 20px;">★</span>
                        <?php endfor; ?>
                        (<?php echo $ticket['rating']; ?>/5)
                    </p>
                <?php else: ?>
                    <p style="margin-top: 10px; color: #7f8c8d;">
                        <em>Klientas dar neįvertino</em>
                    </p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Comments Section -->
    <div class="card">
        <h3>Komunikacija su klientu (<?php echo count($comments); ?>)</h3>
        
        <?php if (empty($comments)): ?>
            <p style="color: #7f8c8d;">Dar nėra komentarų</p>
        <?php else: ?>
            <?php foreach ($comments as $comment): ?>
                <div class="comment <?php echo $comment['role'] === 'staff' || $comment['role'] === 'admin' ? 'staff-comment' : ''; ?>">
                    <div class="comment-header">
                        <span class="comment-author">
                            <?php echo escape($comment['full_name']); ?>
                            <?php if ($comment['role'] === 'staff' || $comment['role'] === 'admin'): ?>
                                <span class="badge badge-success" style="font-size: 10px; margin-left: 5px;">Darbuotojas</span>
                            <?php endif; ?>
                            <?php if ($comment['is_internal']): ?>
                                <span class="badge badge-warning" style="font-size: 10px; margin-left: 5px;">Vidinis</span>
                            <?php endif; ?>
                        </span>
                        <span class="comment-date"><?php echo date('Y-m-d H:i', strtotime($comment['created_at'])); ?></span>
                    </div>
                    <div class="comment-body">
                        <?php echo nl2br(escape($comment['comment'])); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <div style="margin-top: 20px; padding-top: 20px; border-top: 2px solid #e0e6ed;">
            <h4>Atsakyti klientui</h4>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo escape($error); ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <textarea name="comment" class="form-control" 
                              placeholder="Jūsų atsakymas klientui..." 
                              required></textarea>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_internal">
                        Vidinis komentaras (nematomas klientui)
                    </label>
                </div>
                <button type="submit" name="add_comment" class="btn btn-primary">Siųsti atsakymą</button>
            </form>
        </div>
    </div>
    
    <a href="/src/views/staff/tickets.php" class="btn btn-secondary">← Grįžti į sąrašą</a>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>