<?php
include("includes/db.php");
session_start();

// Dashboard counts
$users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'];
$passes = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM passes"))['total'];
$bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM bookings"))['total'];

// Renewals count (status = Active)
$renewals = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM passes WHERE status='Active'"))['total'];

// Check auth - assuming auth.php exists and handles admin check
include("includes/auth.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>E-BUS - Admin Dashboard</title>
  
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
      <a href="dashboard.php" class="nav-item active"><span class="nav-icon">📊</span> Overview</a>
      <a href="routes.php" class="nav-item"><span class="nav-icon">🛣️</span> Routes</a>
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
        <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">☰</button>
        <div class="topbar-search">
          <input type="text" class="form-control" placeholder="🔍 Search...">
        </div>
      </div>
      <div class="topbar-right">
        <div class="user-profile">
          <div class="user-avatar" style="background-color: var(--navy); color: white;">AD</div>
          <div class="user-info">
            <span class="user-name">Admin</span>
            <span class="user-role">Administrator</span>
          </div>
          <span>▼</span>
        </div>
      </div>
    </header>

    <div class="page-content">
      <div class="mb-lg">
        <h1 class="page-title">Admin Dashboard</h1>
      </div>

      <!-- KPIs -->
      <div class="grid-4 mb-lg">
        <div class="card">
          <div class="card-body">
            <div class="text-secondary text-small mb-sm">Total Users</div>
            <div class="font-bold" style="font-size: 24px;"><?= $users ?></div>
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
            <div class="text-secondary text-small mb-sm">Total Bookings</div>
            <div class="font-bold" style="font-size: 24px;"><?= $bookings ?></div>
          </div>
        </div>
        
        <div class="card" style="border-left: 4px solid var(--primary);">
          <div class="card-body">
            <div class="text-secondary text-small mb-sm">Active Renewals</div>
            <div class="font-bold text-primary" style="font-size: 24px;"><?= $renewals ?></div>
          </div>
        </div>
      </div>

      <div class="grid-2 mb-lg" style="grid-template-columns: 2fr 1fr;">
        <!-- Pending Approvals (Mocked for dashboard layout) -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title mb-0">Pending Approvals</h3>
            <a href="manage_passes.php" class="text-primary text-small">View All</a>
          </div>
          <div class="card-body bg-light text-center text-muted" style="min-height: 150px; display:flex; align-items:center; justify-content:center;">
            View Pass applications in the Passes tab
          </div>
        </div>

        <!-- Route Map Context -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title mb-0">Route Map</h3>
          </div>
          <div class="card-body flex items-center justify-center bg-light" style="min-height: 200px; background-color: #e5eaf0; border-radius: var(--radius-sm);">
            <div class="text-muted flex-col items-center">
              <span style="font-size: 24px;">🗺️</span>
              <span class="text-small mt-sm">Active Routes Map</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<script src="js/app.js"></script>
<script src="js/sidebar.js"></script>
</body>
</html>