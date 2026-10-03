<?php
session_start();
include("../includes/db.php");

//  user login redirect
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit();
}

$user_email = is_array($_SESSION['user']) ? $_SESSION['user']['email'] : $_SESSION['user'];

$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>E-BUS - My Profile</title>
  
  <link rel="stylesheet" href="../css/reset.css">
  <link rel="stylesheet" href="../css/variables.css">
  <link rel="stylesheet" href="../css/base.css">
  <link rel="stylesheet" href="../css/layout.css">
  <link rel="stylesheet" href="../css/components.css">
  <link rel="stylesheet" href="../css/forms.css">
  <link rel="stylesheet" href="../css/tables.css">
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
      <a href="my_passes.php" class="nav-item"><span class="nav-icon">&#129706;</span> My Passes</a>
      <a href="my_tickets.php" class="nav-item"><span class="nav-icon">&#129534;</span> My Tickets</a>
      <a href="profile.php" class="nav-item active"><span class="nav-icon">&#128100;</span> Profile</a>
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
        <h1 class="page-title mb-0">My Profile</h1>
      </div>

      <div class="grid-2" style="grid-template-columns: 1fr 2fr;">
        
        <!-- Profile Card -->
        <div class="card flex-col items-center p-xl text-center" style="align-self: start;">
          <div class="user-avatar mb-md" style="width: 100px; height: 100px; font-size: 32px; overflow: hidden; display: flex; align-items: center; justify-content: center; background-color: var(--primary);">
            <?php if(!empty($user['photo'])): ?>
              <img src="<?= htmlspecialchars($user['photo']) ?>?t=<?= time() ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
            <?php else: ?>
              <?= strtoupper(substr($user['name'], 0, 1)) ?>
            <?php endif; ?>
          </div>
          <h2 class="font-bold mb-xs" style="font-size: 24px;"><?= htmlspecialchars($user['name']) ?></h2>
          <div class="text-secondary mb-lg"><?= htmlspecialchars($user['email']) ?></div>
          <a href="update_profile.php" class="btn btn-outline w-full">Edit Profile</a>
        </div>

        <!-- Details -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title mb-0">Personal Information</h3>
          </div>
          <div class="card-body">
            
            <div class="grid-2 mb-lg">
              <div>
                <div class="text-small text-secondary mb-xs">Full Name</div>
                <div class="font-medium"><?= htmlspecialchars($user['name']) ?></div>
              </div>
              <div>
                <div class="text-small text-secondary mb-xs">Role</div>
                <div class="font-medium text-capitalize"><?= htmlspecialchars($user['role'] ?? 'Passenger') ?></div>
              </div>
            </div>

            <div class="grid-2 mb-lg">
              <div>
                <div class="text-small text-secondary mb-xs">Phone Number</div>
                <div class="font-medium"><?= htmlspecialchars($user['phone'] ?? 'N/A') ?></div>
              </div>
              <div>
                <div class="text-small text-secondary mb-xs">Joined On</div>
                <div class="font-medium"><?= isset($user['created_at']) ? date('d M Y', strtotime($user['created_at'])) : 'N/A' ?></div>
              </div>
            </div>

            <hr style="border: 0; border-top: 1px solid var(--border); margin: var(--space-lg) 0;">
            
            <h3 class="card-title mb-md">Account Actions</h3>
            <div class="flex gap-sm">
              <a href="logout.php" class="btn btn-danger btn-outline">Logout</a>
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