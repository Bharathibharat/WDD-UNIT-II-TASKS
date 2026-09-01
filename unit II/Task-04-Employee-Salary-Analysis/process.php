<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$name = $_POST['name'];
$empId = $_POST['empId'];
$dept = $_POST['department'];
$designation = $_POST['designation'];
$basic = (float)$_POST['basic'];
$experience = (int)$_POST['experience'];

function formatCurrency($amt) {
    return '₹' . number_format(round($amt), 2);
}

function calculateHRA($basic) {
    return $basic <= 20000 ? $basic * 0.40 : $basic * 0.30;
}

function calculateIncomeTax($gross) {
    if ($gross > 50000) {
        return ($gross - 50000) * 0.10;
    }
    return 0;
}

// Earnings
$hra = calculateHRA($basic);
$da = $basic * 0.15;
$ta = 1500;
$medical = 1250;
$special = $basic * 0.05;
$increment = $experience * 500;

$gross = $basic + $hra + $da + $ta + $medical + $special + $increment;

// Deductions
$pf = $basic * 0.12;
$profTax = $basic <= 20000 ? 200 : 400;
$incomeTax = calculateIncomeTax($gross);

$totalDeductions = $pf + $profTax + $incomeTax;

// Net
$net = $gross - $totalDeductions;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip - <?= htmlspecialchars($name) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="header no-print">
        <h1>Employee Payslip</h1>
    </header>
    <div class="container">
        <div class="card payslip-card">
            <div class="payslip-header">
                <h2>Company XYZ</h2>
                <p>Payslip for the current month</p>
            </div>
            
            <div class="employee-details">
                <p><strong>Name:</strong> <?= htmlspecialchars($name) ?></p>
                <p><strong>Employee ID:</strong> <?= htmlspecialchars($empId) ?></p>
                <p><strong>Department:</strong> <?= htmlspecialchars($dept) ?></p>
                <p><strong>Designation:</strong> <?= htmlspecialchars($designation) ?></p>
                <p><strong>Experience:</strong> <?= $experience ?> Years</p>
            </div>

            <div class="salary-split">
                <div class="split-section">
                    <h3>Earnings</h3>
                    <table class="table">
                        <tr><td>Basic Salary</td><td><?= formatCurrency($basic) ?></td></tr>
                        <tr><td>HRA</td><td><?= formatCurrency($hra) ?></td></tr>
                        <tr><td>DA</td><td><?= formatCurrency($da) ?></td></tr>
                        <tr><td>TA</td><td><?= formatCurrency($ta) ?></td></tr>
                        <tr><td>Medical Allowance</td><td><?= formatCurrency($medical) ?></td></tr>
                        <tr><td>Special Allowance</td><td><?= formatCurrency($special) ?></td></tr>
                        <tr><td>Experience Increment</td><td><?= formatCurrency($increment) ?></td></tr>
                        <tr class="total-row"><td>Gross Earnings</td><td><?= formatCurrency($gross) ?></td></tr>
                    </table>
                </div>
                
                <div class="split-section">
                    <h3>Deductions</h3>
                    <table class="table">
                        <tr><td>PF (12%)</td><td><?= formatCurrency($pf) ?></td></tr>
                        <tr><td>Professional Tax</td><td><?= formatCurrency($profTax) ?></td></tr>
                        <tr><td>Income Tax</td><td><?= formatCurrency($incomeTax) ?></td></tr>
                        <tr class="total-row"><td>Total Deductions</td><td><?= formatCurrency($totalDeductions) ?></td></tr>
                    </table>
                </div>
            </div>

            <div class="net-summary">
                <h3>Net Salary Payable: <span><?= formatCurrency($net) ?></span></h3>
            </div>
            
            <div class="no-print" style="text-align:center; margin-top: 2rem;">
                <button onclick="window.print()" class="btn btn-primary">Print Payslip</button>
                <a href="index.php" class="btn btn-secondary" style="margin-left: 10px;">Go Back</a>
            </div>
        </div>
    </div>
</body>
</html>
