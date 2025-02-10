
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Icebreaker Finance Home</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'nav.php'; ?>

    <main>
        <h1>Smash Debt with Our Easy-to-Use Debt Tracker</h1>

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

        <h2>Learn About the Debt Snowball Method</h2>
        <iframe
            width="560"
            height="315"
            src="https://www.youtube.com/embed/Q5jlY8_WmEE?si=Rkmu7_eIzgTBIxkn"
            title="Debt Snowball"
            frameborder="0"
            allowfullscreen
        ></iframe>

        <h2>Learn About the Debt Avalanche Method</h2>
        <iframe
            width="560"
            height="315"
            src="https://www.youtube.com/embed/S19s7RwpKSM?si=UupX9dSsHdSNrE5G"
            title="Debt Avalanche"
            frameborder="0"
            allowfullscreen
        ></iframe>

        <script src="script.js"></script>
    </main>
</body>
</html>
