<?php
session_start();
include('../includes/db.php');

$error = "";

// Login
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if ($email && $password) {
        $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' LIMIT 1");
        if (mysqli_num_rows($check) > 0) {
            $user = mysqli_fetch_assoc($check);
            if (password_verify($password, $user['password'])) {
                $_SESSION['user'] = $user;
                header("Location: home.php");
                exit();
            } else {
                $error = "Invalid email or password!";
            }
        } else {
            $error = "Invalid email or password!";
        }
    } else {
        $error = "Please enter email and password!";
    }
}

// Register
if (isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);

    if ($name && $email && $phone && $password) {
        $exists = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' LIMIT 1");
        if (mysqli_num_rows($exists) > 0) {
            $error = "Email already exists!";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            mysqli_query($conn, "INSERT INTO users (name,email,phone,password, role) VALUES ('$name','$email','$phone','$hashed', 'passenger')");
            
            // Auto login
            $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' LIMIT 1");
            $user = mysqli_fetch_assoc($check);
            $_SESSION['user'] = $user;

            header("Location: home.php");
            exit();
        }
    } else {
        $error = "Please fill all fields!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>E-BUS - Passenger Portal</title>
  <link rel="stylesheet" href="../css/reset.css">
  <link rel="stylesheet" href="../css/variables.css">
  <link rel="stylesheet" href="../css/base.css">
  <link rel="stylesheet" href="../css/components.css">
  <link rel="stylesheet" href="../css/forms.css">
</head>
<body style="background-color: var(--bg); display: flex; align-items: center; justify-content: center; min-height: 100vh;">

  <div class="card" style="padding: 40px; max-width: 400px; width: 100%; margin: var(--space-xl);">
    <div class="text-center mb-lg">
      <div style="font-size: 32px; margin-bottom: var(--space-sm);">&#128652;</div>
      <h2 class="font-bold mb-xs" style="font-size: 24px;">Passenger Portal</h2>
      <p class="text-secondary text-small">Welcome to E-BUS</p>
    </div>

    <?php if($error): ?>
      <div class="text-danger font-medium mb-md text-center bg-light p-sm" style="border-radius: 4px;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div id="loginForm">
      <form method="post">
        <div class="form-group">
          <label class="form-label">Email Address</label>
          <input type="email" name="email" class="form-control" placeholder="Enter email" required>
        </div>
        <div class="form-group mb-lg">
          <label class="form-label mb-0">Password</label>
          <input type="password" name="password" class="form-control" placeholder="Enter password" required>
        </div>
        <button type="submit" name="login" class="btn btn-primary w-full mb-md" style="padding: 12px; font-size: 16px;">Log In</button>
      </form>
      <div class="text-center text-small">
        <span class="text-secondary">Don't have an account?</span>
        <button class="text-primary font-medium ml-xs" style="background:none; border:none; cursor:pointer;" onclick="document.getElementById('loginForm').style.display='none'; document.getElementById('registerForm').style.display='block';">Sign up</button>
      </div>
    </div>

    <div id="registerForm" style="display: none;">
      <form method="post">
        <div class="form-group">
          <label class="form-label">Full Name</label>
          <input type="text" name="name" class="form-control" placeholder="Enter your name" required>
        </div>
        <div class="form-group">
          <label class="form-label">Email Address</label>
          <input type="email" name="email" class="form-control" placeholder="Enter email" required>
        </div>
        <div class="form-group">
          <label class="form-label">Phone</label>
          <input type="tel" name="phone" class="form-control" placeholder="Enter phone" required>
        </div>
        <div class="form-group mb-lg">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" placeholder="Create password" required>
        </div>
        <button type="submit" name="register" class="btn btn-primary w-full mb-md" style="padding: 12px; font-size: 16px;">Create Account</button>
      </form>
      <div class="text-center text-small">
        <span class="text-secondary">Already have an account?</span>
        <button class="text-primary font-medium ml-xs" style="background:none; border:none; cursor:pointer;" onclick="document.getElementById('registerForm').style.display='none'; document.getElementById('loginForm').style.display='block';">Log in</button>
      </div>
    </div>

    

  </div>

</body>
</html>
