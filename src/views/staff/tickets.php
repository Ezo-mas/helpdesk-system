<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

requireLogin();
requireRole(ROLE_STAFF);

$currentUser = getCurrentUser();

// Get tickets assigned to this staff member
try {
    $stmt = $pdo->prepare("
        SELECT t.*, 
               u.full_name as creator_name,
               u.email as creator_email
        FROM tickets t
        JOIN users u ON t.user_id = u.id
        WHERE t.assigned_to = ?
        ORDER BY 
            CASE t.status
                WHEN 'naujas' THEN 1
                WHEN 'vykdomas' THEN 2
                WHEN 'laukiama' THEN 3
                WHEN 'uždarytas' THEN 4
            END,
            CASE t.priority
                WHEN 'aukštas' THEN 1
                WHEN 'vidutinis' THEN 2
                WHEN 'žemas' THEN 3
            END,
            t.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $tickets = $stmt->fetchAll();
} catch (PDOException $e) {
    $tickets = [];
    $error = 'Klaida gaunant užklausas';
}

// Get statistics
$stats = [
    'total' => 0,
    'new' => 0,
    'in_progress' => 0,
    'waiting' => 0,
    'closed' => 0
];

foreach ($tickets as $ticket) {
    $stats['total']++;
    switch ($ticket['status']) {
        case 'naujas':
            $stats['new']++;
            break;
        case 'vykdomas':
            $stats['in_progress']++;
            break;
        case 'laukiama':
            $stats['waiting']++;
            break;
        case 'uždarytas':
            $stats['closed']++;
            break;
    }
}

include __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <h1>Mano priskirtos užklausos</h1>
    
    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card primary">
            <h3>Viso užklausų</h3>
            <div class="stat-number"><?php echo $stats['total']; ?></div>
        </div>
        <div class="stat-card" style="border-top: 4px solid #3498db;">
            <h3>Naujos</h3>
            <div class="stat-number"><?php echo $stats['new']; ?></div>
        </div>
        <div class="stat-card warning">
            <h3>Vykdomos</h3>
            <div class="stat-number"><?php echo $stats['in_progress']; ?></div>
        </div>
        <div class="stat-card success">
            <h3>Uždarytos</h3>
            <div class="stat-number"><?php echo $stats['closed']; ?></div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h2>Užklausų sąrašas</h2>
        </div>
        
        <?php if (empty($tickets)): ?>
            <div class="empty-state">
                <h3>Jums dar nėra priskirtų užklausų</h3>
                <p>Administratorius jums priskirs užklausas</p>
            </div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pavadinimas</th>
                        <th>Klientas</th>
                        <th>Būsena</th>
                        <th>Prioritetas</th>
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
                                <?php echo escape($ticket['creator_name']); ?><br>
                                <small style="color: #7f8c8d;"><?php echo escape($ticket['creator_email']); ?></small>
                            </td>
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
                            <td><?php echo date('Y-m-d H:i', strtotime($ticket['created_at'])); ?></td>
                            <td>
                                <a href="/src/views/staff/view-ticket.php?id=<?php echo $ticket['id']; ?>" 
                                   class="btn btn-sm btn-info">Atidaryti</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>