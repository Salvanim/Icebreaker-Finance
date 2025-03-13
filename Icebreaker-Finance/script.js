

document.addEventListener("DOMContentLoaded", function () {
    const debtForm = document.getElementById("debt-form");
    const debtList = document.getElementById("debt-list");
    const totalDebtSpan = document.getElementById("total-debt") || { textContent: "" };
    var debts = [];
    let isLoggedIn = false;
    if(document.getElementById("isLoggedIn") !== null){
        isLoggedIn = document.getElementById("isLoggedIn").innerHTML == "1" ? true : false;
    }

    if (!totalDebtSpan) {
        console.error("Error: `total-debt` element not found in the DOM.");
    }
    const debtFormContainer = document.getElementById("debt-form-container");

    // This function updates only the table (debt-list) and the total debt span
    function updateDebtTable() {
        console.log("updateDebtTable() function triggered");
        const debtsContainer = document.getElementById("debt-list");
        if (!debtsContainer) {
            console.error("Error: debtsContainer element not found.");
            return;
        }

        console.log("Current debts array:", debts);

        // Clear previous rows
        debtsContainer.innerHTML = "";

        // If no debts, show a placeholder row and set total debt to $0.00
        if (debts.length === 0) {
            debtsContainer.innerHTML = "<tr><td colspan='6' class='text-muted'>No debts added yet.</td></tr>";
            if (totalDebtSpan) {
                totalDebtSpan.textContent = "$0.00";
            }
            return;
        }

        // Build rows
        debts.forEach(debt => {
            const amountOwed = parseFloat(debt.amount_owed) || 0;
            const balance = parseFloat(debt.balance) || 0;
            const minPayment = parseFloat(debt.min_payment) || 0;
            const interestRate = parseFloat(debt.interest_rate) || 0;
            const payoffDate = calculatePayoffDate(amountOwed, interestRate, minPayment);

            const row = document.createElement("tr");
            row.id = `debt-row-${debt.debt_id}`;
            row.innerHTML = `
                <td>${debt.debt_name}</td>
                <td>$${amountOwed.toFixed(2)}</td>
                <td>$${balance.toFixed(2)}</td>
                <td>$${minPayment.toFixed(2)}</td>
                <td>${interestRate.toFixed(2)}%</td>
                <td>${payoffDate}</td>
                <td>
                    <button class="btn btn-primary btn-sm delete-btn" onclick="deleteDebt(${debt.debt_id})">Delete</button>
                </td>
            `;
            debtsContainer.appendChild(row);
        });

        // Calculate total debt
        const totalDebt = debts.reduce((sum, d) => sum + (parseFloat(d.amount_owed) || 0), 0);
        if (totalDebtSpan) {
            totalDebtSpan.textContent = `$${totalDebt.toFixed(2)}`;
        }

        console.log("Debt table updated successfully!");
    }

    // Hide/show the debt calculator form
    window.toggleDebtForm = function () {
        if (!debtFormContainer) {
            console.error("Error: `debt-form-container` not found.");
            return;
        }
        debtFormContainer.classList.toggle("hidden");
    };

    function calculatePayoffDate(amount, rate, payment) {
        let balance = amount;
        let monthlyRate = (rate / 100) / 12;
        let months = 0;
        const today = new Date();

        if (payment <= balance * monthlyRate) {
            return "Error: The monthly payment must be greater than the first month's interest.";
        }

        while (balance > 0) {
            let interest = balance * monthlyRate;
            balance += interest - payment;
            months++;
            if (balance < 0) balance = 0;
        }

        let payoffDate = new Date();
        payoffDate.setMonth(today.getMonth() + months);
        return payoffDate.toLocaleDateString("en-US", { month: "long", day: "numeric", year: "numeric" });
    }

    window.addDebt = function () {
        const debtNameInput = document.getElementById("debt-name");
        const methodInput = document.getElementById("method");
        const debtAmountInput = document.getElementById("debt-amount");
        const minPaymentInput = document.getElementById("min-payment");
        const interestRateInput = document.getElementById("interest-rate");

        if (!debtNameInput || !methodInput || !debtAmountInput || !minPaymentInput || !interestRateInput) {
            console.error("One or more input fields are missing.");
            return;
        }

        const debtName = debtNameInput.value.trim();
        const method = methodInput.value;
        const amountOwed = parseFloat(debtAmountInput.value) || 0;
        const balance = amountOwed;
        const minPayment = parseFloat(minPaymentInput.value) || 0;
        const interestRate = parseFloat(interestRateInput.value) || 0;
        const payoffDate = calculatePayoffDate(amountOwed, interestRate, minPayment);

        if (!debtName || !method || amountOwed <= 0 || minPayment <= 0 || interestRate < 0) {
            alert("Please enter valid debt details.");
            return;
        }

        if(payoffDate.includes("Error: ")){
            alert(payoffDate.split("Error: ")[1]);
            return;
        }

        console.log(isLoggedIn);
        // If user not logged in, or if you have a 'loggedIn' check
        if (typeof isLoggedIn !== "undefined" && !isLoggedIn) {
            alert("Debt added successfully! (Demo mode – not saved to database)");
            const demoDebt = {
                debt_id: Date.now(),
                debt_name: debtName,
                debt_type: method,
                amount_owed: amountOwed,
                balance: balance,
                min_payment: minPayment,
                interest_rate: interestRate,
                payoff_date: payoffDate
            };
            debts.push(demoDebt);
            updateDebtTable();
            debtForm.reset();
            if (debtFormContainer) {
                debtFormContainer.classList.add("hidden");
            }
            return;
        } else {
            // For logged in users, send to add-debt.php
            const formData = new URLSearchParams();
            formData.append("debt_name", debtName);
            formData.append("debt_type", method);
            formData.append("debt_amount", amountOwed);
            formData.append("balance", balance);
            formData.append("min_payment", minPayment);
            formData.append("interest_rate", interestRate);
            formData.append("payoff_date", payoffDate);

            fetch("add-debt.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: formData.toString(),
                credentials: "include"
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Debt added successfully!");
                    debts.push({
                        debt_id: data.debt_id,
                        debt_name: debtName,
                        debt_type: method,
                        amount_owed: amountOwed,
                        balance: balance,
                        min_payment: minPayment,
                        interest_rate: interestRate,
                        payoff_date: payoffDate
                    });
                    updateDebtTable();
                    debtForm.reset();
                    if (debtFormContainer) {
                        debtFormContainer.classList.add("hidden");
                    }
                    if (window.location.pathname.includes("account.php")) {
                        window.reload();
                    }
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(error => console.error("Error:", error));
        }
    };

    // Delete a debt
    window.deleteDebt = function (debtId) {
        if (!confirm("Are you sure you want to delete this debt?")) return;
        fetch("delete-debts.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `debt_id=${debtId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Debt deleted successfully!");
                debts = debts.filter(debt => debt.debt_id !== debtId);
                updateDebtTable();
                if(window.location.href.includes("account.php")){
                    updateDebtsSection();
                    window.location.reload();
                }
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => console.error("Error:", error));
    };

    window.deletePayment = function (paymentId, debtID) {
        if (!confirm("Are you sure you want to delete this payment?")) return;

        fetch("delete-payment.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `payment_id=${encodeURIComponent(paymentId)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Payment deleted successfully!");
                window.location.href = `edit-debt.php?debt_id=${encodeURIComponent(debtID)}`;
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("An error occurred while deleting the payment.");
        });
    };

    window.updateDebt = function (debtId) {
        // Fetch input elements
        const debtNameInput = document.getElementById("edit-debt-name");
        const debtAmountInput = document.getElementById("edit-debt-amount");
        const minPaymentInput = document.getElementById("edit-min-payment");
        const interestRateInput = document.getElementById("edit-interest-rate");

        // Check if any element is missing
        if (!debtNameInput || !debtAmountInput || !minPaymentInput || !interestRateInput) {
            console.error("One or more input fields are missing.");
            return;
        }

        // Get values from input fields
        const debtName = debtNameInput.value.trim();
        const debtAmount = parseFloat(debtAmountInput.value) || 0;
        const minPayment = parseFloat(minPaymentInput.value) || 0;
        const interestRate = parseFloat(interestRateInput.value) || 0;
        const payoffDate = calculatePayoffDate(debtAmount, interestRate, minPayment);

        // Validate input values
        if (!debtName || debtAmount <= 0 || minPayment <= 0 || interestRate < 0) {
            alert("Please enter valid debt details.");
            return;
        }

        // Prepare form data
        const formData = new URLSearchParams();
        formData.append("debt_id", debtId);
        formData.append("debt_name", debtName);
        formData.append("debt_amount", debtAmount);
        formData.append("min_payment", minPayment);
        formData.append("interest_rate", interestRate);
        formData.append("payoff_date", payoffDate);

        // Send AJAX request to update-debt.php
        fetch("update-debt.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: formData.toString(),
        })

        .then(response => response.json())
        .then(data => {
            console.log("Update Response:", data);
            if (data.success) {
                alert("Debt updated successfully!");
                location.href = "edit-debt.php?debt_id=" + debtId; // Redirect to main debts page
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => console.error("Error:", error));
    };

    function updateDebtsSection() {
        const debtsContainer = document.getElementById("debt-list");
        if (!debtsContainer) {
            console.error("Error: debtsContainer element not found.");
            return;
        }

        debtsContainer.innerHTML = "<h2 class='debt-account'>Debts</h2>";

        if (debts.length === 0) {
            debtsContainer.innerHTML += "<p class='text-muted'>No debts added yet.</p>";
            return;
        }

        debts.forEach(debt => {
            const debtItem = document.createElement("div");
            debtItem.classList.add("debt-item");
            debtItem.id = `debt-item-${debt.debt_id}`;
            debtItem.innerHTML = `
                <div>${debt.debt_name}</div>
                <button onclick="window.location.href='edit-debt.php?debt_id=${debt.debt_id}'">GO</button>
            `;
            debtsContainer.appendChild(debtItem);
        });
    }


    function fetchDebtsAndUpdateSection() {
        fetch("get-debts.php")
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    debts = data.debts;
                    updateDebtsSection();
                    updateDebtTable();
                } else {
                    console.error("Error fetching debts:", data.message);
                }
            })
            .catch(error => console.error("Error:", error));
    }

    window.addPayment = function (debtId) {
        const paymentAmountInput = document.getElementById("payment-amount");
        const paymentDateInput = document.getElementById("payment-date");
        if (!paymentAmountInput || !paymentDateInput) {
            console.error("Payment input not found.");
            return;
        }

        const paymentAmount = parseFloat(paymentAmountInput.value) || 0;
        const paymentDate = paymentDateInput.value;
        if (paymentAmount <= 0 || !paymentDate) {
            alert("Please enter a valid payment amount and/or date.");
            return;
        }

        const formData = new URLSearchParams();
        formData.append("debt_id", debtId);
        formData.append("payment_amount", paymentAmount);
        formData.append("payment_date", paymentDate);

        fetch("add-payment.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: formData.toString(),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Payment added successfully!");
                location.reload();
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => console.error("Error:", error));
    };

    // Only fetch debts if we are on the account page
    if (window.location.pathname.includes("account.php")) {
        fetchDebtsAndUpdateSection(); // make sure this runs on account page only, caused issues on edit-debt page
    } else {
        console.log("Skipping fetchDebtsAndUpdateSection() on edit-debt.php");
    }
});
