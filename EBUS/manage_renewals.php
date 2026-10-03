<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}
include("includes/db.php");

// Renew / Cancel Handling
if (isset($_GET['action'], $_GET['id'])) {
    $id = (int) $_GET['id'];
    $action = $_GET['action'];

    if ($action == "approve") {
        // Renew pass: Active + extend expiry by 1 month
        $status = "Active";
        $stmt = $conn->prepare("UPDATE passes SET status=?, pass_to = DATE_ADD(pass_to, INTERVAL 1 MONTH) WHERE id=?");
        $stmt->bind_param("si", $status, $id);
    } elseif ($action == "reject") {
        // Cancel pass: Cancelled
        $status = "Cancelled";
        $stmt = $conn->prepare("UPDATE passes SET status=? WHERE id=?");
        $stmt->bind_param("si", $status, $id);
    }
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Pass $status successfully!');window.location='manage_renewals.php';</script>";
    exit();
}

// Get all passes (latest first)
$result = $conn->query("SELECT * FROM passes ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>E-BUS - Manage Renewals</title>
  
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
      <a href="manage_renewals.php" class="nav-item active"><span class="nav-icon">🔄</span> Renewals</a>
      <a href="manage_bookings.php" class="nav-item"><span class="nav-icon">🎫</span> Bookings</a>
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
        <h1 class="page-title mb-0">Renewal Requests</h1>
      </div>

      <div class="card">
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>ID</th>
                <th>User</th>
                <th>Pass Type</th>
                <th>Current Expiry</th>
                <th>Fee</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                  <tr>
                    <td class="font-medium">REN-<?= $row['id'] ?></td>
                    <td>
                      <strong><?= htmlspecialchars($row['name']) ?></strong><br>
                      <small class="text-secondary"><?= htmlspecialchars($row['email']) ?></small>
                    </td>
                    <td><?= htmlspecialchars($row['pass_type']) ?> <br><span class="text-small text-secondary"><?= htmlspecialchars($row['pass_duration']) ?></span></td>
                    <td><?= date('d M Y', strtotime($row['pass_to'])) ?></td>
                    <td class="font-medium">&#8377;<?= htmlspecialchars($row['fee']) ?></td>
                    <td>
                      <?php if($row['status'] == 'Pending'): ?>
                        <span class="badge badge-warning">Pending</span>
                      <?php elseif($row['status'] == 'Active'): ?>
                        <span class="badge badge-success">Active</span>
                      <?php elseif($row['status'] == 'Cancelled'): ?>
                        <span class="badge badge-danger">Cancelled</span>
                      <?php else: ?>
                        <span class="badge badge-neutral"><?= htmlspecialchars($row['status']) ?></span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if ($row['status'] != 'Active' && $row['status'] != 'Cancelled'): ?>
                        <div class="flex gap-sm">
                          <a href="?action=approve&id=<?= $row['id'] ?>" class="btn btn-success btn-sm">Renew</a>
                          <a href="?action=reject&id=<?= $row['id'] ?>" class="btn btn-danger btn-sm">Cancel</a>
                        </div>
                      <?php else: ?>
                         <span class="text-muted text-small">Processed</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                 <tr><td colspan="7" class="text-center text-muted py-xl">No renewal requests found.</td></tr>
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