<?php
session_start();
require __DIR__ . '/model/db.php';

// Check if user is logged in
if (!isset($_SESSION['isLoggedIn'])) {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Unauthorized access."]);
    exit;
}

// Validate request method and parameters
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["payment_id"])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid request."]);
    exit;
}

$userId = $_SESSION['user_id'];
$paymentId = $_POST['payment_id'];

try {
    // Retrieve payment details and validate ownership
    $stmt = $db->prepare("
        SELECT dp.debt_id, dp.payment_amount
        FROM debt_payments dp
        INNER JOIN debt_lookup dl ON dp.debt_id = dl.debt_id
        WHERE dp.payment_id = ? AND dl.user_id = ?
    ");
    $stmt->execute([$paymentId, $userId]);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$payment) {
        echo json_encode(["success" => false, "message" => "Payment not found or access denied."]);
        exit;
    }

    $debtId = $payment['debt_id'];
    $paymentAmount = $payment['payment_amount'];

    // Delete the payment
    $deleteStmt = $db->prepare("DELETE FROM debt_payments WHERE payment_id = ?");
    $deleteStmt->execute([$paymentId]);

    // Update debt balance by adding the payment amount back
    $updateStmt = $db->prepare("UPDATE debt_lookup SET balance = balance + ? WHERE debt_id = ?");
    $updateStmt->execute([$paymentAmount, $debtId]);

    echo json_encode(["success" => true, "message" => "Payment deleted successfully."]);
    exit;

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "A database error occurred."]);
    exit;
}
?>
