<?php
session_start();
include("includes/db.php");

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

// counts
$users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'];
$passes = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM passes"))['total'];
$bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM bookings"))['total'];

// Mock query logic from old system, mapped to data table layout
$query = "SELECT DATE(booking_date) as bdate, COUNT(*) as bcount, SUM(amount) as revenue 
          FROM bookings GROUP BY DATE(booking_date) ORDER BY DATE(booking_date) DESC LIMIT 10";
$reportResult = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>E-BUS - Reports</title>
  
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
      <a href="manage_bookings.php" class="nav-item"><span class="nav-icon">🎫</span> Bookings</a>
      <a href="manage_users.php" class="nav-item"><span class="nav-icon">👥</span> Users</a>
      <a href="reports.php" class="nav-item active"><span class="nav-icon">📈</span> Reports</a>
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
        <h1 class="page-title mb-0">Financial Reports</h1>
        <button class="btn btn-outline" onclick="window.print()">Export / Print</button>
      </div>

      <div class="grid-4 mb-lg">
        <div class="card">
          <div class="card-body">
            <div class="text-secondary text-small mb-sm">Total Bookings</div>
            <div class="font-bold" style="font-size: 24px;"><?= $bookings ?></div>
          </div>
        </div>
        <div class="card">
          <div class="card-body">
            <div class="text-secondary text-small mb-sm">Total Passes</div>
            <div class="font-bold" style="font-size: 24px;"><?= $passes ?></div>
          </div>
        </div>
        <div class="card">
          <div class="card-body">
            <div class="text-secondary text-small mb-sm">Total Users</div>
            <div class="font-bold" style="font-size: 24px;"><?= $users ?></div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h3 class="card-title mb-0">Revenue by Date (Recent Bookings)</h3>
        </div>
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>Date</th>
                <th>Bookings Count</th>
                <th>Revenue</th>
              </tr>
            </thead>
            <tbody>
              <?php if($reportResult && $reportResult->num_rows > 0): ?>
                <?php while($row = $reportResult->fetch_assoc()): ?>
                  <tr>
                    <td><?= date('d M Y', strtotime($row['bdate'])) ?></td>
                    <td><?= $row['bcount'] ?></td>
                    <td class="font-bold text-success">&#8377;<?= $row['revenue'] ?></td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                 <tr><td colspan="3" class="text-center text-muted py-xl">No revenue data available.</td></tr>
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