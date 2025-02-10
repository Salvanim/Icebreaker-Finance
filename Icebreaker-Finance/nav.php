<!--nav kept here to keep cleaner code in other pages-->

<?php
    // only start session if not active already
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
?>
<nav id="nav">
    <ul>
        <li><a href="index.php">Icebreaker Finance</a></li>
        <li><a href="debt-buster-tools.php">Debt Buster Tools</a></li>

        <?php if (isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] === true) : ?>
            <li><a href="account.php">Account</a></li>
            <li><a href="logout.php">Logout</a></li>
        <?php else : ?>
            <li><a href="login-register.php">Login/Register</a></li>
        <?php endif; ?>
    </ul>
</nav>
