<?php
session_start();
include("includes/db.php");

// Only admin can access
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

// Approve or Cancel actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action == "approve") {
        $conn->query("UPDATE passes SET status='Active' WHERE id=$id");
        $_SESSION['msg'] = "Pass approved successfully!";
    } elseif ($action == "cancel") {
        $conn->query("UPDATE passes SET status='Cancelled' WHERE id=$id");
        $_SESSION['msg'] = "Pass cancelled!";
    }

    header("Location: manage_passes.php");
    exit();
}

// Fetch passes
$sql = "SELECT p.id, p.name, p.age, p.gender, p.email, 
               p.from_location, p.to_location, p.route, 
               p.pass_type, p.pass_duration, p.pass_from, p.pass_to, 
               p.fee, p.status, p.created_at, 
               u.name AS user_name
        FROM passes p
        LEFT JOIN users u ON p.user_id = u.id
        ORDER BY p.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>E-BUS - Manage Passes</title>
  
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
      <a href="manage_passes.php" class="nav-item active"><span class="nav-icon">🪪</span> Passes</a>
      <a href="manage_renewals.php" class="nav-item"><span class="nav-icon">🔄</span> Renewals</a>
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
        <h1 class="page-title mb-0">Pass Applications</h1>
      </div>

      <?php if (isset($_SESSION['msg'])): ?>
      <div class="card mb-md p-md bg-light text-success font-medium">
        <?= $_SESSION['msg'] ?>
        <?php unset($_SESSION['msg']); ?>
      </div>
      <?php endif; ?>

      <div class="card">
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>ID</th>
                <th>User</th>
                <th>Pass Type</th>
                <th>From</th>
                <th>To</th>
                <th>Fee</th>
                <th>Status</th>
                <th>Issued At</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                  <tr>
                    <td class="font-medium">APP-<?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['user_name']) ?></td>
                    <td><?= htmlspecialchars($row['pass_type']) ?><br><span class="text-small text-secondary"><?= htmlspecialchars($row['pass_duration']) ?></span></td>
                    <td><?= htmlspecialchars($row['from_location']) ?></td>
                    <td><?= htmlspecialchars($row['to_location']) ?></td>
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
                    <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                    <td>
                      <?php if ($row['status'] == 'Pending'): ?>
                        <a href="?action=approve&id=<?= $row['id'] ?>" class="btn btn-success btn-sm">Approve</a>
                        <a href="?action=cancel&id=<?= $row['id'] ?>" class="btn btn-danger btn-sm ml-xs">Reject</a>
                      <?php else: ?>
                        <span class="text-muted text-small">Processed</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr><td colspan="9" class="text-center text-muted py-xl">No pass applications found.</td></tr>
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