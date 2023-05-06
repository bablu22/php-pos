<?php

session_start();

// If the user is logged in, log them out
if (isset($_SESSION['email']) && isset($_SESSION['role'])) {
  session_unset(); // Unset all session variables
  session_destroy(); // Destroy the session data
}

// Redirect the user to the login page
header('Location:../index.php');
exit();

