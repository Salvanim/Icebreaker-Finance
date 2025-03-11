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
    $userId = $_SESSION['user_id'] ?? null;
    $debtId = $_POST['debt_id'] ?? null;
    $paymentAmount = $_POST['payment-amount'] ?? null;
    $paymentDate = $_POST['payment-date'] ?? null;

    if (!$userId || !$debtId || !$paymentAmount || $paymentAmount <= 0 || !$paymentDate) {
        echo json_encode(["success" => false, "message" => "Invalid input."]);
        exit;
    }

    try {
        // Delete the payment
        $stmt = $db->prepare("INSERT INTO debt_payments (debt_id, payment_amount, payment_date) VALUES (?, ?, ?)");
        $stmt->execute([$debtId, $paymentAmount, $paymentDate]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['message'] = "Payment added successfully.";
        } else {
            $_SESSION['message'] = "Payment could not be added.";
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = "Error adding payment.";
    }
} else {
    $_SESSION['message'] = "Invalid request method.";
}

// Redirect back to the edit_debt page with the correct debt_id
header("Location: edit-debt.php?debt_id=" . urlencode($debtId));
exit;
?>
