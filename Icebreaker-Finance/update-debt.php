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
        ':balance' => $_POST['debt_balance'],
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

    if (!$userId || !$debtId || !$paymentAmount || $paymentAmount <= 0 || !$paymentDate || $balance  < 0) {
        echo json_encode(["success" => false, "message" => "Invalid input."]);
        exit;
    }

    try {
        // Delete the payment
        $stmt = $db->prepare("INSERT INTO debt_payments (debt_id, payment_amount, payment_date) VALUES (?, ?, ?, ?)");
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
