<?php
session_start();
require __DIR__ . '/model/db.php'; // Ensure this is inside PHP tags

// Ensure the user is an admin
if (!isset($_SESSION['isLoggedIn']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Check if database connection exists
if (!$db) {
    die("Database connection failed.");
}

// Fetch users from the database
try {
    $stmt = $db->prepare("SELECT user_id, username, email, `role` FROM users");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching users: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=, initial-scale=1.0">
    <title>Admin Account Mgmt</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'nav.php'; ?>

    


<footer class="footer">
    <p>© 2025 Icebreaker Finance. All rights reserved.</p>
</footer>
<script src="script.js"></script>
</body>
</html>