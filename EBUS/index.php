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
            $error = "Wrong password!";
        }
    } else {
        $error = "Email not found!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>E-BUS - Admin Login</title>
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/variables.css">
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/forms.css">
</head>
<body style="background-color: var(--navy); display: flex; align-items: center; justify-content: center; min-height: 100vh;">

  <div class="card" style="padding: 40px; max-width: 400px; width: 100%; margin: var(--space-xl);">
    <div class="text-center mb-lg">
      <div style="font-size: 32px; margin-bottom: var(--space-sm);">&#128652;</div>
      <h2 class="font-bold mb-xs" style="font-size: 24px;">Admin Portal</h2>
      <p class="text-secondary text-small">Sign in to E-BUS Admin</p>
    </div>

    <?php if($error): ?>
      <div class="text-danger font-medium mb-md text-center bg-light p-sm" style="border-radius: 4px;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
      <div class="form-group">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" placeholder="Enter email" required>
      </div>
      
      <div class="form-group mb-lg">
        <label class="form-label mb-0">Password</label>
        <input type="password" name="password" class="form-control" placeholder="Enter password" required>
      </div>

      <button type="submit" name="login" class="btn btn-primary w-full" style="padding: 12px; font-size: 16px;">Log In</button>
    </form>
    
    
  </div>

</body>
</html>
