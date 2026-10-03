<?php
include("../includes/db.php");

$pass_id = $_GET['id'] ?? 0;

$sql = "SELECT * FROM passes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $pass_id);
$stmt->execute();
$pass = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$pass) {
    die("Pass not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Download Bus Pass</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
  <script src="../assets/js/bootstrap.bundle.min.js"></script>

  <style>
    body { background: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    .card { border-radius: 12px; }
    .user-photo {
      width: 150px;
      height: 150px;
      object-fit: cover;
      border: 2px solid #ccc;
      border-radius: 12px;
    }
    .pass-header { border-bottom: 2px solid #007bff; margin-bottom: 15px; padding-bottom: 10px; }
    .print-btn { margin-top: 20px; }
    @media print {
      .print-btn { display: none; }
      body { background: #fff; }
      .card { box-shadow: none; }
    }
  </style>
</head>
<body>

<div class="container my-5">
  <div class="card p-4 shadow">
    <div class="row">
      
      <!-- Left: Photo + Name -->
      <div class="col-md-4 text-center mb-3">
        <?php if (!empty($pass['photo'])): ?>
        <?php else: ?>
          <div class="border rounded bg-light d-flex align-items-center justify-content-center" 
               style="width:150px;height:150px;">
            No Photo
          </div>
        <?php endif; ?>
        <h5 class="mt-2"><?= htmlspecialchars($pass['name']) ?></h5>
        <p class="text-muted mb-0"><?= htmlspecialchars($pass['email']) ?></p>
      </div>

      <!-- Right: Pass Details -->
      <div class="col-md-8">
        <div class="pass-header">
          <h4 class="text-primary">🚌 Bus Pass</h4>
        </div>
        <p><strong>From → To:</strong> <?= htmlspecialchars($pass['from_location']) ?> → <?= htmlspecialchars($pass['to_location']) ?></p>
        <p><strong>Route:</strong> <?= htmlspecialchars($pass['route']) ?></p>
        <p><strong>Pass Type:</strong> <?= htmlspecialchars($pass['pass_type']) ?></p>
        <p><strong>Pass Duration:</strong> <?= htmlspecialchars($pass['pass_duration']) ?></p>
        <p><strong>Valid From → To:</strong> <?= htmlspecialchars($pass['pass_from']) ?> → <?= htmlspecialchars($pass['pass_to']) ?></p>
        <p><strong>Age / Gender:</strong> <?= htmlspecialchars($pass['age']) ?> / <?= htmlspecialchars($pass['gender']) ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($pass['address']) ?></p>
        <p><strong>Fee:</strong> ₹<?= htmlspecialchars($pass['fee'] ?? $pass['amount'] ?? 0) ?></p>
        <p><strong>Issued On:</strong> <?= htmlspecialchars($pass['created_at'] ?? date('Y-m-d')) ?></p>
      </div>
    </div>

    <!-- Print Button -->
    <div class="text-center">
      <button onclick="window.print()" class="btn btn-primary print-btn">Print Pass</button>
    </div>
  </div>
</div>

</body>
</html>
