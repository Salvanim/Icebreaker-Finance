


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
<footer>
    <p>© 2025 Icebreaker Finance. All rights reserved.</p>
</footer>

<script src="script.js"></script>


</html>