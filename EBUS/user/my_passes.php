<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit();
}

include("../includes/db.php");

// Get logged-in user
$user_email = is_array($_SESSION['user']) ? $_SESSION['user']['email'] : $_SESSION['user'];
$stmt = $conn->prepare("SELECT id, name, email FROM users WHERE email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_id = $user['id'];

// Fetch passes
$query = "SELECT p.id, p.pass_from, p.pass_to, p.from_location, p.to_location, p.distance_km, p.fee, p.pass_type, p.status, p.photo
          FROM passes p
          WHERE p.user_id = ?
          ORDER BY p.id DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$passes = $stmt->get_result();

$active_pass = null;
$pass_list = [];
while($row = $passes->fetch_assoc()) {
    $pass_list[] = $row;
    if ($row['status'] == 'Active' && !$active_pass) {
        $active_pass = $row; // Just grab the first active one for the card
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>E-BUS - My Passes</title>
  
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
            <span class="user-name"><?= htmlspecialchars($user['name']) ?></span>
            <span class="user-role">Passenger</span>
          </div>
        </div>
      </div>
    </header>

    <div class="page-content">
      
      <div class="flex justify-between items-center mb-lg">
        <h1 class="page-title mb-0">My Passes</h1>
        <a href="apply_pass.php" class="btn btn-primary">+ Apply for Pass</a>
      </div>

      <div class="grid-2" style="grid-template-columns: 1fr 2fr;">
        
        <div class="flex-col gap-sm">
          <?php if($active_pass): ?>
          <div class="digital-pass">
            <div class="digital-pass-header">
              <div class="font-bold" style="font-size: 20px;">E-BUS</div>
              <div class="badge" style="background-color: rgba(255,255,255,0.2); color: white;"><?= htmlspecialchars($active_pass['pass_type']) ?> Pass</div>
            </div>
            <div class="digital-pass-body">
              <div class="pass-photo">
                <?php if($active_pass['photo']): ?>
                  <img src="../<?= htmlspecialchars($active_pass['photo']) ?>" alt="Photo" style="width:100%; height:100%; object-fit:cover;">
                <?php else: ?>
                  <span style="font-size: 32px;">👤</span>
                <?php endif; ?>
              </div>
              <div class="pass-details">
                <div class="font-bold mb-xs" style="font-size: 18px;"><?= htmlspecialchars($user['name']) ?></div>
                <div class="text-small" style="color: rgba(255,255,255,0.7);">Pass ID: STU-<?= $active_pass['id'] ?></div>
                <div class="font-medium mt-sm text-small"><?= htmlspecialchars($active_pass['from_location']) ?> &harr; <?= htmlspecialchars($active_pass['to_location']) ?></div>
              </div>
            </div>
            <div class="digital-pass-footer">
              <div>
                <div class="text-small" style="color: rgba(255,255,255,0.7); margin-bottom: 2px;">Valid Till</div>
                <div class="font-bold"><?= date('d M Y', strtotime($active_pass['pass_to'])) ?></div>
              </div>
              <div>
                <a href="download_pass.php?id=<?= $active_pass['id'] ?>" class="btn btn-sm" style="background-color: white; color: black; font-weight: bold;">Download</a>
              </div>
            </div>
          </div>
          <a href="renew_pass.php" class="btn btn-outline mt-sm w-full">Renew Active Pass</a>
          <?php else: ?>
          <div class="card p-xl text-center bg-light">
            <div class="text-secondary mb-md">You don't have an active pass.</div>
            <a href="apply_pass.php" class="btn btn-primary">Apply Now</a>
          </div>
          <?php endif; ?>
        </div>

        <div class="card">
          <div class="card-header">
            <h3 class="card-title mb-0">Pass History</h3>
          </div>
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>Pass ID</th>
                  <th>Type</th>
                  <th>Route</th>
                  <th>Valid Till</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if(count($pass_list) > 0): ?>
                  <?php foreach($pass_list as $pass): ?>
                    <tr>
                      <td class="font-medium">APP-<?= $pass['id'] ?></td>
                      <td><?= htmlspecialchars($pass['pass_type']) ?></td>
                      <td><?= htmlspecialchars($pass['from_location']) ?> &harr; <?= htmlspecialchars($pass['to_location']) ?></td>
                      <td><?= date('d M Y', strtotime($pass['pass_to'])) ?></td>
                      <td>
                        <?php if($pass['status'] == 'Active'): ?>
                          <span class="badge badge-success">Active</span>
                        <?php elseif($pass['status'] == 'Pending'): ?>
                          <span class="badge badge-warning">Pending</span>
                        <?php elseif($pass['status'] == 'Cancelled'): ?>
                          <span class="badge badge-danger">Cancelled</span>
                        <?php else: ?>
                          <span class="badge badge-neutral"><?= htmlspecialchars($pass['status']) ?></span>
                        <?php endif; ?>
                      </td>
                      <td><a href="check_status.php?id=<?= $pass['id'] ?>" class="btn btn-outline btn-sm">Status</a></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td colspan="6" class="text-center text-muted py-xl">No passes applied yet.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
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