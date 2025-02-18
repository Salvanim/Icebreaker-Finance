<?php
session_start();
require __DIR__ . '/model/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'];

    // Check if the username or email already exists
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingUser) {
        echo "Error: Username or Email is already taken.";
    } else {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user into database
        $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $hashedPassword]);

        echo "Registration successful!";
        header("Location: account.php"); // Redirect to account page after registration
        exit;
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'nav.php'; ?>

    <main>
        <div class="container-login">
            <h2>Create an Account</h2>
            <form action="register.php" method="post">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" autocomplete="username" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address:</label>
                    <input type="email" id="email" name="email" autocomplete="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" autocomplete="new-password" required>
                </div>
                <div class="form-buttons">
                    <button type="submit">Register</button>
                </div>
            </form>
        </div>
    </main>

    <footer class="footer">
        <p>© 2025 Icebreaker Finance. All rights reserved.</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>
