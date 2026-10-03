<?php
include("includes/db.php");
session_start();

// Dashboard counts
$users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'];
$passes = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM passes"))['total'];
$bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM bookings"))['total'];

// ✅ Renewals count (status = Active)
$renewals = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM passes WHERE status='Active'"))['total'];

include("includes/auth.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <script src="assets/js/bootstrap.bundle.min.js"></script>
  <style>
    body {
      background: url("/ebus/images/23.png") no-repeat center center fixed;
      background-size: cover;
      font-family: 'Segoe UI', sans-serif;
    }
    .container, .card {
      background-color: rgba(255,255,255,0.95);
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 0 20px rgba(0,0,0,0.2);
    }
    .navbar-custom { background-color: rgba(0,0,0,0.7); }
    footer {
      text-align: center;
      padding: 15px;
      background: rgba(0,0,0,0.7);
      color: white;
      position: fixed;
      bottom: 0;
      width: 100%;
    }
    .card-icon { font-size: 2.5rem; }
    .hover-shadow:hover {
      transform: translateY(-5px);
      box-shadow: 0 0 25px rgba(0,0,0,0.3);
      transition: all 0.3s ease;
    }
    a.text-decoration-none { text-decoration: none; color: inherit; }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="#">
      <img src="/ebus/images/11.png" alt="E-Bus Logo" width="40" height="40">
      E-Bus Admin
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link active" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="manage_users.php">Users</a></li>
        <li class="nav-item"><a class="nav-link" href="manage_passes.php">Passes</a></li>
        <li class="nav-item"><a class="nav-link" href="manage_bookings.php">Bookings</a></li>
        <li class="nav-item"><a class="nav-link" href="routes.php">Routes</a></li>
        <li class="nav-item"><a class="nav-link" href="reports.php">Reports</a></li>
        <li class="nav-item"><a class="nav-link" href="manage_renewals.php">Manage Renewals</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Dashboard Cards -->
<div class="container mt-4">
  <h2 class="mb-4 text-dark">Dashboard Overview</h2>
  <div class="row g-4">
    <div class="col-md-3">
      <a href="manage_users.php" class="text-decoration-none">
        <div class="card text-center p-3 hover-shadow">
          <i class="bi bi-people-fill card-icon text-primary mb-2"></i>
          <h5>Total Users</h5>
          <h3 class="text-primary"><?= $users ?></h3>
        </div>
      </a>
    </div>
    <div class="col-md-3">
      <a href="manage_passes.php" class="text-decoration-none">
        <div class="card text-center p-3 hover-shadow">
          <i class="bi bi-card-checklist card-icon text-success mb-2"></i>
          <h5>Total Passes</h5>
          <h3 class="text-success"><?= $passes ?></h3>
        </div>
      </a>
    </div>
    <div class="col-md-3">
      <a href="manage_bookings.php" class="text-decoration-none">
        <div class="card text-center p-3 hover-shadow">
          <i class="bi bi-ticket-perforated-fill card-icon text-warning mb-2"></i>
          <h5>Total Bookings</h5>
          <h3 class="text-warning"><?= $bookings ?></h3>
        </div>
      </a>
    </div>
    <div class="col-md-3">
      <a href="manage_renewals.php" class="text-decoration-none">
        <div class="card text-center p-3 hover-shadow">
          <i class="bi bi-arrow-repeat card-icon text-danger mb-2"></i>
          <h5>Total Renewals</h5>
          <h3 class="text-danger"><?= $renewals ?></h3>
        </div>
      </a>
    </div>
  </div>
</div>

<footer>
  &copy; <?= date('Y') ?> E-Bus System. All Rights Reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>