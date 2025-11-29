<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

requireLogin();
requireRole(ROLE_ADMIN);

// Get all tickets
try {
    $stmt = $pdo->query("
        SELECT t.*, 
               u.full_name as creator_name,
               s.full_name as staff_name
        FROM tickets t
        JOIN users u ON t.user_id = u.id
        LEFT JOIN users s ON t.assigned_to = s.id
        ORDER BY t.created_at DESC
    ");
    $tickets = $stmt->fetchAll();
} catch (PDOException $e) {
    $tickets = [];
}

include __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Visos užklausos</h2>
        </div>
        
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Pavadinimas</th>
                    <th>Klientas</th>
                    <th>Būsena</th>
                    <th>Prioritetas</th>
                    <th>Priskirta</th>
                    <th>Data</th>
                    <th>Veiksmai</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tickets as $ticket): ?>
                    <tr>
                        <td>#<?php echo $ticket['id']; ?></td>
                        <td><?php echo escape($ticket['title']); ?></td>
                        <td><?php echo escape($ticket['creator_name']); ?></td>
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
                        <td><?php echo $ticket['staff_name'] ? escape($ticket['staff_name']) : '<span style="color:#e67e22;">Nepriskirta</span>'; ?></td>
                        <td><?php echo date('Y-m-d H:i', strtotime($ticket['created_at'])); ?></td>
                        <td>
                            <a href="/admin/edit-ticket?id=<?php echo $ticket['id']; ?>" 
                               class="btn btn-sm btn-info">Valdyti</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>