<?php
session_start();
require_once '../../config/config.php';
require_once '../../models/User.php';
require_once '../../models/Ticket.php';
require_once '../../models/Role.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /public/index.php');
    exit();
}

$userModel = new User();
$ticketModel = new Ticket();
$totalUsers = $userModel->getTotalUsers();
$totalTickets = $ticketModel->getTotalTickets();
$totalOpenTickets = $ticketModel->getTotalOpenTickets();
$totalClosedTickets = $ticketModel->getTotalClosedTickets();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <?php include '../layouts/header.php'; ?>
    <div class="container">
        <h1>Admin Dashboard</h1>
        <div class="stats">
            <div class="stat">
                <h2>Total Users</h2>
                <p><?php echo $totalUsers; ?></p>
            </div>
            <div class="stat">
                <h2>Total Tickets</h2>
                <p><?php echo $totalTickets; ?></p>
            </div>
            <div class="stat">
                <h2>Open Tickets</h2>
                <p><?php echo $totalOpenTickets; ?></p>
            </div>
            <div class="stat">
                <h2>Closed Tickets</h2>
                <p><?php echo $totalClosedTickets; ?></p>
            </div>
        </div>
        <div class="actions">
            <h2>Management Options</h2>
            <a href="/src/views/admin/manage-users.php">Manage Users</a>
            <a href="/src/views/admin/manage-tickets.php">Manage Tickets</a>
        </div>
    </div>
    <?php include '../layouts/footer.php'; ?>
</body>
</html>