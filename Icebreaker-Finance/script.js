
        const debtForm = document.getElementById('debt-form');
        const debtList = document.getElementById('debt-list');
        const totalDebtEl = document.getElementById('total-debt');

        let totalDebt = 0;

        // Handle form submission
        debtForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const debtName = document.getElementById('debt-name').value;
            const debtAmount = parseFloat(document.getElementById('debt-amount').value);
            const minPayment = parseFloat(document.getElementById('min-payment').value);
            const interestRate = parseFloat(document.getElementById('interest-rate').value);

            // Add new debt row
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${debtName}</td>
                <td>$${debtAmount.toFixed(2)}</td>
                <td>$${minPayment.toFixed(2)}</td>
                <td>${interestRate.toFixed(2)}%</td>
                <td><button class="delete-btn">Delete</button></td>
            `;
            debtList.appendChild(row);

            // Update total debt
            totalDebt += debtAmount;
            totalDebtEl.textContent = totalDebt.toFixed(2);

            // Clear form inputs
            debtForm.reset();

            // Handle delete action
            row.querySelector('.delete-btn').addEventListener('click', () => {
                totalDebt -= debtAmount;
                totalDebtEl.textContent = totalDebt.toFixed(2);
                row.remove();
            });
        });
    