<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Agar admin session set nahi hai to login page bhejo
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}
?>