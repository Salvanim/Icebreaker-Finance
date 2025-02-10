<?php
    session_start();
    session_destroy(); // End the session
    header("Location: index.php"); // Redirect to the home page
    exit();
?>
