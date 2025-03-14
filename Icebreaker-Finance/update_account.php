<?php
session_start();
require __DIR__ . '/model/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $newUsername = trim($_POST['newUsername']);
    $newEmail = trim($_POST['newEmail']);
    $newPassword = $_POST['newPassword'];

    // Prepare SQL based on user input
    $updates = [];
    $params = [':user_id' => $userId];

    if (!empty($newUsername)) {
        $updates[] = "username = :username";
        $params[':username'] = $newUsername;
    }
    if (!empty($newEmail)) {
        $updates[] = "email = :email";
        $params[':email'] = $newEmail;
    }
    if (!empty($newPassword)) {
        $updates[] = "password = :password";
        $params[':password'] = password_hash($newPassword, PASSWORD_DEFAULT);
    }

    if (count($updates) > 0) {
        $query = "UPDATE users SET " . implode(", ", $updates) . " WHERE user_id = :user_id";
        $stmt = $db->prepare($query);
        $stmt->execute($params);

        $_SESSION['message'] = "Account updated successfully!";
    } else {
        $_SESSION['message'] = "No changes made.";
    }

    // Redirect with exit to prevent further execution
    header("Location: account.php");
    exit;
}
?>
