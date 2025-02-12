<!-- Navigation kept here to maintain cleaner code across other pages -->

<?php
    // Only start session if not already active
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
?>
<nav id="nav">
    <ul>
        <li class="logo"><a href="index.php">Icebreaker Finance</a></li>

        <div class="nav-right">
            <!-- Resources Dropdown (To the left of Login/Register) -->
            <li class="dropdown">
                <a href="#" class="dropbtn" onclick="toggleDropdown(event)">Resources ▼</a>
                <ul class="dropdown-content" id="dropdown-menu">
                    <li><a href="debt-buster-tools.php">Debt Buster Tools</a></li>
                </ul>
            </li>

            <?php if (isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] === true) : ?>
                <li><a href="account.php">Account</a></li>
                <li><a href="logout.php">Logout</a></li>
            <?php else : ?>
                <li><a href="login-register.php">Login/Register</a></li>
            <?php endif; ?>
        </div>
    </ul>
</nav>

<script>
    function toggleDropdown(event) {
        event.preventDefault();
        const dropdownMenu = document.getElementById("dropdown-menu");

        // Toggle dropdown visibility
        dropdownMenu.classList.toggle("show");

        // Close dropdown when clicking outside
        document.addEventListener("click", function (e) {
            if (!dropdownMenu.contains(e.target) && !event.target.contains(e.target)) {
                dropdownMenu.classList.remove("show");
            }
        }, { once: true });
    }
</script>
