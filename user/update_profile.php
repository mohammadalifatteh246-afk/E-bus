<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$email = $_SESSION['user'];

if (isset($_POST['update'])) {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);

    //  upload
    $photoPath = null;
    if (!empty($_FILES['photo']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $fileName = time() . "_" . basename($_FILES['photo']['name']);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
            $photoPath = $targetFile;
        }
    }

    if ($photoPath) {
        $sql = "UPDATE users SET name=?, phone=?, photo=? WHERE email=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $phone, $photoPath, $email);
    } else {
        $sql = "UPDATE users SET name=?, phone=? WHERE email=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $phone, $email);
    }

    if ($stmt->execute()) {
        $_SESSION['user_name'] = $name;
        header("Location: profile.php?success=1");
        exit();
    } else {
        header("Location: profile.php?error=1");
        exit();
    }
}
?>
