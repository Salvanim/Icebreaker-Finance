<?php
session_start();
require __DIR__ . '/model/db.php';

if (!isset($_SESSION['isLoggedIn'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

$debtId = $_POST['debt_id'] ?? null;
$userId = $_SESSION['user_id'] ?? null;

if (!$debtId || !$userId) {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit;
}

// Get current debt details
try {
    $stmt = $db->prepare("SELECT amount_owed, interest_rate FROM debt_lookup WHERE debt_id = ? AND user_id = ?");
    $stmt->execute([$debtId, $userId]);
    $debt = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$debt) {
        echo json_encode(["success" => false, "message" => "Debt not found"]);
        exit;
    }

    // Calculate new amount owed after applying interest
    $interestRate = $debt['interest_rate'] / 100; // Convert percentage to decimal
    $newAmount = $debt['amount_owed'] + ($debt['amount_owed'] * $interestRate / 12); // Monthly interest applied

    // Update database with new amount
    $updateStmt = $db->prepare("UPDATE debt_lookup SET amount_owed = ? WHERE debt_id = ? AND user_id = ?");
    $updateStmt->execute([$newAmount, $debtId, $userId]);

    echo json_encode(["success" => true, "new_amount" => $newAmount]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error"]);
}
?>
