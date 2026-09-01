<?php
session_start();

$salesData=[
  'Smart Phone X1'=>['Jan'=>145000,'Feb'=>132000,'Mar'=>168000,'Apr'=>155000,'May'=>172000,'Jun'=>190000,'Jul'=>185000,'Aug'=>210000,'Sep'=>195000,'Oct'=>220000,'Nov'=>280000,'Dec'=>320000],
  'Laptop Pro 15'=>['Jan'=>285000,'Feb'=>270000,'Mar'=>295000,'Apr'=>310000,'May'=>290000,'Jun'=>315000,'Jul'=>300000,'Aug'=>325000,'Sep'=>310000,'Oct'=>340000,'Nov'=>380000,'Dec'=>420000],
  'Wireless Earbuds'=>['Jan'=>45000,'Feb'=>52000,'Mar'=>48000,'Apr'=>61000,'May'=>58000,'Jun'=>72000,'Jul'=>68000,'Aug'=>85000,'Sep'=>78000,'Oct'=>92000,'Nov'=>115000,'Dec'=>140000]
];
$months=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

function calculateGrowthRate($curr, $prev) {
    if ($prev == 0) return 0;
    return (($curr - $prev) / $prev) * 100;
}

function getQuarterlyTotals($data) {
    return [
        'Q1' => $data['Jan'] + $data['Feb'] + $data['Mar'],
        'Q2' => $data['Apr'] + $data['May'] + $data['Jun'],
        'Q3' => $data['Jul'] + $data['Aug'] + $data['Sep'],
        'Q4' => $data['Oct'] + $data['Nov'] + $data['Dec']
    ];
}

function calculateTrend($data) {
    $first3 = ($data['Jan'] + $data['Feb'] + $data['Mar']) / 3;
    $last3 = ($data['Oct'] + $data['Nov'] + $data['Dec']) / 3;
    return calculateGrowthRate($last3, $first3);
}

$processedData = [];
$totalRevenue = 0;
$bestProduct = '';
$bestProductSales = 0;
$monthTotals = array_fill_keys($months, 0);

foreach ($salesData as $product => $data) {
    $total = array_sum($data);
    $totalRevenue += $total;
    if ($total > $bestProductSales) {
        $bestProductSales = $total;
        $bestProduct = $product;
    }
    
    foreach ($data as $m => $v) {
        $monthTotals[$m] += $v;
    }

    $quarters = getQuarterlyTotals($data);
    $trend = calculateTrend($data);
    
    $mom = [];
    for ($i = 1; $i < 12; $i++) {
        $mom[$months[$i]] = calculateGrowthRate($data[$months[$i]], $data[$months[$i-1]]);
    }

    $processedData[$product] = [
        'data' => $data,
        'total' => $total,
        'avg' => $total / 12,
        'best_month' => array_keys($data, max($data))[0],
        'worst_month' => array_keys($data, min($data))[0],
        'quarters' => $quarters,
        'trend' => $trend,
        'mom' => $mom
    ];
}

$peakMonth = array_keys($monthTotals, max($monthTotals))[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Trend Analysis</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <header>
            <h1>Sales Trend Analysis</h1>
        </header>

        <div class="summary-cards">
            <div class="card">
                <h3>Total Revenue</h3>
                <div class="value">$<?= number_format($totalRevenue) ?></div>
            </div>
            <div class="card">
                <h3>Top Product</h3>
                <div class="value"><?= htmlspecialchars($bestProduct) ?></div>
            </div>
            <div class="card">
                <h3>Peak Month</h3>
                <div class="value"><?= htmlspecialchars($peakMonth) ?></div>
            </div>
        </div>

        <?php foreach ($processedData as $product => $stats): ?>
        <div class="product-section">
            <h2><?= htmlspecialchars($product) ?></h2>
            
            <div class="product-grid">
                <div class="stats-panel">
                    <div class="stat-row"><span>Annual Total:</span> <strong>$<?= number_format($stats['total']) ?></strong></div>
                    <div class="stat-row"><span>Monthly Avg:</span> <strong>$<?= number_format($stats['avg']) ?></strong></div>
                    <div class="stat-row"><span>Best Month:</span> <strong><?= $stats['best_month'] ?></strong></div>
                    <div class="stat-row"><span>Worst Month:</span> <strong><?= $stats['worst_month'] ?></strong></div>
                    <div class="stat-row">
                        <span>Overall Trend:</span> 
                        <strong class="<?= $stats['trend'] >= 0 ? 'text-green' : 'text-red' ?>">
                            <?= $stats['trend'] >= 0 ? '▲' : '▼' ?> <?= number_format(abs($stats['trend']), 1) ?>%
                        </strong>
                    </div>
                </div>
                
                <div class="chart-panel">
                    <div class="bar-chart">
                        <?php 
                        $maxVal = max($stats['data']);
                        foreach ($months as $m): 
                            $val = $stats['data'][$m];
                            $height = ($val / $maxVal) * 100;
                        ?>
                        <div class="bar-group">
                            <div class="bar" style="height: <?= $height ?>%" title="<?= $m ?>: $<?= number_format($val) ?>"></div>
                            <span class="bar-label"><?= $m ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="quarters">
                <?php foreach ($stats['quarters'] as $q => $v): ?>
                    <div class="quarter-card">
                        <h4><?= $q ?></h4>
                        <div>$<?= number_format($v) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <table class="mom-table">
                <thead>
                    <tr>
                        <th>Month</th>
                        <?php foreach ($months as $m) echo "<th>$m</th>"; ?>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Revenue</td>
                        <?php foreach ($months as $m) echo "<td>$" . number_format($stats['data'][$m]) . "</td>"; ?>
                    </tr>
                    <tr>
                        <td>MoM %</td>
                        <td>-</td>
                        <?php for ($i = 1; $i < 12; $i++): 
                            $val = $stats['mom'][$months[$i]];
                            $class = $val >= 0 ? 'text-green' : 'text-red';
                            $arrow = $val >= 0 ? '▲' : '▼';
                        ?>
                            <td class="<?= $class ?>"><?= $arrow ?> <?= number_format(abs($val), 1) ?>%</td>
                        <?php endfor; ?>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
