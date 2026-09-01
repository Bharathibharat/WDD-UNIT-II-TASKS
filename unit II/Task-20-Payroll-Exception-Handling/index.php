<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll Exception Handling</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="corp-header">
        <div class="container">
            <h1>Enterprise Payroll System</h1>
        </div>
    </header>

    <div class="container layout">
        <div class="form-section">
            <div class="card">
                <h2>Process Employee Payroll</h2>
                <form action="process.php" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Employee ID</label>
                            <input type="text" name="empId" placeholder="EMP1234" required>
                        </div>
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Department</label>
                            <select name="dept" required>
                                <option value="IT">IT</option>
                                <option value="HR">HR</option>
                                <option value="Finance">Finance</option>
                                <option value="Operations">Operations</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Designation</label>
                            <input type="text" name="designation" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Basic Salary (₹)</label>
                            <input type="number" step="0.01" name="basicSalary" required>
                        </div>
                        <div class="form-group">
                            <label>Hours Worked</label>
                            <input type="number" step="0.1" name="hoursWorked" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Overtime Hours</label>
                            <input type="number" step="0.1" name="overtime" required>
                        </div>
                        <div class="form-group">
                            <label>Leaves Taken</label>
                            <input type="number" name="leaves" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Advance Taken (₹)</label>
                        <input type="number" step="0.01" name="advance" value="0">
                    </div>

                    <button type="submit" class="btn-primary">Generate Payslip</button>
                </form>
            </div>
        </div>

        <div class="result-section">
            <?php if (isset($_SESSION['payslip'])): $p = $_SESSION['payslip']; ?>
                <div class="card payslip">
                    <div class="payslip-header">
                        <h2>Payslip</h2>
                        <span class="emp-id"><?= htmlspecialchars($p['empId']) ?></span>
                    </div>
                    <div class="emp-details">
                        <p><strong>Name:</strong> <?= htmlspecialchars($p['name']) ?></p>
                        <p><strong>Dept:</strong> <?= htmlspecialchars($p['dept']) ?> | <strong>Role:</strong> <?= htmlspecialchars($p['designation']) ?></p>
                    </div>
                    
                    <div class="salary-breakdown">
                        <div class="earnings">
                            <h3>Earnings</h3>
                            <div class="row"><span>Basic Salary</span><span>₹<?= number_format($p['basic'], 2) ?></span></div>
                            <div class="row"><span>HRA (40%)</span><span>₹<?= number_format($p['hra'], 2) ?></span></div>
                            <div class="row"><span>DA (15%)</span><span>₹<?= number_format($p['da'], 2) ?></span></div>
                            <div class="row"><span>TA</span><span>₹<?= number_format($p['ta'], 2) ?></span></div>
                            <div class="row"><span>Medical</span><span>₹<?= number_format($p['medical'], 2) ?></span></div>
                            <div class="row"><span>Overtime</span><span>₹<?= number_format($p['overtimePay'], 2) ?></span></div>
                            <div class="row total"><span>Gross Earnings</span><span>₹<?= number_format($p['gross'], 2) ?></span></div>
                        </div>
                        <div class="deductions">
                            <h3>Deductions</h3>
                            <div class="row"><span>PF (12%)</span><span>₹<?= number_format($p['pf'], 2) ?></span></div>
                            <div class="row"><span>Professional Tax</span><span>₹<?= number_format($p['profTax'], 2) ?></span></div>
                            <div class="row"><span>Leave Deduction</span><span>₹<?= number_format($p['leaveDed'], 2) ?></span></div>
                            <div class="row"><span>Advance Recovery</span><span>₹<?= number_format($p['advance'], 2) ?></span></div>
                            <div class="row total"><span>Total Deductions</span><span>₹<?= number_format($p['totDeductions'], 2) ?></span></div>
                        </div>
                    </div>
                    <div class="net-pay">
                        <h3>Net Salary</h3>
                        <div class="amount">₹<?= number_format($p['net'], 2) ?></div>
                    </div>
                </div>
                <?php unset($_SESSION['payslip']); ?>
            <?php elseif (isset($_SESSION['error'])): ?>
                <div class="card error-card">
                    <div class="icon">⚠️</div>
                    <h3>Payroll Processing Error</h3>
                    <p class="error-msg"><?= htmlspecialchars($_SESSION['error']) ?></p>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php else: ?>
                <div class="card empty-state">
                    <p>Enter employee details and submit to generate payslip.</p>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['payroll_log'])): ?>
                <div class="card mt-20">
                    <h3>Recent Processing Log</h3>
                    <table class="log-table">
                        <thead>
                            <tr>
                                <th>Emp ID</th>
                                <th>Status</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_reverse($_SESSION['payroll_log']) as $log): ?>
                                <tr>
                                    <td><?= htmlspecialchars($log['empId']) ?></td>
                                    <td><span class="badge <?= $log['status'] === 'Success' ? 'bg-success' : 'bg-danger' ?>"><?= htmlspecialchars($log['status']) ?></span></td>
                                    <td><?= htmlspecialchars($log['time']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
