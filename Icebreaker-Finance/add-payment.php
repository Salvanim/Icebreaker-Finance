<?php
session_start();
require __DIR__ . '/model/db.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
    exit;
}

$userId = $_SESSION['user_id'] ?? null;
$debtId = $_POST['debt_id'] ?? null;
$paymentAmount = $_POST['payment_amount'] ?? null;
$paymentDate = $_POST['payment_date'] ?? null;

if (!$userId || !$debtId || !$paymentAmount || $paymentAmount <= 0 || !$paymentDate) {
    echo json_encode(["success" => false, "message" => "Invalid input."]);
    exit;
}

try {
    // Insert payment with user-provided date
    $stmt = $db->prepare("INSERT INTO debt_payments (debt_id, payment_amount, payment_date) VALUES (?, ?, ?)");
    $stmt->execute([$debtId, $paymentAmount, $paymentDate]);

    // Update debt balance
    $updateStmt = $db->prepare("UPDATE debt_lookup SET amount_owed = amount_owed - ? WHERE debt_id = ?");
    $updateStmt->execute([$paymentAmount, $debtId]);

    echo json_encode(["success" => true]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
