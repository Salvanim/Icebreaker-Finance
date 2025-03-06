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
            SELECT debt_id, debt_name, amount_owed, min_payment, interest_rate, date_added
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

function getDatesBetween($startDate, $endDate, &$dates = array()) {
    $dates[] = $startDate;
    $nextDate = date('Y-m-d', strtotime($startDate . ' +1 month'));
    if ($nextDate <= $endDate) {
        getDatesBetween($nextDate, $endDate, $dates);
    }
    return $dates;
}

$debt = getDebtDetails($debtId, $userId);
$payments = getDebtPayments($debtId);

// Generate plot image if payments exist
$imageSrc = '';
if (!empty($payments)) {
    // Generate plot data
    $chronologicalPayments = array_reverse($payments);
    $plotDataString = "";
    $amountOwed = $debt["amount_owed"];

    $dateStarted = date_create($debt["date_added"])->format('Y-m-d');
    $today = date_create("today")->format('Y-m-d');
    $datesBetween = getDatesBetween($dateStarted, $today);
    $interestRate = $debt["interest_rate"]/1200;

    foreach ($chronologicalPayments as $payment) {
        $calculatedChange = $amountOwed-$payment['payment_amount'];
        $plotDataString .= $payment['payment_date']. "," . $calculatedChange . "$";
        $amountOwed = $calculatedChange;
    }
    $input = $plotDataString;

    $graphArguments = [
        "xColumnName" => "Date",
        "yColumnName" => "Amount",
        "title" => "Payment History",
        "xlabel" => "Date",
        "ylabel" => "Amount ($)",
        "color" => "blue",
        "linewidth" => 2,
        "marker" => "o"
    ];

    $argumentsJson = json_encode($graphArguments);
    // Execute Python script
    $command = 'python PythonTesting/dataVisualizationGenerator.py ' . escapeshellarg($input);
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head>
<body>
    <?php include 'nav.php'; ?>

    <div class="container mt-4">
        <h2 class="text-primary"><?= htmlspecialchars($debt['debt_name']) ?></h2>

        <div class="card p-3 mb-4">
            <p><strong>Amount Owed:</strong> <span id="amount-owed" class="text-danger">$
                <?= number_format($debt['amount_owed'], 2) ?></span></p>
            <p><strong>Minimum Payment:</strong> $<?= number_format($debt['min_payment'], 2) ?></p>
            <p><strong>Interest Rate:</strong> <?= number_format($debt['interest_rate'], 2) ?>%</p>
        </div>

    <!-- Edit Debt Form -->
    <h3>Edit Debt Details</h3>
    <form id="edit-debt-form" action="update-debt.php" method="POST">
        <input type="hidden" name="debt_id" value="<?= $debtId; ?>">

        <div class="col-md-6">
            <label for="debt-name" class="form-label">Debt Name:</label>
            <input type="text" id="edit-debt-name" class="form-control" value="<?= htmlspecialchars($debt['debt_name']); ?>" required>
        </div>

        <div class="col-md-6">
            <label for="amount-owed-input" class="form-label">Amount Owed:</label>
            <input type="number" id="edit-debt-amount" class="form-control" value="<?= $debt['amount_owed']; ?>" required>
        </div>

        <div class="col-md-6">
            <label for="min-payment" class="form-label">Minimum Payment:</label>
            <input type="number" id="edit-min-payment" class="form-control" value="<?= $debt['min_payment']; ?>" required>
        </div>

        <div class="col-md-6">
            <label for="interest-rate" class="form-label">Interest Rate (%):</label>
            <input type="number" step="0.01" id="edit-interest-rate" class="form-control" value="<?= $debt['interest_rate']; ?>" required>
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
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

    <!-- Payment History Visualization (Plot at the bottom) -->
    <?php if ($imageSrc): ?>
        <img src="<?= $imageSrc ?>" alt="Payment History Plot">
    <?php elseif (!empty($payments)): ?>
        <p class="error">Failed to generate payment history visualization.</p>
    <?php endif; ?>

</body>
</html>
