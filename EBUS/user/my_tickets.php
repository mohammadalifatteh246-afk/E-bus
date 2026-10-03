<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit();
}

include("../includes/db.php");

$user_session = $_SESSION['user'];
$user_email = is_array($user_session) ? $user_session['email'] : $user_session;

// Get logged-in user id and name
$stmt = $conn->prepare("SELECT id, name FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$user_data = $stmt->get_result()->fetch_assoc();
$stmt->close();

$user_id = $user_data['id'];
$user_name = $user_data['name'];

// Fetch all bookings for this user
$query = "SELECT b.*, bt.bus_type_name 
          FROM bookings b 
          JOIN bus_types bt ON b.bus_type_id = bt.id 
          WHERE b.user_id = ? 
          ORDER BY b.booking_date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$bookings = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>E-BUS - My Tickets</title>
  
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
      <a href="book_ticket.php" class="nav-item"><span class="nav-icon">&#127915;</span> Book Ticket</a>
      <a href="my_passes.php" class="nav-item"><span class="nav-icon">&#129706;</span> My Passes</a>
      <a href="my_tickets.php" class="nav-item active"><span class="nav-icon">&#129534;</span> My Tickets</a>
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
      
      <div class="flex justify-between items-center mb-lg">
        <h1 class="page-title mb-0">My Tickets</h1>
        <a href="book_ticket.php" class="btn btn-primary">+ Book New Ticket</a>
      </div>

      <div class="card">
        <div class="card-header">
          <h3 class="card-title mb-0">Ticket History</h3>
        </div>
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>Booking ID</th>
                <th>Route</th>
                <th>Bus Type</th>
                <th>Seat</th>
                <th>Booking Date</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if($bookings->num_rows > 0): ?>
                <?php while($row = $bookings->fetch_assoc()): ?>
                  <tr>
                    <td class="font-medium">EB-<?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['from_location']) ?> &harr; <?= htmlspecialchars($row['to_location']) ?></td>
                    <td><?= htmlspecialchars($row['bus_type_name']) ?></td>
                    <td><?= htmlspecialchars($row['seat_number']) ?></td>
                    <td><?= date('d M Y, h:i A', strtotime($row['booking_date'])) ?></td>
                    <td>
                      <?php if($row['status'] == 'Confirmed' || $row['status'] == 'Completed'): ?>
                        <span class="badge badge-success"><?= htmlspecialchars($row['status']) ?></span>
                      <?php elseif($row['status'] == 'Pending'): ?>
                        <span class="badge badge-warning">Pending</span>
                      <?php else: ?>
                        <span class="badge badge-danger"><?= htmlspecialchars($row['status']) ?></span>
                      <?php endif; ?>
                    </td>
                    <td><a href="ticket.php?booking_id=<?= $row['id'] ?>" class="btn btn-outline btn-sm">View Ticket</a></td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr><td colspan="7" class="text-center text-muted py-xl">No tickets booked yet.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </main>
</div>

<script src="../js/app.js"></script>
<script src="../js/sidebar.js"></script>
</body>
</html>