<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Icebreaker Finance</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
      <a class="navbar-brand" href="index.php">Icebreaker Finance</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" 
              aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
        <ul class="navbar-nav">
          <!-- Resources Dropdown -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="resourcesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Resources
            </a>
            <ul class="dropdown-menu" aria-labelledby="resourcesDropdown">
              <li><a class="dropdown-item" href="debt-buster-tools.php">Debt Buster Tools</a></li>
            </ul>
          </li>
          <!-- Account / My Account Buttons -->
          <?php if (isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] === true) : ?>
            <?php if (basename($_SERVER['PHP_SELF']) === "account.php") : ?>
              <li class="nav-item">
                <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#accountModal">Account</a>
              </li>
            <?php else: ?>
              <li class="nav-item">
                <a class="nav-link" href="account.php">My Account</a>
              </li>
            <?php endif; ?>
            <li class="nav-item">
              <a class="nav-link" href="logout.php">Logout</a>
            </li>
          <?php else : ?>
            <li class="nav-item">
              <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">Login/Register</a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Login Modal -->
  <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="loginModalLabel">Login</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="login.php" method="post">
            <div class="mb-3">
              <label for="loginUsername" class="form-label">Username:</label>
              <input type="text" class="form-control" id="loginUsername" name="username" required>
            </div>
            <div class="mb-3">
              <label for="loginPassword" class="form-label">Password:</label>
              <input type="password" class="form-control" id="loginPassword" name="password" required>
            </div>
            <input type="hidden" name="location" value="<?php echo $_SERVER['REQUEST_URI'];?>">
            <?php echo $_SESSION['feedback'] ?? ''; ?>
            <button type="submit" class="btn btn-primary">Login</button>
          </form>
          <p class="mt-2">Don't have an account? <a href="register.php">Register here</a></p>
        </div>
      </div>
    </div>
  </div>

  <!-- Account Modal (only on account page) -->
  <?php if (basename($_SERVER['PHP_SELF']) === "account.php") : ?>
  <div class="modal fade" id="accountModal" tabindex="-1" aria-labelledby="accountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="accountModalLabel">Update Account Information</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="update_account.php" method="post">
            <div class="mb-3">
              <label for="newUsername" class="form-label">New Username:</label>
              <input type="text" class="form-control" id="newUsername" name="newUsername">
            </div>
            <div class="mb-3">
              <label for="newEmail" class="form-label">New Email:</label>
              <input type="email" class="form-control" id="newEmail" name="newEmail">
            </div>
            <div class="mb-3">
              <label for="newPassword" class="form-label">New Password:</label>
              <input type="password" class="form-control" id="newPassword" name="newPassword">
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
