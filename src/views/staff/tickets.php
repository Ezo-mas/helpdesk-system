<?php
session_start();
require_once '../../config/config.php';
require_once '../../models/Ticket.php';

$ticketModel = new Ticket();
$tickets = $ticketModel->getTicketsByStaffId($_SESSION['user_id']);

include '../layouts/header.php';
?>

<div class="container">
    <h1>Assigned Tickets</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Ticket ID</th>
                <th>Subject</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tickets as $ticket): ?>
                <tr>
                    <td><?php echo htmlspecialchars($ticket['id']); ?></td>
                    <td><?php echo htmlspecialchars($ticket['subject']); ?></td>
                    <td><?php echo htmlspecialchars($ticket['status']); ?></td>
                    <td><?php echo htmlspecialchars($ticket['created_at']); ?></td>
                    <td>
                        <a href="view_ticket.php?id=<?php echo htmlspecialchars($ticket['id']); ?>" class="btn btn-info">View</a>
                        <a href="respond_ticket.php?id=<?php echo htmlspecialchars($ticket['id']); ?>" class="btn btn-primary">Respond</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../layouts/footer.php'; ?>