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

// Function to fetch debt activity securely
function getDebtActivity($userId) {
    global $db;
    if (!$db) return [];
    
    try {
        $stmt = $db->prepare("SELECT d.debt_type, p.payment_date, p.payment_amount FROM debt_lookup d JOIN debt_payments p ON d.debt_id = p.debt_id WHERE d.user_id = ? ORDER BY p.payment_date DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

$recentActivity = getDebtActivity($userId);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Details</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
</head>
<body>
    <?php include 'nav.php'; ?>  

    <?php if ($userRole === "admin") : ?>
        <div class="modify-accounts">
            <button onclick="window.location.href='admin-account-mgmt.php'">Modify Accounts</button>
        </div>
    <?php endif; ?>

    <div class="container">
        <h1>Debt Tracker</h1>
        <form id="debt-form">
            <input type="text" id="debt-name" placeholder="Debt Name" required />
            <select id="method">
                <option value="" disabled selected>Select Debt Method</option>
                <option value="snowball">Snowball Method</option>
                <option value="avalanche">Avalanche Method</option>
            </select>
            <input type="number" id="debt-amount" placeholder="Debt Amount" required />
            <input type="number" id="min-payment" placeholder="Minimum Payment" required />
            <input type="number" step="0.01" id="interest-rate" placeholder="Interest Rate (%)" required />
            <button type="submit">Add Debt</button>
        </form>
        <div class="debt-table">
            <table>
                <thead>
                    <tr>
                        <th>Debt Name</th>
                        <th>Debt Amount</th>
                        <th>Minimum Payment</th>
                        <th>Interest Rate (%)</th>
                        <th>Payoff Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="debt-list"></tbody>
            </table>
            <div class="total-amount">
                <strong>Total Debt:</strong> $<span id="total-debt">0</span>
            </div>
        </div>
    </div>

    <div class="container">
        <h2>Recent Activity</h2>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Account</th>
                    <th>Action</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentActivity as $activity): ?>
                    <tr>
                        <td><?= htmlspecialchars($activity['payment_date']) ?></td>
                        <td><?= htmlspecialchars($activity['debt_type']) ?></td>
                        <td>Payment Made</td>
                        <td>$<?= htmlspecialchars(number_format($activity['payment_amount'], 2)) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <footer class="footer">
        <p>© 2025 Icebreaker Finance. All rights reserved.</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>
