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
        // Retrieve the associated debt_id
        $stmt = $db->prepare("
            SELECT debt_id FROM debt_payments
            WHERE payment_id = ?
            AND debt_id IN (SELECT debt_id FROM debt_lookup WHERE user_id = ?)
        ");
        $stmt->execute([$paymentId, $userId]);
        $debt = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$debt) {
            $_SESSION['message'] = "Payment not found.";
            header("Location: payment-transactions.php");
            exit;
        }

        $debtId = $debt['debt_id'];

        // Delete the payment
        $stmt = $db->prepare("DELETE FROM debt_payments WHERE payment_id = ?");
        $stmt->execute([$paymentId]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['message'] = "Payment deleted successfully.";
        } else {
            $_SESSION['message'] = "Payment could not be deleted.";
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = "Error deleting payment.";
    }
} else {
    $_SESSION['message'] = "Invalid request method.";
}

// Redirect back to the edit_debt page with the correct debt_id
header("Location: edit-debt.php?debt_id=" . urlencode($debtId));
exit;
?>
