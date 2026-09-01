<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Calculation Result</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Calculation Results</h1>
        <p><a href="index.php" style="color:white;">&larr; New Calculation</a></p>
    </header>

    <div class="container">
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $type = $_POST['loanType'];
            $P = (float)$_POST['amount'];
            $annualRate = (float)$_POST['interestRate'];
            $n = (int)$_POST['tenure'];
            $feePercent = (float)$_POST['processingFee'];

            function calculateEMI($P, $annualRate, $n) {
                if ($annualRate == 0) return $P / $n;
                $r = $annualRate / 12 / 100; // monthly interest rate
                return $P * $r * pow(1 + $r, $n) / (pow(1 + $r, $n) - 1);
            }

            function generateAmortization($P, $annualRate, $emi, $n) {
                $r = $annualRate / 12 / 100;
                $balance = $P;
                $schedule = [];
                // Generate up to 12 months for brevity
                $limit = min($n, 12);
                for ($i = 1; $i <= $limit; $i++) {
                    $interest = $balance * $r;
                    $principal = $emi - $interest;
                    $balance -= $principal;
                    if ($balance < 0) $balance = 0;
                    
                    $schedule[] = [
                        'month' => $i,
                        'emi' => $emi,
                        'principal' => $principal,
                        'interest' => $interest,
                        'balance' => $balance
                    ];
                }
                return $schedule;
            }

            $emi = calculateEMI($P, $annualRate, $n);
            $totalPayment = $emi * $n;
            $totalInterest = $totalPayment - $P;
            $feeAmount = $P * ($feePercent / 100);
            
            $principalPercent = ($P / $totalPayment) * 100;
            $interestPercent = ($totalInterest / $totalPayment) * 100;

            $schedule = generateAmortization($P, $annualRate, $emi, $n);
            ?>

            <div class="card">
                <h2><?= htmlspecialchars($type) ?> Summary</h2>
                <div class="summary-cards">
                    <div class="summary-card">
                        <p>Monthly EMI</p>
                        <h3>₹ <?= number_format($emi, 2) ?></h3>
                    </div>
                    <div class="summary-card">
                        <p>Total Interest</p>
                        <h3>₹ <?= number_format($totalInterest, 2) ?></h3>
                    </div>
                    <div class="summary-card">
                        <p>Total Payment</p>
                        <h3>₹ <?= number_format($totalPayment, 2) ?></h3>
                    </div>
                </div>
                <div style="text-align:center; margin-bottom:1rem;">
                    <strong>Processing Fee:</strong> ₹ <?= number_format($feeAmount, 2) ?> (<?= $feePercent ?>%)
                </div>

                <div class="visual-bar">
                    <div class="bar-principal" style="width: <?= $principalPercent ?>%;"></div>
                    <div class="bar-interest" style="width: <?= $interestPercent ?>%;"></div>
                </div>
                <div class="legend">
                    <div class="legend-item"><div class="dot bar-principal"></div> Principal (<?= number_format($principalPercent, 1) ?>%)</div>
                    <div class="legend-item"><div class="dot bar-interest"></div> Interest (<?= number_format($interestPercent, 1) ?>%)</div>
                </div>
            </div>

            <div class="card">
                <h2>Amortization Schedule (First <?= count($schedule) ?> Months)</h2>
                <div style="overflow-x:auto;">
                    <table class="amortization-table">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>EMI (₹)</th>
                                <th>Principal (₹)</th>
                                <th>Interest (₹)</th>
                                <th>Balance (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($schedule as $row): ?>
                            <tr>
                                <td><?= $row['month'] ?></td>
                                <td><?= number_format($row['emi'], 2) ?></td>
                                <td><?= number_format($row['principal'], 2) ?></td>
                                <td><?= number_format($row['interest'], 2) ?></td>
                                <td><?= number_format($row['balance'], 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php
        }
        ?>
    </div>
</body>
</html>
