document.addEventListener("DOMContentLoaded", function () {
    const debtForm = document.getElementById("debt-form");
    const debtList = document.getElementById("debt-list");
    const totalDebtSpan = document.getElementById("total-debt");
    const debtFormContainer = document.getElementById("debt-form-container");
    const debtsContainer = document.querySelector(".debts");

    let debts = [];

    // Toggle Form Visibility
    window.toggleDebtForm = function () {
        debtFormContainer.classList.toggle("hidden");
    };

    // Function to add a debt
    window.addDebt = function () {
        const debtNameInput = document.getElementById("debt-name");
        const methodInput = document.getElementById("method");
        const debtAmountInput = document.getElementById("debt-amount");
        const minPaymentInput = document.getElementById("min-payment");
        const interestRateInput = document.getElementById("interest-rate");

        const debtName = debtNameInput.value.trim();
        const method = methodInput.value;
        const debtAmount = parseFloat(debtAmountInput.value) || 0;
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

                // Immediately update the UI with the new debt
                const newDebt = {
                    debt_id: data.debt_id,
                    debt_name: debtName,
                    debt_type: method,
                    amount_owed: debtAmount,
                    min_payment: minPayment,
                    interest_rate: interestRate
                };

                debts.push(newDebt);
                updateDebtTable();
                updateDebtsSection();

                // Reset the form and hide it
                debtForm.reset();
                debtFormContainer.classList.add("hidden");
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => console.error("Error:", error));
    };

    // Fetch debts on page load and update UI
    function fetchDebtsAndUpdateSection() {
        fetch("get-debts.php")
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    debts = data.debts;  // Update global debts array
                    updateDebtsSection();
                    updateDebtTable();
                } else {
                    console.error("Error fetching debts:", data.message);
                }
            })
            .catch(error => console.error("Error:", error));
    }

    // Update Debt List on the Left Section
    function updateDebtsSection() {
        debtsContainer.innerHTML = `<h2 class="debt-account">Debts</h2>`;

        if (debts.length === 0) {
            debtsContainer.innerHTML += `<p class="text-muted">No debts added yet.</p>`;
        } else {
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

        // Add "All" button at the end
        const allDebtsButton = document.createElement("div");
        allDebtsButton.classList.add("debt-item");
        allDebtsButton.innerHTML = `<button onclick="window.location.href='all-debts.php'">All</button>`;
        debtsContainer.appendChild(allDebtsButton);
    }

    function updateDebtTable() {
        console.log("Debts Array:", debts); // Debugging
        const debtList = document.getElementById("debt-list");
        if (!debtList) {
            console.error("Error: debt-list element not found");
            return;
        }
    
        debtList.innerHTML = "";
    
        if (debts.length === 0) {
            debtList.innerHTML = "<tr><td colspan='6' class='text-muted'>No debts added yet.</td></tr>";
            document.getElementById("total-debt").textContent = "$0.00";
            return;
        }
    
        debts.forEach(debt => {
            console.log("Debt Object:", debt); // Debugging each debt object
    
            const amountOwed = Number(debt.amount_owed) || 0;
            const minPayment = Number(debt.min_payment) || 0;
            const interestRate = Number(debt.interest_rate) || 0;
    
            const row = document.createElement("tr");
            row.id = `debt-row-${debt.debt_id}`;
            row.innerHTML = `
                <td>${debt.debt_name}</td>
                <td>${debt.debt_type}</td>
                <td>$${amountOwed.toFixed(2)}</td>
                <td>$${minPayment.toFixed(2)}</td>
                <td>${interestRate.toFixed(2)}%</td>
                <td>
                    <button class="delete-btn" onclick="deleteDebt(${debt.debt_id})">Delete</button>
                </td>
            `;
    
            debtList.appendChild(row);
        });
    
        // Update total debt
        const totalDebt = debts.reduce((sum, debt) => sum + (Number(debt.amount_owed) || 0), 0);
        document.getElementById("total-debt").textContent = `$${totalDebt.toFixed(2)}`;
    }
    
    

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

                // Remove the deleted row from the table
                const row = document.querySelector(`#debt-row-${debtId}`);
                if (row) row.remove();

                // Remove from the debt list
                const debtItem = document.querySelector(`#debt-item-${debtId}`);
                if (debtItem) debtItem.remove();

                // Remove from the array and update UI
                debts = debts.filter(debt => debt.debt_id !== debtId);
                updateDebtTable();
                updateDebtsSection();
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => console.error("Error:", error));
    };

    // Fetch debts on page load
    fetchDebtsAndUpdateSection();
});
