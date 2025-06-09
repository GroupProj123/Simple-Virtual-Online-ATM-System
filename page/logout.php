<?php
session_start(); // Start or resume the session

// Clear all session variables
$_SESSION = array();

// Destroy the session data on the server
session_destroy();

// Redirect user to the login page after logout
header("Location: login.php");
exit;
?>
