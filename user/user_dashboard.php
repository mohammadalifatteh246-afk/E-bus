<?php
include(__DIR__ . "/includes/auth.php");
?>
<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit();
}

// Include database connection
include("../includes/db.php"); // make sure this path is correct

$user_email = $_SESSION['user'];

// Fetch user info safely
$stmt = mysqli_prepare($conn, "SELECT name, email FROM users WHERE email = ?");
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $user_email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
    } else {
        // If no user found, redirect to login
        header("Location: ../index.php");
        exit();
    }

    mysqli_stmt_close($stmt);
} else {
    die("Database query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Dashboard - E-Bus</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
  <script src="../assets/js/bootstrap.bundle.min.js"></script>

  <style>
body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color: #f0f2f5;
  margin: 0;
  background: url('/ebus/images/4.png') no-repeat center center fixed;
  background-size: cover;
}

/* Sidebar */
.sidebar {
  height: 100vh;
  width: 240px;
  position: fixed;
  top: 0;
  left: 0;
  background-color: #98aeeeff;
  color: white;
  padding-top: 20px;
  transition: 0.3s;
}
.sidebar .profile {
  text-align: center;
  margin-bottom: 20px;
  position: relative;
}
.sidebar .profile img {
  width: 80px;
  border-radius: 50%;
  margin-bottom: 10px;
}
/* Online dot */
.online-status {
  position: absolute;
  bottom: 55px;
  right: 75px;
  width: 15px;
  height: 15px;
  background-color: #4CAF50;
  border: 2px solid white;
  border-radius: 50%;
}

.sidebar h4 {
  margin: 0;
  font-weight: 600;
}
.sidebar a {
  display: flex;
  align-items: center;
  gap: 10px;
  color: white;
  padding: 12px 20px;
  text-decoration: none;
  transition: 0.2s;
  border-radius: 5px;
}
.sidebar a:hover {
  background-color: #2e59d9;
}

/* Main content */
.content {
  margin-left: 240px;
  padding: 20px;
}

/* Dashboard cards */
.dashboard-card {
  border-radius: 15px;
  box-shadow: 0 6px 15px rgba(0,0,0,0.1);
  transition: transform 0.3s, box-shadow 0.3s;
  background-color: white;
  overflow: hidden;
}
.dashboard-card img {
  width: 100%;
  height: 150px;
  object-fit: cover;
  border-top-left-radius: 15px;
  border-top-right-radius: 15px;
}
.dashboard-card h4 {
  margin-top: 10px;
  font-size: 1.25rem;
}
.dashboard-card p {
  font-size: 0.9rem;
  color: #555;
}
.dashboard-card .btn {
  border-radius: 20px;
  font-size: 0.85rem;
  margin-top: 10px;
  transition: 0.3s;
}
.dashboard-card .btn:hover {
  background-color: #4e73df;
  color: white;
}
.dashboard-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

/* Responsive */
@media (max-width: 768px) {
  .sidebar {
    width: 100%;
    height: auto;
    position: relative;
  }
  .content {
    margin-left: 0;
  }
  .dashboard-card img {
    height: 120px;
  }
}
</style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
  <div class="profile">
    <img src="/ebus/images/19.png" alt="User">
    <span class="online-status"></span> <!-- Online dot -->
    <h4><?php echo htmlspecialchars($user['name']); ?></h4>
    <p>User</p>
  </div>
    <a href="user_dashboard.php" class="list-group-item list-group-item-action active">🏠 Dashboard</a>
    <a href="profile.php" class="list-group-item list-group-item-action">👤 My Profile</a>
    <a href="apply_pass.php" class="list-group-item list-group-item-action">🚌 Apply Pass</a>
     <a href="renew_pass.php" class="list-group-item list-group-item-action">🚌 Renew_Pass</a>
    <a href="book_ticket.php" class="list-group-item list-group-item-action">🎟️ Book Tickets</a>
    <a href="check_status.php" class="list-group-item list-group-item-action">🎟️ check status</a>
    <a href="routes.php" class="list-group-item list-group-item-action">📍 Routes</a>
    <a href="logout.php" class="list-group-item list-group-item-action">🚪 Logout</a>
  </div>

  <!-- Main content -->
  <div class="content">
    <div class="container mt-5 pt-5"> <!-- pt-5 pushes cards lower -->
      <div class="row g-4 mb-5">
        <div class="col-md-4">
          <div class="card dashboard-card text-center">
            <img src="/ebus/images/8.png" alt="Bookings">
            <div class="p-3">
              <h4>🎟️ My Bookings</h4>
              <p>View and manage your bus tickets.</p>
              <a href="book_ticket.php" class="btn btn-light btn-sm">Go</a>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card dashboard-card text-center">
            <img src="/ebus/images/0.jpeg" alt="Passes">
            <div class="p-3">
              <h4>🚌 My Passes</h4>
              <p>Apply and check your bus passes.</p>
              <a href="my_passes.php" class="btn btn-light btn-sm">Go</a>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card dashboard-card text-center">
            <img src="/ebus/images/5.png" alt="Routes">
            <div class="p-3">
              <h4>📍 Routes</h4>
              <p>Explore all available bus routes.</p>
              <a href="routes.php" class="btn btn-light btn-sm">Go</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
