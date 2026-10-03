<?php
$conn = mysqli_connect('localhost', 'root', '', 'ebus_pass_db');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
