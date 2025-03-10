<?php 
session_start();
require __DIR__ . '/model/db.php';

// Ensure user is logged in
if (!isset($_SESSION['isLoggedIn'])) {
    header("Location: index.php");
    exit;
}

// Get user role
$userRole = $_SESSION['role'] ?? 'user';
$userId = $_SESSION['user_id'] ?? null;

// Fetch user debts for the debt list
function getUserDebts($userId) {
    global $db;
    if (!$db || !$userId) return [];

    try {
        $stmt = $db->prepare("
            SELECT debt_id, debt_name, debt_type, amount_owed, min_payment, interest_rate
            FROM debt_lookup
            WHERE user_id = ?
            ORDER BY debt_id DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

// Fetch payment history to show recent activity
function getDebtActivity($userId) {
    global $db;
    if (!$db || !$userId) return [];
    
    try {
        $stmt = $db->prepare("
            SELECT d.debt_type, p.payment_date, p.payment_amount 
            FROM debt_lookup d 
            JOIN debt_payments p ON d.debt_id = p.debt_id 
            WHERE d.user_id = ? 
            ORDER BY p.payment_date DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

// get data for the page
$debts = getUserDebts($userId);
$recentActivity = getDebtActivity($userId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'nav.php'; ?>  

    <!--admin only button for modifying user accounts-->
    <?php if ($userRole === "admin") : ?>
        <div class="modify-accounts-container">
            <a href="admin-account-mgmt.php" class="btn btn-sm btn-primary">Modify Accounts</a>
        </div>
    <?php endif; ?>

    <!-- debt list section (server-side rendered) -->
    <div class="container my-4">
        <h2 class="text-center custom-blue">Debts</h2>
        <div class="list-group">    
            <?php if (!empty($debts)) : ?>
                <?php foreach ($debts as $debt) : ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><?= htmlspecialchars($debt['debt_name']) ?></span>
                        <a href="edit-debt.php?debt_id=<?= $debt['debt_id'] ?>" class="btn btn-sm btn-primary">GO</a>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <p class="text-muted">No debts added yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Debt Tracker Banner -->
    <section class="banner bg-primary text-white text-center py-2">
        <div class="container">
            <h1 class="display-4">Debt Calculator</h1>
            <p class="lead">Take control of your finances with our easy-to-use calculator.</p>
        </div>
    </section>

    <section class="debt-method">
        <div class="container debt-calculator-container">
            <!-- Error Message Container -->
            <div id="error-message" class="error-message"></div>

            <!-- Plus button to toggle debt form -->
            <button class="btn btn-primary btn-lg add-debt-btn" onclick="toggleDebtForm()">+</button>

            <!-- Hidden debt form container -->
            <div id="debt-form-container" class="container mt-3 hidden">
                <form id="debt-form" class="row g-2 align-items-center">
                    <div class="col-12 col-md-auto">
                        <input type="text" id="debt-name" class="form-control" placeholder="Debt Name" required />
                    </div>
                    <div class="col-12 col-md-auto">
                        <select id="method" class="form-select" required>
                            <option value="" disabled selected>Select Method</option>
                            <option value="snowball">Snowball</option>
                            <option value="avalanche">Avalanche</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-auto">
                        <input type="number" id="debt-amount" class="form-control" placeholder="Amount" required />
                    </div>
                    <div class="col-12 col-md-auto">
                        <input type="number" id="min-payment" class="form-control" placeholder="Min Payment" required />
                    </div>
                    <div class="col-12 col-md-auto">
                        <input type="number" step="0.01" id="interest-rate" class="form-control" placeholder="Interest (%)" required />
                    </div>
                    <div class="col-12 col-md-auto">
                        <button type="button" class="btn btn-success" onclick="addDebt()">GO</button>
                    </div>
                </form>
            </div>

            <!-- Toggle Switch for Snowball / Avalanche -->
            <div class="form-check form-switch text-center my-3">
                <input class="form-check-input" type="checkbox" id="methodToggle" onchange="toggleMethod(this)">
                <label class="form-check-label" for="methodToggle" id="methodLabel">Snowball Method</label>
            </div>

            <!-- Debt Table Layout for Medium and Larger Screens -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center" id="debt-table">
                    <thead class="table-primary">
                        <tr>
                            <th>Debt Name</th>
                            <th>Debt Amount</th>
                            <th>Minimum Payment</th>
                            <th>Interest Rate (%)</th>
                            <th>Payoff Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="debt-list">
                        <?php if (!empty($debts)): ?>
                            <?php foreach ($debts as $debt): ?>
                                <tr id="debt-row-<?= $debt['debt_id']; ?>">
                                    <td><?= htmlspecialchars($debt['debt_name']); ?></td>
                                    <td>$<?= number_format($debt['amount_owed'] ?? 0, 2); ?></td>
                                    <td>$<?= number_format($debt['min_payment'] ?? 0, 2); ?></td>
                                    <td><?= number_format($debt['interest_rate'] ?? 0, 2); ?>%</td>
                                    <td><!-- Add payoff date calculation if available --></td>
                                    <td>
                                        <button class="btn btn-danger btn-sm delete-btn" onclick="deleteDebt(<?= $debt['debt_id']; ?>)">Delete</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-muted">No debts added yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php
            // Calculate total debt server-side for the initial page load
            $totalDebt = 0;
            if (!empty($debts)) {
                foreach ($debts as $debt) {
                    $totalDebt += $debt['amount_owed'];
                }
            }
            ?>
            <div class="total-amount text-center">
                <strong>Total Debt:</strong> <span id="total-debt">$<?= number_format($totalDebt, 2); ?></span>
            </div>
        </div>
    </section>

    <!-- Debt Card Layout for Small Screens (optional) -->
    <div class="d-block d-md-none">
        <div id="debt-cards">
            <?php if (!empty($debts)): ?>
                <?php foreach ($debts as $debt): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Debt Name: <?= htmlspecialchars($debt['debt_name']); ?></h5>
                            <p class="card-text">
                                <strong>Debt Type:</strong> <?= htmlspecialchars($debt['debt_type']); ?><br>
                                <strong>Amount Owed:</strong> $<?= number_format($debt['amount_owed'] ?? 0, 2); ?><br>
                                <strong>Min Payment:</strong> $<?= number_format($debt['min_payment'] ?? 0, 2); ?><br>
                                <strong>Interest Rate:</strong> <?= number_format($debt['interest_rate'] ?? 0, 2); ?>%
                            </p>
                            <button class="btn btn-danger btn-sm" onclick="deleteDebt(<?= $debt['debt_id']; ?>)">Delete</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">No debts added yet.</p>
            <?php endif; ?>
        </div>
        <div class="total-amount text-center mt-3">
            <strong>Total Debt:</strong> $<span id="total-debt-mobile">0</span>
        </div>
    </div>

    <footer class="footer">
        <p>© 2025 Icebreaker Finance. All rights reserved.</p>
    </footer>

    <!-- Use the updated script.js below -->
    <script src="script.js"></script>
</body>
</html>
