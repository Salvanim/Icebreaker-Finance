<?php
session_start();
require __DIR__ . '/model/db.php';

if (!isset($_SESSION['isLoggedIn'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

$userId = $_SESSION['user_id'] ?? null;

// Fetch debts for the user
$stmt = $db->prepare("SELECT debt_id, debt_name, debt_type,
                      CAST(amount_owed AS DECIMAL(10,2)) AS amount_owed,
                      CAST(min_payment AS DECIMAL(10,2)) AS min_payment,
                      CAST(interest_rate AS DECIMAL(10,2)) AS interest_rate
                      FROM debt_lookup WHERE user_id = ?");
$stmt->execute([$userId]);
$debts = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(["success" => true, "debts" => $debts]);

?>
