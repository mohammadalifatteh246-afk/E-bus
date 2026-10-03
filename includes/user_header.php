<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Dashboard - eBus</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .navbar {
      background-color: #003366;
    }
    .navbar-brand, .nav-link, .navbar-text {
      color: white !important;
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand" href="user_dashboard.php">eBus User</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon bg-light"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="apply_pass.php">Apply Pass</a></li>
        <li class="nav-item"><a class="nav-link" href="book_ticket.php">Book Ticket</a></li>
        <li class="nav-item"><a class="nav-link" href="my_passes.php">My Passes</a></li>
        <li class="nav-item"><a class="nav-link" href="my_bookings.php">My Bookings</a></li>
        <li class="nav-item"><a class="nav-link" href="routes.php">Routes</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>