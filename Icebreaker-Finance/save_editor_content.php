<?php
session_start();
require __DIR__ . '/model/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        die("Unauthorized access.");
    }

    $editor_content = trim($_POST['editor_content'] ?? '');

    if (!empty($editor_content)) {
        // Insert new content instead of updating
        $stmt = $db->prepare("INSERT INTO site_content (section, content) VALUES ('admin_tools', ?)");
        $stmt->execute([$editor_content]);

        $_SESSION['message'] = "Resource added successfully!";
    } else {
        $_SESSION['message'] = "Content cannot be empty.";
    }
}

    // Redirect back to the Debt Buster Tools page
    header("Location: debt-buster-tools.php");
    exit;

?>
