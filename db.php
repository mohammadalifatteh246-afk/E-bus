<?php
$conn = mysqli_connect("localhost", "root", "", "ebus_pass_db");
if (!$conn) {
  die("Database connection failed: " . mysqli_connect_error());
}
?>