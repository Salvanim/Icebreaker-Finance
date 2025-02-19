<?php
session_start();
require __DIR__ . '/model/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        die("Unauthorized access.");
    }

    $editor_content = $_POST['editor_content'] ?? '';

    // Check if content exists
    $stmt = $db->prepare("SELECT * FROM site_content WHERE section = 'admin_tools'");
    $stmt->execute();
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        // Update existing content
        $stmt = $db->prepare("UPDATE site_content SET content = ? WHERE section = 'admin_tools'");
        $stmt->execute([$editor_content]);
    } else {
        // Insert new content
        $stmt = $db->prepare("INSERT INTO site_content (section, content) VALUES ('admin_tools', ?)");
        $stmt->execute([$editor_content]);
    }

    // Redirect back to the Debt Buster Tools page
    header("Location: debt-buster-tools.php");
    exit;
}
?>
