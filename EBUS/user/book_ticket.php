<?php
// Start session
if (session_status() == PHP_SESSION_NONE) session_start();
include(__DIR__ . '/../includes/db.php');
include(__DIR__ . '/../includes/api_config.php');

// Access control
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit();
}

$user_session = $_SESSION['user'];
$user_email = is_array($user_session) ? $user_session['email'] : $user_session;

// Fetch full user data
$stmt = $conn->prepare("SELECT id, name, email, role FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    session_destroy();
    header("Location: ../index.php");
    exit();
}

// Check role
if (isset($user['role']) && $user['role'] === 'admin') {
    die("Access Denied. Admins cannot book tickets.");
}

$user_id    = $user['id'];
$user_name  = $user['name'];

// Fetch bus types with their extra charges
$bus_types_result = $conn->query("SELECT id, bus_type_name, extra_charge FROM bus_types ORDER BY bus_type_name ASC");

// Variables
$success = $error = '';
$ticket = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $from        = trim($_POST['from'] ?? '');
    $to          = trim($_POST['to'] ?? '');
    $seat_number = trim($_POST['seat_number'] ?? '');
    $bus_type_id = $_POST['bus_type_id'] ?? '';
    $distance_km = floatval($_POST['distance_km'] ?? 0);

    // Fetch extra charge for selected bus type to ensure security
    $extra_charge = 0;
    if ($bus_type_id) {
        $bc_stmt = $conn->prepare("SELECT extra_charge FROM bus_types WHERE id = ?");
        $bc_stmt->bind_param("i", $bus_type_id);
        $bc_stmt->execute();
        $bc_stmt->bind_result($extra_charge);
        $bc_stmt->fetch();
        $bc_stmt->close();
    }

    // Dynamic ticket price based on distance and bus type
    $ticket_price = ($distance_km * RATE_PER_KM) + PROCESSING_FEE + $extra_charge;

    if (!$from || !$to || !$seat_number || !$bus_type_id) {
        $error = "Please fill all fields.";
    } elseif ($distance_km <= 0) {
        $error = "Please select valid locations and calculate the route first.";
    } else {
        $stmt2 = $conn->prepare("
            INSERT INTO bookings 
            (user_id, from_location, to_location, distance_km, seat_number, bus_type_id, booking_date, status, amount)
            VALUES (?, ?, ?, ?, ?, ?, NOW(), 'Confirmed', ?)
        ");
        $stmt2->bind_param("issdsid", $user_id, $from, $to, $distance_km, $seat_number, $bus_type_id, $ticket_price);

        if ($stmt2->execute()) {
            $booking_id = $stmt2->insert_id;
            header("Location: ticket.php?booking_id=" . $booking_id);
            exit();
        } else {
            $error = "Failed to book ticket. Try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>E-BUS - Book Ticket</title>
  
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
<body>

<div class="app-container">
  
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <aside class="sidebar" id="sidebar">
    <div class="sidebar-header">&#128652; E-BUS</div>
    <nav class="sidebar-nav">
      <a href="home.php" class="nav-item"><span class="nav-icon">&#127968;</span> Home</a>
      <a href="book_ticket.php" class="nav-item active"><span class="nav-icon">&#127915;</span> Book Ticket</a>
      <a href="my_passes.php" class="nav-item"><span class="nav-icon">&#129706;</span> My Passes</a>
      <a href="my_tickets.php" class="nav-item"><span class="nav-icon">&#129534;</span> My Tickets</a>
      <a href="profile.php" class="nav-item"><span class="nav-icon">&#128100;</span> Profile</a>
      <a href="logout.php" class="nav-item"><span class="nav-icon">&#128682;</span> Logout</a>
    </nav>
  </aside>

  <main class="main-content">
    <header class="topbar">
      <div class="topbar-left">
        <button class="sidebar-toggle" id="sidebarToggle">☰</button>
      </div>
      <div class="topbar-right">
        <div class="user-profile">
          <div class="user-avatar" style="overflow: hidden; display: flex; align-items: center; justify-content: center; background-color: var(--primary);">
            <?php 
              $header_avatar_url = '';
              $header_avatar_char = 'U';
              if(isset($_SESSION['user']) && is_array($_SESSION['user'])) {
                  if(!empty($_SESSION['user']['photo'])) $header_avatar_url = $_SESSION['user']['photo'];
                  $header_avatar_char = $_SESSION['user']['name'][0] ?? 'U';
              }
            ?>
            <?php if($header_avatar_url): ?>
              <img src="<?= htmlspecialchars($header_avatar_url) ?>?t=<?= time() ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
            <?php else: ?>
              <?= strtoupper(substr($header_avatar_char, 0, 1)) ?>
            <?php endif; ?>
          </div>
          <div class="user-info">
            <span class="user-name"><?= htmlspecialchars($user_name) ?></span>
            <span class="user-role">Passenger</span>
          </div>
        </div>
      </div>
    </header>

    <div class="page-content">
      
      <div class="mb-lg">
        <h1 class="page-title mb-0">Book a Ticket</h1>
      </div>

      <?php if($error): ?>
      <div class="card mb-md p-md bg-light text-danger font-medium">
        <?= htmlspecialchars($error) ?>
      </div>
      <?php endif; ?>

      <div class="card" style="max-width: 800px;">
        <form method="post" id="bookingForm">
          <div class="card-body">
            
            <h3 class="card-title">1. Route Details</h3>
            <div class="grid-2 mb-md">
              <div class="form-group">
                <label class="form-label">Leaving From</label>
                <input type="text" name="from" id="fromInput" class="form-control" placeholder="City or Stop" required>
              </div>
              <div class="form-group">
                <label class="form-label">Going To</label>
                <input type="text" name="to" id="toInput" class="form-control" placeholder="City or Stop" required>
              </div>
            </div>

            <!-- Distance and route calculation placeholder for JS to fill -->
            <div class="form-group">
                <label class="form-label">Calculated Distance (KM)</label>
                <input type="text" name="distance_km" id="distanceKm" class="form-control" placeholder="Auto-calculated distance" required readonly style="background-color: #f8f9fa; cursor: not-allowed;">
                
            </div>

            <hr style="border-top: 1px solid var(--border); margin: var(--space-lg) 0;">

            <h3 class="card-title">2. Bus Preferences</h3>
            <div class="grid-2 mb-md">
              <div class="form-group">
                <label class="form-label">Bus Type</label>
                <select name="bus_type_id" class="form-control form-select" required>
                  <option value="" disabled selected>Select Bus Type</option>
                  <?php while($bt = $bus_types_result->fetch_assoc()): ?>
                    <option value="<?= $bt['id'] ?>"><?= htmlspecialchars($bt['bus_type_name']) ?> (+&#8377;<?= $bt['extra_charge'] ?>)</option>
                  <?php endwhile; ?>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Seat Number</label>
                <input type="text" name="seat_number" class="form-control" placeholder="e.g. 12A" required>
              </div>
            </div>

          </div>
          <div class="card-footer text-right">
            <button type="submit" class="btn btn-primary">Confirm Booking</button>
          </div>
        </form>
      </div>

    </div>
  </main>
</div>

<script src="../js/app.js"></script>
<script src="../js/sidebar.js"></script>
  <script src="../js/geocoder.js?v=2"></script>
</body>
</html>