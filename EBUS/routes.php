<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: index.php");
  exit();
}
include('includes/db.php');

// Add new route
if (isset($_POST['add'])) {
  $route_name = $_POST['route_name'];
  $from = $_POST['from'];
  $to = $_POST['to'];
  $distance_km = floatval($_POST['distance_km']);
  $time = $_POST['time'];

  mysqli_query($conn, "INSERT INTO routes (bus_number, route_name, from_location, to_location, distance_km, departure_time) 
                       VALUES ('', '$route_name', '$from', '$to', '$distance_km', '$time')");
  header("Location: routes.php");
  exit();
}

// Delete route
if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);
  mysqli_query($conn, "DELETE FROM routes WHERE id = $id");
  header("Location: routes.php");
  exit();
}

$routes = mysqli_query($conn, "SELECT * FROM routes ORDER BY departure_time ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>E-BUS - Manage Routes</title>
  
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
      <a href="routes.php" class="nav-item active"><span class="nav-icon">🛣️</span> Routes</a>
      <a href="manage_passes.php" class="nav-item"><span class="nav-icon">🪪</span> Passes</a>
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
        <h1 class="page-title mb-0">Manage Routes</h1>
        <button class="btn btn-primary" data-toggle="modal" data-target="addRouteModal">+ Add New Route</button>
      </div>

      <div class="card">
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>Route Name</th>
                <th>From</th>
                <th>To</th>
                <th>Distance</th>
                <th>Departure Time</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if(mysqli_num_rows($routes) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($routes)): ?>
                  <tr>
                    <td class="font-medium"><?= htmlspecialchars($row['route_name']) ?></td>
                    <td><?= htmlspecialchars($row['from_location']) ?></td>
                    <td><?= htmlspecialchars($row['to_location']) ?></td>
                    <td><?= htmlspecialchars($row['distance_km']) ?> km</td>
                    <td><?= htmlspecialchars($row['departure_time']) ?></td>
                    <td>
                      <a href="?delete=<?= $row['id'] ?>" class="btn btn-outline btn-danger btn-sm" onclick="return confirm('Delete this route?')">Delete</a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr><td colspan="6" class="text-center text-muted py-xl">No routes found.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </main>
</div>

<!-- Add Route Modal -->
<div class="modal-overlay" id="addRouteModal">
  <div class="modal">
    <div class="modal-header">
      <h3 class="modal-title">Add New Route</h3>
      <button class="modal-close" data-dismiss="modal">&times;</button>
    </div>
    <form method="post">
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Route Name</label>
          <input type="text" name="route_name" class="form-control" placeholder="e.g. Ahmedabad Express" required>
        </div>
        <div class="grid-2">
          <div class="form-group">
            <label class="form-label">Origin</label>
            <input type="text" name="from" class="form-control" placeholder="City name" required>
          </div>
          <div class="form-group">
            <label class="form-label">Destination</label>
            <input type="text" name="to" class="form-control" placeholder="City name" required>
          </div>
        </div>
        <div class="grid-2">
          <div class="form-group">
            <label class="form-label">Distance (km)</label>
            <input type="text" name="distance_km" class="form-control" placeholder="Auto-calculated distance" required readonly style="background-color: #f8f9fa; cursor: not-allowed;">
          </div>
          <div class="form-group">
            <label class="form-label">Departure Time</label>
            <input type="time" name="time" class="form-control" required>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="submit" name="add" class="btn btn-primary">Save Route</button>
      </div>
    </form>
  </div>
</div>

<script src="js/app.js"></script>
<script src="js/sidebar.js"></script>
<script src="js/modal.js"></script>
  <script src="js/geocoder.js?v=2"></script>
</body>
</html>
