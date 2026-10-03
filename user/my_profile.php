<?php
session_start();
include("../includes/db.php");

//  user login redirect
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

// User  email session 
$email = $_SESSION['user'];
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
  <script src="../assets/js/bootstrap.bundle.min.js"></script>
  <style>
    body {
      background: url("/ebus/images/15.png") no-repeat center center fixed;
      background-size: cover;
      font-family: "Segoe UI", sans-serif;
    }
    .profile-card {
      max-width: 550px;
      margin: auto;
      background: rgba(255, 255, 255, 0.9); /*  transparency  BG */
      border-radius: 20px;
      padding: 30px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.25);
      animation: fadeIn 0.6s ease-in-out;
      position: relative;
    }
    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(20px);}
      to {opacity: 1; transform: translateY(0);}
    }
    .btn-custom {
      background: #2193b0;
      color: #fff;
      border-radius: 30px;
      font-weight: 500;
    }
    .btn-custom:hover {
      background: #176c83;
      color: #fff;
    }
    .form-control {
      border-radius: 12px;
    }
    .photo-box img {
      border: 4px solid #2193b0;
      padding: 3px;
    }
    /* Back button */
    .back-btn {
      position: absolute;
      top: 15px;
      left: 15px;
      font-size: 0.9rem;
      border-radius: 20px;
      padding: 6px 14px;
    }
  </style>
</head>
<body>

<div class="container mt-5">
  <div class="profile-card text-center">
    
    <!-- 🔙 Back Button -->
    <a href="dashboard.php" class="btn btn-outline-secondary back-btn">⬅ Back</a>

    <h3 class="mb-4">👤 My Profile</h3>

    <form method="POST" action="update_profile.php" enctype="multipart/form-data">
      <div class="mb-3 text-center photo-box">
        <?php if (!empty($user['photo']) && file_exists($user['photo'])) { ?>
          <img src="<?= htmlspecialchars($user['photo']); ?>" 
               class="rounded-circle mb-3 shadow" 
               width="130" height="130" alt="Profile Photo">
        <?php } else { ?>
          <img src="images/default.png" 
               class="rounded-circle mb-3 shadow" 
               width="130" height="130" alt="Default Photo">
        <?php } ?>

        <div class="mt-2">
          <label class="btn btn-outline-primary rounded-pill px-3">
            <input type="file" name="photo" hidden onchange="previewPhoto(this)"> 📷 Change Photo
          </label>
        </div>
        <!-- Preview image -->
        <div id="previewContainer" class="mt-3"></div>
      </div>

      <div class="mb-3 text-start">
        <label class="form-label fw-semibold">Full Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']); ?>" class="form-control" required>
      </div>

      <div class="mb-3 text-start">
        <label class="form-label fw-semibold">Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" class="form-control" readonly>
      </div>

      <div class="mb-3 text-start">
        <label class="form-label fw-semibold">Phone</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']); ?>" class="form-control">
      </div>

      <button type="submit" name="update" class="btn btn-custom w-100 py-2">💾 Update Profile</button>
    </form>
  </div>
</div>

<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewContainer').innerHTML = 
                '<img src="'+ e.target.result +'" class="rounded-circle shadow border border-success" width="130" height="130">';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

</body>
</html>