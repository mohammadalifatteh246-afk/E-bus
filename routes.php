<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: index.php");
  exit();
}
include('includes/db.php');
include('includes/header.php');

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
<title>Manage Bus Routes</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/bootstrap.min.css">
<script src="assets/js/bootstrap.bundle.min.js"></script>

<style>
body {
    background: url("/ebus/images/26.png") no-repeat center center fixed;
    background-size: cover;
    font-family: 'Segoe UI', sans-serif;
    min-height: 100vh;
}
.container {
    background: rgba(255,255,255,0.95);
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 0 25px rgba(0,0,0,0.25);
    margin-top: 30px;
    margin-bottom: 50px;
}
.table-hover tbody tr:hover {
    background-color: rgba(0,123,255,0.1);
    transition: 0.3s;
}
.card { box-shadow: 0 0 15px rgba(0,0,0,0.1); }
</style>
</head>
<body>

<div class="container mt-5">
  <h2 class="mb-4 text-center text-primary">🚌 Manage Bus Routes</h2>

  <!-- Add Route Form -->
  <div class="card mb-4 shadow">
    <div class="card-body">
      <form method="post" class="row g-3">
        <div class="col-md-2">
          <input type="text" name="route_name" class="form-control" placeholder="Route Name" required>
        </div>
        <div class="col-md-2">
          <input type="text" name="from" class="form-control" placeholder="From Location" required>
        </div>
        <div class="col-md-2">
          <input type="text" name="to" class="form-control" placeholder="To Location" required>
        </div>
        <div class="col-md-2">
          <input type="number" step="0.1" name="distance_km" class="form-control" placeholder="Distance (KM)" required>
        </div>
        <div class="col-md-2">
          <input type="time" name="time" class="form-control" required>
        </div>
        <div class="col-md-2 d-grid">
          <button type="submit" name="add" class="btn btn-success">Add</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Routes Table -->
  <div class="table-responsive">
    <table class="table table-bordered table-hover text-center align-middle">
      <thead class="table-dark">
        <tr>
          <th>#</th>
          <th>Route Name</th>
          <th>From</th>
          <th>To</th>
          <th>Distance (KM)</th>
          <th>Departure Time</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (mysqli_num_rows($routes) > 0): $i = 1; ?>
          <?php while ($r = mysqli_fetch_assoc($routes)): ?>
            <tr>
              <td><?= $i++ ?></td>
              <td><strong><?= htmlspecialchars($r['route_name']) ?></strong></td>
              <td><?= htmlspecialchars($r['from_location']) ?></td>
              <td><?= htmlspecialchars($r['to_location']) ?></td>
              <td><?= htmlspecialchars($r['distance_km']) ?> km</td>
              <td><?= date('h:i A', strtotime($r['departure_time'])) ?></td>
              <td>
                <a href="?delete=<?= $r['id'] ?>" 
                   class="btn btn-sm btn-danger" 
                   onclick="return confirm('Delete this route?')">Delete</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="7" class="text-center">🚍 No routes found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include('includes/footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
