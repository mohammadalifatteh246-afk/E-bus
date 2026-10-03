<?php
session_start();
include(__DIR__ . '/../includes/db.php');
include(__DIR__ . '/../includes/api_config.php');

if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit();
}

// If no pending pass, redirect back
if (!isset($_SESSION['pending_pass'])) {
    header("Location: apply_pass.php");
    exit();
}

$passData    = $_SESSION['pending_pass'];
$distance_km = $passData['distance_km'] ?? 0;
$passData['distance_km'] = $distance_km;
$amount      = $_GET['amount'] ?? ($passData['fee'] ?? 0);
$passData['fee'] = $passData['fee'] ?? $amount;
$type        = $_GET['type']   ?? 'pass';

$success = false;

// Simulate payment success (replace with real gateway integration)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ✅ Use photo path from session (uploaded in apply_pass.php)
    $photo = $passData['photo'] ?? '';

    $stmt = $conn->prepare("
        INSERT INTO passes 
        (user_id, name, age, dob, gender, email, from_location, to_location, distance_km, route, 
         pass_type, pass_duration, pass_from, pass_to, address, fee, photo, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Active')
    ");

    $stmt->bind_param(
        "iissssssdssssssds",
        $passData['user_id'],       // i
        $passData['name'],          // i
        $passData['age'],           // s
        $passData['dob'],           // s
        $passData['gender'],        // s
        $passData['email'],         // s
        $passData['from_location'], // s
        $passData['to_location'],   // s
        $passData['distance_km'],   // d
        $passData['route'],         // s
        $passData['pass_type'],     // s
        $passData['pass_duration'], // s
        $passData['pass_from'],     // s
        $passData['pass_to'],       // s
        $passData['address'],       // s
        $passData['fee'],           // d
        $photo                      // s
    );

    if ($stmt->execute()) {
        $success = true;
        unset($_SESSION['pending_pass']); // clear session after success
    } else {
        die("Database Error: " . $stmt->error);
    }
    $stmt->close();
}

// Fare breakdown values
$pass_type       = $passData['pass_type'] ?? 'Regular';
$rate_per_km     = defined('RATE_PER_KM') ? RATE_PER_KM : 4;
$processing_fee  = defined('PROCESSING_FEE') ? PROCESSING_FEE : 2;
$rate_amount     = round($distance_km * $rate_per_km, 2);
$is_student      = (strcasecmp($pass_type, 'Student') === 0);
$subtotal        = $rate_amount + $processing_fee;
$discount_rate   = defined('STUDENT_DISCOUNT') ? STUDENT_DISCOUNT : 0.50;
$discount_amount = $is_student ? round($subtotal * $discount_rate, 2) : 0;
$total_amount    = floatval($amount ?? ($subtotal - $discount_amount));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payment - E-Bus</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/bootstrap.min.css">

  <!-- Leaflet CSS & JS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

  <script src="../assets/js/bootstrap.bundle.min.js"></script>
  <style>
    body {
      background: url("/ebus/images/18.png") no-repeat center center fixed;
      background-size: cover;
      font-family: 'Segoe UI', Tahoma, sans-serif;
      margin: 0;
    }
    .overlay {
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0, 0, 0, 0.55);
      z-index: -1;
    }
    .navbar-custom {
      background: rgba(0, 0, 0, 0.6);
      padding: 0.7rem 1rem;
    }
    .navbar-custom a {
      color: #fff;
      text-decoration: none;
      font-weight: 500;
    }
    .pay-container {
      max-width: 550px;
      margin: 50px auto 60px auto;
      background: rgba(255, 255, 255, 0.95);
      padding: 35px 40px;
      border-radius: 20px;
      box-shadow: 0 8px 30px rgba(0,0,0,0.4);
      backdrop-filter: blur(8px);
    }
    h2 {
      font-weight: 600;
      margin-bottom: 15px;
    }
    .route-badge {
      font-size: 14px;
      color: #444;
      margin-bottom: 15px;
      background: #f1f3f5;
      padding: 8px 14px;
      border-radius: 8px;
      display: inline-block;
      border: 1px solid #e2e8f0;
    }
    #map {
      height: 250px;
      width: 100%;
      border-radius: 12px;
      border: 1px solid #ddd;
      margin-bottom: 20px;
      z-index: 1;
    }
    .fare-card {
      background: #ffffff;
      border: 1px solid #dee2e6;
      border-radius: 12px;
      padding: 16px 20px;
      margin-bottom: 20px;
      text-align: left;
      box-shadow: 0 2px 6px rgba(0,0,0,0.04);
    }
    .fare-card h5 {
      font-size: 16px;
      font-weight: 700;
      margin-bottom: 12px;
      color: #333;
      border-bottom: 1px solid #f0f0f0;
      padding-bottom: 8px;
    }
    .fare-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 8px;
      font-size: 15px;
      color: #495057;
    }
    .fare-total-row {
      border-top: 2px dashed #ced4da;
      padding-top: 10px;
      margin-top: 10px;
      font-size: 18px;
      font-weight: 700;
      color: #198754;
    }
    .btn-pay {
      font-size: 18px;
      padding: 14px;
      border-radius: 12px;
      width: 100%;
      transition: 0.3s ease;
    }
    .btn-pay:hover {
      transform: scale(1.02);
    }
    .success-icon {
      font-size: 60px;
      color: #28a745;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>

<div class="overlay"></div>

<!-- Top Navbar -->
<nav class="navbar-custom">
  <a href="user_dashboard.php"><i class="bi bi-arrow-left-circle"></i> Back</a>
</nav>

<div class="pay-container text-center">
  <?php if($success): ?>
    <div class="success-icon">✅</div>
    <h2 class="text-success">Payment Successful!</h2>
    <p>Your bus pass has been generated and activated.</p>
    <a href="my_passes.php" class="btn btn-primary mt-3"><i class="bi bi-ticket-perforated"></i> View My Pass</a>
  <?php else: ?>
    <h2>💳 Complete Payment</h2>

    <?php if(!empty($passData['from_location']) && !empty($passData['to_location'])): ?>
      <div class="route-badge">
        <i class="bi bi-geo-alt-fill text-success"></i> <strong><?= htmlspecialchars($passData['from_location']) ?></strong>
        <i class="bi bi-arrow-right mx-1"></i>
        <i class="bi bi-geo-alt-fill text-danger"></i> <strong><?= htmlspecialchars($passData['to_location']) ?></strong>
      </div>
    <?php endif; ?>

    <!-- Read-only Route Map (Hidden per user request) -->
    <div id="map" style="display: none;"></div>

    <!-- Fare Breakdown Card -->
    <div class="fare-card">
      <h5><i class="bi bi-receipt"></i> Fare Breakdown</h5>
      <div class="fare-row">
        <span>Distance:</span>
        <span class="fw-semibold"><?= htmlspecialchars($distance_km) ?> km</span>
      </div>
      <div class="fare-row">
        <span>Rate: ₹<?= $rate_per_km ?>/km × <?= htmlspecialchars($distance_km) ?>:</span>
        <span class="fw-semibold">₹<?= number_format($rate_amount, 2) ?></span>
      </div>
      <div class="fare-row">
        <span>Processing Fee:</span>
        <span class="fw-semibold">₹<?= $processing_fee ?></span>
      </div>
      <?php if ($is_student): ?>
      <div class="fare-row text-success">
        <span>Discount (Student 50%):</span>
        <span class="fw-semibold">-₹<?= number_format($discount_amount, 2) ?></span>
      </div>
      <?php endif; ?>
      <div class="fare-row fare-total-row">
        <span>Total:</span>
        <span>₹<?= number_format($total_amount, 2) ?></span>
      </div>
    </div>

    <form method="post">
      <button type="submit" class="btn btn-success btn-pay">
        <i class="bi bi-credit-card-fill"></i> Pay Now (₹<?= number_format($total_amount, 2) ?>)
      </button>
    </form>
  <?php endif; ?>
</div>

<?php if(!$success): ?>

<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>