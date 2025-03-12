document.addEventListener("DOMContentLoaded", function () {
    let debts = [];

    const debtForm = document.getElementById("debt-form");
    const debtList = document.getElementById("debt-list");
    const totalDebtSpan = document.getElementById("total-debt") || { textContent: "" };
    let isLoggedIn = false;
    if(document.getElementById("isLoggedIn") !== null){
        isLoggedIn = document.getElementById("isLoggedIn").innerHTML == "1" ? true : false;
    }

    if (!totalDebtSpan) {
        console.error("Error: `total-debt` element not found in the DOM.");
    }

    // Just to confirm we have the form container
    const debtFormContainer = document.getElementById("debt-form-container");

    // This function updates only the table (debt-list) and the total debt span
    function updateDebtTable() {
        const debtsContainer = document.getElementById("debt-list");
        if (!debtsContainer) {
            console.error("Error: debtsContainer element not found.");
            return;
        }

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
            const minPayment = parseFloat(debt.min_payment) || 0;
            const interestRate = parseFloat(debt.interest_rate) || 0;
            const payoffDate = calculatePayoffDate(amountOwed, interestRate, minPayment);
            const row = document.createElement("tr");
            row.id = `debt-row-${debt.debt_id}`;
            row.innerHTML = `
                <td>${debt.debt_name}</td>
                <td>$${amountOwed.toFixed(2)}</td>
                <td>$${minPayment.toFixed(2)}</td>
                <td>${interestRate.toFixed(2)}%</td>
                <td>${payoffDate}</td>
            `;

            row.innerHTML += `<td> <button class="btn btn-danger btn-sm delete-btn" onclick="deleteDebt(${debt.debt_id})">Delete</button> </td>`

            debtsContainer.appendChild(row);
        });

        // Calculate total debt
        const totalDebt = debts.reduce((sum, d) => sum + (parseFloat(d.amount_owed) || 0), 0);
        if (totalDebtSpan) {
            totalDebtSpan.textContent = `$${totalDebt.toFixed(2)}`;
        }
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

    // Add a new debt
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
        const debtAmount = parseFloat(debtAmountInput.value) || 0;
        const minPayment = parseFloat(minPaymentInput.value) || 0;
        const interestRate = parseFloat(interestRateInput.value) || 0;
        const payoffDate = calculatePayoffDate(debtAmount, interestRate, minPayment);

        if (!debtName || !method || debtAmount <= 0 || minPayment <= 0 || interestRate < 0) {
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
                amount_owed: debtAmount,
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
            formData.append("debt_amount", debtAmount);
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
                        amount_owed: debtAmount,
                        min_payment: minPayment,
                        interest_rate: interestRate,
                        payoff_date: payoffDate
                    });
                    updateDebtTable();
                    debtForm.reset();
                    if (debtFormContainer) {
                        debtFormContainer.classList.add("hidden");
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
                debts = debts.filter(d => d.debt_id !== debtId);
                updateDebtTable();
                if(window.location.href.includes("account.php")){
                    window.location.reload();
                }
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => console.error("Error:", error));
    };

    // Toggle avalanche/snowball label
    window.toggleMethod = function (toggle) {
        const selectedMethod = toggle.checked ? "avalanche" : "snowball";
        document.getElementById("methodLabel").textContent = toggle.checked
            ? "Avalanche Method"
            : "Snowball Method";

        if (toggle.checked) {
            toggle.classList.add("avalanche");
            toggle.classList.remove("snowball");
        } else {
            toggle.classList.add("snowball");
            toggle.classList.remove("avalanche");
        }
        // If you want to do something special with the table based on method, do it here:
        // updateDebtTableWithMethod(selectedMethod); // Or remove if not needed
    };

    // Fetch debts from get-debts.php and update the table
    function fetchDebtsAndUpdateTable() {
        fetch("get-debts.php")
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    debts = data.debts;
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
        fetchDebtsAndUpdateTable();
    } else {
        console.log("Skipping fetchDebtsAndUpdateTable() on non-account pages");
    }

});
