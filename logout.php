<?php
session_start();

// Clear all session data
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to index.php
header("Location: index.php");
exit;
?>
