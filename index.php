<?php
session_start();
include("includes/db.php");

$error = ""; 
if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // check email in admin table
    $query = mysqli_query($conn, "SELECT * FROM admin WHERE email='$email'");
    $user = mysqli_fetch_assoc($query);

    if ($user) {
       
        if ($password === $user['password']) {
            $_SESSION['admin'] = $user['email'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = " Wrong password!";
        }
    } else {
        $error = " Email not found!";
    }
}
?>
<DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <script src="assets/js/bootstrap.bundle.min.js"></script>

  <style>
    body {
      background: url("/ebus/images/22.png") no-repeat center center fixed;
      background-size: cover;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }
    .login-card {
      max-width: 1000px;
      width: 100%;
      background: rgba(255, 255, 255, 0.2);
      border-radius: 45px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.4);
      padding: 100px;
      text-align: center;
      backdrop-filter: blur(42px);  /*  Blur effect */
      -webkit-backdrop-filter: blur(42px);
      border: 1px solid rgba(255, 255, 255, 0.3);
    }
    .login-card img {
      width: 80px;
      margin-bottom: 45px;
    }
    .login-card h3 {
      margin-bottom: 60px;
      color: #fff;
      font-weight: bold;
      text-shadow: 0 2px 4px rgba(0,0,0,0.4);
    }
    .form-control {
      border-radius: 50px;
      background: rgba(255, 255, 255, 0.9);
    }
    .btn-custom {
      background: #007bff;
      color: white;
      border-radius: 60px;
      font-weight: bold;
    }
    .btn-custom:hover {
      background: #0056b3;
    }
    .alert {
      font-size: 0.9rem;
    }
  </style>
</head>
<body>

<div class="login-card">
  <!-- Bus Logo -->
 <img src="/ebus/images/11.png" alt="Bus Logo" width="90">
  <h3>Admin Login</h3>
  
  <?php if (!empty($error)) { ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php } ?>
  
  <form method="post">
    <div class="mb-3">
      <input type="email" name="email" class="form-control" placeholder="Enter email" autocomplete="off" required>
    </div>
    <div class="mb-3">
      <input type="password" name="password" class="form-control" placeholder="Enter password" autocomplete="new-password" required>
    </div>
    <button type="submit" name="login" class="btn btn-custom w-100">Login</button>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>