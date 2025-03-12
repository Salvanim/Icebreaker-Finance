<?php
session_start();
require __DIR__ . '/model/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['success' => false, 'message' => 'Authentication required']));
}

$userId = $_SESSION['user_id'];
$debtId = $_POST['debt_id'] ?? null;

try {
    $db->beginTransaction();

    $stmt = $db->prepare("UPDATE debt_lookup SET
        debt_name = :name,
        amount_owed = :owed,
        balance = :balance,
        min_payment = :payment,
        interest_rate = :rate
        WHERE debt_id = :id AND user_id = :uid
    ");

    $stmt->execute([
        ':name' => $_POST['debt_name'],
        ':owed' => $_POST['debt_amount'],
        ':balance' => $_POST['balance'],
        ':payment' => $_POST['min_payment'],
        ':rate' => $_POST['interest_rate'],
        ':id' => $debtId,
        ':uid' => $userId
    ]);

    $db->commit();
    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    $db->rollBack();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
