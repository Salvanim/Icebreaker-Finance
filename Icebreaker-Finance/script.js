
//load debt form
document.addEventListener("DOMContentLoaded", function () {
    //get debt form
    const debtForm = document.getElementById("debt-form");
    //get table
    const debtList = document.getElementById("debt-list");
    //get element that displays debt amount
    const totalDebtSpan = document.getElementById("total-debt");
    //debt array
    let debts = [];

    //submit form and reload page with debt info
    debtForm.addEventListener("submit", function (event) {
        event.preventDefault();
        //get debt values and store them
        const debtName = document.getElementById("debt-name").value;
        const debtAmount = parseFloat(document.getElementById("debt-amount").value);
        const minPayment = parseFloat(document.getElementById("min-payment").value);
        const interestRate = parseFloat(document.getElementById("interest-rate").value);
        //check if values are greater than zero and interest rate is not a negative number
        if (debtAmount <= 0 || minPayment <= 0 || interestRate < 0) {
            alert("Please enter valid debt details.");
            return;
        }
        //calculate payoff date
        const payoffDate = calculatePayoffDate(debtAmount, interestRate, minPayment)
        //check for error when calculating the payoff date
        if (payoffDate.startsWith("The monthly payment must")){
            alert(payoffDate);
            return;
        }
        //new object created with the values entered and the calculated payoff date
        const newDebt = {
            name: debtName,
            amount: debtAmount,
            minPayment: minPayment,
            interestRate: interestRate,
            balance: debtAmount, 
            payoffDate: payoffDate
        };
        //add the new object to the debt array
        debts.push(newDebt);
        //update the table to include the new entry
        updateDebtTable();
        //reset the form so more debts can be added to the table
        debtForm.reset();
    });

    //calculate payoff date based on debt entry
    function calculatePayoffDate(amount, rate, payment) {
        //get remaining balance 
        let balance = amount;
        //convert the interest rate into a monthly interest rate
        let monthlyRate = (rate / 100) / 12;
        //counter for number of months to payoff debt
        let months = 0;
        //date object to show current date
        const today = new Date();

        //check if the payment is enough to cover first months interest - checking for negative amortization
        if(payment <= balance * monthlyRate){
            return "The monthly payment must be greater than the first months interest. Please check your numbers and try again, your debt will not be paid off with the current calculation."
        }
        //while loop that runs until the balance is zero
        while (balance > 0) {
            let interest = balance * monthlyRate;
            balance += interest - payment;
            months++;
            //prevents a negative balance
            if (balance < 0) balance = 0; 
        }
        //date object to calculate payoff date
        let payoffDate = new Date();
        //add the months to payoff to the current month
        payoffDate.setMonth(today.getMonth() + months);
        //output payoff date
        return payoffDate.toLocaleDateString("en-US", { month: "long", day: "numeric", year: "numeric" });
    }

    //update debt table
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

            //add new debt row to table
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
