<?php
session_start();
include("includes/db.php");

// Only admin can access
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}
include('includes/header.php');

// Approve or Cancel actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action == "approve") {
        $conn->query("UPDATE passes SET status='Active' WHERE id=$id");
        $_SESSION['msg'] = "Pass approved successfully!";
    } elseif ($action == "cancel") {
        $conn->query("UPDATE passes SET status='Cancelled' WHERE id=$id");
        $_SESSION['msg'] = "Pass cancelled!";
    }

    header("Location: manage_passes.php");
    exit();
}

// Fetch passes (removed p.duration)
$sql = "SELECT p.id, p.name, p.age, p.gender, p.email, 
               p.from_location, p.to_location, p.route, 
               p.pass_type, p.pass_duration, p.pass_from, p.pass_to, 
               p.fee, p.status, p.created_at, 
               u.name AS user_name
        FROM passes p
        LEFT JOIN users u ON p.user_id = u.id
        ORDER BY p.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Passes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background: url("/ebus/images/25.png") no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .overlay {
            background-color: rgba(255, 255, 255, 0.85);
            min-height: 100vh;
            padding: 30px;
        }
        .table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }
        h2 {
            font-weight: bold;
            color: #0d6efd;
        }
    </style>
</head>
<body>
<div class="overlay">
    <div class="container mt-4">
        <h2 class="mb-4 text-center">Manage Passes</h2>

        <?php if ($result->num_rows > 0): ?>
            <table class="table table-bordered table-striped shadow">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Pass Type</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Fee</th>
                        <th>Status</th>
                        <th>Issued At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['user_name']) ?></td>
                            <td><?= htmlspecialchars($row['pass_type']) ?> (<?= htmlspecialchars($row['pass_duration']) ?>)</td>
                            <td><?= htmlspecialchars($row['from_location']) ?></td>
                            <td><?= htmlspecialchars($row['to_location']) ?></td>
                            <td>₹<?= number_format($row['fee'], 2) ?></td>
                            <td>
                                <?php if ($row['status'] == "Active"): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php elseif ($row['status'] == "Expired"): ?>
                                    <span class="badge bg-secondary">Expired</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Cancelled</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $row['created_at'] ?></td>
                            <td>
                                <?php if ($row['status'] != "Active"): ?>
                                    <a href="manage_passes.php?action=approve&id=<?= $row['id'] ?>" class="btn btn-success btn-sm">Approve</a>
                                <?php endif; ?>
                                <?php if ($row['status'] != "Cancelled"): ?>
                                    <a href="manage_passes.php?action=cancel&id=<?= $row['id'] ?>" class="btn btn-danger btn-sm">Cancel</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">No passes found.</div>
        <?php endif; ?>
    </div>
</div>

<?php if (isset($_SESSION['msg'])): ?>
<script>
Swal.fire({
  icon: 'success',
  title: 'Success',
  text: '<?= $_SESSION['msg'] ?>',
  timer: 2000,
  showConfirmButton: false
});
</script>
<?php unset($_SESSION['msg']); endif; ?>

</body>
</html>
