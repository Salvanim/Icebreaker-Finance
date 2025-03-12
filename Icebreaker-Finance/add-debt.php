<?php
session_start();
require __DIR__ . '/model/db.php';

if (!isset($_SESSION['isLoggedIn'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

$userId = $_SESSION['user_id'] ?? null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $debtName = $_POST['debt_name'] ?? '';
    $debtType = $_POST['debt_type'] ?? '';
    $amountOwed = $_POST['debt_amount'] ?? 0;
    $minPayment = $_POST['min_payment'] ?? 0;
    $interestRate = $_POST['interest_rate'] ?? 0;
    $status = 'active';
    $debtVis = 1;

    if (empty($debtName) || empty($debtType) || $amountOwed <= 0 || $minPayment <= 0 || $interestRate < 0) {
        echo json_encode(["success" => false, "message" => "Invalid input"]);
        exit;
    }

    try {
        $stmt = $db->prepare("INSERT INTO debt_lookup (user_id, debt_type, amount_owed, interest_rate, status, debt_name, debt_vis, min_payment, date_added)
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, CURDATE())");
                              
        $stmt->execute([$userId, $debtType, $amountOwed, $interestRate, $status, $debtName, $debtVis, $minPayment]);

        $debtId = $db->lastInsertId();
        echo json_encode(["success" => true, "debt_id" => $debtId]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
    }
}
?>
