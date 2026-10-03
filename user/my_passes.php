<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit();
}

include("../includes/db.php");

// Get logged-in user
$user_email = $_SESSION['user'];
$stmt = $conn->prepare("SELECT id, name, email FROM users WHERE email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_id = $user['id'];

// Fetch passes
$query = "SELECT p.id, p.pass_from, p.pass_to, p.from_location, p.to_location, p.distance_km, p.fee, p.pass_type 
          FROM passes p
          WHERE p.user_id = ?
          ORDER BY p.pass_from DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$passes = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Passes - E-Bus</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
  <script src="../assets/js/bootstrap.bundle.min.js"></script>

  <style>
    body {
      background: url("/ebus/images/18.png") no-repeat center center fixed;
      background-size: cover;
      font-family: 'Segoe UI', Tahoma, sans-serif;
    }
    .overlay {
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0, 0, 0, 0.6);
      z-index: -1;
    }
    .navbar-custom {
      background: rgba(0, 0, 0, 0.6);
      padding: 0.7rem 1rem;
    }
    .navbar-custom a {
      color: #fff;
      text-decoration: none;
      font-weight: 500;
    }
    .card {
      border-radius: 20px;
      background: rgba(255, 255, 255, 0.95);
      box-shadow: 0 8px 25px rgba(0,0,0,0.4);
      backdrop-filter: blur(8px);
    }
    .table th, .table td {
      vertical-align: middle;
    }
    .download-btn {
      transition: 0.3s ease;
    }
    .download-btn:hover {
      transform: scale(1.05);
    }
  </style>
</head>
<body>

<div class="overlay"></div>

<!-- Navbar with Dashboard Button -->
<nav class="navbar-custom">
  <a href="user_dashboard.php"><i class="bi bi-grid-fill"></i> Dashboard</a>
</nav>

<div class="container mt-5">
  <div class="text-center mb-5 text-white">
    <h2 class="fw-bold">🎟️ My Bus Passes</h2>
    <p class="text-light">View and download your active and past passes.</p>
  </div>

  <?php if ($passes->num_rows > 0): ?>
    <div class="card shadow-lg">
      <div class="card-body">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-dark">
            <tr>
              <th scope="col">#</th>
              <th scope="col">From</th>
              <th scope="col">To</th>
              <th scope="col">Distance</th>
              <th scope="col">Fee</th>
              <th scope="col">Valid From</th>
              <th scope="col">Valid To</th>
              <th scope="col">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $passes->fetch_assoc()): ?>
              <tr>
                <td><span class="badge bg-secondary"><?php echo $row['id']; ?></span></td>
                <td><?php echo htmlspecialchars($row['from_location'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($row['to_location'] ?? ''); ?></td>
                <td><?php echo isset($row['distance_km']) ? $row['distance_km'] . ' km' : '-'; ?></td>
                <td>₹<?php echo isset($row['fee']) ? number_format($row['fee'], 2) : '-'; ?></td>
                <td><?php echo date("d M Y", strtotime($row['pass_from'])); ?></td>
                <td><?php echo date("d M Y", strtotime($row['pass_to'])); ?></td>
                <td>
                  <a href="download_pass.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success download-btn">
                    ⬇️ Download
                  </a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php else: ?>
    <div class="alert alert-info text-center shadow-sm">
      <h5>No passes found </h5>
      <p>You don’t have any passes yet. Apply for a new pass below:</p>
      <a href="apply_pass.php" class="btn btn-primary"> Apply for Pass</a>
    </div>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>