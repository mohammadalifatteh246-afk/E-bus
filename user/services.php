<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Our Services - E-Bus</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
  <script src="../assets/js/bootstrap.bundle.min.js"></script>
  <style>
    body {
      background: url('https://images.unsplash.com/photo-1504384308090-c894fdcc538d') no-repeat center center fixed;
      background-size: cover;
      font-family: 'Segoe UI', sans-serif;
    }
    .overlay {
      background: rgba(0,0,0,0.7);
      position: fixed;
      inset: 0;
      z-index: -1;
    }
    .services-section {
      padding: 80px 0;
      color: #fff;
      text-align: center;
    }
    .services-section h2 {
      font-weight: bold;
      margin-bottom: 50px;
      font-size: 2.5rem;
      color: #00d4ff;
    }
    .card {
      border-radius: 20px;
      background: rgba(255, 255, 255, 0.95);
      transition: all 0.4s ease-in-out;
      box-shadow: 0 6px 20px rgba(0,0,0,0.3);
      padding: 30px 20px;
    }
    .card:hover {
      transform: translateY(-10px) scale(1.03);
      box-shadow: 0 12px 30px rgba(0,0,0,0.5);
    }
    .card i {
      font-size: 55px;
      color: #007bff;
      margin-bottom: 20px;
      background: #e9f3ff;
      padding: 20px;
      border-radius: 50%;
      transition: 0.3s;
    }
    .card:hover i {
      background: #007bff;
      color: #fff;
    }
    .card h5 {
      font-weight: bold;
      margin-bottom: 15px;
    }
    footer {
      margin-top: 50px;
      text-align: center;
      color: #ddd;
      font-size: 14px;
    }
  </style>
</head>
<body>

<div class="overlay"></div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="home.php"><i class="bi bi-bus-front"></i> E-Bus</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
        <li class="nav-item"><a class="nav-link active" href="services.php">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
        <li class="nav-item"><a class="nav-link" href="index.php">Login</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Services Section -->
<div class="container services-section">
  <h2>Our Services</h2>
  <div class="row g-4">
    <div class="col-md-4">
      <div class="card text-center">
        <i class="bi bi-ticket-perforated"></i>
        <h5>Online Bus Pass</h5>
        <p>Apply and renew your bus passes online with ease and convenience.</p>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card text-center">
        <i class="bi bi-calendar-check"></i>
        <h5>Seat Booking</h5>
        <p>Book your bus seats in advance and travel stress-free with our system.</p>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card text-center">
        <i class="bi bi-geo-alt"></i>
        <h5>Route Finder</h5>
        <p>Check available routes and select the best option for your journey.</p>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card text-center">
        <i class="bi bi-cash-coin"></i>
        <h5>Secure Payments</h5>
        <p>Pay online using multiple secure payment gateways integrated.</p>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card text-center">
        <i class="bi bi-person-lines-fill"></i>
        <h5>User Profile</h5>
        <p>Manage your account, update details, and track booking history.</p>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card text-center">
        <i class="bi bi-graph-up"></i>
        <h5>Reports & Insights</h5>
        <p>Admins can generate reports and analyze travel data effectively.</p>
      </div>
    </div>
  </div>
</div>

<footer>
  <p>© <?php echo date("Y"); ?> E-Bus | All Rights Reserved</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>