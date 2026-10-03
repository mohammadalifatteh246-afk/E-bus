<?php 
session_start();
if(!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit;
}
$user_session = $_SESSION['user'];
$user_name = is_array($user_session) ? ($user_session['name'] ?? 'Passenger') : 'Passenger';
// If it's just a string, we might want to fetch from DB, but 'Passenger' is fine for a quick fallback since they can just re-login.
// To be perfect:
if (!is_array($user_session)) {
    include_once("../includes/db.php");
    $stmt = $conn->prepare("SELECT name FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $user_session);
    $stmt->execute();
    $u = $stmt->get_result()->fetch_assoc();
    if($u) { $user_name = $u['name']; }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>E-BUS - Home</title>
  
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
      <a href="home.php" class="nav-item active"><span class="nav-icon">&#127968;</span> Home</a>
      <a href="book_ticket.php" class="nav-item"><span class="nav-icon">&#127915;</span> Book Ticket</a>
      <a href="my_passes.php" class="nav-item"><span class="nav-icon">&#129706;</span> My Passes</a>
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
        <h1 class="page-title">Welcome back, <?= htmlspecialchars($user_name) ?>!</h1>
        <p class="text-secondary">Where would you like to travel today?</p>
      </div>

      <div class="grid-2 mb-lg">
        <div class="card p-lg text-center" style="cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='none'" onclick="window.location.href='book_ticket.php'">
          <div style="font-size: 48px; margin-bottom: var(--space-md);">&#127915;</div>
          <h3 class="font-bold mb-xs text-primary">Book a Ticket</h3>
          <p class="text-secondary text-small">Find routes, select seats, and book your journey instantly.</p>
        </div>

        <div class="card p-lg text-center" style="cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='none'" onclick="window.location.href='apply_pass.php'">
          <div style="font-size: 48px; margin-bottom: var(--space-md);">&#129706;</div>
          <h3 class="font-bold mb-xs text-primary">Apply for Pass</h3>
          <p class="text-secondary text-small">Get daily travel convenience with regular and student passes.</p>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h3 class="card-title mb-0">Recent Activity</h3>
        </div>
        <div class="card-body bg-light text-center p-xl">
          <div class="text-secondary">No recent trips. Ready to book your first ticket?</div>
          <a href="book_ticket.php" class="btn btn-primary mt-md">Book Now</a>
        </div>
      </div>
    </div>
  </main>
</div>

<script src="../js/app.js"></script>
<script src="../js/sidebar.js"></script>
</body>
</html>