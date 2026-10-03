<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit();
}

$user_email = is_array($_SESSION['user']) ? $_SESSION['user']['email'] : $_SESSION['user'];

$success = "";
$error = "";

if (isset($_POST['update'])) {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);

    // photo upload (ignoring photo in UI for now, but keeping backend logic)
    $photoPath = null;
    if (!empty($_FILES['photo']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $fileName = time() . "_" . basename($_FILES['photo']['name']);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
            $photoPath = $targetFile;
        }
    }

    if ($photoPath) {
        $stmt = $conn->prepare("UPDATE users SET name=?, phone=?, photo=? WHERE email=?");
        $stmt->bind_param("ssss", $name, $phone, $photoPath, $user_email);
    } else {
        $stmt = $conn->prepare("UPDATE users SET name=?, phone=? WHERE email=?");
        $stmt->bind_param("sss", $name, $phone, $user_email);
    }

    if ($stmt->execute()) {
        $success = "Profile updated successfully!";
        // update session if it's an array
        if(is_array($_SESSION['user'])) {
            $_SESSION['user']['name'] = $name;
            if ($photoPath) {
                $_SESSION['user']['photo'] = $photoPath;
            }
        }
    } else {
        $error = "Failed to update profile.";
    }
}

$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>E-BUS - Update Profile</title>
  
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
      
      <div class="mb-lg">
        <a href="profile.php" class="text-primary text-small mb-sm inline-block">&larr; Back to Profile</a>
        <h1 class="page-title mb-0">Update Profile</h1>
      </div>

      <?php if($success): ?>
        <div class="card mb-md p-md bg-light text-success font-medium"><?= $success ?></div>
      <?php endif; ?>
      <?php if($error): ?>
        <div class="card mb-md p-md bg-light text-danger font-medium"><?= $error ?></div>
      <?php endif; ?>

      <div class="card" style="max-width: 600px;">
        <form method="post" enctype="multipart/form-data">
          <div class="card-body">
            <div class="form-group">
              <label class="form-label">Full Name</label>
              <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
            </div>
            <div class="grid-2">
              <div class="form-group">
                <label class="form-label">Email (Cannot be changed)</label>
                <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" readonly>
              </div>
              <div class="form-group">
                <label class="form-label">Phone Number</label>
                <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
              </div>
            </div>
            
            <div class="form-group">
              <label class="form-label">Profile Photo</label>
              <input type="file" name="photo" class="form-control" accept="image/*">
              <?php if(!empty($user['photo'])): ?>
                <div class="text-small text-muted mt-xs">You currently have a photo uploaded.</div>
              <?php endif; ?>
            </div>

          </div>
          <div class="card-footer flex justify-end gap-sm">
            <a href="profile.php" class="btn btn-secondary">Cancel</a>
            <button type="submit" name="update" class="btn btn-primary">Save Changes</button>
          </div>
        </form>
      </div>

    </div>
  </main>
</div>

<script src="../js/app.js"></script>
<script src="../js/sidebar.js"></script>
</body>
</html>