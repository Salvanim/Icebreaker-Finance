<?php
session_start();
require __DIR__ . '/model/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Get user from database
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Set session variables
        $_SESSION['isLoggedIn'] = true;
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role']; // Store user role in session
        $_SESSION['loggedIN'] = true;
        // Redirect based on role
        if ($user['role'] === 'admin') {

            header("Location: admin-account-mgmt.php");
        } else {
            header("Location: account.php");
        }
        exit;
    } else {
        $_SESSION['loggedIN'] = false;
        $_SESSION['feedback'] = "Invalid Login";
        header("Location: " . $_POST['location']);
    }
}
?>
