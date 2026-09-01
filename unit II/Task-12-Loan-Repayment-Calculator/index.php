<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Repayment Calculator - Task 12</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Loan Repayment & EMI Calculator</h1>
        <p>Calculate your monthly installments and amortization schedule</p>
    </header>

    <div class="container">
        <div class="card">
            <h2>Loan Details</h2>
            <form action="process.php" method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Loan Type</label>
                        <select name="loanType" required>
                            <option value="Home Loan">Home Loan</option>
                            <option value="Personal Loan">Personal Loan</option>
                            <option value="Car Loan">Car Loan</option>
                            <option value="Education Loan">Education Loan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Loan Amount (₹)</label>
                        <input type="number" name="amount" min="1000" step="1000" placeholder="100000" required>
                    </div>
                    <div class="form-group">
                        <label>Annual Interest Rate (%)</label>
                        <input type="number" name="interestRate" min="0.1" step="0.1" placeholder="8.5" required>
                    </div>
                    <div class="form-group">
                        <label>Tenure (Months)</label>
                        <select name="tenure" required>
                            <option value="6">6 Months</option>
                            <option value="12">1 Year (12 Months)</option>
                            <option value="24">2 Years (24 Months)</option>
                            <option value="36">3 Years (36 Months)</option>
                            <option value="60">5 Years (60 Months)</option>
                            <option value="120">10 Years (120 Months)</option>
                            <option value="180">15 Years (180 Months)</option>
                            <option value="240">20 Years (240 Months)</option>
                            <option value="360">30 Years (360 Months)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Processing Fee (%)</label>
                        <input type="number" name="processingFee" min="0" step="0.1" value="1.0" required>
                    </div>
                </div>
                <button type="submit" class="btn">Calculate EMI</button>
            </form>
        </div>
    </div>
</body>
</html>
