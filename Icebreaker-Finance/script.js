document.addEventListener("DOMContentLoaded", function () {
    const debtForm = document.getElementById("debt-form");
    const debtList = document.getElementById("debt-list");
    const totalDebtSpan = document.getElementById("total-debt");

    let debts = [];

    debtForm.addEventListener("submit", function (event) {
        event.preventDefault();

        const debtName = document.getElementById("debt-name").value;
        const debtAmount = parseFloat(document.getElementById("debt-amount").value);
        const minPayment = parseFloat(document.getElementById("min-payment").value);
        const interestRate = parseFloat(document.getElementById("interest-rate").value);

        if (debtAmount <= 0 || minPayment <= 0 || interestRate < 0) {
            alert("Please enter valid debt details.");
            return;
        }

        const newDebt = {
            name: debtName,
            amount: debtAmount,
            minPayment: minPayment,
            interestRate: interestRate,
            balance: debtAmount, 
            payoffDate: calculatePayoffDate(debtAmount, interestRate, minPayment)
        };

        debts.push(newDebt);
        updateDebtTable();
        debtForm.reset();
    });

    function calculatePayoffDate(amount, rate, payment) {
        let balance = amount;
        let monthlyRate = (rate / 100) / 12;
        let months = 0;
        const today = new Date();

        while (balance > 0) {
            let interest = balance * monthlyRate;
            balance += interest - payment;
            months++;

            if (balance < 0) balance = 0; // Prevent negative balance
        }

        let payoffDate = new Date();
        payoffDate.setMonth(today.getMonth() + months);
        return payoffDate.toLocaleDateString("en-US", { month: "long", day: "numeric", year: "numeric" });
    }

    function updateDebtTable() {
       
        //Get selected debt method
        const method = document.getElementById("method").value;

        //Sort debts by method
        if (method === "snowball") {
            debts.sort((a, b) => a.amount - b.amount);
        } else if (method === "avalanche")  {
            debts.sort((a,b) => b.interestRate - a.interestRate);
        }
        
        //clear and repopulate table
        debtList.innerHTML = "";

        //Add sorted debts to table
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
        //Update total debt 
        const totalDebt = debts.reduce((sum, debt) => sum + debt.amount, 0);
        totalDebtSpan.textContent = totalDebt.toFixed(2);
    }
    //remove debt
    window.removeDebt = function (index) {
        debts.splice(index, 1);
        updateDebtTable();
    };
});
