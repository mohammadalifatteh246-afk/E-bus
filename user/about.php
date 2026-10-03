<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>About - Bus Pass Management System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
  <script src="../assets/js/bootstrap.bundle.min.js"></script>

  <style>
    body {
      font-family: "Segoe UI", Arial, sans-serif;
      background: #f9fbfd;
      color: #333;
    }
    /* Navbar */
    .navbar {
      box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }
    .navbar-brand {
      font-size: 1.3rem;
      letter-spacing: 1px;
    }

    /* About Header */
    .about-header {
      background: linear-gradient(135deg, #007bff, #00c6ff);
      color: white;
      padding: 80px 20px;
      text-align: center;
      border-radius: 0 0 50px 50px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    .about-header h1 {
      font-size: 2.8rem;
      font-weight: bold;
    }
    .about-header p {
      font-size: 1.1rem;
      opacity: 0.9;
    }

    /* Cards */
    .card {
      border: none;
      border-radius: 20px;
      box-shadow: 0px 6px 20px rgba(0,0,0,0.08);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      background: #fff;
    }
    .card:hover {
      transform: translateY(-8px);
      box-shadow: 0px 10px 25px rgba(0,0,0,0.12);
    }
    .card i {
      font-size: 2.5rem;
      padding: 15px;
      border-radius: 50%;
      background: #f1f5ff;
      display: inline-block;
    }
    .card h4 {
      margin-top: 15px;
      font-weight: 600;
    }
    .card p {
      font-size: 0.95rem;
      color: #555;
    }

    /* Footer */
    footer {
      background: #222;
      color: #bbb;
      padding: 25px 0;
      margin-top: 50px;
    }
    footer a {
      color: #00c6ff;
      text-decoration: none;
      transition: color 0.3s;
    }
    footer a:hover {
      color: #fff;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand fw-bold" href="home.php"><i class="bi bi-bus-front"></i> Bus Pass</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
        <li class="nav-item"><a class="nav-link active" href="about.php">About</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
        <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="index.php">User Login</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- About Header -->
<div class="about-header">
  <h1>About Our System</h1>
  <p>Smart, Easy & Quick way to manage your Bus Pass</p>
</div>

<!-- About Content -->
<div class="container mt-5">
  <div class="row text-center">
    <div class="col-md-4 mb-4">
      <div class="card p-4">
        <i class="bi bi-laptop text-primary"></i>
        <h4 class="mt-3">Digital Application</h4>
        <p>Apply for your bus pass online without standing in long queues. A hassle-free process for everyone.</p>
      </div>
    </div>
    <div class="col-md-4 mb-4">
      <div class="card p-4">
        <i class="bi bi-arrow-repeat text-success"></i>
        <h4 class="mt-3">Easy Renewal</h4>
        <p>Renew your bus pass anytime from anywhere in just a few clicks. Save time & effort!</p>
      </div>
    </div>
    <div class="col-md-4 mb-4">
      <div class="card p-4">
        <i class="bi bi-bar-chart text-danger"></i>
        <h4 class="mt-3">Admin Management</h4>
        <p>Admins can manage users, routes, bookings, and generate reports with ease.</p>
      </div>
    </div>
  </div>
</div>

<!-- Footer -->
<footer class="text-center">
  <p>© <?php echo date("Y"); ?> Bus Pass Management System | <a href="contact.php">Contact Us</a></p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
