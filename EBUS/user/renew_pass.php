<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit();
}

include(__DIR__ . '/../includes/db.php');
include(__DIR__ . '/../includes/api_config.php');

$error = '';
$user_email = is_array($_SESSION['user']) ? $_SESSION['user']['email'] : $_SESSION['user'];

// fetch
$stmt = $conn->prepare("SELECT * FROM passes WHERE email=? ORDER BY id DESC LIMIT 1");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$old_pass = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$old_pass) {
    echo "<script>alert('No existing pass found. Please apply for a new pass first.'); window.location.href='apply_pass.php';</script>";
    exit();
}

// Fetch user info for name
$stmt = $conn->prepare("SELECT id, name FROM users WHERE email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$stmt->bind_result($user_id, $user_name);
$stmt->fetch();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name          = trim($_POST['name'] ?? '');
    $dob           = trim($_POST['dob'] ?? '');
    $age           = (int)trim($_POST['age'] ?? 0);
    $gender        = trim($_POST['gender'] ?? '');
    $from_location = trim($_POST['from_location'] ?? '');
    $to_location   = trim($_POST['to_location'] ?? '');
    $distance_km   = floatval($_POST['distance_km'] ?? 0);
    $route         = trim($_POST['route'] ?? '');
    $pass_type     = trim($_POST['pass_type'] ?? '');
    $duration      = trim($_POST['renew_duration'] ?? '');
    $email         = trim($_POST['email'] ?? '');
    $address       = trim($_POST['address'] ?? '');

    // calculate validity
    $old_to   = $old_pass['pass_to'];
    $new_from = date("Y-m-d", strtotime($old_to . " +1 day"));
    
    // If old pass is already expired, start from today
    if(strtotime($new_from) < strtotime(date("Y-m-d"))) {
        $new_from = date("Y-m-d");
    }

    if ($duration == "1 Month") {
        $new_to = date("Y-m-d", strtotime($new_from . " +1 month"));
    } elseif ($duration == "3 Months") {
        $new_to = date("Y-m-d", strtotime($new_from . " +3 months"));
    } elseif ($duration == "6 Months") {
        $new_to = date("Y-m-d", strtotime($new_from . " +6 months"));
    } else {
        $new_to = date("Y-m-d", strtotime($new_from . " +1 year"));
    }

    // fallback if calculateFare is missing
    if (!function_exists('calculateFare')) {
        function calculateFare($dist, $type, $dur) {
            $rate = defined('RATE_PER_KM') ? RATE_PER_KM : 2;
            $base = $dist * $rate;
            $days = 30;
            if ($dur === '3 Months') $days = 90;
            if ($dur === '6 Months') $days = 180;
            $fare = $base * $days;
            if ($type === 'Student') $fare *= 0.5;
            return $fare;
        }
    }

    $fee = calculateFare($distance_km, $pass_type, $duration);

    // Keep old photo if new not uploaded
    $photoPath = $old_pass['photo'];
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photoName = time() . "_" . preg_replace('/\s+/', '_', basename($_FILES['photo']['name']));
        $photoDir = __DIR__ . "/uploads/photos";
        if (!is_dir($photoDir)) {
            mkdir($photoDir, 0777, true);
        }
        $targetFile = $photoDir . "/" . $photoName;
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
            $photoPath = "uploads/photos/" . $photoName;
        }
    }

    $_SESSION['pending_pass'] = [
        'user_id'       => $user_id,
        'name'          => $name,
        'email'         => $email,
        'age'           => $age,
        'dob'           => $dob,
        'gender'        => $gender,
        'from_location' => $from_location,
        'to_location'   => $to_location,
        'distance_km'   => $distance_km,
        'route'         => $route,
        'pass_type'     => $pass_type,
        'pass_duration' => $duration,
        'pass_from'     => $new_from,
        'pass_to'       => $new_to,
        'address'       => $address,
        'photo'         => $photoPath,
        'fee'           => $fee
    ];
    header("Location: payment.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>E-BUS - Renew Pass</title>
  
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
            <span class="user-name"><?= htmlspecialchars($user_name) ?></span>
            <span class="user-role">Passenger</span>
          </div>
        </div>
      </div>
    </header>

    <div class="page-content">
      
      <div class="mb-lg">
        <a href="my_passes.php" class="text-primary text-small mb-sm inline-block">&larr; Back to My Passes</a>
        <h1 class="page-title mb-0">Renew Pass</h1>
        <p class="text-secondary">Verify your previous details to renew your pass</p>
      </div>

      <form method="post" enctype="multipart/form-data">
        <div class="grid-2" style="grid-template-columns: 2fr 1fr;">
          
          <!-- Form Section -->
          <div class="flex-col gap-lg">
            
            <div class="card">
              <div class="card-header">
                <h3 class="card-title mb-0">Personal Details</h3>
              </div>
              <div class="card-body">
                <div class="form-group">
                  <label class="form-label">Full Name</label>
                  <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($old_pass['name']) ?>" required>
                </div>
                <div class="grid-2">
                  <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($old_pass['email']) ?>" readonly required>
                  </div>
                  <div class="form-group">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="dob" class="form-control" value="<?= htmlspecialchars($old_pass['dob']) ?>" required>
                  </div>
                </div>
                <div class="grid-2">
                  <div class="form-group">
                    <label class="form-label">Age</label>
                    <input type="number" name="age" class="form-control" value="<?= htmlspecialchars($old_pass['age']) ?>" required>
                  </div>
                  <div class="form-group">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-control form-select" required>
                      <option value="Male" <?= $old_pass['gender']=='Male'?'selected':'' ?>>Male</option>
                      <option value="Female" <?= $old_pass['gender']=='Female'?'selected':'' ?>>Female</option>
                      <option value="Other" <?= $old_pass['gender']=='Other'?'selected':'' ?>>Other</option>
                    </select>
                  </div>
                </div>
                <div class="form-group mb-0">
                  <label class="form-label">Full Address</label>
                  <textarea name="address" class="form-control" rows="2" required><?= htmlspecialchars($old_pass['address']) ?></textarea>
                </div>
              </div>
            </div>

            <div class="card">
              <div class="card-header">
                <h3 class="card-title mb-0">Pass Details</h3>
              </div>
              <div class="card-body">
                <div class="grid-2">
                  <div class="form-group">
                    <label class="form-label">Pass Type</label>
                    <select name="pass_type" id="passType" class="form-control form-select" required>
                      <option value="Regular" <?= $old_pass['pass_type']=='Regular'?'selected':'' ?>>Regular</option>
                      <option value="Student" <?= $old_pass['pass_type']=='Student'?'selected':'' ?>>Student</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label class="form-label">Renew Duration</label>
                    <select name="renew_duration" id="passDuration" class="form-control form-select" required>
                      <option value="1 Month">1 Month</option>
                      <option value="3 Months">3 Months</option>
                      <option value="6 Months">6 Months</option>
                    </select>
                  </div>
                </div>
                
                <div class="grid-2 mb-md">
                  <div class="form-group">
                    <label class="form-label">From Location</label>
                    <input type="text" name="from_location" id="fromInput" class="form-control" value="<?= htmlspecialchars($old_pass['from_location']) ?>" required>
                  </div>
                  <div class="form-group">
                    <label class="form-label">To Location</label>
                    <input type="text" name="to_location" id="toInput" class="form-control" value="<?= htmlspecialchars($old_pass['to_location']) ?>" required>
                  </div>
                </div>
                
                <div class="form-group">
                  <label class="form-label">Distance (KM)</label>
                  <input type="number" step="0.1" name="distance_km" id="distanceKm" class="form-control" value="<?= htmlspecialchars($old_pass['distance_km']) ?>" required>
                </div>
                
                <div class="card p-md bg-light mb-0">
                  <div class="text-small text-secondary mb-xs">Previous Expiry</div>
                  <div class="font-medium"><?= date('d M Y', strtotime($old_pass['pass_to'])) ?></div>
                  <div class="text-small text-success mt-xs">New validity will begin after this date.</div>
                </div>

                <input type="hidden" name="route" value="<?= htmlspecialchars($old_pass['route'] ?? 'Standard') ?>">
              </div>
            </div>

          </div>

          <!-- Upload and Submit Section -->
          <div class="flex-col gap-lg">
            
            <div class="card">
              <div class="card-header">
                <h3 class="card-title mb-0">Photo</h3>
              </div>
              <div class="card-body text-center">
                <div id="photoPreview" class="bg-light mb-md flex items-center justify-center" style="width: 150px; height: 180px; margin: 0 auto; border: 2px dashed var(--border); border-radius: var(--radius-sm); overflow: hidden;">
                  <?php if($old_pass['photo']): ?>
                    <img id="photoImg" src="../<?= htmlspecialchars($old_pass['photo']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                  <?php else: ?>
                    <span class="text-secondary text-small" id="photoPlaceholder">No Photo</span>
                    <img id="photoImg" src="" style="display: none; width: 100%; height: 100%; object-fit: cover;">
                  <?php endif; ?>
                </div>
                <input type="file" name="photo" id="photoInput" accept="image/*" class="form-control">
                <div class="text-small text-muted mt-xs">Upload new photo to update. (Optional)</div>
              </div>
            </div>

            <div class="card">
              <div class="card-body">
                <div class="flex justify-between items-center mb-sm">
                  <span class="text-secondary">Base Fare</span>
                  <span class="font-medium" id="summaryBase">&#8377;0</span>
                </div>
                <div class="flex justify-between items-center mb-sm">
                  <span class="text-secondary">Discount</span>
                  <span class="font-medium text-success" id="summaryDiscount">-&#8377;0</span>
                </div>
                <hr style="border: 0; border-top: 1px dashed var(--border); margin: var(--space-md) 0;">
                <div class="flex justify-between items-center">
                  <span class="font-bold">Total Estimated</span>
                  <span class="font-bold text-primary" style="font-size: 20px;" id="summaryTotal">&#8377;0</span>
                </div>
              </div>
              <div class="card-footer">
                <button type="submit" class="btn btn-primary w-full">Proceed to Payment</button>
              </div>
            </div>

          </div>

        </div>
      </form>

    </div>
  </main>
</div>

<script src="../js/app.js"></script>
<script src="../js/sidebar.js"></script>
<script src="../js/pass.js"></script>
<script>
  // Trigger initial calculation
  window.addEventListener('load', function() {
    if(typeof calculateFare === 'function') {
      calculateFare();
    }
  });
</script>
</body>
</html>