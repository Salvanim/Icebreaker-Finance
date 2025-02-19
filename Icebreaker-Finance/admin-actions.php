<?php
$data = json_decode(file_get_contents('php://input'), true);
if ($data['action'] == 'toggleAdmin') {
    // Handle toggle admin
    $userId = $data['userId'];
    echo '<script>alert("Welcome to Geeks for Geeks")</script>';
    // Update database logic here
} elseif ($data['action'] == 'deleteUser') {
    $userId = $data['userId'];
    echo '<script>alert("Welcome to Geeks for Geeks")</script>';
}
?>
