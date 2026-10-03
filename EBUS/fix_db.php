<?php
include("includes/db.php");
$conn->query("ALTER TABLE users ADD COLUMN photo VARCHAR(255) DEFAULT NULL");
echo "Done";
?>