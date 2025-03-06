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
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
</head>
<body>
    <?php include 'nav.php'; ?>  

    <?php if ($userRole === "admin") : ?>
        <div class="modify-accounts">
            <button onclick="window.location.href='admin-account-mgmt.php'">Modify Accounts</button>
        </div>
    <?php endif; ?>

    <!-- debt list section -->
    <div class="debts">
    <h2 class="debt-account">Debts</h2>

    <!-- Toggle Button for Sorting -->
    <div class="toggle-method">
        <button id="snowball-btn" class="active" onclick="toggleDebtMethod('snowball')">Snowball</button>
        <button id="avalanche-btn" onclick="toggleDebtMethod('avalanche')">Avalanche</button>
    </div>

    <?php if (!empty($debts)) : ?>
        <?php foreach ($debts as $debt) : ?>
            <div class="debt-item">
                <div><?= htmlspecialchars($debt['debt_name']) ?></div>
                <button onclick="window.location.href='edit-debt.php?debt_id=<?= $debt['debt_id'] ?>'">GO</button>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <p class="text-muted">No debts added yet.</p>
    <?php endif; ?>
</div>




        <!--recent debt activity-->
    <div class="container">
    <h2 class="section-title">Recent Debt Activity</h2>
    <div class="divider"></div>

    <?php if (!empty($recentActivity)) : ?>
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Debt Type</th>
                    <th>Payment Date</th>
                    <th>Payment Amount</th>
                </tr>
            </thead>
            <tbody id="recent-debt-list">
                <?php foreach ($recentActivity as $activity) : ?>
                    <tr>
                        <td><?= htmlspecialchars($activity['debt_type']) ?></td>
                        <td><?= htmlspecialchars(date("M d, Y", strtotime($activity['payment_date']))) ?></td>
                        <td>$<?= number_format($activity['payment_amount'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p class="text-muted">No recent debt payments recorded.</p>
    <?php endif; ?>
</div>


    <!-- Debt Tracker -->
<section class="banner">
    <p class="banner-text">Try our debt repayment calculator</p>
</section>

<section class="debt-method">
    
    <div class="container">
        <div id="error-message" class="error-message">

    </div>

        <!-- plus button to show and hide debt Form -->
        <button class="add-debt-btn" onclick="toggleDebtForm()">+</button>

        <!-- hidden form -->
        <div id="debt-form-container" class="hidden">
            <form id="debt-form" onsubmit="event.preventDefault(); addDebt();">
                <input type="text" id="debt-name" placeholder="Debt Name" required />
                <select id="method" required>
                    <option value="" disabled selected>Select Debt Method</option>
                    <option value="snowball">Snowball Method</option>
                    <option value="avalanche">Avalanche Method</option>
                </select>
                <input type="number" id="debt-amount" placeholder="Debt Amount" required />
                <input type="number" id="min-payment" placeholder="Minimum Payment" required />
                <input type="number" step="0.01" id="interest-rate" placeholder="Interest Rate (%)" required />
                <button type="button" class="go-button" onclick="addDebt()">GO</button>
            </form>
        </div>

        <!-- Debt Table -->
<div class="debt-table">
    <table>
        <thead>
            <tr class="table-header">
                <th>Debt Name</th>
                <th>Debt Type</th>
                <th>Amount Owed</th>
                <th>Minimum Payment</th>
                <th>Interest Rate (%)</th>
                <th>Action</th>
            </tr>
        </thead>
        
        <tbody id="debt-list">
            <?php if (!empty($debts)): ?>
                <?php foreach ($debts as $debt): ?>
                    <tr id="debt-row-<?= $debt['debt_id']; ?>">
                        <td><?= htmlspecialchars($debt['debt_name']); ?></td>
                        <td><?= htmlspecialchars($debt['debt_type']); ?></td>
                        <td>$<?= number_format($debt['amount_owed'], 2); ?></td>
                        <td>$<?= number_format($debt['min_payment'], 2); ?></td>
                        <td><?= number_format($debt['interest_rate'], 2); ?>%</td>
                        <td><button class="delete-btn" onclick="deleteDebt(<?=$debt['debt_id']; ?>)">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-muted">No debts added yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

    </div>
</section>

    <footer class="footer">
        <p>© 2025 Icebreaker Finance. All rights reserved.</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>
