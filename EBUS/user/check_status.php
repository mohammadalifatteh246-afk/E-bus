<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include(__DIR__ . '/../includes/db.php');

$status_message = "";
$pass_details = null;

$pass_id = null;
if (isset($_GET['id'])) {
    $pass_id = intval($_GET['id']);
} elseif (isset($_POST['pass_id'])) {
    $pass_id = intval($_POST['pass_id']);
}

if ($pass_id) {
    $stmt = $conn->prepare("SELECT name, pass_type, pass_duration, route, pass_to, status FROM passes WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $pass_id);
        $stmt->execute();
        $stmt->bind_result($name, $pass_type, $pass_duration, $route, $expiry, $status);
        if ($stmt->fetch()) {
            $pass_details = [
                "id" => $pass_id,
                "name" => $name,
                "pass_type" => $pass_type,
                "pass_duration" => $pass_duration,
                "route" => $route,
                "expiry" => $expiry,
                "status" => $status
            ];
            
            if ($status == 'Pending') {
                $status_message = "Your pass is pending approval.";
            } elseif ($status == 'Active' || $status == 'Approved') {
                $status_message = "Your pass has been approved and is active.";
            } elseif ($status == 'Cancelled' || $status == 'Rejected') {
                $status_message = "Sorry, your pass has been rejected/cancelled.";
            } else {
                $status_message = "Status: " . htmlspecialchars($status);
            }
        } else {
            $status_message = "Invalid Pass ID. Please check and try again.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>E-BUS - Pass Status</title>
  
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
        <?php if(isset($_SESSION['user'])): 
          $u_name = is_array($_SESSION['user']) ? ($_SESSION['user']['name'] ?? 'Passenger') : 'Passenger';
        ?>
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
              <span class="user-name"><?= htmlspecialchars($u_name) ?></span>
              <span class="user-role">Passenger</span>
            </div>
          </div>
        <?php else: ?>
          <a href="../login.php" class="btn btn-primary btn-sm">Login</a>
        <?php endif; ?>
      </div>
    </header>

    <div class="page-content">
      
      <div class="mb-lg">
        <a href="my_passes.php" class="text-primary text-small mb-sm inline-block">&larr; Back to My Passes</a>
        <h1 class="page-title mb-0">Application Status</h1>
      </div>

      <div class="card p-xl mb-lg text-center" style="max-width: 600px; margin: 0 auto;">
        <h3 class="card-title mb-md">Check Pass Status</h3>
        <form method="post" class="flex gap-sm">
          <input type="text" name="pass_id" class="form-control flex-1" placeholder="Enter Pass ID (e.g. 123)" required value="<?= htmlspecialchars($pass_id ?? '') ?>">
          <button type="submit" class="btn btn-primary">Check Status</button>
        </form>
      </div>

      <?php if ($pass_details): ?>
      <div class="card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-body">
          
          <div class="text-center mb-lg">
            <h2 class="font-bold mb-xs">APP-<?= $pass_details['id'] ?></h2>
            <div class="text-secondary"><?= htmlspecialchars($pass_details['pass_type']) ?> Pass (<?= htmlspecialchars($pass_details['pass_duration']) ?>)</div>
          </div>

          <div class="status-timeline">
            <div class="timeline-step">
              <div class="step-indicator completed"></div>
              <div class="step-content">
                <div class="font-bold">Application Submitted</div>
                <div class="text-small text-secondary">Document uploaded successfully</div>
              </div>
            </div>
            
            <?php 
              $is_active = ($pass_details['status'] == 'Active' || $pass_details['status'] == 'Approved');
              $is_rejected = ($pass_details['status'] == 'Cancelled' || $pass_details['status'] == 'Rejected');
            ?>

            <div class="timeline-step">
              <div class="step-indicator <?= $is_active || $is_rejected ? 'completed' : 'active' ?>"></div>
              <div class="step-content">
                <div class="font-bold">Under Review</div>
                <div class="text-small text-secondary">Admin is verifying your details</div>
              </div>
            </div>
            
            <div class="timeline-step">
              <div class="step-indicator <?= $is_active ? 'completed' : ($is_rejected ? 'rejected' : '') ?>"></div>
              <div class="step-content">
                <div class="font-bold">Decision</div>
                <?php if($is_active): ?>
                  <div class="text-small text-success font-medium">Approved</div>
                <?php elseif($is_rejected): ?>
                  <div class="text-small text-danger font-medium">Rejected</div>
                <?php else: ?>
                  <div class="text-small text-secondary">Pending approval</div>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <?php if($is_active): ?>
            <div class="mt-xl text-center">
              <a href="download_pass.php?id=<?= $pass_details['id'] ?>" class="btn btn-primary w-full">Download Digital Pass</a>
            </div>
          <?php endif; ?>

        </div>
      </div>
      <?php elseif($status_message): ?>
      <div class="card p-xl mb-lg text-center bg-light text-danger font-medium" style="max-width: 600px; margin: 0 auto;">
        <?= htmlspecialchars($status_message) ?>
      </div>
      <?php endif; ?>

    </div>
  </main>
</div>

<script src="../js/app.js"></script>
<script src="../js/sidebar.js"></script>
</body>
</html>