<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

requireLogin();
requireRole(ROLE_USER);

$ticket_id = $_GET['id'] ?? 0;
$error = '';
$success = '';

// Get ticket details
try {
    $stmt = $pdo->prepare("
        SELECT t.*, 
               u.full_name as creator_name,
               s.full_name as staff_name
        FROM tickets t
        LEFT JOIN users u ON t.user_id = u.id
        LEFT JOIN users s ON t.assigned_to = s.id
        WHERE t.id = ? AND t.user_id = ?
    ");
    $stmt->execute([$ticket_id, $_SESSION['user_id']]);
    $ticket = $stmt->fetch();
    
    if (!$ticket) {
        setMessage('Užklausa nerasta arba neturite teisės ją peržiūrėti', 'danger');
        redirect('/user/my-tickets');
    }
} catch (PDOException $e) {
    setMessage('Klaida gaunant užklausą', 'danger');
    redirect('/user/my-tickets');
}

// Get comments
try {
    $stmt = $pdo->prepare("
        SELECT c.*, u.full_name, u.role
        FROM comments c
        JOIN users u ON c.user_id = u.id
        WHERE c.ticket_id = ? AND c.is_internal = 0
        ORDER BY c.created_at ASC
    ");
    $stmt->execute([$ticket_id]);
    $comments = $stmt->fetchAll();
} catch (PDOException $e) {
    $comments = [];
}

// Handle new comment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    $comment = trim($_POST['comment'] ?? '');
    
    if (empty($comment)) {
        $error = 'Komentaras negali būti tuščias';
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO comments (ticket_id, user_id, comment, is_internal) 
                VALUES (?, ?, ?, 0)
            ");
            $stmt->execute([$ticket_id, $_SESSION['user_id'], $comment]);
            
            setMessage('Komentaras pridėtas', 'success');
            redirect('/user/ticket/' . $ticket_id);
        } catch (PDOException $e) {
            $error = 'Klaida pridedant komentarą';
        }
    }
}

// Handle rating (only if ticket is closed and not rated yet)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_rating'])) {
    $rating = intval($_POST['rating'] ?? 0);
    
    if ($ticket['status'] === 'uždarytas' && $ticket['rating'] === null && $rating >= 1 && $rating <= 5) {
        try {
            $stmt = $pdo->prepare("UPDATE tickets SET rating = ? WHERE id = ?");
            $stmt->execute([$rating, $ticket_id]);
            
            setMessage('Dėkojame už įvertinimą!', 'success');
            redirect('/user/ticket/' . $ticket_id);
        } catch (PDOException $e) {
            $error = 'Klaida išsaugant įvertinimą';
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
                    Sukurta: <?php echo date('Y-m-d H:i', strtotime($ticket['created_at'])); ?>
                </p>
            </div>
            <div style="text-align: right;">
                <span class="badge badge-<?php 
                    echo $ticket['status'] === 'naujas' ? 'new' : 
                        ($ticket['status'] === 'vykdomas' ? 'in-progress' : 
                        ($ticket['status'] === 'laukiama' ? 'waiting' : 'closed')); 
                ?>">
                    <?php echo ucfirst(escape($ticket['status'])); ?>
                </span>
                <span class="badge priority-<?php echo $ticket['priority']; ?>">
                    Prioritetas: <?php echo ucfirst(escape($ticket['priority'])); ?>
                </span>
            </div>
        </div>
        
        <div style="padding: 20px; background: #f8f9fa; border-radius: 6px; margin-bottom: 20px;">
            <h4>Aprašymas:</h4>
            <p><?php echo nl2br(escape($ticket['description'])); ?></p>
        </div>
        
        <?php if ($ticket['assigned_to']): ?>
            <p><strong>Priskirta:</strong> <?php echo escape($ticket['staff_name']); ?></p>
        <?php else: ?>
            <p style="color: #e67e22;"><strong>Būsena:</strong> Dar nepriskirta darbuotojui</p>
        <?php endif; ?>
        
        <?php if ($ticket['status'] === 'uždarytas'): ?>
            <div style="margin-top: 20px; padding: 15px; background: #d4edda; border-left: 4px solid #28a745; border-radius: 4px;">
                <strong>Užklausa uždaryta:</strong> <?php echo date('Y-m-d H:i', strtotime($ticket['closed_at'])); ?>
                
                <?php if ($ticket['rating']): ?>
                    <p style="margin-top: 10px;">
                        <strong>Jūsų įvertinimas:</strong> 
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span style="color: <?php echo $i <= $ticket['rating'] ? '#f39c12' : '#ddd'; ?>; font-size: 20px;">★</span>
                        <?php endfor; ?>
                    </p>
                <?php else: ?>
                    <form method="POST" style="margin-top: 15px;">
                        <p><strong>Įvertinkite pagalbos kokybę:</strong></p>
                        <div style="display: flex; gap: 10px; align-items: center; margin-top: 10px;">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <label style="cursor: pointer;">
                                    <input type="radio" name="rating" value="<?php echo $i; ?>" required style="display: none;">
                                    <span style="font-size: 30px; color: #ddd;" 
                                          onmouseover="this.style.color='#f39c12'" 
                                          onmouseout="if(!this.previousElementSibling.checked) this.style.color='#ddd'">★</span>
                                </label>
                            <?php endfor; ?>
                            <button type="submit" name="submit_rating" class="btn btn-sm btn-primary">Įvertinti</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Comments Section -->
    <div class="card">
        <h3>Komentarai (<?php echo count($comments); ?>)</h3>
        
        <?php if (empty($comments)): ?>
            <p style="color: #7f8c8d;">Dar nėra komentarų</p>
        <?php else: ?>
            <?php foreach ($comments as $comment): ?>
                <div class="comment <?php echo $comment['role'] === 'staff' || $comment['role'] === 'admin' ? 'staff-comment' : ''; ?>">
                    <div class="comment-header">
                        <span class="comment-author">
                            <?php echo escape($comment['full_name']); ?>
                            <?php if ($comment['role'] === 'staff' || $comment['role'] === 'admin'): ?>
                                <span class="badge badge-success" style="font-size: 10px; margin-left: 5px;">Pagalbos darbuotojas</span>
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
        
        <?php if ($ticket['status'] !== 'uždarytas'): ?>
            <div style="margin-top: 20px; padding-top: 20px; border-top: 2px solid #e0e6ed;">
                <h4>Pridėti komentarą</h4>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo escape($error); ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <textarea name="comment" class="form-control" 
                                  placeholder="Jūsų komentaras ar papildoma informacija..." 
                                  required></textarea>
                    </div>
                    <button type="submit" name="add_comment" class="btn btn-primary">Pridėti komentarą</button>
                </form>
            </div>
        <?php else: ?>
            <p style="color: #7f8c8d; margin-top: 20px;">
                <em>Užklausa uždaryta. Nebegalite pridėti komentarų.</em>
            </p>
        <?php endif; ?>
    </div>
    
    <a href="/user/my-tickets" class="btn btn-secondary">← Grįžti į sąrašą</a>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>