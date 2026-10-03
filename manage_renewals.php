<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../index.php");
    exit();
}
include("includes/db.php");
include("includes/header.php");

// ✅ Renew / Cancel Handling
if (isset($_GET['action'], $_GET['id'])) {
    $id = (int) $_GET['id'];
    $action = $_GET['action'];

    if ($action == "approve") {
        // Renew pass: Active + extend expiry by 1 month
        $status = "Active";
        $stmt = $conn->prepare("UPDATE passes SET status=?, pass_to = DATE_ADD(pass_to, INTERVAL 1 MONTH) WHERE id=?");
        $stmt->bind_param("si", $status, $id);
    } elseif ($action == "reject") {
        // Cancel pass: Cancelled
        $status = "Cancelled";
        $stmt = $conn->prepare("UPDATE passes SET status=? WHERE id=?");
        $stmt->bind_param("si", $status, $id);
    }
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Pass $status successfully!');window.location='manage_renewals.php';</script>";
    exit();
}

// ✅ Get all passes (latest first)
$result = $conn->query("SELECT * FROM passes ORDER BY id DESC");
?>
<style>
  body {
    background: url("/ebus/images/25.png") no-repeat center center fixed;
    background-size: cover;
    font-family: Arial, sans-serif;
  }

  .card {
    background-color: rgba(255, 255, 255, 0.95);
    border-radius: 12px;
    padding: 20px;
  }

  table.table th, table.table td {
    vertical-align: middle;
  }

  table.table th {
    background-color: #f8f9fa;
    font-weight: 600;
  }

  /* Status badges */
  .badge-approved {
    background-color: #28a745;
    color: #fff;
    font-weight: 500;
    padding: 5px 10px;
    border-radius: 8px;
  }

  .badge-cancelled {
    background-color: #dc3545;
    color: #fff;
    font-weight: 500;
    padding: 5px 10px;
    border-radius: 8px;
  }

  .badge-pending {
    background-color: #ffc107;
    color: #212529;
    font-weight: 500;
    padding: 5px 10px;
    border-radius: 8px;
  }

  /* Action buttons */
  .btn-sm {
    font-size: 0.85rem;
    padding: 6px 12px;
    border-radius: 6px;
  }

  /* Table hover effect */
  table.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
  }

  h2.fw-bold {
    color: #fff;
  }

  @media (max-width: 768px) {
    table.table thead {
      display: none;
    }
    table.table tbody td {
      display: block;
      width: 100%;
      text-align: right;
      position: relative;
      padding-left: 50%;
      border: none;
      border-bottom: 1px solid #dee2e6;
    }
    table.table tbody td::before {
      content: attr(data-label);
      position: absolute;
      left: 15px;
      width: calc(50% - 30px);
      text-align: left;
      font-weight: 600;
    }
    .text-center {
      text-align: left !important;
    }
  }
</style>


<div class="container mt-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-white">♻ Manage Renewed Passes</h2>
    <a href="dashboard.php" class="btn btn-secondary">⬅ Back to Dashboard</a>
  </div>

  <div class="card shadow-lg">
    <div class="card-body">
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>User</th>
            <th>Email</th>
            <th>Pass Type</th>
            <th>Duration</th>
            <th>Valid From</th>
            <th>Valid To</th>
            <th>Status</th>
            <th>Fee Paid</th>
            <th class="text-center">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= $row['id'] ?></td>
            <td>
              <strong><?= htmlspecialchars($row['name']) ?></strong><br>
              <small class="text-muted"><?= htmlspecialchars($row['gender']) ?>, <?= htmlspecialchars($row['dob']) ?></small>
            </td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><span class="badge bg-info"><?= htmlspecialchars($row['pass_type']) ?></span></td>
            <td><?= htmlspecialchars($row['pass_duration']) ?></td>
            <td><?= $row['pass_from'] ?></td>
            <td><?= $row['pass_to'] ?></td>
            <td>
              <?php if ($row['status']=="Active"): ?>
                <span class="badge badge-approved">Active</span>
              <?php elseif ($row['status']=="Cancelled"): ?>
                <span class="badge badge-cancelled">Cancelled</span>
              <?php else: ?>
                <span class="badge badge-pending">Pending</span>
              <?php endif; ?>
            </td>
            <td>₹ <?= number_format($row['fee'],2) ?></td>
            <td class="text-center">
              <?php if($row['status'] != "Cancelled"): ?>
                <a href="?action=approve&id=<?= $row['id'] ?>" class="btn btn-success btn-sm me-2">✔ Renew</a>
                <a href="?action=reject&id=<?= $row['id'] ?>" class="btn btn-danger btn-sm">✖ Cancel</a>
              <?php else: ?>
                <span class="text-muted">✅ Done</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include("includes/footer.php"); ?>
