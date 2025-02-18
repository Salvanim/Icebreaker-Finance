


<?php
session_start();
require __DIR__ . '/model/db.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? ''; //check for login or register
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'];

    
    // Get user from database
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo "User found: " . $user['username'] . "<br>";
        echo "Hashed password in DB: " . $user['password'] . "<br>";

        if (password_verify($password, $user['password'])) {
            echo "Password matched!<br>";
            // Successful login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: ../index.php");
            exit;
        } else {
            echo "Password did not match.<br>";
        }
    } else {
        echo "User not found.<br>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register/Login</title>
    <link rel="stylesheet" href="style.css">

    
</head>
<body>
<?php include 'nav.php'; ?>

    
    <main>
        <div class="container-login">
            <form action="/home-account.html" method="post">
                <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" autocomplete="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" autocomplete="email">
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" autocomplete="password">
            </div>
            <div class="form-buttons">
                <button type="submit" name="action" value="register">Register</button>
                <button type="submit" name="action" value="login">Login</button>
            </div>
        </form>
    </div>
</main>
<footer class="footer">
    <p>© 2025 Icebreaker Finance. All rights reserved.</p>
    </footer>

<script src="script.js"></script>


</html>