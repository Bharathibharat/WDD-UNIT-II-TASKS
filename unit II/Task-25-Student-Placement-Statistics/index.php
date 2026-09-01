<?php
$placements=[
  ['name'=>'Arun Kumar','rollNo'=>'CS001','dept'=>'CSE','cgpa'=>8.9,'company'=>'TCS','package'=>450000,'placed'=>true,'year'=>2024,'city'=>'Chennai'],
  ['name'=>'Priya Devi','rollNo'=>'CS002','dept'=>'CSE','cgpa'=>9.2,'company'=>'Infosys','package'=>550000,'placed'=>true,'year'=>2024,'city'=>'Bangalore'],
  ['name'=>'Ravi Shankar','rollNo'=>'CS003','dept'=>'CSE','cgpa'=>6.8,'company'=>'','package'=>0,'placed'=>false,'year'=>2024,'city'=>''],
  ['name'=>'Meena K','rollNo'=>'CS004','dept'=>'CSE','cgpa'=>9.5,'company'=>'Google','package'=>2500000,'placed'=>true,'year'=>2024,'city'=>'Hyderabad'],
  ['name'=>'Suresh B','rollNo'=>'CS005','dept'=>'IT','cgpa'=>8.2,'company'=>'Wipro','package'=>380000,'placed'=>true,'year'=>2024,'city'=>'Pune'],
  ['name'=>'Kavitha S','rollNo'=>'CS006','dept'=>'IT','cgpa'=>7.5,'company'=>'HCL','package'=>420000,'placed'=>true,'year'=>2024,'city'=>'Chennai'],
  ['name'=>'Muthu R','rollNo'=>'CS007','dept'=>'ECE','cgpa'=>7.0,'company'=>'','package'=>0,'placed'=>false,'year'=>2024,'city'=>''],
  ['name'=>'Saranya V','rollNo'=>'CS008','dept'=>'ECE','cgpa'=>8.4,'company'=>'Bosch','package'=>600000,'placed'=>true,'year'=>2024,'city'=>'Coimbatore'],
  ['name'=>'Arun R','rollNo'=>'CS009','dept'=>'MECH','cgpa'=>7.8,'company'=>'TVS Motors','package'=>480000,'placed'=>true,'year'=>2024,'city'=>'Hosur'],
  ['name'=>'Divya L','rollNo'=>'CS010','dept'=>'MECH','cgpa'=>6.5,'company'=>'','package'=>0,'placed'=>false,'year'=>2024,'city'=>''],
  ['name'=>'Karthik S','rollNo'=>'CS011','dept'=>'CSE','cgpa'=>8.7,'company'=>'Cognizant','package'=>420000,'placed'=>true,'year'=>2024,'city'=>'Chennai'],
  ['name'=>'Nithya V','rollNo'=>'CS012','dept'=>'CSE','cgpa'=>9.0,'company'=>'Amazon','package'=>1800000,'placed'=>true,'year'=>2024,'city'=>'Hyderabad'],
  ['name'=>'Vijay K','rollNo'=>'CS013','dept'=>'IT','cgpa'=>8.1,'company'=>'Accenture','package'=>500000,'placed'=>true,'year'=>2024,'city'=>'Bangalore'],
  ['name'=>'Rekha M','rollNo'=>'CS014','dept'=>'IT','cgpa'=>7.3,'company'=>'Tech Mahindra','package'=>350000,'placed'=>true,'year'=>2024,'city'=>'Pune'],
  ['name'=>'Siva P','rollNo'=>'CS015','dept'=>'ECE','cgpa'=>8.9,'company'=>'Qualcomm','package'=>900000,'placed'=>true,'year'=>2024,'city'=>'Bangalore'],
  ['name'=>'Anitha D','rollNo'=>'CS016','dept'=>'ECE','cgpa'=>7.2,'company'=>'','package'=>0,'placed'=>false,'year'=>2024,'city'=>''],
  ['name'=>'Mani R','rollNo'=>'CS017','dept'=>'MECH','cgpa'=>8.0,'company'=>'Ashok Leyland','package'=>520000,'placed'=>true,'year'=>2024,'city'=>'Chennai'],
  ['name'=>'Deepa S','rollNo'=>'CS018','dept'=>'CSE','cgpa'=>9.3,'company'=>'Microsoft','package'=>2200000,'placed'=>true,'year'=>2024,'city'=>'Hyderabad'],
  ['name'=>'Bala K','rollNo'=>'CS019','dept'=>'CSE','cgpa'=>7.9,'company'=>'IBM','package'=>480000,'placed'=>true,'year'=>2024,'city'=>'Bangalore'],
  ['name'=>'Selvi A','rollNo'=>'CS020','dept'=>'IT','cgpa'=>8.5,'company'=>'Capgemini','package'=>440000,'placed'=>true,'year'=>2024,'city'=>'Mumbai'],
  ['name'=>'Raja M','rollNo'=>'CS021','dept'=>'IT','cgpa'=>6.9,'company'=>'','package'=>0,'placed'=>false,'year'=>2024,'city'=>''],
  ['name'=>'Vani K','rollNo'=>'CS022','dept'=>'ECE','cgpa'=>8.3,'company'=>'Intel','package'=>750000,'placed'=>true,'year'=>2024,'city'=>'Bangalore'],
  ['name'=>'Mohan R','rollNo'=>'CS023','dept'=>'MECH','cgpa'=>7.6,'company'=>'','package'=>0,'placed'=>false,'year'=>2024,'city'=>''],
  ['name'=>'Preethi S','rollNo'=>'CS024','dept'=>'CSE','cgpa'=>9.1,'company'=>'Flipkart','package'=>1200000,'placed'=>true,'year'=>2024,'city'=>'Bangalore'],
  ['name'=>'Ganesh P','rollNo'=>'CS025','dept'=>'CSE','cgpa'=>8.6,'company'=>'Zoho','package'=>800000,'placed'=>true,'year'=>2024,'city'=>'Chennai'],
  ['name'=>'Uma V','rollNo'=>'CS026','dept'=>'IT','cgpa'=>8.8,'company'=>'Oracle','package'=>950000,'placed'=>true,'year'=>2024,'city'=>'Bangalore'],
  ['name'=>'Ramu K','rollNo'=>'CS027','dept'=>'ECE','cgpa'=>7.4,'company'=>'','package'=>0,'placed'=>false,'year'=>2024,'city'=>''],
  ['name'=>'Latha P','rollNo'=>'CS028','dept'=>'MECH','cgpa'=>8.2,'company'=>'BHEL','package'=>560000,'placed'=>true,'year'=>2024,'city'=>'Trichy'],
  ['name'=>'Guna S','rollNo'=>'CS029','dept'=>'CSE','cgpa'=>9.4,'company'=>'Adobe','package'=>1600000,'placed'=>true,'year'=>2024,'city'=>'Noida'],
  ['name'=>'Nila R','rollNo'=>'CS030','dept'=>'IT','cgpa'=>7.7,'company'=>'Mphasis','package'=>410000,'placed'=>true,'year'=>2024,'city'=>'Pune']
];

// Calculations
$totalStudents = count($placements);
$placedStudents = array_filter($placements, fn($s) => $s['placed']);
$unplacedStudents = array_filter($placements, fn($s) => !$s['placed']);
$placedCount = count($placedStudents);
$unplacedCount = count($unplacedStudents);
$placementRate = ($placedCount / $totalStudents) * 100;

$packages = array_column($placedStudents, 'package');
$highestPackage = !empty($packages) ? max($packages) : 0;
$lowestPackage = !empty($packages) ? min($packages) : 0;

function calculateMean($pkgs) {
    if(empty($pkgs)) return 0;
    return array_sum($pkgs) / count($pkgs);
}

function calculateMedian($pkgs) {
    if(empty($pkgs)) return 0;
    sort($pkgs);
    $count = count($pkgs);
    $mid = floor(($count - 1) / 2);
    if($count % 2 == 0) {
        return ($pkgs[$mid] + $pkgs[$mid + 1]) / 2;
    }
    return $pkgs[$mid];
}

function calculateMode($pkgs, $bracket = 100000) {
    if(empty($pkgs)) return 0;
    $brackets = [];
    foreach($pkgs as $p) {
        $b = floor($p / $bracket) * $bracket;
        $brackets[$b] = ($brackets[$b] ?? 0) + 1;
    }
    arsort($brackets);
    return array_key_first($brackets);
}

function calculateStdDev($pkgs) {
    if(empty($pkgs)) return 0;
    $mean = calculateMean($pkgs);
    $variance = 0;
    foreach($pkgs as $p) {
        $variance += pow($p - $mean, 2);
    }
    return sqrt($variance / count($pkgs));
}

$meanPackage = calculateMean($packages);
$medianPackage = calculateMedian($packages);
$modePackage = calculateMode($packages);
$stdDevPackage = calculateStdDev($packages);

function deptWiseStats($placements) {
    $stats = [];
    foreach($placements as $p) {
        $dept = $p['dept'];
        if(!isset($stats[$dept])) {
            $stats[$dept] = ['total'=>0, 'placed'=>0, 'packages'=>[]];
        }
        $stats[$dept]['total']++;
        if($p['placed']) {
            $stats[$dept]['placed']++;
            $stats[$dept]['packages'][] = $p['package'];
        }
    }
    return $stats;
}

$deptStats = deptWiseStats($placements);

// Companies count
$companies = array_filter(array_column($placements, 'company'));
$companyCounts = array_count_values($companies);
arsort($companyCounts);

// Salary Brackets
$brackets = ['<5L'=>0, '5-10L'=>0, '10-20L'=>0, '>20L'=>0];
foreach($packages as $p) {
    if($p < 500000) $brackets['<5L']++;
    elseif($p <= 1000000) $brackets['5-10L']++;
    elseif($p <= 2000000) $brackets['10-20L']++;
    else $brackets['>20L']++;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Placement Statistics</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Campus Placement Statistics 2024</h1>
    </header>
    
    <div class="container">
        <!-- KPI Dashboard -->
        <div class="dashboard-grid">
            <div class="stat-card">
                <h3>Total Students</h3>
                <div class="value"><?= $totalStudents ?></div>
            </div>
            <div class="stat-card">
                <h3>Placed Students</h3>
                <div class="value"><?= $placedCount ?></div>
            </div>
            <div class="stat-card">
                <h3>Placement Rate</h3>
                <div class="value"><?= number_format($placementRate, 1) ?>%</div>
            </div>
            <div class="stat-card">
                <h3>Highest Package</h3>
                <div class="value">₹<?= number_format($highestPackage) ?></div>
            </div>
            <div class="stat-card">
                <h3>Average Package</h3>
                <div class="value">₹<?= number_format($meanPackage) ?></div>
            </div>
        </div>

        <div class="grid-2">
            <!-- Statistical Analysis -->
            <div class="card">
                <h2>Statistical Analysis (Packages)</h2>
                <table>
                    <tr>
                        <td><strong>Mean (Average)</strong></td>
                        <td>₹<?= number_format($meanPackage, 2) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Median (Middle Value)</strong></td>
                        <td>₹<?= number_format($medianPackage, 2) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Mode (Most Common Bracket)</strong></td>
                        <td>~₹<?= number_format($modePackage) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Standard Deviation</strong></td>
                        <td>₹<?= number_format($stdDevPackage, 2) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Lowest Package</strong></td>
                        <td>₹<?= number_format($lowestPackage) ?></td>
                    </tr>
                </table>
            </div>

            <!-- Salary Distribution -->
            <div class="card">
                <h2>Salary Distribution</h2>
                <div class="bar-chart">
                    <?php 
                    $maxBracket = max($brackets);
                    foreach($brackets as $label => $count): 
                        $width = $maxBracket > 0 ? ($count / $maxBracket) * 100 : 0;
                    ?>
                    <div class="bar-row">
                        <div class="bar-label"><?= $label ?></div>
                        <div class="bar-track">
                            <div class="bar-fill" style="width: <?= $width ?>%;"></div>
                        </div>
                        <div class="bar-value"><?= $count ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="grid-2">
            <!-- Department Breakdown -->
            <div class="card">
                <h2>Department Breakdown</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Dept</th>
                            <th>Rate</th>
                            <th>Avg Package</th>
                            <th>Max Package</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($deptStats as $dept => $dStat): 
                            $dRate = ($dStat['placed'] / $dStat['total']) * 100;
                            $dAvg = calculateMean($dStat['packages']);
                            $dMax = !empty($dStat['packages']) ? max($dStat['packages']) : 0;
                        ?>
                        <tr>
                            <td><strong><?= $dept ?></strong></td>
                            <td><?= number_format($dRate, 1) ?>%</td>
                            <td>₹<?= number_format($dAvg) ?></td>
                            <td>₹<?= number_format($dMax) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Top Companies -->
            <div class="card">
                <h2>Top Hiring Companies</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Company</th>
                            <th>Students Hired</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i=0;
                        foreach($companyCounts as $comp => $count): 
                            if($i++ >= 5) break;
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($comp) ?></td>
                            <td><?= $count ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Full Student List -->
        <div class="card">
            <h2>Full Student List</h2>
            <table>
                <thead>
                    <tr>
                        <th>Roll No</th>
                        <th>Name</th>
                        <th>Dept</th>
                        <th>CGPA</th>
                        <th>Status</th>
                        <th>Company</th>
                        <th>Package</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($placements as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['rollNo']) ?></td>
                        <td><?= htmlspecialchars($p['name']) ?></td>
                        <td><?= htmlspecialchars($p['dept']) ?></td>
                        <td><?= $p['cgpa'] ?></td>
                        <td>
                            <?php if($p['placed']): ?>
                                <span class="badge badge-placed">Placed</span>
                            <?php else: ?>
                                <span class="badge badge-unplaced">Unplaced</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($p['company'] ?: '-') ?></td>
                        <td><?= $p['package'] > 0 ? '₹'.number_format($p['package']) : '-' ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</body>
</html>
