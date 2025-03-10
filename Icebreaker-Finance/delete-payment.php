<?php
session_start();
require __DIR__ . '/model/db.php';

if (!isset($_SESSION['isLoggedIn'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['payment_id'])) {
    $paymentId = $_POST['payment_id'];

    try {
        $stmt = $db->prepare("DELETE FROM debt_lookup WHERE payment_id = ?");
        $stmt->execute([$paymentId]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Debt not found or unauthorized"]);
        }

        $paymentAmount = $payment['payment_amount'];
        $debtId = $payment['debt_id'];

        // Delete the payment record
        $stmt = $db->prepare("DELETE FROM debt_payments WHERE payment_id = ?");
        $stmt->execute([$paymentId]);

        if ($stmt->rowCount() <= 0) {
            $db->rollBack();
            echo json_encode(["success" => false, "message" => "Failed to delete payment"]);
            exit;
        }

        // Add the payment amount back to the corresponding debt's balance.
        $stmt = $db->prepare("UPDATE debt_lookup SET balance = balance + ? WHERE debt_id = ?");
        $stmt->execute([$paymentAmount, $debtId]);

        // Commit transaction
        $db->commit();
        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Database error"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}
?>
