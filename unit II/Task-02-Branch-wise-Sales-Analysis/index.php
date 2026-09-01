<?php
$salesData = [
  'Chennai'=>[45000,52000,48000,61000,55000,70000,66000,72000,68000,75000,80000,90000],
  'Coimbatore'=>[38000,41000,44000,49000,53000,58000,55000,62000,60000,65000,70000,75000],
  'Madurai'=>[32000,35000,30000,38000,42000,45000,48000,50000,52000,55000,58000,62000],
  'Trichy'=>[28000,31000,29000,35000,38000,40000,43000,46000,44000,48000,52000,56000],
  'Salem'=>[22000,25000,23000,28000,30000,33000,35000,38000,36000,40000,43000,47000]
];
$months=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

function calculateBranchStats($data) {
    global $months;
    $stats = [];
    foreach ($data as $branch => $sales) {
        $total = array_sum($sales);
        $avg = $total / count($sales);
        $maxSale = max($sales);
        $bestMonthIndex = array_search($maxSale, $sales);
        $bestMonth = $months[$bestMonthIndex];
        
        $stats[] = [
            'name' => $branch,
            'total' => $total,
            'avg' => $avg,
            'best_month' => $bestMonth,
            'best_month_sales' => $maxSale,
            'sales' => $sales
        ];
    }
    return $stats;
}

function rankBranches($stats) {
    usort($stats, fn($a, $b) => $b['total'] <=> $a['total']);
    return $stats;
}

$branchStats = calculateBranchStats($salesData);
$rankedBranches = rankBranches($branchStats);

$totalRevenue = array_sum(array_column($rankedBranches, 'total'));
$bestBranch = $rankedBranches[0];
$maxBranchTotal = $bestBranch['total'];

function getProgressPercent($total, $max) {
    return ($max > 0) ? ($total / $max) * 100 : 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Branch-wise Sales Analysis</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="header">
        <h1>Branch-wise Sales Analysis</h1>
    </header>
    <div class="container">
        <div class="summary-cards">
            <div class="card summary-card">
                <h3>Total Revenue</h3>
                <p>₹<?= number_format($totalRevenue) ?></p>
            </div>
            <div class="card summary-card best-branch-card">
                <h3>Best Performing Branch</h3>
                <p><?= htmlspecialchars($bestBranch['name']) ?> (₹<?= number_format($bestBranch['total']) ?>)</p>
            </div>
        </div>

        <div class="card">
            <h2>Branch Performance</h2>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Branch</th>
                            <th>Total Sales</th>
                            <th>Monthly Average</th>
                            <th>Best Month</th>
                            <th>Performance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($rankedBranches as $index => $branch): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($branch['name']) ?></td>
                                <td>₹<?= number_format($branch['total']) ?></td>
                                <td>₹<?= number_format($branch['avg'], 2) ?></td>
                                <td><?= $branch['best_month'] ?> (₹<?= number_format($branch['best_month_sales']) ?>)</td>
                                <td class="progress-col">
                                    <div class="progress-bar-container">
                                        <div class="progress-bar" style="width: <?= getProgressPercent($branch['total'], $maxBranchTotal) ?>%"></div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
