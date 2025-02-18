<?php
    session_start();

    // set user role dynamically from session
    $userRole = $_SESSION['userRole'] ?? 'user'; // default role is 'user'
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Details</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!--include nav bar-->
    <?php include 'nav.php'; ?>  

    <!-- Modify Accounts Section (Admin Only) -->
    <?php if ($userRole === "admin") : ?>
        <div class="modify-accounts">
            <button onclick="window.location.href='admin-account-mgmt.php'">Modify Accounts</button>
        </div>
    <?php endif; ?>

    <!-- debt list section -->
    <div class="debts">
        <h2 class="debt-account">Debts</h2>
        <div class="debt-item">
            <div>Credit Card</div>
            <button onclick="window.location.href='edit-debt.php'">GO</button>
        </div>
        <div class="debt-item">
            <div>Car</div>
            <button onclick="window.location.href='edit-debt.php'">GO</button>
        </div>
        <div class="debt-item">
            <div>House</div>
            <button onclick="window.location.href='edit-debt.php'">GO</button>
        </div>
        <div class="debt-item">
            <button onclick="window.location.href='all-debts.php'">All</button>
        </div>
    </div>

    <!-- debt tracker section-->
    <div class="container">
        <h1>Debt Tracker</h1>
        <div id="error-message" class="error-message"></div>
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

    <!-- recent activity on all accounts by date -->
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
                <?php
                    // placeholder data - !!!need to add database fetch to this!!!!
                    $recentActivity = [
                        ["2025-02-06", "Credit Card", "Payment Made", "-$200.00"],
                        ["2025-02-05", "Car Loan", "Interest Applied", "+$15.75"],
                        ["2025-02-04", "House Loan", "Minimum Payment", "-$1,000.00"],
                        ["2025-02-03", "Credit Card", "New Purchase", "+$75.25"],
                    ];

                    // loop through recent activity and display
                    foreach ($recentActivity as $activity) {
                        echo "<tr>";
                        echo "<td>{$activity[0]}</td>"; // date
                        echo "<td>{$activity[1]}</td>"; // account
                        echo "<td>{$activity[2]}</td>"; // activity
                        echo "<td>{$activity[3]}</td>"; // amount
                        echo "</tr>";
                    }
                ?>
            </tbody>
        </table>
    </div>
    <footer class="footer">
    <p>© 2025 Icebreaker Finance. All rights reserved.</p>
    </footer>
    <script src="script.js"></script>

</body>
</html>
