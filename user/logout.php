<?php
session_start();
session_unset();
session_destroy();

// Redirect to user login
header("Location: home.php");
exit();
?>