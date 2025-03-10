<?php
session_start();
require __DIR__ . '/model/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Icebreaker Finance Home</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
      
</head>
<body>
<?php include 'nav.php'; ?>

    <section class="banner bg-primary text-white text-center py-2">
    <div class="container">
    <h1 class="display-4">Icebreaker Finance</h1>
        <p class="lead">Smash Debt with Our Easy-to-Use Debt Snowball and Avalanche Trackers</p>
    </div>
    </section>

    <!-- Debt Snowball Method -->
    <div class="container text-center">
      <h2 class="section-title">Debt Snowball Method</h2>
      <div class="divider mx-auto"></div>
      <div class="row justify-content-center">
          <div class="col-12 col-lg-10 col-xl-8">
              <div class="ratio ratio-16x9">
                  <iframe 
                      src="https://www.youtube.com/embed/Q5jlY8_WmEE?si=Rkmu7_eIzgTBIxkn"
                      title="Debt Snowball"
                      allowfullscreen>
                  </iframe>
              </div>
          </div>
      </div>
    </div>

    <!-- Debt Avalanche Method -->
    <div class="container text-center">
      <h2 class="section-title">Debt Avalanche Method</h2>
      <div class="divider mx-auto"></div>
      <div class="row justify-content-center">
          <div class="col-12 col-lg-10 col-xl-8">
              <div class="ratio ratio-16x9">
                  <iframe 
                      src="https://www.youtube.com/embed/S19s7RwpKSM?si=UupX9dSsHdSNrE5G"
                      title="Debt Avalanche"
                      allowfullscreen>
                  </iframe>
              </div>
          </div>
      </div>
    </div>

    <!--Debt Tracker-->
    <section class="banner bg-primary text-white text-center py-3 mt-5">
    <div class="container">
    <h1 class="display-4">Take the First Step</h1> 
        <p class="lead">Try our debt repayment calculator</p>
    </div>
    </section>

    <section class="debt-method">
        <div class="container">
            <div id="error-message" class="error-message"></div>

            <!-- plus button to show and hide debt Form -->
            <button class="add-debt-btn" onclick="toggleDebtForm()">+</button>

            <!-- hidden form -->
            <div id="debt-form-container" class="hidden">
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
                        <input type="number" id="debt-amount" class="form-control" placeholder="Debt Amount" required />
                    </div>
                    <div class="col-12 col-md-auto">
                        <input type="number" id="min-payment" class="form-control" placeholder="Min Payment" required />
                    </div>
                    <div class="col-12 col-md-auto">
                        <input type="number" step="0.01" id="interest-rate" class="form-control" placeholder="Interest (%)" required />
                    </div>
                    <div class="col-12 col-md-auto">
                        <button type="button" class="btn btn-primary" onclick="addDebt()">GO</button>
                    </div>
                </form>
            </div>

            <!-- table layout for medium and large screens - shows table -->
            <div class="d-none d-md-block">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-10">
                        <div class="debt-table table-responsive">
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
                                <tbody id="debt-list" class="table-data">
                                    <!-- Debt rows will be injected here -->
                                </tbody>
                            </table>
                            <?php
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
                    </div>
                </div>
            </div>

            <!-- card layout for smaller screens - shows cards -->
            <div class="d-block d-md-none">
                <div class="container">
                    <div id="debt-cards">
                        <!-- Debt cards will be injected here -->
                    </div>
                    <div class="total-amount text-center mt-3">
                        <strong>Total Debt:</strong> $<span id="total-debt-mobile">0</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <p>© 2025 Icebreaker Finance. All rights reserved.</p>
    </footer>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
  const totalDebtElem = document.getElementById("total-debt");
  if (!totalDebtElem) {
    console.error("Error: `total-debt` element not found in the DOM.");
  } else {
    totalDebtElem.textContent = "$0.00";
    console.log("Total debt element found and updated.");
  }
});
</script>
    <script src="script.js"></script>
</body>
</html>
