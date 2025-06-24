<?php
session_start();
session_unset(); // Clear all session variables
session_destroy(); // Destroy the session
header("Location: signIn.php"); // Redirect to login page
exit();
?>