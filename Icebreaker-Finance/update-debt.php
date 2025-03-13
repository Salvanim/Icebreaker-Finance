<?php
// Start a session to access user authentication data
session_start();
// Include the database connection file
require __DIR__ . '/model/db.php';

// Set response content type to JSON for AJAX handling
header('Content-Type: application/json');

// Check if user is logged in by verifying session user_id
if (!isset($_SESSION['user_id'])) {
    // Return error response if user is not authenticated
    die(json_encode(['success' => false, 'message' => 'Authentication required']));
}

// Get user ID from session and debt ID from POST data
$userId = $_SESSION['user_id'];
$debtId = $_POST['debt_id'] ?? null;

try {
    // Begin database transaction to ensure data consistency
    $db->beginTransaction();

    // Prepare SQL statement to update debt information
    $stmt = $db->prepare("UPDATE debt_lookup SET
        debt_name = :name,
        amount_owed = :owed,
        balance = :balance,
        min_payment = :payment,
        interest_rate = :rate
        WHERE debt_id = :id AND user_id = :uid
    ");

    // Execute the prepared statement with sanitized POST data
    $stmt->execute([
        ':name' => $_POST['debt_name'],
        ':owed' => $_POST['debt_amount'],
        ':balance' => $_POST['debt_balance'],
        ':payment' => $_POST['min_payment'],
        ':rate' => $_POST['interest_rate'],
        ':id' => $debtId,
        ':uid' => $userId
    ]);

    // Commit the transaction if all operations succeeded
    $db->commit();
    // Return success response
    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    // Rollback transaction on database error
    $db->rollBack();
    // Return error details (Note: In production, don't expose full error messages)
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

// WARNING: This redirect will not work after JSON output
// Headers are already sent due to previous echo statements
// Consider moving this redirect logic to the front-end JavaScript instead
header("Location: edit-debt.php?debt_id=" . urlencode($debtId));
exit;
?>
