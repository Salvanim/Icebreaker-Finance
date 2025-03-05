document.addEventListener("DOMContentLoaded", function () {
    const debtForm = document.getElementById("debt-form");
    const debtList = document.getElementById("debt-list");
    const totalDebtSpan = document.getElementById("total-debt");
    const debtFormContainer = document.getElementById("debt-form-container");
    const debtsContainer = document.querySelector(".debts");
    console.log(debtsContainer);

    
    let debts = [];
    
    window.toggleDebtForm = function () {
        debtFormContainer.classList.toggle("hidden");
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
    
    function updateDebtTable() {
        debtList.innerHTML = "";
        if (debts.length === 0) {
            debtList.innerHTML = "<tr><td colspan='6' class='text-muted'>No debts added yet.</td></tr>";
            totalDebtSpan.textContent = "$0.00";
            return;
        }
        
        debts.forEach(debt => {
            const row = document.createElement("tr");
            row.id = `debt-row-${debt.debt_id}`;
            row.innerHTML = `
                <td>${debt.debt_name}</td>
                <td>${debt.debt_type}</td>
                <td>$${debt.amount_owed.toFixed(2)}</td>
                <td>$${debt.min_payment.toFixed(2)}</td>
                <td>${debt.interest_rate.toFixed(2)}%</td>
                <td>
                    <button class="delete-btn" onclick="deleteDebt(${debt.debt_id})">Delete</button>
                </td>
            `;
            debtList.appendChild(row);
        });
        
        const totalDebt = debts.reduce((sum, debt) => sum + debt.amount_owed, 0);
        totalDebtSpan.textContent = `$${totalDebt.toFixed(2)}`;
    }
    
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
    
    fetchDebtsAndUpdateSection();
});
