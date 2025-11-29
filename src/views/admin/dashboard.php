<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

requireLogin();
requireRole(ROLE_ADMIN);

// Get statistics
try {
    // Total users
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $total_users = $stmt->fetch()['total'];
    
    // Total tickets
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM tickets");
    $total_tickets = $stmt->fetch()['total'];
    
    // Open tickets
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM tickets WHERE status != 'uždarytas'");
    $open_tickets = $stmt->fetch()['total'];
    
    // Closed tickets
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM tickets WHERE status = 'uždarytas'");
    $closed_tickets = $stmt->fetch()['total'];
    
    // Unassigned tickets
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM tickets WHERE assigned_to IS NULL");
    $unassigned_tickets = $stmt->fetch()['total'];
    
    // Average rating
    $stmt = $pdo->query("SELECT AVG(rating) as avg_rating FROM tickets WHERE rating IS NOT NULL");
    $avg_rating = round($stmt->fetch()['avg_rating'] ?? 0, 1);
    
    // Recent tickets
    $stmt = $pdo->query("
        SELECT t.*, u.full_name as creator_name, s.full_name as staff_name
        FROM tickets t
        JOIN users u ON t.user_id = u.id
        LEFT JOIN users s ON t.assigned_to = s.id
        ORDER BY t.created_at DESC
        LIMIT 10
    ");
    $recent_tickets = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $error = 'Klaida gaunant statistiką';
}

include __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <h1>Administratoriaus valdymo skydas</h1>
    
    <!-- Statistics Grid -->
    <div class="stats-grid">
        <div class="stat-card primary">
            <h3>Viso vartotojų</h3>
            <div class="stat-number"><?php echo $total_users; ?></div>
        </div>
        <div class="stat-card warning">
            <h3>Viso užklausų</h3>
            <div class="stat-number"><?php echo $total_tickets; ?></div>
        </div>
        <div class="stat-card danger">
            <h3>Atvirų užklausų</h3>
            <div class="stat-number"><?php echo $open_tickets; ?></div>
        </div>
        <div class="stat-card success">
            <h3>Uždarytų užklausų</h3>
            <div class="stat-number"><?php echo $closed_tickets; ?></div>
        </div>
        <div class="stat-card" style="border-top: 4px solid #e67e22;">
            <h3>Nepriskirtų užklausų</h3>
            <div class="stat-number"><?php echo $unassigned_tickets; ?></div>
        </div>
        <div class="stat-card" style="border-top: 4px solid #f39c12;">
            <h3>Vidutinis įvertinimas</h3>
            <div class="stat-number"><?php echo $avg_rating; ?>/5 ★</div>
        </div>
    </div>
    
    <!-- Recent Tickets -->
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h2>Naujausios užklausos</h2>
            <a href="/src/views/admin/tickets.php" class="btn btn-primary">Visos užklausos</a>
        </div>
        
        <?php if (empty($recent_tickets)): ?>
            <div class="empty-state">
                <p>Nėra užklausų</p>
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
                        <th>Priskirta</th>
                        <th>Data</th>
                        <th>Veiksmai</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_tickets as $ticket): ?>
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
                                <a href="/src/views/admin/edit-ticket.php?id=<?php echo $ticket['id']; ?>" 
                                   class="btn btn-sm btn-info">Valdyti</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <!-- Quick Links -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px;">
        <a href="/src/views/admin/users.php" class="card" style="text-decoration: none; color: inherit; text-align: center; padding: 30px;">
            <h3>👥 Valdyti vartotojus</h3>
            <p style="color: #7f8c8d;">Peržiūrėti ir redaguoti vartotojų teises</p>
        </a>
        <a href="/src/views/admin/tickets.php" class="card" style="text-decoration: none; color: inherit; text-align: center; padding: 30px;">
            <h3>🎫 Visos užklausos</h3>
            <p style="color: #7f8c8d;">Peržiūrėti ir priskirti užklausas</p>
        </a>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>