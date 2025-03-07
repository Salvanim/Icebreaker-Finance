

document.addEventListener("DOMContentLoaded", function () {
    const debtForm = document.getElementById("debt-form");
    const debtList = document.getElementById("debt-list");
    const totalDebtSpan = document.getElementById("total-debt") || { textContent: "" };
    if (totalDebtSpan) {
        totalDebtSpan.textContent = "$0.00";
    } else {
        console.error("Error: `total-debt` element not found in the DOM.");
    }
    const debtFormContainer = document.getElementById("debt-form-container");
    const debtsContainer = document.querySelector(".debts") || document.getElementById("debt-list");

    console.log(debtFormContainer);


    let debts = [];
    //function to hide/show the debt calculator form
    window.toggleDebtForm = function () {
        console.log("Toggle button clicked!");
    
        const debtFormContainer = document.getElementById("debt-form-container");
    
        if (!debtFormContainer) {
            console.error("Error: `debt-form-container` not found.");
            return;
        }
    
        console.log("Toggling form visibility...");
        debtFormContainer.classList.toggle("hidden");
    
        console.log("Form class list:", debtFormContainer.classList);
    };
    
    

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
        const amountOwed = parseFloat(debt.amount_owed) || 0;
        const minPayment = parseFloat(minPaymentInput.value) || 0;
        const interestRate = parseFloat(interestRateInput.value) || 0;

        if (!debtName || !method || debtAmount <= 0 || minPayment <= 0 || interestRate < 0) {
            alert("Please enter valid debt details.");
            return;
        }

        const formData = new URLSearchParams();
        formData.append("debt_name", debtName);
        formData.append("debt_type", method);
        formData.append("debt_amount", debtAmount);
        formData.append("min_payment", minPayment);
        formData.append("interest_rate", interestRate);

        fetch("add-debt.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: formData.toString(),
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
                    interest_rate: interestRate
                });
                updateDebtTable();
                updateDebtsSection();
                debtForm.reset();
                debtFormContainer.classList.add("hidden");
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => console.error("Error:", error));
    };
    //function to add payments to individual debts
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

    //function to update the debt table after changes have been made
    function updateDebtTable() {
        console.log("✅ updateDebtTable() function triggered!");
        
        const debtsContainer = document.getElementById("debt-list");
        if (!debtsContainer) {
            console.error("❌ Error: debtsContainer element not found.");
            return;
        }
    
        console.log("Current debts array:", debts); // Debugging the debts array
    
        debtsContainer.innerHTML = ""; // Clear previous rows
    
        if (debts.length === 0) {
            console.log("✅ No debts found. Showing empty table message.");
            debtsContainer.innerHTML = "<tr><td colspan='6' class='text-muted'>No debts added yet.</td></tr>";
            totalDebtSpan.textContent = "$0.00";
            return;
        }
    
        debts.forEach(debt => {
            console.log("Processing debt:", debt); // Debugging each debt object
    
            const amountOwed = parseFloat(debt.amount_owed) || 0;
            const minPayment = parseFloat(debt.min_payment) || 0;
            const interestRate = parseFloat(debt.interest_rate) || 0;
    
            const row = document.createElement("tr");
            row.id = `debt-row-${debt.debt_id}`;
            row.innerHTML = `
                <td>${debt.debt_name}</td>
                <td>${debt.debt_type}</td>
                <td>$${amountOwed.toFixed(2)}</td>
                <td>$${minPayment.toFixed(2)}</td>
                <td>${interestRate.toFixed(2)}%</td>
                <td>
                    <button class="btn btn-danger btn-sm delete-btn" onclick="deleteDebt(${debt.debt_id})">Delete</button>
                </td>
            `;
    
            debtsContainer.appendChild(row);
        });
    
        console.log(" Debt table updated successfully!");
    }
    


        // Update total debt
        const totalDebt = debts.reduce((sum, debt) => sum + (Number(debt.amount_owed) || 0), 0);
        document.getElementById("total-debt").textContent = `$${totalDebt.toFixed(2)}`;
    



    // Function to delete a debt
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
                updateDebtsSection();
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => console.error("Error:", error));
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
                location.href = "account.php"; // Redirect to main debts page
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => console.error("Error:", error));
    };



    function updateDebtsSection() {
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

    if (window.location.pathname.includes("account.php")) {
        fetchDebtsAndUpdateSection(); // make sure this runs on account page only, caused issues on edit-debt page
    } else {
        console.log("Skipping fetchDebtsAndUpdateSection() on edit-debt.php");
    }
});
