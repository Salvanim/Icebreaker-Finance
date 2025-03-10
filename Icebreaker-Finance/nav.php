<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>
<nav id="nav">
    <ul>
        <li class="logo"><a href="index.php">Icebreaker Finance</a></li>
        <div class="nav-right">

            <!-- Resources Dropdown -->
            <li class="dropdown">
                <a href="#" class="dropbtn" onclick="toggleDropdown(event)">Resources ▼</a>
                <ul class="dropdown-content" id="dropdown-menu">
                    <li><a href="debt-buster-tools.php">Debt Buster Tools</a></li>
                </ul>
            </li>

            <!-- Account / My Account Button -->
            <?php if (isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] === true) : ?>
                <?php if (basename($_SERVER['PHP_SELF']) === "account.php") : ?>
                    <li><a href="#" onclick="openAccountModal()">Account</a></li>
                <?php else: ?>
                    <li><a href="account.php">My Account</a></li>
                <?php endif; ?>
                <li><a href="logout.php">Logout</a></li>
            <?php else : ?>
                <li><a href="#" onclick="openLoginModal()">Login/Register</a></li>
            <?php endif; ?>

        </div>
    </ul>
</nav>


<!-- Login Modal -->
<div id="loginModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeLoginModal()">&times;</span>
        <h2>Login</h2>
        <form action="login.php" method="post">
            <label for="loginUsername">Username:</label>
            <input type="text" id="loginUsername" name="username" required>

            <label for="loginPassword">Password:</label>
            <input type="password" id="loginPassword" name="password" required>

            <button type="submit" class="btn btn-primary">Login</button>
            <input readonly hidden type="text" name="location" value="<?php echo $_SERVER['REQUEST_URI'];?>">
            <?php 
                echo $_SESSION['feedback'] ?? '';

            ?>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</div>

<!-- Account Modal (appears on account page only) -->
<?php if (basename($_SERVER['PHP_SELF']) === "account.php") : ?>
<div id="accountModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeAccountModal()">&times;</span>
        <h2>Update Account Information</h2>
        <form action="update_account.php" method="post">
            <label for="newUsername">New Username:</label>
            <input type="text" id="newUsername" name="newUsername">

            <label for="newEmail">New Email:</label>
            <input type="email" id="newEmail" name="newEmail">

            <label for="newPassword">New Password:</label>
            <input type="password" id="newPassword" name="newPassword">
            
            <button type="submit">Update</button>
        </form>
    </div>
</div>
<?php endif; ?>

<?php
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
        $_SESSION['loggedIN'] = true;
    }
?>

<script>

    function toggleDropdown(event) {
        event.preventDefault();
        const dropdownMenu = document.getElementById("dropdown-menu");
        dropdownMenu.classList.toggle("show");
        document.addEventListener("click", (e) => {
            if (!dropdownMenu.contains(e.target) && e.target !== event.target) {
                dropdownMenu.classList.remove("show");
            }
        }, { once: true });
    }

    function openLoginModal() {
        const modal = document.getElementById("loginModal");
        if (modal) {
            modal.style.display = "block";
            setTimeout(() => {
                modal.classList.add("show");
            }, 10);
        } else {
            console.error("Login modal not found in the DOM.");
        }
    }

    function closeLoginModal() {
        const modal = document.getElementById("loginModal");
        if (modal) {
            modal.classList.remove("show");
            setTimeout(() => {
                modal.style.display = "none";
            }, 300);

            fetch("reset_session.php", { method: "POST" })
                .then(response => response.text())
                .then(data => console.log("Session reset:", data))
                .catch(error => console.error("Error resetting session:", error));
        }
    }

    function openAccountModal() {
        const modal = document.getElementById("accountModal");
        if (modal) {
            console.log("Opening Account Modal...");
            modal.style.display = "block";
            modal.style.opacity = "1";
            modal.style.visibility = "visible";
            modal.style.zIndex = "10000";

            const modalContent = document.querySelector("#accountModal .modal-content");
            if (modalContent) {
                modalContent.style.opacity = "1";
                modalContent.style.visibility = "visible";
                modalContent.style.transform = "translate(-50%, -50%)";
            }
        } else {
            console.error("Account modal not found in the DOM.");
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

    // closes modals when clicking outside of them
    window.onclick = function(event) {
        const loginModal = document.getElementById("loginModal");
        const accountModal = document.getElementById("accountModal");
        if (event.target === loginModal) {
            closeLoginModal();
        }
        if (event.target === accountModal) {
            closeAccountModal();
        }
    };
</script>

