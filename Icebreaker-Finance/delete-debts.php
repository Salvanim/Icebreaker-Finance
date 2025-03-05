<?php
session_start();
require __DIR__ . '/model/db.php';

if (!isset($_SESSION['isLoggedIn'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['debt_id'])) {
    $debtId = $_POST['debt_id'];

    try {
        $stmt = $db->prepare("DELETE FROM debt_lookup WHERE debt_id = ? AND user_id = ?");
        $stmt->execute([$debtId, $_SESSION['user_id']]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Debt not found or unauthorized"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Database error"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}
?>
