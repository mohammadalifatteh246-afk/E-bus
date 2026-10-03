<?php
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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>E-BUS - Your Ticket</title>
  
  <link rel="stylesheet" href="../css/reset.css">
  <link rel="stylesheet" href="../css/variables.css">
  <link rel="stylesheet" href="../css/base.css">
  <link rel="stylesheet" href="../css/layout.css">
  <link rel="stylesheet" href="../css/components.css">
  <link rel="stylesheet" href="../css/forms.css">
  <link rel="stylesheet" href="../css/tables.css">
  <link rel="stylesheet" href="../css/pages.css">
  <link rel="stylesheet" href="../css/responsive.css">
</head>
<body style="background-color: var(--bg);">

<div class="flex-col items-center justify-center p-xl" style="min-height: 100vh;">
  
  <div class="mb-lg no-print">
    <a href="home.php" class="btn btn-secondary mb-md">&larr; Back to Dashboard</a>
    <div class="flex gap-sm">
      <button class="btn btn-primary" onclick="window.print()">&#128424; Print Ticket</button>
    </div>
  </div>

  <div class="card" style="width: 100%; max-width: 500px;">
    
    <div class="card-header flex justify-between items-center" style="border-bottom: 2px dashed var(--border); padding-bottom: var(--space-md);">
      <div>
        <h3 class="card-title mb-xs">E-BUS Ticket</h3>
        <div class="text-small text-secondary">PNR: EB-<?= $ticket['id'] ?></div>
      </div>
      <div class="badge badge-success"><?= htmlspecialchars($ticket['status']) ?></div>
    </div>

    <div class="card-body">
      <div class="flex justify-between items-center mb-lg">
        <div class="flex-col">
          <div class="text-small text-secondary mb-xs">From</div>
          <div class="font-bold" style="font-size: 18px;"><?= htmlspecialchars($ticket['from_location']) ?></div>
        </div>
        <div class="text-primary" style="font-size: 24px;">&rarr;</div>
        <div class="flex-col text-right">
          <div class="text-small text-secondary mb-xs">To</div>
          <div class="font-bold" style="font-size: 18px;"><?= htmlspecialchars($ticket['to_location']) ?></div>
        </div>
      </div>

      <div class="grid-2 mb-md bg-light p-md" style="border-radius: var(--radius-sm);">
        <div>
          <div class="text-small text-secondary mb-xs">Passenger</div>
          <div class="font-medium"><?= htmlspecialchars($ticket['name']) ?></div>
        </div>
        <div>
          <div class="text-small text-secondary mb-xs">Booking Date</div>
          <div class="font-medium"><?= date('d M Y, h:i A', strtotime($ticket['booking_date'])) ?></div>
        </div>
      </div>

      <div class="grid-2">
        <div>
          <div class="text-small text-secondary mb-xs">Seat & Class</div>
          <div class="font-medium">Seat <?= htmlspecialchars($ticket['seat_number']) ?> (<?= htmlspecialchars($ticket['bus_type_name']) ?>)</div>
        </div>
        <div>
          <div class="text-small text-secondary mb-xs">Total Amount</div>
          <div class="font-medium text-primary">&#8377;<?= htmlspecialchars($ticket['amount']) ?></div>
        </div>
      </div>

    </div>

    <div class="card-footer text-center bg-light" style="border-top: 2px dashed var(--border);">
      <div class="text-small text-secondary">Please present this ticket (printed or digital) at boarding.</div>
    </div>
  </div>

  <style>
    @media print {
      body { background-color: white !important; }
      .no-print { display: none !important; }
      .card { box-shadow: none !important; border: 1px solid #ddd; margin: 0 auto; }
    }
  </style>

</div>

</body>
</html>
