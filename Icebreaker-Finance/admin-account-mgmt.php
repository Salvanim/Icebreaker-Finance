<?php
session_start();
require __DIR__ . '/model/db.php';
// Ensure the user is an admin
if (!isset($_SESSION['isLoggedIn']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Check if database connection exists
if (!$db) {
    die("Database connection failed.");
}

// Fetch users from the database
try {
    $stmt = $db->prepare("SELECT user_id, username, email, `role` FROM users");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching users: " . $e->getMessage());
}

// Function to generate a user row
function addToTable($username, $role, $toggleAdmin, $deleteUser) {
    return "<tr>
                <td>{$username}</td>
                <td>{$role}</td>
                <td>{$toggleAdmin}</td>
                <td>{$deleteUser}</td>
            </tr>";
}

// Function to fetch user data
function getUserData() {
    global $db;
    try {
        $stmt = $db->prepare("SELECT * FROM users");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $finalOutput = "";

        #<button class='toggle-admin' data-id='{$userID}'>Toggle Admin</button>

        foreach ($users as $user) {
            $userID = htmlspecialchars($user['user_id']);
            $username = htmlspecialchars($user['username']);
            $role = htmlspecialchars($user['role']);

            $toggleAdminButton = "";
            if($role == "admin"){
                $toggleAdminButton =
                "<label class='toggle-switch'>
                    <input type='checkbox' checked=True class='toggle-admin' data-id='{$userID}'/>
                    <span class='slider'></span>
                 </label>";
            } else {
                $toggleAdminButton =
                "<label class='toggle-switch'>
                    <input type='checkbox' class='toggle-admin' data-id='{$userID}'/>
                    <span class='slider'></span>
                 </label>";
            }

            $deleteUserButton = "<button class='delete-user' data-id='{$userID}'>Delete</button>";

            $finalOutput .= addToTable($username, $role, $toggleAdminButton, $deleteUserButton);
        }
        return $finalOutput;
    } catch (PDOException $e) {
        return "<tr><td colspan='4'>Error fetching users: " . $e->getMessage() . "</td></tr>";
    }
}

// AJAX Request Handling
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $userId = filter_input(INPUT_POST, 'userId', FILTER_VALIDATE_INT);
    if (!$userId) {
        echo json_encode(["error" => "Invalid user ID"]);
        exit();
    }

    try {
        if ($_POST['action'] === 'toggleAdmin') {
            $stmt = $db->prepare("UPDATE users SET role = IF(role = 'admin', 'user', 'admin') WHERE user_id = ?");
            $stmt->execute([$userId]);
            echo json_encode(["success" => "User role updated"]);
        } elseif ($_POST['action'] === 'deleteUser') {
            $stmt = $db->prepare("DELETE FROM users WHERE user_id = ?");
            $stmt->execute([$userId]);
            echo json_encode(["success" => "User deleted"]);
        } else {
            echo json_encode(["error" => "Invalid action"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Account Mgmt</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<?php include 'nav.php'; ?>
<?php include 'pythonInteraction.php'; ?>

<h2>Admin User Management</h2>

<table id="usersTable">
    <tr>
        <th>Username</th>
        <th>Role</th>
        <th>Toggle Admin</th>
        <th>Delete User</th>
    </tr>
    <?php echo getUserData(); ?>
</table>




<footer class="footer">
    <p>© 2025 Icebreaker Finance. All rights reserved.</p>
</footer>

<script>
$(document).ready(function() {
    $(".toggle-admin").click(function() {
        let userId = $(this).data("id");
        $.post("admin-account-mgmt.php", { action: "toggleAdmin", userId: userId }, function(response) {
            let result = JSON.parse(response);
            location.reload();
        });
    });

    $(".delete-user").click(function() {
        if (!confirm("Are you sure you want to delete this user?")) return;
        let userId = $(this).data("id");
        $.post("admin-account-mgmt.php", { action: "deleteUser", userId: userId }, function(response) {
            let result = JSON.parse(response);
            location.reload();
        });
    });
});
</script>

<script src="script.js"></script>
</body>
</html>
