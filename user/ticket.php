<?php
// -----------------------------
// ticket.php
// -----------------------------

if (session_status() == PHP_SESSION_NONE) session_start();
include(__DIR__ . '/../includes/db.php');

// Check booking_id
if (!isset($_GET['booking_id'])) {
    die("<h3 style='color:red;text-align:center;margin-top:50px;'>Booking not found.</h3>");
}
$booking_id = (int)$_GET['booking_id'];

// Fetch booking with user + bus type info
$stmt = $conn->prepare("
    SELECT b.*, b.distance_km, u.name, u.email, bt.bus_type_name
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN bus_types bt ON b.bus_type_id = bt.id
    WHERE b.id = ?
");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("<h3 style='color:red;text-align:center;margin-top:50px;'>Booking not found.</h3>");
}

$ticket = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Your Ticket</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/bootstrap.min.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="../assets/js/bootstrap.bundle.min.js"></script>
<style>
body {
    background: url("/ebus/images/17.png") no-repeat center center fixed;
    background-size: cover;
    font-family: 'Segoe UI', Tahoma, sans-serif;
    margin: 0;
    padding: 0;
}
.navbar-custom {
    background: rgba(0,0,0,0.6);
    padding: 0.8rem 1rem;
}
.navbar-custom a {
    color: #fff;
    text-decoration: none;
    font-weight: 500;
}
.card {
    max-width: 750px;
    margin: 40px auto;
    padding: 2rem;
    border-radius: 20px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.4);
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(8px);
}
h2 {
    text-align: center;
    margin-bottom: 1.5rem;
    font-weight: 600;
    color: #333;
}
.ticket-details p {
    font-size: 1.1rem;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.ticket-details i {
    color: #0d6efd;
}
.badge-status {
    font-size: 1rem;
    padding: 0.5em 1em;
}
</style>
</head>
<body>

<!-- Top Navbar -->
<nav class="navbar-custom">
    <a href="user_dashboard.php">⬅ Back to Dashboard</a>
</nav>

<div class="container">
    <div class="card">
        <h2>🎫 Ticket Confirmation</h2>
        <div class="ticket-details">
            <p><i class="bi bi-person-fill"></i><strong>Name:</strong> <?= htmlspecialchars($ticket['name']) ?></p>
            <p><i class="bi bi-envelope-fill"></i><strong>Email:</strong> <?= htmlspecialchars($ticket['email']) ?></p>
            <hr>
            <p><i class="bi bi-geo-alt-fill"></i><strong>From:</strong> <?= htmlspecialchars($ticket['from_location']) ?></p>
            <p><i class="bi bi-geo"></i><strong>To:</strong> <?= htmlspecialchars($ticket['to_location']) ?></p>
            <p><i class="bi bi-chair"></i><strong>Seat Number:</strong> <?= htmlspecialchars($ticket['seat_number']) ?></p>
            <p><i class="bi bi-bus-front-fill"></i><strong>Bus Type:</strong> <?= htmlspecialchars($ticket['bus_type_name']) ?></p>
            <p><i class="bi bi-calendar-event"></i><strong>Booking Date:</strong> <?= htmlspecialchars($ticket['booking_date']) ?></p>
            <p><i class="bi bi-currency-rupee"></i><strong>Amount Paid:</strong> ₹<?= htmlspecialchars($ticket['amount']) ?></p>
            <p><i class="bi bi-signpost-split"></i><strong>Distance:</strong> <?= htmlspecialchars($ticket['distance_km'] ?? '0') ?> km</p>
            <p><i class="bi bi-check-circle-fill text-success"></i><strong>Status:</strong> 
                <span class="badge bg-success badge-status">Paid</span>
            </p>
        </div>
        <div class="text-center mt-4">
            <a href="book_ticket.php" class="btn btn-primary">+ Book Another Ticket</a>
        </div>
        <div class="mt-4" style="display: none;">
            <div id="ticketMap" style="height: 250px; border-radius: 10px;"></div>
        </div>
    </div>
</div>

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

</body>
</html>