<?php
session_start();
require __DIR__ . '/model/db.php';

// Ensure user is logged in
if (!isset($_SESSION['isLoggedIn'])) {
    header("Location: index.php");
    exit;
}

$userId = $_SESSION['user_id'] ?? null;
$debtId = $_GET['debt_id'] ?? null;

// Fetch debt details
function getDebtDetails($debtId, $userId) {
    global $db;
    if (!$db || !$debtId || !$userId) return null;

    try {
        $stmt = $db->prepare("
            SELECT debt_id, debt_name, amount_owed, min_payment, interest_rate
            FROM debt_lookup
            WHERE debt_id = ? AND user_id = ?
        ");
        $stmt->execute([$debtId, $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return null;
    }
}

// Fetch transactions/payments for the debt
function getDebtPayments($debtId) {
    global $db;
    if (!$db || !$debtId) return [];

    try {
        $stmt = $db->prepare("
            SELECT payment_id, payment_date, payment_amount
            FROM debt_payments
            WHERE debt_id = ?
            ORDER BY payment_date DESC
        ");
        $stmt->execute([$debtId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

$debt = getDebtDetails($debtId, $userId);
$payments = getDebtPayments($debtId);

// Generate plot image if payments exist
$imageSrc = '';
if (!empty($payments)) {
    // Generate plot data
    $chronologicalPayments = array_reverse($payments);
    $plotDataString = "";
    foreach ($chronologicalPayments as $payment) {
        $plotDataString = $payment['payment_date']. "," . $payment['payment_amount'] . "\n";
    }
    $input = $plotDataString;

    // Execute Python script
    $command = "python PythonTesting/dataVisualizationGenerator.py " . $input . " 2>&1";
    $output = shell_exec($command);

    // Validate base64 output
    if ($output && base64_decode(trim($output), true)) {
        $imageSrc = 'data:image/png;base64,' . trim($output);
    } else {
        error_log("Failed to generate plot. Output: " . $output);
    }
}

if (!$debt) {
    echo "<p class='error'>Debt not found.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Debt</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head>
<body>
    <?php include 'nav.php'; ?>

    <h2>Debt Details: <?= htmlspecialchars($debt['debt_name']) ?></h2>

    <p>Amount Owed: <span id="amount-owed">$<?= number_format($debt['amount_owed'], 2) ?></span></p>
    <p>Minimum Payment: $<?= number_format($debt['min_payment'], 2) ?></p>
    <p>Interest Rate: <?= number_format($debt['interest_rate'], 2) ?>%</p>

    <!-- Payment History Visualization -->
    <?php if ($imageSrc): ?>
        <h3>Payment History</h3>
        <img src="<?= $imageSrc ?>" alt="Payment History Plot">
    <?php elseif (!empty($payments)): ?>
        <p class="error">Failed to generate payment history visualization.</p>
    <?php endif; ?>

    <!-- Edit Debt Form -->
    <h3>Edit Debt Details</h3>
    <form id="edit-debt-form" action="update-debt.php" method="POST">
        <input type="hidden" name="debt_id" value="<?= $debtId; ?>">

        <label for="debt-name">Debt Name:</label>
        <input type="text" id="debt-name" name="debt_name" value="<?= htmlspecialchars($debt['debt_name']); ?>" required>

        <label for="amount-owed-input">Amount Owed:</label>
        <input type="number" id="amount-owed-input" name="amount_owed" value="<?= $debt['amount_owed']; ?>" required>

        <label for="min-payment">Minimum Payment:</label>
        <input type="number" id="min-payment" name="min_payment" value="<?= $debt['min_payment']; ?>" required>

        <label for="interest-rate">Interest Rate (%):</label>
        <input type="number" step="0.01" id="interest-rate" name="interest_rate" value="<?= $debt['interest_rate']; ?>" required>

        <button type="submit">Save Changes</button>
    </form>

    <!-- Payment Transactions -->
    <h3>Payment Transactions</h3>
    <table>
        <thead>
            <tr>
                <th>Payment Date</th>
                <th>Amount</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="payment-list">
            <?php if (!empty($payments)): ?>
                <?php foreach ($payments as $payment): ?>
                    <tr id="payment-row-<?= $payment['payment_id'] ?>">
                        <td><?= date("M d, Y", strtotime($payment['payment_date'])) ?></td>
                        <td>$<?= number_format($payment['payment_amount'], 2) ?></td>
                        <td><button onclick="deletePayment(<?= $payment['payment_id'] ?>)">Delete</button></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="3" class="text-muted">No payments recorded yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Add Payment Form -->
    <h3>Add a Payment</h3>
    <form id="add-payment-form">
        <input type="number" id="payment-amount" placeholder="Payment Amount" required>
        <button type="button" onclick="addPayment(<?= $debtId ?>)">Submit</button>
    </form>

    <!-- Interest Adjustment -->
    <h3>Apply Interest</h3>
    <button onclick="applyInterest(<?= $debtId ?>)">Apply Interest</button>

</body>
</html>
