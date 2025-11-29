<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

requireLogin();
requireRole(ROLE_USER);

$currentUser = getCurrentUser();

// Get user's tickets
try {
    $stmt = $pdo->prepare("
        SELECT t.*, 
               u.full_name as creator_name,
               s.full_name as staff_name
        FROM tickets t
        LEFT JOIN users u ON t.user_id = u.id
        LEFT JOIN users s ON t.assigned_to = s.id
        WHERE t.user_id = ?
        ORDER BY t.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $tickets = $stmt->fetchAll();
} catch (PDOException $e) {
    $tickets = [];
    $error = 'Klaida gaunant užklausas';
}

include __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h2>Mano užklausos</h2>
            <a href="/src/views/user/create-ticket.php" class="btn btn-primary">+ Nauja užklausa</a>
        </div>
        
        <?php if (empty($tickets)): ?>
            <div class="empty-state">
                <h3>Jūs dar neturite užklausų</h3>
                <p>Sukurkite naują užklausą paspaudę mygtuką viršuje</p>
                <a href="/src/views/user/create-ticket.php" class="btn btn-primary">Sukurti užklausą</a>
            </div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pavadinimas</th>
                        <th>Būsena</th>
                        <th>Prioritetas</th>
                        <th>Priskirta</th>
                        <th>Sukurta</th>
                        <th>Veiksmai</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $ticket): ?>
                        <tr>
                            <td>#<?php echo $ticket['id']; ?></td>
                            <td><?php echo escape($ticket['title']); ?></td>
                            <td>
                                <span class="badge badge-<?php 
                                    echo $ticket['status'] === 'naujas' ? 'new' : 
                                        ($ticket['status'] === 'vykdomas' ? 'in-progress' : 
                                        ($ticket['status'] === 'laukiama' ? 'waiting' : 'closed')); 
                                ?>">
                                    <?php echo ucfirst(escape($ticket['status'])); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge priority-<?php echo $ticket['priority']; ?>">
                                    <?php echo ucfirst(escape($ticket['priority'])); ?>
                                </span>
                            </td>
                            <td><?php echo $ticket['staff_name'] ? escape($ticket['staff_name']) : 'Nepriskirta'; ?></td>
                            <td><?php echo date('Y-m-d H:i', strtotime($ticket['created_at'])); ?></td>
                            <td>
                                <a href="/src/views/user/view-ticket.php?id=<?php echo $ticket['id']; ?>" 
                                   class="btn btn-sm btn-info">Peržiūrėti</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>