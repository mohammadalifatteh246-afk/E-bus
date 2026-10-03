<?php
session_start();
include("includes/db.php");

// Only admin can access
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

// For success message popup
$actionDone = "";

// Handle Approve / Cancel Actions
if (isset($_GET['action'], $_GET['id'])) {
    $id = (int)$_GET['id'];

    if ($_GET['action'] === 'approve') {
        $stmt = $conn->prepare("UPDATE bookings SET status='Confirmed' WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $actionDone = "approved";
    } elseif ($_GET['action'] === 'cancel') {
        $stmt = $conn->prepare("UPDATE bookings SET status='Cancelled' WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $actionDone = "cancelled";
    }

    $_SESSION['actionDone'] = $actionDone;
    header("Location: manage_bookings.php");
    exit();
}

// Fetch Bookings
$sql = "SELECT b.id, 
               u.name AS user_name, 
               b.from_location, 
               b.to_location, 
               b.seat_number, 
               bt.bus_type_name, 
               b.booking_date, 
               b.amount, 
               b.status
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN bus_types bt ON b.bus_type_id = bt.id
        ORDER BY b.id DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>E-BUS - Manage Bookings</title>
  
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/variables.css">
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/layout.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/forms.css">
  <link rel="stylesheet" href="css/tables.css">
  <link rel="stylesheet" href="css/responsive.css">
</head>
<body>

<div class="app-container">
  
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <aside class="sidebar" id="sidebar">
    <div class="sidebar-header">&#128652; E-BUS</div>
    <nav class="sidebar-nav">
      <a href="dashboard.php" class="nav-item"><span class="nav-icon">📊</span> Overview</a>
      <a href="routes.php" class="nav-item"><span class="nav-icon">🛣️</span> Routes</a>
      <a href="manage_passes.php" class="nav-item"><span class="nav-icon">🪪</span> Passes</a>
      <a href="manage_renewals.php" class="nav-item"><span class="nav-icon">🔄</span> Renewals</a>
      <a href="manage_bookings.php" class="nav-item active"><span class="nav-icon">🎫</span> Bookings</a>
      <a href="manage_users.php" class="nav-item"><span class="nav-icon">👥</span> Users</a>
      <a href="reports.php" class="nav-item"><span class="nav-icon">📈</span> Reports</a>
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
          <div class="user-avatar" style="background-color: var(--navy); color: white;">AD</div>
          <div class="user-info">
            <span class="user-name">Admin</span>
            <span class="user-role">Administrator</span>
          </div>
        </div>
      </div>
    </header>

    <div class="page-content">
      
      <div class="flex justify-between items-center mb-lg">
        <h1 class="page-title mb-0">Manage Bookings</h1>
      </div>

      <?php if (isset($_SESSION['actionDone'])): ?>
      <div class="card mb-md p-md bg-light text-success font-medium">
        Booking <?= htmlspecialchars($_SESSION['actionDone']) ?> successfully!
        <?php unset($_SESSION['actionDone']); ?>
      </div>
      <?php endif; ?>

      <div class="card">
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Passenger</th>
                <th>Route</th>
                <th>Bus Type</th>
                <th>Date</th>
                <th>Seat</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                  <tr>
                    <td class="font-medium">BK-<?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['user_name']) ?></td>
                    <td><?= htmlspecialchars($row['from_location']) ?> &rarr; <?= htmlspecialchars($row['to_location']) ?></td>
                    <td><?= htmlspecialchars($row['bus_type_name']) ?></td>
                    <td><?= date('d M Y', strtotime($row['booking_date'])) ?></td>
                    <td><?= htmlspecialchars($row['seat_number']) ?></td>
                    <td class="font-medium">&#8377;<?= htmlspecialchars($row['amount']) ?></td>
                    <td>
                      <?php if($row['status'] == 'Pending'): ?>
                        <span class="badge badge-warning">Pending</span>
                      <?php elseif($row['status'] == 'Confirmed'): ?>
                        <span class="badge badge-success">Confirmed</span>
                      <?php elseif($row['status'] == 'Cancelled'): ?>
                        <span class="badge badge-danger">Cancelled</span>
                      <?php else: ?>
                        <span class="badge badge-neutral"><?= htmlspecialchars($row['status']) ?></span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if ($row['status'] == 'Pending'): ?>
                        <div class="flex gap-sm">
                          <a href="?action=approve&id=<?= $row['id'] ?>" class="btn btn-success btn-sm">Confirm</a>
                          <a href="?action=cancel&id=<?= $row['id'] ?>" class="btn btn-danger btn-sm">Cancel</a>
                        </div>
                      <?php else: ?>
                        <span class="text-muted text-small">Processed</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr><td colspan="9" class="text-center text-muted py-xl">No bookings found.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </main>

</div>

<script src="js/app.js"></script>
<script src="js/sidebar.js"></script>
</body>
</html>