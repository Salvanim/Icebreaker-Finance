<?php
// Start session only if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- Main Navigation Bar -->
<nav id="nav">
    <ul>
        <!-- Website Logo -->
        <li class="logo"><a href="index.php">Icebreaker Finance</a></li>

        <!-- Right-aligned Navigation Items -->
        <div class="nav-right">

            <!-- Resources Dropdown Menu -->
            <li class="dropdown">
                <!-- Dropdown trigger with toggle function -->
                <a href="#" class="dropbtn" onclick="toggleDropdown(event)">Resources ▼</a>
                <!-- Dropdown content (hidden by default) -->
                <ul class="dropdown-content" id="dropdown-menu">
                    <li><a href="debt-buster-tools.php">Debt Buster Tools</a></li>
                </ul>
            </li>

            <!-- User Authentication Section -->
            <?php if (isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] === true) : ?>
                <?php if (basename($_SERVER['PHP_SELF']) === "account.php") : ?>
                    <!-- Show modal trigger when already on account page -->
                    <li><a href="#" onclick="openAccountModal()">Account</a></li>
                <?php else: ?>
                    <!-- Regular account link for other pages -->
                    <li><a href="account.php">My Account</a></li>
                <?php endif; ?>
                <!-- Logout Option -->
                <li><a href="logout.php">Logout</a></li>
            <?php else : ?>
                <!-- Show login/register option for guests -->
                <li><a href="#" onclick="openLoginModal()">Login/Register</a></li>
            <?php endif; ?>

        </div>
    </ul>
</nav>

<!-- Login/Registration Modal -->
<div id="loginModal" class="modal">
    <div class="modal-content">
        <!-- Close button for modal -->
        <span class="close" onclick="closeLoginModal()">&times;</span>
        <h2>Login</h2>
        <!-- Login Form -->
        <form action="login.php" method="post">
            <label for="loginUsername">Username:</label>
            <input type="text" id="loginUsername" name="username" required>

            <label for="loginPassword">Password:</label>
            <input type="password" id="loginPassword" name="password" required>

            <button type="submit" class="btn btn-primary">Login</button>
            <!-- Hidden field to track current page for redirect -->
            <input readonly hidden type="text" name="location" value="<?php echo $_SERVER['REQUEST_URI'];?>">
            <?php
                // Display any login feedback/errors from session
                echo $_SESSION['feedback'] ?? '';
            ?>
        </form>
        <!-- Registration link -->
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</div>

<!-- Account Management Modal (Visible only on account page) -->
<?php if (basename($_SERVER['PHP_SELF']) === "account.php") : ?>
<div id="accountModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeAccountModal()">&times;</span>
        <h2>Update Account Information</h2>
        <!-- Account Update Form -->
        <form action="update_account.php" method="post">
            <label for="newUsername">New Username:</label>
            <input type="text" id="newUsername" name="newUsername">

            <label for="newEmail">New Email:</label>
            <input type="email" id="newEmail" name="newEmail">

            <label for="newPassword">New Password:</label>
            <input type="password" id="newPassword" name="newPassword">

            <button class="btn-primary" type="submit">Update</button>
        </form>
    </div>
</div>
<?php endif; ?>

<?php
// Automatically show login modal if previous login attempt failed
if (isset($_SESSION['loggedIN']) && $_SESSION['loggedIN'] === false){
    echo '<script type="text/javascript">
    const modal = document.getElementById("loginModal");
    if (modal) {
        modal.style.display = "block";
        setTimeout(() => {
            modal.classList.add("show");
        }, 10);
    } else {
        console.error("Login modal not found in the DOM.");
    }
    </script>';
    $_SESSION['loggedIN'] = true; // Reset login attempt flag
}
?>

<script>
    // Toggle dropdown menu visibility
    function toggleDropdown(event) {
        event.preventDefault();
        const dropdownMenu = document.getElementById("dropdown-menu");
        dropdownMenu.classList.toggle("show");
        // Close dropdown when clicking outside
        document.addEventListener("click", (e) => {
            if (!dropdownMenu.contains(e.target) && e.target !== event.target) {
                dropdownMenu.classList.remove("show");
            }
        }, { once: true });
    }

    // Modal control functions
    function openLoginModal() {
        const modal = document.getElementById("loginModal");
        if (modal) {
            modal.style.display = "block";
            setTimeout(() => {
                modal.classList.add("show");
            }, 10);
        }
    }

    function closeLoginModal() {
        const modal = document.getElementById("loginModal");
        if (modal) {
            modal.classList.remove("show");
            setTimeout(() => {
                modal.style.display = "none";
            }, 300);

            // Reset session feedback via AJAX
            fetch("reset_session.php", { method: "POST" })
                .then(response => response.text())
                .catch(error => console.error("Error resetting session:", error));
        }
    }

    // Account modal control
    function openAccountModal() {
        const modal = document.getElementById("accountModal");
        if (modal) {
            modal.style.display = "block";
            // Animation handling
            setTimeout(() => {
                modal.style.opacity = "1";
                modal.style.visibility = "visible";
            }, 10);
        }
    }

    function closeAccountModal() {
        const modal = document.getElementById("accountModal");
        if (modal) {
            modal.classList.remove("show");
            setTimeout(() => {
                modal.style.display = "none";
            }, 300);
        }
    }

    // Close modals when clicking outside
    window.onclick = function(event) {
        const loginModal = document.getElementById("loginModal");
        const accountModal = document.getElementById("accountModal");
        if (event.target === loginModal) closeLoginModal();
        if (event.target === accountModal) closeAccountModal();
    };
</script>
