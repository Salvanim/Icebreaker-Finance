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

    // Delete payments first
    $stmt = $db->prepare("DELETE FROM debt_payments
                        WHERE debt_id IN (
                            SELECT debt_id FROM debt_lookup
                            WHERE debt_id = ? AND user_id = ?
                        )");
    $stmt->execute([$debtId, $userId]);

    // Delete debt
    $stmt = $db->prepare("DELETE FROM debt_lookup
                        WHERE debt_id = ? AND user_id = ?");
    $stmt->execute([$debtId, $userId]);

    $db->commit();

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No debt found']);
    }

} catch (PDOException $e) {
    $db->rollBack();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
