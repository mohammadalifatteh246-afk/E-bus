<?php
session_start();
include("../includes/db.php"); // if you want to save messages in DB

// Handle form submission
$success = $error = "";
if (isset($_POST['send'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
    $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $subject = mysqli_real_escape_string($conn, $_POST['subject'] ?? '');
    $message = mysqli_real_escape_string($conn, $_POST['message'] ?? '');

    if ($name && $email && $subject && $message) {
        // Optionally save in database
        mysqli_query($conn, "INSERT INTO contact_messages (name,email,subject,message) VALUES ('$name','$email','$subject','$message')");
        $success = "Your message has been sent successfully!";
    } else {
        $error = "Please fill all fields!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us - E-Bus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background: url('https://images.unsplash.com/photo-1504384308090-c894fdcc538d') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', sans-serif;
        }
        .overlay {
            background: rgba(0,0,0,0.6);
            position: fixed;
            inset: 0;
            z-index: -1;
        }
        .contact-card {
            background: rgba(255,255,255,0.95);
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
            max-width: 700px;
            margin: 80px auto;
            padding: 40px;
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
            font-weight: bold;
            color: #007bff;
        }
        .form-control {
            border-radius: 12px;
            padding: 12px;
            box-shadow: inset 0 2px 6px rgba(0,0,0,0.05);
        }
        .btn-primary {
            background: linear-gradient(45deg, #007bff, #00c6ff);
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.3s ease-in-out;
        }
        .btn-primary:hover {
            background: linear-gradient(45deg, #0056b3, #0096c7);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        footer {
            margin-top: 50px;
            text-align: center;
            color: #fff;
        }
    </style>
</head>
<body>

<div class="overlay"></div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand fw-bold" href="home.php"><i class="bi bi-bus-front"></i> E-Bus</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
        <li class="nav-item"><a class="nav-link active" href="contact.php">Contact</a></li>
        <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="index.php">Login</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="contact-card">
    <h2>Contact Us</h2>

    <?php if(!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
    <?php if(!empty($success)) echo "<div class='alert alert-success'>$success</div>"; ?>

    <form method="post">
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" placeholder="Your Name" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" placeholder="Your Email" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Subject</label>
            <input type="text" name="subject" class="form-control" placeholder="Subject" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Message</label>
            <textarea name="message" class="form-control" rows="5" placeholder="Your Message" required></textarea>
        </div>

        <button type="submit" name="send" class="btn btn-primary w-100"><i class="bi bi-send"></i> Send Message</button>
    </form>
</div>

<footer>
  <p>© <?php echo date("Y"); ?> E-Bus | All Rights Reserved</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>