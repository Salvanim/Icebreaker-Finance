<?php
session_start();
require __DIR__ . '/model/db.php';

// Ensure the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resource_id = $_POST['resource_id'] ?? '';
    $updated_content = trim($_POST['editor_content'] ?? '');

    if (!empty($resource_id) && !empty($updated_content)) {
        // Update the content in the database
        $stmt = $db->prepare("UPDATE site_content SET content = ? WHERE id = ?");
        $stmt->execute([$updated_content, $resource_id]);

        $_SESSION['message'] = "Resource updated successfully!";
    } else {
        $_SESSION['message'] = "Content cannot be empty.";
    }
}

// redirect back to the resource page
header("Location: debt-buster-tools.php");
exit;
?>
