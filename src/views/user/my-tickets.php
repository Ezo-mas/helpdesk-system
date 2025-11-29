<?php
session_start();
require_once '../../config/database.php';
require_once '../../models/Ticket.php';

$ticketModel = new Ticket($db);
$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    header("Location: /auth/login.php");
    exit();
}

$tickets = $ticketModel->getTicketsByUserId($userId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/style.css">
    <title>My Tickets</title>
</head>
<body>
    <?php include '../layouts/header.php'; ?>

    <div class="container">
        <h1>My Tickets</h1>
        <?php if (empty($tickets)): ?>
            <p>You have not submitted any tickets.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Ticket ID</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $ticket): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($ticket['id']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['subject']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['status']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <?php include '../layouts/footer.php'; ?>
</body>
</html>