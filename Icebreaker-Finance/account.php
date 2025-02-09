<?php
    
    $userRole = "user"; 
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
    <nav id="nav">
        <ul>
            <li><a href="index.php">Icebreaker Finance</a></li>
            <li><a href="debt-buster-tools.php">Debt Buster Tools</a></li>
            <li><a href="account.php">Account</a></li>
            <li><a href="index.php">Logout</a></li>
        </ul>
</nav>

 <!-- modify accounts for admin -->
 <?php if ($userRole === "admin") : ?>
        <div class="modify-accounts">
            <button onclick="window.location.href='admin-account-mgmt'">Modify Accounts</button>
        </div>
    <?php endif; ?>

<!-- debt list section -->
<div class="debts">
        <h2>Debts</h2>
        <div class="debt-item">
            <div>Credit Card</div>
            <button onclick="window.location.href='edit-debt.html'">GO</button>
        </div>
        <div class="debt-item">
            <div>Car</div>
            <button onclick="window.location.href='edit-debt.html'">GO</button>
        </div>
        <div class="debt-item">
            <div>House</div>
            <button onclick="window.location.href='edit-debt.html'">GO</button>
        </div>
        <div>
            <button onclick="window.location.href='all-debts.html'">All</button>
        </div>
    </div>

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
        <script src="script.js"></script>
</body>
</html>