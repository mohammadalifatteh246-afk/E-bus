<?php
session_start();
if (!isset($_SESSION['user'])) {
  header("Location: index.php"); // agar login nahi hai to login page bhej do
  exit();
}

include('../includes/db.php');

// Fetch all available routes
$routes = mysqli_query($conn, "SELECT * FROM routes ORDER BY from_location, to_location");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Available Bus Routes - E-Bus</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
  <script src="../assets/js/bootstrap.bundle.min.js"></script>
  <style>
    body {
      background: url("/ebus/images/26.png") no-repeat center center fixed;
      background-size: cover;
      font-family: 'Segoe UI', Tahoma, sans-serif;
    }
    .overlay {
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0, 0, 0, 0.6);
      z-index: -1;
    }
    .navbar-custom {
      background: rgba(0, 0, 0, 0.7);
      padding: 0.8rem 1rem;
    }
    .navbar-custom a {
      color: #fff;
      text-decoration: none;
      font-weight: 500;
    }
    .routes-card {
      background: rgba(255,255,255,0.95);
      border-radius: 16px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.4);
      backdrop-filter: blur(10px);
      padding: 20px;
      margin-top: 50px;
    }
    h3 {
      color: #0d6efd;
      font-weight: 700;
      text-align: center;
      margin-bottom: 25px;
    }
    .table-hover tbody tr:hover {
      background: rgba(13,110,253,0.1);
      transition: 0.3s;
    }
    .badge-route {
      background: #0d6efd;
      color: #fff;
      padding: 5px 10px;
      border-radius: 8px;
      font-size: 0.85rem;
    }
  </style>
</head>
<body>

<div class="overlay"></div>

<!-- Navbar with Dashboard Button -->
<nav class="navbar-custom">
  <a href="user_dashboard.php"><i class="bi bi-arrow-left-circle"></i> Dashboard</a>
</nav>

<div class="container">
  <div class="routes-card">
    <h3>🚍 Available Bus Routes</h3>

    <?php if (mysqli_num_rows($routes) == 0): ?>
      <div class="alert alert-warning text-center mt-4"> No routes found.</div>
    <?php else: ?>
      <table class="table table-striped table-hover text-center align-middle">
        <thead class="table-primary">
          <tr>
            <th>#</th>
            <th><i class="bi bi-geo-alt-fill"></i> From</th>
            <th><i class="bi bi-geo-fill"></i> To</th>
            <th><i class="bi bi-clock-fill"></i> Departure</th>
            <th><i class="bi bi-clock-history"></i> Arrival time</th>
          </tr>
        </thead>
        <tbody>
          <?php $i = 1; while ($row = mysqli_fetch_assoc($routes)): ?>
            <tr>
              <td><span class="badge-route"><?= $i++ ?></span></td>
              <td><?= htmlspecialchars($row['from_location']) ?></td>
              <td><?= htmlspecialchars($row['to_location']) ?></td>
              <td><?= !empty($row['departure_time']) ? date("h:i A", strtotime($row['departure_time'])) : '--:--' ?></td>
              <td><?= !empty($row['arrival_time']) ? date("h:i A", strtotime($row['arrival_time'])) : '--:--' ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>

<?php include('../includes/user_footer.php'); ?>
</body>
</html>