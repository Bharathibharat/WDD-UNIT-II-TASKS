<?php
session_start();
if (!isset($_SESSION['accounts'])) {
    $_SESSION['accounts'] = [
      'ACC001' => ['name' => 'Arun Kumar', 'balance' => 25000.00, 'type' => 'Savings', 'status' => 'Active'],
      'ACC002' => ['name' => 'Priya Devi', 'balance' => 150000.00, 'type' => 'Current', 'status' => 'Active'],
      'ACC003' => ['name' => 'Ravi Shankar', 'balance' => 5000.00, 'type' => 'Savings', 'status' => 'Frozen']
    ];
}
if (!isset($_SESSION['transactions'])) {
    $_SESSION['transactions'] = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banking Exception Handling</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <header class="bank-header">
        <div class="container">
            <h1>Global Trust Bank</h1>
            <p>Secure Transaction Portal</p>
        </div>
    </header>

    <div class="container main-layout">
        <div class="left-panel">
            <div class="accounts-list">
                <h2>Account Overview</h2>
                <?php foreach ($_SESSION['accounts'] as $accId => $acc): ?>
                    <div class="account-card <?= strtolower($acc['status']) ?>">
                        <div class="acc-header">
                            <span class="acc-id"><?= htmlspecialchars($accId) ?></span>
                            <span class="acc-status"><?= htmlspecialchars($acc['status']) ?></span>
                        </div>
                        <h3><?= htmlspecialchars($acc['name']) ?></h3>
                        <p class="acc-type"><?= htmlspecialchars($acc['type']) ?> Account</p>
                        <div class="acc-bal">₹<?= number_format($acc['balance'], 2) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="right-panel">
            <div class="transaction-form-box">
                <h2>Perform Transaction</h2>
                <form action="process.php" method="POST">
                    <div class="form-group">
                        <label>Transaction Type</label>
                        <select name="type" id="txnType" required onchange="toggleTarget()">
                            <option value="Deposit">Deposit</option>
                            <option value="Withdrawal">Withdrawal</option>
                            <option value="Transfer">Transfer</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Source Account ID</label>
                        <input type="text" name="source" placeholder="e.g. ACC001" required>
                    </div>

                    <div class="form-group" id="targetDiv" style="display: none;">
                        <label>Target Account ID (For Transfer)</label>
                        <input type="text" name="target" placeholder="e.g. ACC002">
                    </div>

                    <div class="form-group">
                        <label>Amount (₹)</label>
                        <input type="number" step="0.01" name="amount" required>
                    </div>

                    <button type="submit" class="btn-submit">Process Transaction</button>
                </form>
            </div>

            <?php if (isset($_SESSION['msg'])): ?>
                <div class="msg-box <?= $_SESSION['msg_type'] ?>">
                    <?= htmlspecialchars($_SESSION['msg']) ?>
                    <?php if (isset($_SESSION['msg_detail'])): ?>
                        <div class="detail"><?= htmlspecialchars($_SESSION['msg_detail']) ?></div>
                    <?php endif; ?>
                </div>
                <?php 
                unset($_SESSION['msg']); 
                unset($_SESSION['msg_type']); 
                unset($_SESSION['msg_detail']); 
                ?>
            <?php endif; ?>

            <div class="transaction-history">
                <h2>Recent Transactions</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Type</th>
                            <th>Source</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_reverse($_SESSION['transactions']) as $txn): ?>
                        <tr>
                            <td><?= htmlspecialchars($txn['time']) ?></td>
                            <td><?= htmlspecialchars($txn['type']) ?></td>
                            <td><?= htmlspecialchars($txn['source']) ?></td>
                            <td>₹<?= number_format($txn['amount'], 2) ?></td>
                            <td><span class="status-badge <?= $txn['status'] === 'Success' ? 'success' : 'failed' ?>"><?= htmlspecialchars($txn['status']) ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($_SESSION['transactions'])): ?>
                        <tr><td colspan="5" style="text-align: center;">No transactions yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        function toggleTarget() {
            var type = document.getElementById('txnType').value;
            document.getElementById('targetDiv').style.display = type === 'Transfer' ? 'block' : 'none';
        }
    </script>
</body>
</html>
