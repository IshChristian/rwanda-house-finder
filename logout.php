<?php
// Start the session
session_start();

// Destroy all session data
session_unset();  // Clears all session variables
session_destroy();  // Destroys the session

// Redirect to login page
header("Location: ./login.php");
exit();
?>
