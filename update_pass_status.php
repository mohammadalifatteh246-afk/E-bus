<?php
session_start();
header('Content-Type: application/json');
include('includes/db.php');

if(!isset($_SESSION['admin'])){
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$id = intval($_POST['id'] ?? 0);
$status = $_POST['status'] ?? '';

if(!$id || !in_array($status, ['Active', 'Rejected'])){
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit();
}

$stmt = $conn->prepare("UPDATE passes SET status = ? WHERE id = ?");
$stmt->bind_param('si', $status, $id);

if($stmt->execute()){
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>