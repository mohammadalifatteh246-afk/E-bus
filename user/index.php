<?php
session_start();
include('../includes/db.php');

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if ($email && $password) {
        $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' LIMIT 1");
        if (mysqli_num_rows($check) > 0) {
            $user = mysqli_fetch_assoc($check);
            if (password_verify($password, $user['password'])) {
               
                $_SESSION['user'] = $user['email'];     
                $_SESSION['user_name'] = $user['name']; 

                header("Location: user_dashboard.php");
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
            mysqli_query($conn, "INSERT INTO users (name,email,phone,password) VALUES ('$name','$email','$phone','$hashed')");

            //  Save user data in session
            $_SESSION['user'] = $email;
            $_SESSION['user_name'] = $name; //  add name here

            header("Location: user_dashboard.php");
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
<title>User Login & Register - eBus</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../assets/css/bootstrap.min.css">
<link rel="stylesheet" href="assets/icons/css/all.min.css">


<script src="../assets/js/bootstrap.bundle.min.js"></script>

<style>
body {
    background: url("/ebus/images/16.png") no-repeat center center fixed;
    background-size: cover;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}
body::before {
    content: "";
    position: absolute;
    top:0; left:0; right:0; bottom:0;
    background: rgba(0,0,0,0.55);
}
.box {
    position: relative;
    z-index: 2;
    background: #fff;
    padding: 40px 30px;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.4);
    width: 100%;
    max-width: 420px;
    transition: transform 0.3s ease-in-out;
}
.box:hover { transform: translateY(-5px); }
h3 {
    text-align:center;
    color:#007bff;
    margin-bottom:20px;
    font-weight: 700;
}
.form-control {
    border-radius:12px;
    height:48px;
    padding-left: 40px;
}
.input-group-text {
    border-radius:12px 0 0 12px;
    background:#f1f1f1;
}
.btn {
    border-radius:12px;
    padding:12px;
    font-size:16px;
    font-weight: 500;
}
.alert { margin-top:15px; }
.toggle-btn { cursor:pointer; font-weight: 600; color:#007bff; }
.toggle-btn:hover { text-decoration:underline; }
</style>
</head>
<body>

<div class="box">
    <h3>🚍 Welcome to eBus</h3>

    <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

    <!-- Login Form -->
<input type="text" style="display:none">
<input type="password" style="display:none">

<form method="post" id="loginForm" autocomplete="off">
    <div class="mb-3 input-group">
        <span class="input-group-text"><i class="fa fa-envelope"></i></span>
        <input type="email" name="email" class="form-control" placeholder="Email address"
               value="" autocomplete="new-email" required />
    </div>
    <div class="mb-3 input-group">
    <span class="input-group-text"><i class="fa fa-lock"></i></span>
    <input type="password" id="password" name="password" class="form-control" placeholder="Password"
           autocomplete="new-password" required />
</div>

    <!-- Eye Icon Button -->
    <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
    <p class="mt-3 text-center">
        New user? <span class="toggle-btn" onclick="toggleForm()">Register here</span>
    </p>
</form>

    <!-- Register Form -->
    <form method="post" id="registerForm" style="display:none;" autocomplete="off">
        <div class="mb-3 input-group">
            <span class="input-group-text"><i class="fa fa-user"></i></span>
            <input type="text" name="name" class="form-control" placeholder="Full name" required />
        </div>
        <div class="mb-3 input-group">
            <span class="input-group-text"><i class="fa fa-envelope"></i></span>
            <input type="email" name="email" class="form-control" placeholder="Email address" autocomplete="new-email" required />
        </div>
        <div class="mb-3 input-group">
            <span class="input-group-text"><i class="fa fa-phone"></i></span>
            <input type="text" name="phone" class="form-control" placeholder="Phone number" required />
        </div>
        <div class="mb-3 input-group">
            <span class="input-group-text"><i class="fa fa-lock"></i></span>
            <input type="password" name="password" class="form-control" placeholder="Password" autocomplete="new-password" required />
        </div>
        <button type="submit" name="register" class="btn btn-success w-100">Register</button>
        <p class="mt-3 text-center">
            Already have an account? <span class="toggle-btn" onclick="toggleForm()">Login here</span>
        </p>
    </form>
</div>

<script>
function toggleForm(){
    const login = document.getElementById('loginForm');
    const register = document.getElementById('registerForm');
    login.style.display = login.style.display === 'none' ? 'block' : 'none';
    register.style.display = register.style.display === 'none' ? 'block' : 'none';
}
</script>
</body>
</html>