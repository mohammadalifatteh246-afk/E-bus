<?php
session_start();
include(__DIR__ . '/../includes/db.php');

if (!isset($_SESSION['user']) || !isset($_SESSION['pending_booking'])) {
    die("No pending booking.");
}

$booking_id = $_SESSION['pending_booking'];

// Fetch ticket & amount
$stmt = $conn->prepare("
    SELECT b.*, u.name, u.email, bt.bus_type_name
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN bus_types bt ON b.bus_type_id = bt.id
    WHERE b.id = ? AND u.email = ?
");
$stmt->bind_param("is", $booking_id, $_SESSION['user']);
$stmt->execute();
$ticket = $stmt->get_result()->fetch_assoc();

if (!$ticket) die("Booking not found.");

$success_message = '';

// Payment submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update booking as paid
    $stmt2 = $conn->prepare("UPDATE bookings SET status='Confirmed', payment_status='Paid' WHERE id=?");
    $stmt2->bind_param("i", $booking_id);
    $stmt2->execute();

    // Send email
    $to_email = $ticket['email'];
    $subject = "Your Bus Ticket Confirmation";
    $message = "Hello ".$ticket['name'].",\n\nYour ticket has been confirmed.\n\n".
               "From: ".$ticket['from_location']."\n".
               "To: ".$ticket['to_location']."\n".
               "Seat Number: ".$ticket['seat_number']."\n".
               "Bus Type: ".$ticket['bus_type_name']."\n".
               "Booking Date: ".$ticket['booking_date']."\n".
               "Status: Paid \n\n".
               "Thank you for booking with us!";
    $headers = "From: no-reply@yourbus.com";

    if (@mail($to_email, $subject, $message, $headers)) {
        $success_message = "= Payment successful and ticket emailed to ".$ticket['email'];
    } else {
        $success_message = " Payment successful but email could not be sent.";
    }

    unset($_SESSION['pending_booking']);
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Pay Ticket</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/bootstrap.min.css">
  <script src="../assets/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="container mt-5">
<h2>Pay Ticket</h2>
<p><strong>Total Amount:</strong> ₹<?= $ticket['amount'] ?? 200 ?></p>

<?php if($success_message): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
    <a href="ticket.php?booking_id=<?= $booking_id ?>" class="btn btn-primary mt-3">View Ticket</a>
<?php else: ?>
    <form method="POST">
        <button type="submit" class="btn btn-success">Pay & Confirm</button>
    </form>
<?php endif; ?>

</div>
</body>
</html>