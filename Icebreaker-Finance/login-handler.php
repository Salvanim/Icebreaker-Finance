<?php
session_start();
header('Content-Type: application/json');

// Check if session variables exist
if (empty($_SESSION)) {
    echo json_encode(false);
} else {
    echo json_encode($_SESSION['isLoggedIn']);
}
?>
