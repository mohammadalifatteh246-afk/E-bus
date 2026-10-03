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

// Monthly bookings
$monthlyData = [];
$result = mysqli_query($conn, "
    SELECT MONTH(booking_date) as month, COUNT(*) as total 
    FROM bookings 
    GROUP BY MONTH(booking_date)
");
while ($row = mysqli_fetch_assoc($result)) {
    $monthlyData[$row['month']] = $row['total'];
}

// Prepare chart data
$months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
$chartLabels = json_encode($months);
$chartData = [];
for ($i=1; $i<=12; $i++) {
    $chartData[] = isset($monthlyData[$i]) ? $monthlyData[$i] : 0;
}
$chartData = json_encode($chartData);
?>
<?php include("includes/header.php"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Reports</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/bootstrap.min.css">
<script src="assets/js/bootstrap.bundle.min.js"></script>
<style>
body {
    background: url("/ebus/images/25.png") no-repeat center center fixed;
    background-size: cover;
    font-family: 'Segoe UI', sans-serif;
    min-height: 100vh;
}
.container {
    background: rgba(255,255,255,0.95);
    border-radius: 10px;
    padding: 30px;
    box-shadow: 0 0 20px rgba(0,0,0,0.2);
    margin-top: 50px;
    margin-bottom: 50px;
}
.card { box-shadow: 0 0 15px rgba(0,0,0,0.1); }
</style>
</head>
<body>

<div class="container mt-5">
  <h2 class="mb-4 text-center">📊 Admin Reports</h2>
  <div class="row mb-4">
    <div class="col-md-4">
      <div class="card text-center shadow">
        <div class="card-body">
          <h5 class="card-title">Total Users</h5>
          <p class="display-6"><?php echo $users; ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card text-center shadow">
        <div class="card-body">
          <h5 class="card-title">Total Passes</h5>
          <p class="display-6"><?php echo $passes; ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card text-center shadow">
        <div class="card-body">
          <h5 class="card-title">Total Bookings</h5>
          <p class="display-6"><?php echo $bookings; ?></p>
        </div>
      </div>
    </div>
  </div>

  <!-- Chart -->
  <div class="card shadow p-3">
    <h5 class="card-title text-center">📅 Monthly Bookings</h5>
    <canvas id="bookingChart" height="100"></canvas>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('bookingChart');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo $chartLabels; ?>,
        datasets: [{
            label: 'Bookings',
            data: <?php echo $chartData; ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.7)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>

<?php include("includes/footer.php"); ?>
</body>
</html>