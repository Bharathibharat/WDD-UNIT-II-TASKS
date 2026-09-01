<?php
$students = [
  ['name'=>'Arun Kumar','rollNo'=>'CS001','tamil'=>78,'english'=>82,'maths'=>91,'science'=>88,'social'=>75],
  ['name'=>'Priya Devi','rollNo'=>'CS002','tamil'=>85,'english'=>90,'maths'=>76,'science'=>92,'social'=>88],
  ['name'=>'Ravi Shankar','rollNo'=>'CS003','tamil'=>55,'english'=>60,'maths'=>48,'science'=>52,'social'=>58],
  ['name'=>'Meena Kumari','rollNo'=>'CS004','tamil'=>92,'english'=>88,'maths'=>95,'science'=>90,'social'=>94],
  ['name'=>'Suresh Babu','rollNo'=>'CS005','tamil'=>65,'english'=>70,'maths'=>72,'science'=>68,'social'=>66],
  ['name'=>'Kavitha S','rollNo'=>'CS006','tamil'=>45,'english'=>50,'maths'=>38,'science'=>42,'social'=>35],
  ['name'=>'Muthu Raja','rollNo'=>'CS007','tamil'=>80,'english'=>75,'maths'=>82,'science'=>78,'social'=>79],
  ['name'=>'Saranya V','rollNo'=>'CS008','tamil'=>70,'english'=>65,'maths'=>68,'science'=>72,'social'=>74]
];

function calculateGrade($pct) {
    return match (true) {
        $pct >= 90 => 'O',
        $pct >= 80 => 'A+',
        $pct >= 70 => 'A',
        $pct >= 60 => 'B+',
        $pct >= 50 => 'B',
        default => 'U'
    };
}

function calculateResult($student) {
    if ($student['tamil'] >= 35 && $student['english'] >= 35 && $student['maths'] >= 35 && $student['science'] >= 35 && $student['social'] >= 35) {
        return 'Pass';
    }
    return 'Fail';
}

$processedStudents = array_map(function($student) {
    $total = $student['tamil'] + $student['english'] + $student['maths'] + $student['science'] + $student['social'];
    $student['total'] = $total;
    $student['percentage'] = $total / 5;
    $student['grade'] = calculateGrade($student['percentage']);
    $student['result'] = calculateResult($student);
    return $student;
}, $students);

function rankStudents($students) {
    usort($students, fn($a, $b) => $b['percentage'] <=> $a['percentage']);
    return $students;
}

$rankedStudents = rankStudents($processedStudents);

$totalStudents = count($rankedStudents);
$passed = count(array_filter($rankedStudents, fn($s) => $s['result'] === 'Pass'));
$failed = $totalStudents - $passed;
$classAvg = array_sum(array_column($rankedStudents, 'percentage')) / $totalStudents;
$topper = $rankedStudents[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Performance Analysis</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="header">
        <h1>Student Performance Analysis</h1>
    </header>
    <div class="container">
        <div class="cards-container">
            <div class="card summary-card">
                <h3>Total Students</h3>
                <p><?= $totalStudents ?></p>
            </div>
            <div class="card summary-card">
                <h3>Passed</h3>
                <p class="text-success"><?= $passed ?></p>
            </div>
            <div class="card summary-card">
                <h3>Failed</h3>
                <p class="text-danger"><?= $failed ?></p>
            </div>
            <div class="card summary-card">
                <h3>Class Average</h3>
                <p><?= number_format($classAvg, 2) ?>%</p>
            </div>
            <div class="card summary-card topper-card">
                <h3>Class Topper</h3>
                <p><?= htmlspecialchars($topper['name']) ?></p>
                <p><?= number_format($topper['percentage'], 2) ?>%</p>
            </div>
        </div>

        <div class="card">
            <h2>Full Results</h2>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Roll No</th>
                            <th>Name</th>
                            <th>Tamil</th>
                            <th>English</th>
                            <th>Maths</th>
                            <th>Science</th>
                            <th>Social</th>
                            <th>Total</th>
                            <th>%</th>
                            <th>Grade</th>
                            <th>Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($rankedStudents as $index => $student): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($student['rollNo']) ?></td>
                                <td><?= htmlspecialchars($student['name']) ?></td>
                                <td><?= $student['tamil'] ?></td>
                                <td><?= $student['english'] ?></td>
                                <td><?= $student['maths'] ?></td>
                                <td><?= $student['science'] ?></td>
                                <td><?= $student['social'] ?></td>
                                <td><?= $student['total'] ?></td>
                                <td><?= number_format($student['percentage'], 2) ?></td>
                                <td><span class="badge grade-<?= $student['grade'] === 'U' ? 'U' : 'Pass' ?>"><?= $student['grade'] ?></span></td>
                                <td>
                                    <span class="badge <?= $student['result'] === 'Pass' ? 'badge-success' : 'badge-danger' ?>">
                                        <?= $student['result'] ?>
                                    </span>
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
