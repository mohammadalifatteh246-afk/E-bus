<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ebus_pass_db";   //  yaha apna sahi DB name daalo

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$status_message = "";
$alert_class = "info";
$pass_details = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pass_id = intval($_POST['pass_id']);

    $stmt = $conn->prepare("SELECT name, route, pass_to, status FROM passes WHERE id = ?");
    $stmt->bind_param("i", $pass_id);
    $stmt->execute();
    $stmt->bind_result($name, $route, $expiry, $status);
    if ($stmt->fetch()) {
        $pass_details = [
            "name" => $name,
            "route" => $route,
            "expiry" => $expiry,
            "status" => $status
        ];

        if ($status == 'Pending') {
            $status_message = "⏳ Your pass is pending approval.";
            $alert_class = "warning";
        } elseif ($status == 'Approved') {
            $status_message = "✅ Congratulations! Your pass has been approved.";
            $alert_class = "success";
        } elseif ($status == 'Rejected') {
            $status_message = "❌ Sorry, your pass has been rejected.";
            $alert_class = "danger";
        } elseif ($status == 'Expired') {
            $status_message = "⚠️ Your pass has expired. Please renew it.";
            $alert_class = "secondary";
        } else {
            $status_message = "ℹ️ Your pass status: " . htmlspecialchars($status);
            $alert_class = "info";
        }
    } else {
        $status_message = "❓ Invalid Pass ID. Please check and try again.";
        $alert_class = "danger";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Check Pass Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-primary text-white text-center fs-4 fw-bold">
                    🚌 Check Your E-Bus Pass Status
                </div>
                <div class="card-body p-4">
                    <form method="post" action="">
                        <div class="mb-3">
                            <label class="form-label">Enter Your Pass ID:</label>
                            <input type="number" name="pass_id" class="form-control" placeholder="e.g. 101" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 rounded-pill">Check Status</button>
                    </form>

                    <?php if (!empty($status_message)) { ?>
                        <div class="alert alert-<?php echo $alert_class; ?> mt-4 rounded-3">
                            <?php echo $status_message; ?>
                        </div>

                        <?php if (!empty($pass_details)) { ?>
                            <ul class="list-group mt-3">
                                <li class="list-group-item"><b>Name:</b> <?php echo $pass_details["name"]; ?></li>
                                <li class="list-group-item"><b>Route:</b> <?php echo $pass_details["route"]; ?></li>
                                <li class="list-group-item"><b>Expiry Date:</b> <?php echo $pass_details["expiry"]; ?></li>
                                <li class="list-group-item"><b>Status:</b> <?php echo $pass_details["status"]; ?></li>
                            </ul>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
