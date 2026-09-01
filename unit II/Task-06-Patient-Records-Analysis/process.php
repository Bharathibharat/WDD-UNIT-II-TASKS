<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$name = $_POST['name'];
if(!preg_match('/^[A-Za-z ]{2,60}$/', $name)) {
    die("Invalid Name Format");
}

$age = (int)$_POST['age'];
$gender = $_POST['gender'];
$height = (float)$_POST['height'];
$weight = (float)$_POST['weight'];
$sys = (int)$_POST['sys'];
$dia = (int)$_POST['dia'];
$sugar = (int)$_POST['sugar'];
$temp = (float)$_POST['temp'];
$chol = (int)$_POST['chol'];

function calculateBMI($w, $h) {
    return $w / pow($h/100, 2);
}

function categorizeBMI($bmi) {
    if ($bmi < 18.5) return ['Underweight', 'warn'];
    if ($bmi < 25) return ['Normal', 'ok'];
    if ($bmi < 30) return ['Overweight', 'warn'];
    return ['Obese', 'danger'];
}

function categorizeBP($s, $d) {
    if ($s >= 180 || $d >= 120) return ['Crisis', 'danger'];
    if ($s >= 140 || $d >= 90) return ['High Stage 2', 'danger'];
    if ($s >= 130 || $d >= 80) return ['High Stage 1', 'warn'];
    if ($s >= 120 && $d < 80) return ['Elevated', 'warn'];
    return ['Normal', 'ok'];
}

function categorizeSugar($s) {
    if ($s < 70) return ['Low', 'warn'];
    if ($s < 100) return ['Normal', 'ok'];
    if ($s <= 125) return ['Pre-diabetic', 'warn'];
    return ['Diabetic', 'danger'];
}

function categorizeTemp($t) {
    if ($t < 96) return ['Hypothermia', 'danger'];
    if ($t <= 99.5) return ['Normal', 'ok'];
    if ($t <= 103) return ['Fever', 'warn'];
    return ['High Fever', 'danger'];
}

function categorizeChol($c) {
    if ($c < 200) return ['Desirable', 'ok'];
    if ($c <= 239) return ['Borderline', 'warn'];
    return ['High', 'danger'];
}

$bmi = calculateBMI($weight, $height);
$bmiCat = categorizeBMI($bmi);
$bpCat = categorizeBP($sys, $dia);
$sugarCat = categorizeSugar($sugar);
$tempCat = categorizeTemp($temp);
$cholCat = categorizeChol($chol);

function calculateRiskLevel($cats) {
    $danger = 0;
    $warn = 0;
    foreach ($cats as $c) {
        if ($c[1] === 'danger') $danger++;
        if ($c[1] === 'warn') $warn++;
    }
    
    if ($danger >= 2) return ['High Risk', 'danger'];
    if ($danger == 1 || $warn >= 2) return ['Moderate Risk', 'warn'];
    return ['Low Risk', 'ok'];
}

$risk = calculateRiskLevel([$bmiCat, $bpCat, $sugarCat, $tempCat, $cholCat]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Report</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="header">
        <h1>Patient Medical Report</h1>
    </header>
    <div class="container">
        <div class="card report-card">
            <div class="report-header">
                <h2><?= htmlspecialchars($name) ?></h2>
                <span class="badge badge-<?= $risk[1] ?> risk-badge">Overall: <?= $risk[0] ?></span>
            </div>
            
            <p class="patient-info">
                Age: <?= $age ?> | Gender: <?= $gender ?> | Height: <?= $height ?>cm | Weight: <?= $weight ?>kg
            </p>

            <table class="table">
                <thead>
                    <tr>
                        <th>Vital Sign</th>
                        <th>Reading</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>BMI</td>
                        <td><?= number_format($bmi, 1) ?></td>
                        <td><span class="badge badge-<?= $bmiCat[1] ?>"><?= $bmiCat[0] ?></span></td>
                    </tr>
                    <tr>
                        <td>Blood Pressure</td>
                        <td><?= $sys ?> / <?= $dia ?> mmHg</td>
                        <td><span class="badge badge-<?= $bpCat[1] ?>"><?= $bpCat[0] ?></span></td>
                    </tr>
                    <tr>
                        <td>Blood Sugar</td>
                        <td><?= $sugar ?> mg/dL</td>
                        <td><span class="badge badge-<?= $sugarCat[1] ?>"><?= $sugarCat[0] ?></span></td>
                    </tr>
                    <tr>
                        <td>Temperature</td>
                        <td><?= $temp ?> °F</td>
                        <td><span class="badge badge-<?= $tempCat[1] ?>"><?= $tempCat[0] ?></span></td>
                    </tr>
                    <tr>
                        <td>Cholesterol</td>
                        <td><?= $chol ?> mg/dL</td>
                        <td><span class="badge badge-<?= $cholCat[1] ?>"><?= $cholCat[0] ?></span></td>
                    </tr>
                </tbody>
            </table>
            
            <div style="margin-top: 2rem;">
                <a href="index.php" class="btn btn-primary">Back to Form</a>
            </div>
        </div>
    </div>
</body>
</html>
