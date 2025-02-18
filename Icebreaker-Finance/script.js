document.addEventListener("DOMContentLoaded", function () {
    // Get elements
    const debtForm = document.getElementById("debt-form");
    const debtList = document.getElementById("debt-list");
    const totalDebtSpan = document.getElementById("total-debt");
    const debtFormContainer = document.getElementById("debt-form-container"); // NEW: Form container
    let debts = [];

    // NEW: Toggle Form Visibility
    window.toggleDebtForm = function () {
        if (debtFormContainer) {
            console.log("Toggling form visibility"); // Debugging
            debtFormContainer.classList.toggle("hidden");
        } else {
            console.error("Error: debt-form-container not found");
        }
    };


    // Submit form (same as before)
    debtForm.addEventListener("submit", function (event) {
        event.preventDefault();

        // Get debt values and store them
        const debtName = document.getElementById("debt-name").value;
        const debtAmount = parseFloat(document.getElementById("debt-amount").value);
        const minPayment = parseFloat(document.getElementById("min-payment").value);
        const interestRate = parseFloat(document.getElementById("interest-rate").value);

        // Check if values are valid
        if (debtAmount <= 0 || minPayment <= 0 || interestRate < 0) {
            alert("Please enter valid debt details.");
            return;
        }

        // Calculate payoff date
        const payoffDate = calculatePayoffDate(debtAmount, interestRate, minPayment);
        if (payoffDate.startsWith("The monthly payment must")) {
            alert(payoffDate);
            return;
        }

        // Create a new debt object
        const newDebt = {
            name: debtName,
            amount: debtAmount,
            minPayment: minPayment,
            interestRate: interestRate,
            balance: debtAmount,
            payoffDate: payoffDate
        };

        // Add to array
        debts.push(newDebt);

        // Update the table
        updateDebtTable();

        // Reset form
        debtForm.reset();

        // Hide form after submission (optional)
        debtFormContainer.classList.add("hidden");
    });

    // NEW: Function to add debt (called by "GO" button)
    window.addDebt = function () {
        debtForm.dispatchEvent(new Event("submit"));
    };

    // Calculate payoff date (unchanged)
    function calculatePayoffDate(amount, rate, payment) {
        let balance = amount;
        let monthlyRate = (rate / 100) / 12;
        let months = 0;
        const today = new Date();

        if (payment <= balance * monthlyRate) {
            return "The monthly payment must be greater than the first month's interest.";
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

    // Update debt table (unchanged)
    function updateDebtTable() {
        const method = document.getElementById("method").value;

        if (method === "snowball") {
            debts.sort((a, b) => a.amount - b.amount);
        } else if (method === "avalanche") {
            debts.sort((a, b) => b.interestRate - a.interestRate);
        }

        debtList.innerHTML = "";

        debts.forEach((debt, index) => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${debt.name}</td>
                <td>$${debt.amount.toFixed(2)}</td>
                <td>$${debt.minPayment.toFixed(2)}</td>
                <td>${debt.interestRate.toFixed(2)}%</td>
                <td>${debt.payoffDate}</td>
                <td><button onclick="removeDebt(${index})">Delete</button></td>
            `;

            debtList.appendChild(row);
        });

        const totalDebt = debts.reduce((sum, debt) => sum + debt.amount, 0);
        totalDebtSpan.textContent = totalDebt.toFixed(2);
    }

    // Remove debt (unchanged)
    window.removeDebt = function (index) {
        debts.splice(index, 1);
        updateDebtTable();
    };
});
