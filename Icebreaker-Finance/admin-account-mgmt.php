<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=, initial-scale=1.0">
    <title>Admin Account Mgmt</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'nav.php'; ?>
<?php include 'pythonInteraction.php'; ?>

<?php
    function addToTable($username, $role, $toggleAdmin, $deleteUser) {
        return "<tr>
                    <td>{$username}</td>
                    <td>&nbsp;{$role}</td>
                    <td>&nbsp;{$toggleAdmin}</td>
                    <td>&nbsp;{$deleteUser}</td>
                </tr>";
    }

    function getUserData() {
        require __DIR__ . '/model/db.php';
        $stmt = $db->prepare("SELECT * FROM users");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $finalOutput = "";
        foreach ($users as $user) {
            /*$toggleAdminButton = "<button type='button' onclick='toggleAdmin(" . $user['user_id'] . ")'>Toggle Admin</button>";
            $deleteUserButton = "<button type='button'onclick='toggleAdmin(" . $user['user_id'] . ")'>Delete User</button>";*/
            $toggleAdminButton = "<input type="submit" name="toggleAdmin" class="button" value="Toggle Admin" />";
            $deleteUserButton = "<input type="submit" name="deleteUser" class="button" value="Delete User" />";

            $finalOutput .= addToTable($user["username"], $user["role"], $toggleAdminButton, $deleteUserButton);
        }
        return $finalOutput;
    }
?>

<form method="post">
    <table id="usersTable">
    <tr>
        <th>Username</th>
        <th>Role</th>
        <th>Toggle Admin</th>
        <th>Delete User</th>
    </tr>
    <?php echo getUserData()?>
    </table>
</form>

<footer class="footer">
    <p>© 2025 Icebreaker Finance. All rights reserved.</p>
</footer>
</script>
<script>
    function toggleAdmin(id) {
        console.log(id);
    }

    function deleteUser(id) {
        console.log(id);
    }
</script>
</body>
</html>
