<?php
$campaigns=[
  ['id'=>'C001','name'=>'Summer Sale 2024','platform'=>'Google Ads','type'=>'Search','budget'=>50000,'spent'=>48500,'impressions'=>125000,'clicks'=>3750,'conversions'=>188,'revenue'=>94000,'startDate'=>'2024-06-01','endDate'=>'2024-06-30'],
  ['id'=>'C002','name'=>'Back to School','platform'=>'Facebook','type'=>'Display','budget'=>35000,'spent'=>35000,'impressions'=>280000,'clicks'=>5600,'conversions'=>112,'revenue'=>56000,'startDate'=>'2024-07-15','endDate'=>'2024-08-15'],
  ['id'=>'C003','name'=>'Festive Offer','platform'=>'Instagram','type'=>'Social','budget'=>45000,'spent'=>43200,'impressions'=>180000,'clicks'=>9000,'conversions'=>270,'revenue'=>135000,'startDate'=>'2024-10-01','endDate'=>'2024-10-31'],
  ['id'=>'C004','name'=>'New Year Deal','platform'=>'Google Ads','type'=>'Search','budget'=>60000,'spent'=>59800,'impressions'=>95000,'clicks'=>2850,'conversions'=>143,'revenue'=>71500,'startDate'=>'2024-12-26','endDate'=>'2025-01-05'],
  ['id'=>'C005','name'=>'Brand Awareness Q1','platform'=>'YouTube','type'=>'Video','budget'=>80000,'spent'=>80000,'impressions'=>500000,'clicks'=>2500,'conversions'=>50,'revenue'=>25000,'startDate'=>'2024-01-01','endDate'=>'2024-03-31'],
  ['id'=>'C006','name'=>'Product Launch X','platform'=>'LinkedIn','type'=>'B2B','budget'=>70000,'spent'=>68000,'impressions'=>45000,'clicks'=>2250,'conversions'=>225,'revenue'=>337500,'startDate'=>'2024-09-01','endDate'=>'2024-09-30'],
  ['id'=>'C007','name'=>'Flash Sale Friday','platform'=>'Facebook','type'=>'Retargeting','budget'=>20000,'spent'=>19500,'impressions'=>60000,'clicks'=>4200,'conversions'=>336,'revenue'=>168000,'startDate'=>'2024-11-29','endDate'=>'2024-11-30'],
  ['id'=>'C008','name'=>'Email Campaign','platform'=>'Email','type'=>'Email','budget'=>5000,'spent'=>4800,'impressions'=>50000,'clicks'=>3500,'conversions'=>175,'revenue'=>87500,'startDate'=>'2024-08-01','endDate'=>'2024-08-31'],
  ['id'=>'C009','name'=>'Influencer Collab','platform'=>'Instagram','type'=>'Influencer','budget'=>100000,'spent'=>100000,'impressions'=>800000,'clicks'=>24000,'conversions'=>480,'revenue'=>240000,'startDate'=>'2024-05-01','endDate'=>'2024-05-31'],
  ['id'=>'C010','name'=>'Search Retargeting','platform'=>'Google Ads','type'=>'Retargeting','budget'=>30000,'spent'=>28500,'impressions'=>40000,'clicks'=>3200,'conversions'=>256,'revenue'=>128000,'startDate'=>'2024-04-01','endDate'=>'2024-04-30']
];

function calculateCampaignMetrics(&$campaign) {
    $campaign['ctr'] = ($campaign['clicks'] / $campaign['impressions']) * 100;
    $campaign['cvr'] = ($campaign['conversions'] / $campaign['clicks']) * 100;
    $campaign['cpc'] = $campaign['spent'] / $campaign['clicks'];
    $campaign['cpl'] = $campaign['spent'] / $campaign['conversions'];
    $campaign['roas'] = $campaign['revenue'] / $campaign['spent'];
    $campaign['roi'] = (($campaign['revenue'] - $campaign['spent']) / $campaign['spent']) * 100;
    $campaign['budget_utilization'] = ($campaign['spent'] / $campaign['budget']) * 100;
}

foreach($campaigns as &$c) {
    calculateCampaignMetrics($c);
}
unset($c);

function portfolioSummary($campaigns) {
    $totalBudget = array_sum(array_column($campaigns, 'budget'));
    $totalSpent = array_sum(array_column($campaigns, 'spent'));
    $totalRevenue = array_sum(array_column($campaigns, 'revenue'));
    $overallRoi = (($totalRevenue - $totalSpent) / $totalSpent) * 100;
    return [
        'budget' => $totalBudget,
        'spent' => $totalSpent,
        'revenue' => $totalRevenue,
        'roi' => $overallRoi
    ];
}

function platformAnalysis($campaigns) {
    $platforms = [];
    foreach($campaigns as $c) {
        $p = $c['platform'];
        if(!isset($platforms[$p])) {
            $platforms[$p] = ['spent'=>0, 'revenue'=>0, 'conversions'=>0, 'clicks'=>0];
        }
        $platforms[$p]['spent'] += $c['spent'];
        $platforms[$p]['revenue'] += $c['revenue'];
        $platforms[$p]['conversions'] += $c['conversions'];
        $platforms[$p]['clicks'] += $c['clicks'];
    }
    foreach($platforms as &$plat) {
        $plat['roi'] = (($plat['revenue'] - $plat['spent']) / $plat['spent']) * 100;
        $plat['cpl'] = $plat['spent'] / max(1, $plat['conversions']);
    }
    return $platforms;
}

$summary = portfolioSummary($campaigns);
$platforms = platformAnalysis($campaigns);

// Sorting
$sort = $_GET['sort'] ?? 'roi';
usort($campaigns, function($a, $b) use ($sort) {
    return $b[$sort] <=> $a[$sort];
});

// Best Performers
$bestRoi = $campaigns[0];
usort($campaigns, fn($a, $b) => $b['ctr'] <=> $a['ctr']);
$bestCtr = $campaigns[0];
usort($campaigns, fn($a, $b) => $b['roas'] <=> $a['roas']);
$bestRoas = $campaigns[0];

// Restore requested sort
usort($campaigns, function($a, $b) use ($sort) {
    return $b[$sort] <=> $a[$sort];
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Digital Marketing Analysis</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Marketing Campaign Analytics</h1>
    </header>
    
    <div class="container">
        <div class="kpi-grid">
            <div class="kpi-card">
                <h3>Total Spend</h3>
                <div class="kpi-value">$<?= number_format($summary['spent']) ?></div>
            </div>
            <div class="kpi-card">
                <h3>Total Revenue</h3>
                <div class="kpi-value">$<?= number_format($summary['revenue']) ?></div>
            </div>
            <div class="kpi-card">
                <h3>Overall ROI</h3>
                <div class="kpi-value" style="color: <?= $summary['roi'] > 0 ? '#4caf50' : '#f44336' ?>">
                    <?= number_format($summary['roi'], 1) ?>%
                </div>
            </div>
            <div class="kpi-card">
                <h3>Best Campaign (ROI)</h3>
                <div class="kpi-value" style="font-size: 24px;"><?= htmlspecialchars($bestRoi['name']) ?></div>
            </div>
        </div>

        <div class="sort-panel">
            <strong>Sort By: </strong>
            <a href="?sort=roi" class="btn">ROI</a>
            <a href="?sort=roas" class="btn">ROAS</a>
            <a href="?sort=ctr" class="btn">CTR</a>
            <a href="?sort=cvr" class="btn">CVR</a>
            <a href="?sort=revenue" class="btn">Revenue</a>
        </div>

        <div class="campaign-grid">
            <?php foreach($campaigns as $c): 
                $roiClass = $c['roi'] > 50 ? 'roi-high' : ($c['roi'] > 0 ? 'roi-med' : 'roi-low');
            ?>
            <div class="campaign-card <?= $roiClass ?>">
                <div class="campaign-name"><?= htmlspecialchars($c['name']) ?></div>
                <div class="campaign-meta"><?= htmlspecialchars($c['platform']) ?> | <?= htmlspecialchars($c['type']) ?></div>
                
                <div class="metric-row">
                    <span class="metric-label">Spend:</span>
                    <span class="metric-val">$<?= number_format($c['spent']) ?></span>
                </div>
                <div class="metric-row">
                    <span class="metric-label">Revenue:</span>
                    <span class="metric-val">$<?= number_format($c['revenue']) ?></span>
                </div>
                <div class="metric-row">
                    <span class="metric-label">ROI:</span>
                    <span class="metric-val" style="color: <?= $c['roi'] > 0 ? '#4caf50' : '#f44336' ?>"><?= number_format($c['roi'], 1) ?>%</span>
                </div>
                <div class="metric-row">
                    <span class="metric-label">CTR:</span>
                    <span class="metric-val"><?= number_format($c['ctr'], 2) ?>%</span>
                </div>
                <div class="metric-row">
                    <span class="metric-label">CVR:</span>
                    <span class="metric-val"><?= number_format($c['cvr'], 2) ?>%</span>
                </div>
                <div class="metric-row">
                    <span class="metric-label">ROAS:</span>
                    <span class="metric-val"><?= number_format($c['roas'], 2) ?>x</span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="card">
            <h2>Platform Analysis</h2>
            <table>
                <thead>
                    <tr>
                        <th>Platform</th>
                        <th>Total Spend</th>
                        <th>Total Revenue</th>
                        <th>Cost Per Lead (CPL)</th>
                        <th>ROI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($platforms as $pName => $pData): ?>
                    <tr>
                        <td><?= htmlspecialchars($pName) ?></td>
                        <td>$<?= number_format($pData['spent']) ?></td>
                        <td>$<?= number_format($pData['revenue']) ?></td>
                        <td>$<?= number_format($pData['cpl'], 2) ?></td>
                        <td style="color: <?= $pData['roi'] > 0 ? '#4caf50' : '#f44336' ?>">
                            <?= number_format($pData['roi'], 1) ?>%
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="card">
            <h2>Detailed Comparison</h2>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Campaign</th>
                            <th>Budget Util.</th>
                            <th>Impressions</th>
                            <th>Clicks</th>
                            <th>Conv.</th>
                            <th>CPC</th>
                            <th>CPL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($campaigns as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['name']) ?></td>
                            <td><?= number_format($c['budget_utilization'], 1) ?>%</td>
                            <td><?= number_format($c['impressions']) ?></td>
                            <td><?= number_format($c['clicks']) ?></td>
                            <td><?= number_format($c['conversions']) ?></td>
                            <td>$<?= number_format($c['cpc'], 2) ?></td>
                            <td>$<?= number_format($c['cpl'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>
</html>
