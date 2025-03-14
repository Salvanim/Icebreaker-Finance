<?php
session_start();
require __DIR__ . '/model/db.php';

// Ensure user is logged in
if (!isset($_SESSION['isLoggedIn'])) {
    echo"<p hidden id='isLoggedIn'> 0 </p>";
    header("Location: index.php");
    exit;
} else {
    echo "<p hidden id='isLoggedIn'>" . $_SESSION['isLoggedIn'] . "</p>";
}

$userId = $_SESSION['user_id'] ?? null;
$debtId = isset($_GET['debt_id']) ? intval($_GET['debt_id']) : null;

// Fetch debt details
function getDebtDetails($debtId, $userId) {
    global $db;
    if (!$db || !$debtId || !$userId) return null;

    try {
        $stmt = $db->prepare("
            SELECT debt_id, debt_name, amount_owed, balance, min_payment, interest_rate, date_added
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

// gets deates every mouth between 2 dates
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

// Prepare payment breakdown data (computed in chronological order)
$breakdown = [];
if (!empty($payments)) {
    $paymentsAscending = array_reverse($payments);

    // Calculate initial balance as current amount owed plus total payments made
    $totalPayments = array_sum(array_column($payments, 'payment_amount'));
    $initialBalance = $debt['balance'] + $totalPayments;
    $runningBalance = $initialBalance;
    $monthlyInterestRate = $debt["interest_rate"] / 1200;

    foreach ($paymentsAscending as $payment) {
        $interest = $runningBalance * $monthlyInterestRate;
        $principal = $payment['payment_amount'] - $interest;
        if ($principal < 0) {
            $principal = 0;
            $interest = $payment['payment_amount'];
        }
        $breakdown[$payment['payment_id']] = [
            'interest' => $interest,
            'principal' => $principal,
            'balance_after' => $runningBalance - $principal,
        ];
        $runningBalance -= $principal;
    }
}

// Generate plot image if payments exist (unchanged from before)
// Generate plot image if payments exist
$imageSrc = '';
if (!empty($payments)) {
    $chronologicalPayments = array_reverse($payments);
    $amountOwed = $debt["amount_owed"];
    $plotDataString = $debt["date_added"] . "," . $amountOwed . "$";
    $dateStarted = date_create($debt["date_added"])->format('Y-m-d');
    $today = date_create("today")->format('Y-m-d');
    $datesBetween = getDatesBetween($dateStarted, $today);
    $interestRate = $debt["interest_rate"]/1200;

    foreach ($chronologicalPayments as $payment) {
        $calculatedChange = $amountOwed - $payment['payment_amount'];
        $plotDataString .= $payment['payment_date'] . "," . $calculatedChange . "$";
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
<?php
if (isset($_SESSION['isLoggedIn'])) {
    echo "<p hidden id='isLoggedIn'>" . $_SESSION['isLoggedIn'] . "</p>";
} else {
    echo"<p hidden id='isLoggedIn'> 0 </p>";
}

?>
    <?php include 'nav.php';
        if($debt['balance'] <= 0){?>
            <div class="snowflakes" aria-hidden="true">
        <!--Defines large amount fo sow flakes -->
                <div class="snowflake">
                    <div class="inner">❅</div>
                </div>
                <div class="snowflake">
                    <div class="inner">❅</div>
                </div>
                <div class="snowflake">
                    <div class="inner">❅</div>
                </div>
                <div class="snowflake">
                    <div class="inner">❅</div>
                </div>
                <div class="snowflake">
                    <div class="inner">❅</div>
                </div>
                <div class="snowflake">
                    <div class="inner">❅</div>
                </div>
                <div class="snowflake">
                    <div class="inner">❅</div>
                </div>
                <div class="snowflake">
                    <div class="inner">❅</div>
                </div>
                <div class="snowflake">
                    <div class="inner">❅</div>
                </div>
                <div class="snowflake">
                    <div class="inner">❅</div>
                </div>
                <div class="snowflake">
                    <div class="inner">❅</div>
                </div>
                <div class="snowflake">
                    <div class="inner">❅</div>
                </div>
            </div>
        <?php }
    ?>

    <div class="container mt-4">
        <h2 class="text-primary"><?= htmlspecialchars($debt['debt_name']) ?></h2>

        <div class="card p-3 mb-4">
            <p><strong>Amount Owed:</strong> <span id="amount-owed" class="text-primary">$<?= number_format($debt['amount_owed'], 2) ?></span></p>
            <p><strong>Balance:</strong> <span id="balance" class="text-primary">$<?= number_format($debt['balance'], 2) ?></span></p>
            <p><strong>Minimum Payment:</strong> $<?= number_format($debt['min_payment'], 2) ?></p>
            <p><strong>Interest Rate:</strong> <?= number_format($debt['interest_rate'], 2) ?>%</p>
        </div>

        <!-- Edit Debt Form -->
        <h3>Edit Debt Details</h3>
        <form id="edit-debt-form" action="update-debt.php" method="POST">
            <input type="hidden" name="debt_id" value="<?= $debtId; ?>">

            <div class="col-md-6">
                <label for="debt-name" class="form-label">Debt Name:</label>
                <input type="text" id="edit-debt-name" name="debt_name" class="form-control" value="<?= htmlspecialchars($debt['debt_name']); ?>" required>
            </div>

            <div class="col-md-6">
                <label for="amount-owed-input" class="form-label">Amount Owed:</label>
                <input type="number" id="edit-debt-amount" name="debt_amount" class="form-control" value="<?= $debt['amount_owed']; ?>" required>
            </div>

            <div class="col-md-6">
                <label for="balance" class="form-label">Balance:</label>
                <input type="number" id="balance" name="debt_balance" class="form-control" value="<?= $debt['balance']; ?>" required>
            </div>

            <div class="col-md-6">
                <label for="min-payment" class="form-label">Minimum Payment:</label>
                <input type="number" id="edit-min-payment" name="min_payment" class="form-control" value="<?= $debt['min_payment']; ?>" required>
            </div>

            <div class="col-md-6">
                <label for="interest-rate" class="form-label">Interest Rate (%):</label>
                <input type="number" step="0.01" id="edit-interest-rate" name="interest_rate" class="form-control" value="<?= $debt['interest_rate']; ?>" required>
            </div>

            <div class="col-12 mt-3">
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>

         <!-- Payment Transactions -->
        <h3 class="mt-4">Payment Transactions</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Payment Date</th>
                    <th>Amount</th>
                    <th>Principal</th>
                    <th>Interest</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="payment-list">
                <?php if (!empty($payments)): ?>

                    <?php foreach ($payments as $payment):
                        $break = isset($breakdown[$payment['payment_id']]) ? $breakdown[$payment['payment_id']] : ['principal' => 0, 'interest' => 0];
                    ?>
                        <tr id="payment-row-<?= $payment['payment_id'] ?>">
                            <td><?= date("M d, Y", strtotime($payment['payment_date'])) ?></td>
                            <td>$<?= number_format($payment['payment_amount'], 2) ?></td>
                            <td>$<?= number_format($break['principal'], 2) ?></td>
                            <td>$<?= number_format($break['interest'], 2) ?></td>
                            <td><button onclick="deletePayment(<?= $payment['payment_id'] ?>, <?= $debtId ?>)">Delete</button></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-muted">No payments recorded yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Add Payment Form (unchanged) -->
        <h3>Add a Payment</h3>
        <form id="add-payment-form" class="row g-3">
            <div class="col-md-6">
                <input type="number" id="payment-amount" class="form-control" placeholder="Payment Amount" required>
            </div>
            <div class="col-md-6">
                <input type="date" id="payment-date" class="form-control" required>
            </div>
            <div class="col-12">
                <button type="button" class="btn btn-primary" onclick="addPayment(<?= $debtId ?>)">Submit</button>
            </div>
        </form>

        <!-- Payment History Visualization -->
        <?php if ($imageSrc): ?>
            <div class="row my-5">  <!-- Increased vertical spacing -->
                <div class="col-12 col-lg-10 mx-auto px-0">  <!-- Wider container with no horizontal padding -->
                    <div class="ratio ratio-16x9">  <!-- Responsive aspect ratio container -->
                        <img src="<?= $imageSrc ?>"
                             alt="Payment History Plot"
                             class="img-fluid"
                             style="object-fit: contain; background-color: #f8f9fa;">  <!-- Maintain aspect ratio -->
                    </div>
                </div>
            </div>
        <?php elseif (!empty($payments)): ?>
            <div class="row">
                <div class="col-12 text-center">
                    <p class="error">Failed to generate payment history visualization.</p>
                </div>
            </div>
        <?php endif; ?>

    </div>
</body>
</html>
