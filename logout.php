<?php
session_start();
session_unset();    // Remove all session variables
session_destroy();  // Destroy the actual session
header("Location: login.php"); // Send them back to the login screen
exit();
?>