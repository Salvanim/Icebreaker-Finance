<?php
// Start session to access user authentication data
session_start();

// Include database connection configuration
require __DIR__ . '/model/db.php';

// Verify that the request method is POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
    exit;
}

// Retrieve user ID from session and payment details from POST data
$userId = $_SESSION['user_id'] ?? null;           // Currently logged-in user
$debtId = $_POST['debt_id'] ?? null;              // ID of debt being paid
$paymentAmount = $_POST['payment_amount'] ?? null; // Payment amount
$paymentDate = $_POST['payment_date'] ?? null;     // User-specified payment date

// Validate all required input parameters
if (!$userId || !$debtId || !$paymentAmount || $paymentAmount <= 0 || !$paymentDate) {
    echo json_encode(["success" => false, "message" => "Invalid input."]);
    exit;
}

try {
    // Record the payment in debt_payments table using prepared statement
    $stmt = $db->prepare("INSERT INTO debt_payments (debt_id, payment_amount, payment_date)
                         VALUES (?, ?, ?)");
    $stmt->execute([$debtId, $paymentAmount, $paymentDate]);

    // Update remaining balance in debt_lookup table using prepared statement
    $updateStmt = $db->prepare("UPDATE debt_lookup
                               SET balance = balance - ?
                               WHERE debt_id = ?");
    $updateStmt->execute([$paymentAmount, $debtId]);

    // Return success response if both operations complete successfully
    echo json_encode(["success" => true]);

} catch (PDOException $e) {
    // Handle database errors and return specific error message
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
