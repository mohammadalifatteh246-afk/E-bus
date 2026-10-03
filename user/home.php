<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Bus Pass Management - Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
  <script src="../assets/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

  <style>
    body, html {
      margin: 0;
      font-family: Arial, sans-serif;
      scroll-behavior: smooth; /* smooth scroll */
    }
    .hero {
      background: url("/ebus/images/3.jpg") no-repeat center center/cover;
      height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      color: white;

    }
    .navbar {
      background: rgba(0, 0, 0, 0.7);
    }
    .navbar a {
      color: white !important;
      margin-right: 15px;
      font-weight: 500;
    }
    .navbar a:hover {
      color: orange !important;
    }
    .hero-content {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  text-align: center;
  color: white;
}

.hero-content h1 {
  font-size: 48px;
  font-weight: bold;
  margin-bottom: 15px;
}

.hero-content p {
  font-size: 20px;
  margin-bottom: 25px;
}

.hero-content .btn {
  font-size: 20px;
  padding: 12px 30px;
}
    section {
      padding: 60px 20px;
    }
    #about {
      background: #f8f9fa;
      text-align: center;
    }
    #contact {
      background: #e9ecef;
      text-align: center;
    }
    footer {
      background: rgba(0,0,0,0.8);
      color: #ccc;
      text-align: center;
      padding: 15px;
      margin-top: 20px;
    }
  </style>
</head>
<body>

<!-- Hero Section -->
<div class="hero">
  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg">
    <div class="container">
      <a class="navbar-brand text-white" href="home.php">Welcome to Bus Pass Management</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
       <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link active" href="home.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
          <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
          <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
          <li class="nav-item"><a class="nav-link" href="index.php">User Login</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Hero Content -->
  <div class="hero-content">
    <h1>Welcome to Bus Pass Management</h1>
    <p>Apply, Renew and Manage your bus pass easily.</p>
    <a href="index.php" class="btn btn-warning btn-lg">Login</a>
  </div>
</div>
  


  <!-- Why Choose JayBus Section -->
<section class="py-5 text-white" style="background: rgba(0,0,0,0.75);">
  <div class="container">
    <div class="text-center mb-5">
      <h2>Why Choose JayBus For E Bus Pass And Ticket Booking?</h2>
      <p>JayBus is India’s fastest growing online ticket booking platform And E Bus Pass System Provide. We are the official ticketing partner of several GSRTC operators and over 4000+ private bus partners covering more than 3,50,000 bus routes.</p>
    </div>

    <div class="row text-center g-4">
      <div class="col-md-3">
        <div class="feature-card">
          <img src="https://cdn-icons-png.flaticon.com/512/854/854894.png" alt="Routes" width="50">
          <h5>3,50,000+ Bus Routes</h5>
          <p>Offering unparalleled choices for your travel needs.</p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="feature-card">
          <img src="https://cdn-icons-png.flaticon.com/512/888/888879.png" alt="Bus Partners" width="50">
          <h5>4000+ Bus Partners</h5>
          <p>Ranging from State RTCs to private partners.</p>
        </div>
      </div>
      <div class="col-md-3">
  <div class="feature-card text-center">
    <img src="https://cdn-icons-png.flaticon.com/512/2910/2910760.png" alt="E-Pass" width="50">
    <h5>My E-Pass</h5>
    <p>View your active bus pass, validity, and travel benefits instantly.</p>
  </div>
</div>
      <div class="col-md-3">
        <div class="feature-card">
          <img src="https://cdn-icons-png.flaticon.com/512/3221/3221810.png" alt="Fast Booking" width="50">
          <h5>Fastest Bus Booking</h5>
          <p>Swift and seamless bus ticket booking experience.</p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="feature-card">
          <img src="https://cdn-icons-png.flaticon.com/512/725/725643.png" alt="Support" width="50">
          <h5>24/7 Customer Support</h5>
          <p>Available for all your bus booking needs.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Online Bus Booking Services Section -->
<section class="py-5 text-white" style="background: rgba(0,0,0,0.85);">
  <div class="container">
    <div class="text-center mb-5">
      <h2>Online Bus Booking And E Buss Pass Services</h2>
      <p>JayBus is India`s leading online bus ticket booking and buss pass service provider. Check out budget-friendly offers and save big with discount coupons. Compare schedules, prices, and plan an ideal bus journey.</p>
    </div>

    <div class="mb-4">
      <h4>Booking Services</h4>
      <p>With JayBus, travellers can book bus tickets online at the lowest fares. Choose from Private and GSRTC buses online. Save time and money using JayCash for refunds.</p>
    </div>

    <div class="mb-4">
      <h4>Bus Types</h4>
      <div class="row g-3">
        <div class="col-md-3">AC Buses</div>
        <div class="col-md-3">Non AC Buses</div>
        <div class="col-md-3">Government Buses</div>
        <div class="col-md-3">Mini Buses</div>
        <div class="col-md-3">Volvo AC Buses</div>
        <div class="col-md-3">Sleeper AC Buses</div>
        <div class="col-md-3">Sleeper Buses</div>
        <div class="col-md-3">Electric Buses</div>
        <div class="col-md-3">Express Buses</div>
      </div>
      <p class="mt-3">The fare depends on bus type, operator, distance, amenities, and season. Travelling by bus is cost-effective and convenient.</p>
    </div>

    <div class="mb-4">
      <h4>Benefits of Booking Bus Tickets Online</h4>
      <ul>
        <li>Avoid long queues at offline bus counters.</li>
        <li>No hassle of travel agents.</li>
        <li>Choose from multiple bus services.</li>
        <li>Book Private and SRTC bus tickets online.</li>
        <li>Check ticket availability, bus timings, price, and boarding/dropping points online.</li>
        <li>Access discounts and cashback offers.</li>
        <li>Free cancellation - get 100% refund if plans change.</li>
        <li>24/7 customer support.</li>
        <li>Simple, safe, and secure transactions.</li>
      </ul>
    </div>
  
  <div class="md-4">
        <h4> Benefits of E-Bus Pass</h4>
        <ul>
          <li> Unlimited travel on selected routes</li>
          <li> Cost-effective compared to daily tickets</li>
          <li> Flexible pass options – Monthly, Quarterly, Yearly</li>
          <li> Digital pass – no need to carry paper tickets</li>
          <li> Quick renewal with one click</li>
          <li> Secure online payment options</li>
        </ul>
  </div>
      </div>
</section>

<!-- Footer -->
<footer class="bg-dark text-light pt-5 mt-auto">
  <div class="container">
    <div class="row">

      <!-- Quick Links -->
      <div class="col-md-3 mb-4">
        <h5>Quick Links</h5>
        <ul class="list-unstyled">
          <li><a href="routes.php" class="text-light text-decoration-none">Popular Bus Routes</a></li>
          <li><a href="#" class="text-light text-decoration-none">Popular Cities</a></li>
          <li><a href="#" class="text-light text-decoration-none">About E-Bus</a></li>
          <li><a href="contact.php" class="text-light text-decoration-none">Contact Us</a></li>
          <li><a href="#" class="text-light text-decoration-none">Sitemap</a></li>
          <li><a href="#" class="text-light text-decoration-none">Offers</a></li>
          <li><a href="#" class="text-light text-decoration-none">Careers</a></li>
        </ul>
      </div>

      <!-- Legal & Info -->
      <div class="col-md-3 mb-4">
        <h5>Info & Legal</h5>
        <ul class="list-unstyled">
          <li><a href="#" class="text-light text-decoration-none">Info</a></li>
          <li><a href="#" class="text-light text-decoration-none">T&C</a></li>
          <li><a href="#" class="text-light text-decoration-none">Privacy Policy</a></li>
          <li><a href="#" class="text-light text-decoration-none">User Agreement</a></li>
          <li><a href="#" class="text-light text-decoration-none">Report Security Issues</a></li>
        </ul>
      </div>

      <!-- Services -->
      <div class="col-md-3 mb-4">
        <h5>Services</h5>
        <ul class="list-unstyled">
          <li><a href="#" class="text-light text-decoration-none">Bus Operator Registration</a></li>
          <li><a href="#" class="text-light text-decoration-none">Agent Registration</a></li>
          <li><a href="#" class="text-light text-decoration-none">Insurance Partner</a></li>
          <li><a href="#" class="text-light text-decoration-none">Primo Bus</a></li>
          <li><a href="#" class="text-light text-decoration-none">Bus Timetable</a></li>
        </ul>
      </div>

      <!-- Global Sites & Partners -->
      <div class="col-md-3 mb-4">
        <h5>Global Sites</h5>
        <ul class="list-unstyled">
          <li><a href="#" class="text-light text-decoration-none">India</a></li>
          <li><a href="#" class="text-light text-decoration-none">Singapore</a></li>
          <li><a href="#" class="text-light text-decoration-none">Malaysia</a></li>
          <li><a href="#" class="text-light text-decoration-none">Indonesia</a></li>
          <li><a href="#" class="text-light text-decoration-none">Peru</a></li>
          <li><a href="#" class="text-light text-decoration-none">Colombia</a></li>
          <li><a href="#" class="text-light text-decoration-none">Cambodia</a></li>
          <li><a href="#" class="text-light text-decoration-none">Vietnam</a></li>
        </ul>
        <h6 class="mt-3">Our Partners</h6>
        <ul class="list-unstyled">
          <li><a href="#" class="text-light text-decoration-none">Goibibo Bus</a></li>
          <li><a href="#" class="text-light text-decoration-none">Goibibo Hotels</a></li>
          <li><a href="#" class="text-light text-decoration-none">MakeMyTrip Hotels</a></li>
        </ul>
      </div>

    </div>
    <hr class="bg-light">
    <p class="text-center mb-0">&copy; 2025 E-Bus. Trusted by over 56+ million happy customers globally. 🚌</p>
  </div>
</footer>



  <!-- Footer -->
  <footer class="bg-dark text-light py-3 mt-auto">
    <div class="container text-center">
      <p class="mb-0">&copy; 2025 E-Bus. Trusted by millions of happy customers 🚌</p>
    </div>
  </footer>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>