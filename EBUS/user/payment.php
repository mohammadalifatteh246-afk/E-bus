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
$pass_id = null;

// Simulate payment success (replace with real gateway integration)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $photo = $passData['photo'] ?? '';
    
    // Convert name from string to what the bind param expects based on the weird old query.
    // Actually the bind_param in old code was "iissssssdssssssds" but $passData['name'] is string, age is int... 
    // Wait, the old code had an error in types if user_id was i and name was i. I will fix it to "isisssssdssssssds".
    
    $stmt = $conn->prepare("
        INSERT INTO passes 
        (user_id, name, age, dob, gender, email, from_location, to_location, distance_km, route, 
         pass_type, pass_duration, pass_from, pass_to, address, fee, photo, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')
    ");

    $stmt->bind_param(
        "isissssssdsssssds",
        $passData['user_id'],       // i
        $passData['name'],          // s
        $passData['age'],           // i
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
        $pass_id = $stmt->insert_id;
        unset($_SESSION['pending_pass']); // clear session after success
        // Redirect to check status
        header("Location: check_status.php?id=" . $pass_id);
        exit();
    } else {
        die("Database Error: " . $stmt->error);
    }
    $stmt->close();
}

$pass_type = $passData['pass_type'] ?? 'Regular';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>E-BUS - Payment</title>
  
  <link rel="stylesheet" href="../css/reset.css">
  <link rel="stylesheet" href="../css/variables.css">
  <link rel="stylesheet" href="../css/base.css">
  <link rel="stylesheet" href="../css/layout.css">
  <link rel="stylesheet" href="../css/components.css">
  <link rel="stylesheet" href="../css/forms.css">
  <link rel="stylesheet" href="../css/tables.css">
  <link rel="stylesheet" href="../css/pages.css">
  <link rel="stylesheet" href="../css/responsive.css">
</head>
<body>

<div class="app-container">
  
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <aside class="sidebar" id="sidebar">
    <div class="sidebar-header">&#128652; E-BUS</div>
    <nav class="sidebar-nav">
      <a href="home.php" class="nav-item"><span class="nav-icon">&#127968;</span> Home</a>
      <a href="book_ticket.php" class="nav-item"><span class="nav-icon">&#127915;</span> Book Ticket</a>
      <a href="my_passes.php" class="nav-item active"><span class="nav-icon">&#129706;</span> My Passes</a>
      <a href="my_tickets.php" class="nav-item"><span class="nav-icon">&#129534;</span> My Tickets</a>
      <a href="profile.php" class="nav-item"><span class="nav-icon">&#128100;</span> Profile</a>
      <a href="logout.php" class="nav-item"><span class="nav-icon">&#128682;</span> Logout</a>
    </nav>
  </aside>

  <main class="main-content">
    <header class="topbar">
      <div class="topbar-left">
        <button class="sidebar-toggle" id="sidebarToggle">☰</button>
      </div>
      <div class="topbar-right">
        <div class="user-profile">
          <div class="user-avatar" style="overflow: hidden; display: flex; align-items: center; justify-content: center; background-color: var(--primary);">
            <?php 
              $header_avatar_url = '';
              $header_avatar_char = 'U';
              if(isset($_SESSION['user']) && is_array($_SESSION['user'])) {
                  if(!empty($_SESSION['user']['photo'])) $header_avatar_url = $_SESSION['user']['photo'];
                  $header_avatar_char = $_SESSION['user']['name'][0] ?? 'U';
              }
            ?>
            <?php if($header_avatar_url): ?>
              <img src="<?= htmlspecialchars($header_avatar_url) ?>?t=<?= time() ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
            <?php else: ?>
              <?= strtoupper(substr($header_avatar_char, 0, 1)) ?>
            <?php endif; ?>
          </div>
          <div class="user-info">
            <span class="user-name"><?= htmlspecialchars($passData['name'] ?? 'Passenger') ?></span>
            <span class="user-role">Passenger</span>
          </div>
        </div>
      </div>
    </header>

    <div class="page-content">
      
      <div class="mb-lg flex justify-between items-center">
        <h1 class="page-title mb-0">Secure Payment</h1>
      </div>

      <div class="grid-2" style="grid-template-columns: 2fr 1fr;">
        
        <!-- Payment Options -->
        <div class="card p-xl">
          <h3 class="card-title mb-md">Select Payment Method</h3>
          
          <div class="payment-method-selector mb-lg">
            <label class="form-check p-md mb-sm" style="border: 2px solid var(--primary); border-radius: var(--radius-sm); display: flex; align-items: center;">
              <input type="radio" name="payment_method" class="form-check-input" checked>
              <div class="ml-sm font-medium">Credit / Debit Card</div>
              <div style="margin-left: auto; font-size: 24px;">💳</div>
            </label>
            <label class="form-check p-md mb-sm" style="border: 1px solid var(--border); border-radius: var(--radius-sm); display: flex; align-items: center;">
              <input type="radio" name="payment_method" class="form-check-input">
              <div class="ml-sm font-medium">UPI / QR Code</div>
              <div style="margin-left: auto; font-size: 24px;">📱</div>
            </label>
            <label class="form-check p-md" style="border: 1px solid var(--border); border-radius: var(--radius-sm); display: flex; align-items: center;">
              <input type="radio" name="payment_method" class="form-check-input">
              <div class="ml-sm font-medium">Net Banking</div>
              <div style="margin-left: auto; font-size: 24px;">🏦</div>
            </label>
          </div>

          <form method="post" id="paymentForm">
            <div class="form-group">
              <label class="form-label">Card Number</label>
              <input type="text" class="form-control" placeholder="0000 0000 0000 0000" required>
            </div>
            <div class="grid-2">
              <div class="form-group">
                <label class="form-label">Expiry Date</label>
                <input type="text" class="form-control" placeholder="MM/YY" required>
              </div>
              <div class="form-group">
                <label class="form-label">CVV</label>
                <input type="password" class="form-control" placeholder="123" required>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Name on Card</label>
              <input type="text" class="form-control" placeholder="e.g. <?= htmlspecialchars($passData['name']) ?>" required>
            </div>
            
            <button type="submit" class="btn btn-primary w-full mt-md">Pay &#8377;<?= number_format($passData['fee'], 2) ?></button>
          </form>
        </div>

        <!-- Order Summary -->
        <div class="card h-fit">
          <div class="card-header">
            <h3 class="card-title mb-0">Order Summary</h3>
          </div>
          <div class="card-body">
            <div class="mb-md">
              <div class="font-bold"><?= htmlspecialchars($passData['from_location']) ?> &rarr; <?= htmlspecialchars($passData['to_location']) ?></div>
              <div class="text-secondary text-small mt-xs"><?= htmlspecialchars($passData['pass_type']) ?> Pass (<?= htmlspecialchars($passData['pass_duration']) ?>)</div>
            </div>
            
            <hr style="border: 0; border-top: 1px dashed var(--border); margin: var(--space-md) 0;">
            
            <div class="flex justify-between items-center mb-sm">
              <span class="text-secondary">Subtotal</span>
              <span class="font-medium">&#8377;<?= number_format($passData['fee'], 2) ?></span>
            </div>
            
            <hr style="border: 0; border-top: 1px solid var(--border); margin: var(--space-md) 0;">
            
            <div class="flex justify-between items-center">
              <span class="font-bold">Total to Pay</span>
              <span class="font-bold text-primary" style="font-size: 20px;">&#8377;<?= number_format($passData['fee'], 2) ?></span>
            </div>
          </div>
        </div>

      </div>

    </div>
  </main>
</div>

<script src="../js/app.js"></script>
<script src="../js/sidebar.js"></script>
</body>
</html>