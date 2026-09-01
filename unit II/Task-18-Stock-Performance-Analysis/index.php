<?php
session_start();

$portfolio=[
  ['symbol'=>'TCS','name'=>'Tata Consultancy Services','sector'=>'IT','buyPrice'=>3200,'currentPrice'=>3850,'quantity'=>10,'buyDate'=>'2024-01-15'],
  ['symbol'=>'INFY','name'=>'Infosys Ltd','sector'=>'IT','buyPrice'=>1450,'currentPrice'=>1680,'quantity'=>25,'buyDate'=>'2024-02-10'],
  ['symbol'=>'RELIANCE','name'=>'Reliance Industries','sector'=>'Energy','buyPrice'=>2400,'currentPrice'=>2750,'quantity'=>8,'buyDate'=>'2024-03-05'],
  ['symbol'=>'HDFC','name'=>'HDFC Bank','sector'=>'Banking','buyPrice'=>1600,'currentPrice'=>1520,'quantity'=>15,'buyDate'=>'2024-01-20'],
  ['symbol'=>'WIPRO','name'=>'Wipro Ltd','sector'=>'IT','buyPrice'=>480,'currentPrice'=>420,'quantity'=>50,'buyDate'=>'2024-04-01'],
  ['symbol'=>'TATASTEEL','name'=>'Tata Steel Ltd','sector'=>'Metal','buyPrice'=>125,'currentPrice'=>148,'quantity'=>100,'buyDate'=>'2024-02-15'],
  ['symbol'=>'BAJAJ','name'=>'Bajaj Finance','sector'=>'Finance','buyPrice'=>6800,'currentPrice'=>7200,'quantity'=>5,'buyDate'=>'2024-03-20'],
  ['symbol'=>'SUNPHARMA','name'=>'Sun Pharmaceutical','sector'=>'Pharma','buyPrice'=>1100,'currentPrice'=>1250,'quantity'=>20,'buyDate'=>'2024-01-10']
];

function calculateStockPerformance(&$stock) {
    $stock['investment'] = $stock['buyPrice'] * $stock['quantity'];
    $stock['currentValue'] = $stock['currentPrice'] * $stock['quantity'];
    $stock['gainLoss'] = $stock['currentValue'] - $stock['investment'];
    $stock['gainPct'] = ($stock['gainLoss'] / $stock['investment']) * 100;
    
    $buyDate = new DateTime($stock['buyDate']);
    $today = new DateTime('now');
    $diff = $today->diff($buyDate);
    $stock['daysHeld'] = max(1, $diff->days);
    
    $stock['annualizedReturn'] = ($stock['gainPct'] / $stock['daysHeld']) * 365;
}

foreach ($portfolio as &$stock) {
    calculateStockPerformance($stock);
}
unset($stock);

$sortBy = $_GET['sort'] ?? 'symbol';
usort($portfolio, function($a, $b) use ($sortBy) {
    if ($sortBy === 'gainpct') return $b['gainPct'] <=> $a['gainPct'];
    if ($sortBy === 'amount') return $b['currentValue'] <=> $a['currentValue'];
    if ($sortBy === 'investment') return $b['investment'] <=> $a['investment'];
    return strcmp($a['symbol'], $b['symbol']);
});

function portfolioSummary($portfolio) {
    $summary = [
        'totalInvestment' => 0,
        'totalValue' => 0,
        'best' => null,
        'worst' => null
    ];
    $maxGain = -INF;
    $minGain = INF;

    foreach ($portfolio as $p) {
        $summary['totalInvestment'] += $p['investment'];
        $summary['totalValue'] += $p['currentValue'];
        
        if ($p['gainPct'] > $maxGain) {
            $maxGain = $p['gainPct'];
            $summary['best'] = $p;
        }
        if ($p['gainPct'] < $minGain) {
            $minGain = $p['gainPct'];
            $summary['worst'] = $p;
        }
    }
    $summary['overallPL'] = $summary['totalValue'] - $summary['totalInvestment'];
    $summary['overallPct'] = ($summary['totalInvestment'] > 0) ? ($summary['overallPL'] / $summary['totalInvestment']) * 100 : 0;
    
    return $summary;
}

function sectorAllocation($portfolio) {
    $sectors = [];
    $totalVal = 0;
    foreach ($portfolio as $p) {
        $sec = $p['sector'];
        if (!isset($sectors[$sec])) $sectors[$sec] = 0;
        $sectors[$sec] += $p['currentValue'];
        $totalVal += $p['currentValue'];
    }
    foreach ($sectors as &$val) {
        $val = ($val / $totalVal) * 100;
    }
    arsort($sectors);
    return $sectors;
}

$summary = portfolioSummary($portfolio);
$allocation = sectorAllocation($portfolio);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Performance Analysis</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <header>
            <h1>Portfolio Dashboard</h1>
        </header>

        <div class="dashboard-cards">
            <div class="card">
                <h3>Total Value</h3>
                <div class="value">₹<?= number_format($summary['totalValue']) ?></div>
                <div class="subtext">Invested: ₹<?= number_format($summary['totalInvestment']) ?></div>
            </div>
            <div class="card">
                <h3>Overall P&L</h3>
                <div class="value <?= $summary['overallPL'] >= 0 ? 'text-green' : 'text-red' ?>">
                    <?= $summary['overallPL'] >= 0 ? '+' : '' ?>₹<?= number_format($summary['overallPL']) ?>
                </div>
                <div class="subtext <?= $summary['overallPct'] >= 0 ? 'text-green' : 'text-red' ?>">
                    <?= number_format($summary['overallPct'], 2) ?>% Returns
                </div>
            </div>
            <div class="card">
                <h3>Best Performer</h3>
                <div class="value text-green"><?= htmlspecialchars($summary['best']['symbol']) ?></div>
                <div class="subtext">+<?= number_format($summary['best']['gainPct'], 2) ?>%</div>
            </div>
            <div class="card">
                <h3>Worst Performer</h3>
                <div class="value text-red"><?= htmlspecialchars($summary['worst']['symbol']) ?></div>
                <div class="subtext"><?= number_format($summary['worst']['gainPct'], 2) ?>%</div>
            </div>
        </div>

        <div class="main-content">
            <div class="portfolio-section">
                <div class="controls">
                    <a href="?sort=symbol" class="btn <?= $sortBy === 'symbol' ? 'active' : '' ?>">Symbol</a>
                    <a href="?sort=gainpct" class="btn <?= $sortBy === 'gainpct' ? 'active' : '' ?>">Gain %</a>
                    <a href="?sort=amount" class="btn <?= $sortBy === 'amount' ? 'active' : '' ?>">Value</a>
                    <a href="?sort=investment" class="btn <?= $sortBy === 'investment' ? 'active' : '' ?>">Investment</a>
                </div>

                <table class="portfolio-table">
                    <thead>
                        <tr>
                            <th>Symbol</th>
                            <th>Qty</th>
                            <th>Avg Buy</th>
                            <th>LTP</th>
                            <th>Invested</th>
                            <th>Value</th>
                            <th>P&L</th>
                            <th>Gain %</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($portfolio as $stock): 
                            $isGain = $stock['gainLoss'] >= 0;
                            $colorClass = $isGain ? 'text-green' : 'text-red';
                        ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($stock['symbol']) ?></strong>
                                <div class="sector-label"><?= htmlspecialchars($stock['sector']) ?></div>
                            </td>
                            <td><?= $stock['quantity'] ?></td>
                            <td>₹<?= number_format($stock['buyPrice']) ?></td>
                            <td>₹<?= number_format($stock['currentPrice']) ?></td>
                            <td>₹<?= number_format($stock['investment']) ?></td>
                            <td>₹<?= number_format($stock['currentValue']) ?></td>
                            <td class="<?= $colorClass ?>"><?= $isGain ? '+' : '' ?>₹<?= number_format($stock['gainLoss']) ?></td>
                            <td>
                                <span class="badge <?= $isGain ? 'badge-green' : 'badge-red' ?>">
                                    <?= $isGain ? '▲' : '▼' ?> <?= number_format(abs($stock['gainPct']), 2) ?>%
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="sidebar">
                <div class="card">
                    <h3>Sector Allocation</h3>
                    <div class="allocation-list">
                        <?php foreach ($allocation as $sec => $pct): ?>
                        <div class="allocation-item">
                            <div class="alloc-header">
                                <span><?= htmlspecialchars($sec) ?></span>
                                <span><?= number_format($pct, 1) ?>%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress" style="width: <?= $pct ?>%"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
