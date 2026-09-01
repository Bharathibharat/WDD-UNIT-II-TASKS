<?php
session_start();

$players=[
  ['name'=>'Arjun Vel','team'=>'Alpha','game1'=>850,'game2'=>920,'game3'=>780,'game4'=>1050,'game5'=>900],
  ['name'=>'Priya Raj','team'=>'Beta','game1'=>760,'game2'=>840,'game3'=>900,'game4'=>820,'game5'=>880],
  ['name'=>'Ravi Kumar','team'=>'Alpha','game1'=>920,'game2'=>880,'game3'=>850,'game4'=>760,'game5'=>940],
  ['name'=>'Meena S','team'=>'Gamma','game1'=>700,'game2'=>750,'game3'=>800,'game4'=>850,'game5'=>900],
  ['name'=>'Suresh P','team'=>'Beta','game1'=>1000,'game2'=>950,'game3'=>1100,'game4'=>980,'game5'=>1050],
  ['name'=>'Kavitha D','team'=>'Gamma','game1'=>650,'game2'=>700,'game3'=>720,'game4'=>680,'game5'=>750],
  ['name'=>'Muthu K','team'=>'Alpha','game1'=>880,'game2'=>860,'game3'=>900,'game4'=>920,'game5'=>860],
  ['name'=>'Saranya M','team'=>'Beta','game1'=>820,'game2'=>800,'game3'=>840,'game4'=>860,'game5'=>820],
  ['name'=>'Arun R','team'=>'Gamma','game1'=>750,'game2'=>780,'game3'=>800,'game4'=>820,'game5'=>840],
  ['name'=>'Divya L','team'=>'Alpha','game1'=>900,'game2'=>920,'game3'=>940,'game4'=>960,'game5'=>980],
  ['name'=>'Karthik S','team'=>'Beta','game1'=>830,'game2'=>850,'game3'=>870,'game4'=>890,'game5'=>910],
  ['name'=>'Nithya V','team'=>'Gamma','game1'=>700,'game2'=>720,'game3'=>740,'game4'=>760,'game5'=>780]
];

function standardDeviation($scores) {
    $num_of_elements = count($scores);
    $variance = 0.0;
    $average = array_sum($scores) / $num_of_elements;
    foreach($scores as $i) {
        $variance += pow(($i - $average), 2);
    }
    return (float)sqrt($variance/$num_of_elements);
}

function getTrend($scores) {
    $firstAvg = ($scores[0] + $scores[1]) / 2;
    $lastAvg = ($scores[3] + $scores[4]) / 2;
    $diff = $lastAvg - $firstAvg;
    if ($diff > 20) return 'Improving';
    if ($diff < -20) return 'Declining';
    return 'Stable';
}

function calculateStats(&$player) {
    $scores = [$player['game1'], $player['game2'], $player['game3'], $player['game4'], $player['game5']];
    $player['total'] = array_sum($scores);
    $player['avg'] = $player['total'] / 5;
    $player['best'] = max($scores);
    $player['worst'] = min($scores);
    $player['std_dev'] = standardDeviation($scores);
    $player['trend'] = getTrend($scores);
}

foreach ($players as &$player) {
    calculateStats($player);
}
unset($player);

$sortBy = $_GET['sortby'] ?? 'total';
usort($players, function($a, $b) use ($sortBy) {
    return $b[$sortBy] <=> $a[$sortBy];
});

function teamAnalysis($players) {
    $teams = [];
    foreach ($players as $p) {
        $t = $p['team'];
        if (!isset($teams[$t])) $teams[$t] = ['total' => 0, 'count' => 0];
        $teams[$t]['total'] += $p['total'];
        $teams[$t]['count']++;
    }
    $res = [];
    foreach ($teams as $t => $data) {
        $res[$t] = $data['total'] / $data['count'];
    }
    arsort($res);
    return $res;
}

$teamStats = teamAnalysis($players);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Player Score Analysis</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Esports Player Analysis</h1>
        
        <div class="podium">
            <?php
            $medals = ['gold', 'silver', 'bronze'];
            for ($i = 0; $i < 3; $i++) {
                if (isset($players[$i])) {
                    echo "<div class='podium-card {$medals[$i]}'>";
                    echo "<div class='medal-icon'></div>";
                    echo "<h2>" . htmlspecialchars($players[$i]['name']) . "</h2>";
                    echo "<p>Team " . htmlspecialchars($players[$i]['team']) . "</p>";
                    echo "<div class='score'>" . number_format($players[$i]['total']) . " pts</div>";
                    echo "</div>";
                }
            }
            ?>
        </div>

        <div class="controls">
            <span>Sort By:</span>
            <a href="?sortby=total" class="btn <?= $sortBy === 'total' ? 'active' : '' ?>">Total Score</a>
            <a href="?sortby=avg" class="btn <?= $sortBy === 'avg' ? 'active' : '' ?>">Average</a>
            <a href="?sortby=best" class="btn <?= $sortBy === 'best' ? 'active' : '' ?>">Best Game</a>
        </div>

        <div class="dashboard">
            <div class="main-table">
                <table>
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Player</th>
                            <th>Team</th>
                            <th>Total</th>
                            <th>Avg</th>
                            <th>Best</th>
                            <th>Worst</th>
                            <th>Std Dev</th>
                            <th>Trend</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($players as $index => $p): ?>
                        <tr>
                            <td>#<?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($p['name']) ?></td>
                            <td><span class="team-badge <?= strtolower($p['team']) ?>"><?= htmlspecialchars($p['team']) ?></span></td>
                            <td><strong><?= number_format($p['total']) ?></strong></td>
                            <td><?= number_format($p['avg'], 1) ?></td>
                            <td><?= number_format($p['best']) ?></td>
                            <td><?= number_format($p['worst']) ?></td>
                            <td><?= number_format($p['std_dev'], 1) ?></td>
                            <td><span class="trend <?= strtolower($p['trend']) ?>"><?= htmlspecialchars($p['trend']) ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="sidebar">
                <div class="card">
                    <h3>Team Performance (Avg)</h3>
                    <ul class="team-list">
                        <?php foreach ($teamStats as $team => $avg): ?>
                        <li>
                            <span class="team-badge <?= strtolower($team) ?>"><?= htmlspecialchars($team) ?></span>
                            <span><?= number_format($avg, 1) ?> pts</span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
