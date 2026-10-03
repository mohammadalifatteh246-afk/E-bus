<?php
session_start();
include("includes/db.php");

// Only admin can access
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

include('includes/header.php');

// For success message popup
$actionDone = "";

// Handle Approve / Cancel Actions
if (isset($_GET['action'], $_GET['id'])) {
    $id = (int)$_GET['id'];

    if ($_GET['action'] === 'approve') {
        $stmt = $conn->prepare("UPDATE bookings SET status='Confirmed' WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $actionDone = "approved";
    } elseif ($_GET['action'] === 'cancel') {
        $stmt = $conn->prepare("UPDATE bookings SET status='Cancelled' WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $actionDone = "cancelled";
    }

    $_SESSION['actionDone'] = $actionDone;
    header("Location: manage_bookings.php");
    exit();
}

// ================== Fetch Bookings ==================
$sql = "SELECT b.id, 
               u.name AS user_name, 
               b.from_location, 
               b.to_location, 
               b.seat_number, 
               bt.bus_type_name, 
               b.booking_date, 
               b.amount, 
               b.status
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN bus_types bt ON b.bus_type_id = bt.id
        ORDER BY b.id DESC";

$result = $conn->query($sql);
if (!$result) {
    die("SQL Error: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background: url("/ebus/images/24.png") no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', sans-serif;
        }

        /* Blur effect */
    body::before {
        content: "";
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: inherit;
        filter: blur(8px);
        z-index: -1;
    }
        .container {
            margin-top: 40px;
            background: rgba(255,255,255,0.95);
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        table {
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0px 2px 10px rgba(0,0,0,0.1);
        }
        th {
            background: #007bff;
            color: white;
            text-align: center;
        }
        td {
            text-align: center;
            vertical-align: middle;
        }
        h2 {
            font-weight: bold;
            color: #333;
        }
        .btn-sm {
            padding: 4px 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="mb-4 text-center">🚌 Manage Bookings</h2>
    <table class="table table-bordered table-hover">
        <thead>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>From</th>
            <th>To</th>
            <th>Seat</th>
            <th>Bus Type</th>
            <th>Date</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['user_name']) ?></td>
                    <td><?= htmlspecialchars($row['from_location']) ?></td>
                    <td><?= htmlspecialchars($row['to_location']) ?></td>
                    <td><?= htmlspecialchars($row['seat_number']) ?></td>
                    <td><?= htmlspecialchars($row['bus_type_name']) ?></td>
                    <td><?= $row['booking_date'] ?></td>
                    <td>₹<?= number_format($row['amount'], 2) ?></td>
                    <td>
                        <?php if ($row['status'] == 'Pending'): ?>
                            <span class="badge bg-warning text-dark">Pending</span>
                        <?php elseif ($row['status'] == 'Confirmed'): ?>
                            <span class="badge bg-success">Confirmed</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Cancelled</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($row['status'] == 'Pending'): ?>
                            <a href="manage_bookings.php?action=approve&id=<?= $row['id'] ?>" class="btn btn-success btn-sm">Approve</a>
                            <a href="manage_bookings.php?action=cancel&id=<?= $row['id'] ?>" class="btn btn-danger btn-sm">Cancel</a>
                        <?php elseif ($row['status'] == 'Confirmed'): ?>
                            <a href="manage_bookings.php?action=cancel&id=<?= $row['id'] ?>" class="btn btn-danger btn-sm">Cancel</a>
                        <?php else: ?>
                            <a href="manage_bookings.php?action=approve&id=<?= $row['id'] ?>" class="btn btn-success btn-sm">Approve</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="10" class="text-center text-muted">No bookings found</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if (isset($_SESSION['actionDone'])): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Booking <?= $_SESSION['actionDone'] ?> successfully!',
        showConfirmButton: false,
        timer: 2000
    });
</script>
<?php unset($_SESSION['actionDone']); endif; ?>
</body>
</html>