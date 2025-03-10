<?php
session_start();
require __DIR__ . '/model/db.php';

// Ensure user is logged in
if (!isset($_SESSION['isLoggedIn'])) {
    http_response_code(403);
    echo "Unauthorized access.";
    exit;
}

// Check if payment_id is provided via POST
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["payment_id"])) {
    $paymentId = intval($_POST["payment_id"]);
    $userId = $_SESSION['user_id'] ?? null;

    if (!$paymentId || !$userId) {
        http_response_code(400);
        echo "Invalid request.";
        exit;
    }

    try {
        $stmt = $db->prepare("DELETE FROM debt_lookup WHERE payment_id = ?");
        $stmt->execute([$paymentId]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Debt not found or unauthorized"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Database error"]);
    }
} else {
    $_SESSION['message'] = "Invalid request method.";
}
?>
