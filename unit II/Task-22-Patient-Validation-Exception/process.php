<?php
session_start();

// Custom Exceptions
class MedicalException extends Exception {}

class InvalidPatientIdException extends MedicalException {
    public function __construct($id){
        parent::__construct("Invalid Patient ID: '$id'. Format must be PAT followed by 4 digits (e.g., PAT1234).");
    }
}

class InvalidVitalException extends MedicalException {
    private string $vitalName; 
    private float $value; 
    private string $normalRange;
    
    public function __construct($vn, $v, $nr){
        $this->vitalName = $vn; 
        $this->value = $v; 
        $this->normalRange = $nr;
        parent::__construct("Invalid vital - $vn: $v (Normal: $nr)");
    }
    
    public function getVitalName(): string { return $this->vitalName; }
    public function getValue(): float { return $this->value; }
    public function getNormalRange(): string { return $this->normalRange; }
}

class CriticalVitalException extends InvalidVitalException {
    public function __construct($vn, $v, $nr){
        parent::__construct($vn, $v, $nr);
    }
}

class AgeOutOfRangeException extends MedicalException {}
class InvalidMeasurementException extends MedicalException {}

$errors = [];
$warnings = [];
$success = false;
$auditLog = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $patientName = htmlspecialchars(trim($_POST['patientName']));
        $patientId = trim($_POST['patientId']);
        $age = (int)$_POST['age'];
        $height = (float)$_POST['height'];
        $weight = (float)$_POST['weight'];
        $sysBp = (int)$_POST['sysBp'];
        $diaBp = (int)$_POST['diaBp'];
        $bloodSugar = (int)$_POST['bloodSugar'];
        $temperature = (float)$_POST['temperature'];
        $spo2 = (int)$_POST['spo2'];
        $heartRate = (int)$_POST['heartRate'];
        
        // Validations
        if (!preg_match('/^PAT\d{4}$/', $patientId)) {
            throw new InvalidPatientIdException($patientId);
        }
        
        if ($age < 0 || $age > 120) {
            throw new AgeOutOfRangeException("Age $age is out of valid range (0-120).");
        }
        
        if ($height < 30 || $height > 300 || $weight < 1 || $weight > 500) {
            throw new InvalidMeasurementException("Height or Weight is out of valid physical range.");
        }
        
        if ($sysBp < 50 || $sysBp > 300) {
            throw new CriticalVitalException("Systolic BP", $sysBp, "90-120");
        }
        if ($diaBp < 30 || $diaBp > 200) {
            throw new CriticalVitalException("Diastolic BP", $diaBp, "60-80");
        }
        if ($bloodSugar < 20 || $bloodSugar > 600) {
            throw new CriticalVitalException("Blood Sugar", $bloodSugar, "70-99");
        }
        if ($temperature < 90 || $temperature > 115) {
            throw new CriticalVitalException("Temperature", $temperature, "97-99");
        }
        if ($spo2 < 0 || $spo2 > 100) {
            throw new InvalidVitalException("SpO2", $spo2, "95-100");
        }
        if ($heartRate < 20 || $heartRate > 300) {
            throw new CriticalVitalException("Heart Rate", $heartRate, "60-100");
        }

        // Warnings Check
        if ($sysBp > 140) $warnings[] = "High Blood Pressure Warning (Systolic > 140)";
        if ($spo2 < 95) $warnings[] = "Low Oxygen Saturation Warning (SpO2 < 95%)";
        if ($bloodSugar > 125) $warnings[] = "High Blood Sugar Warning (Fasting > 125 mg/dL)";
        
        $success = true;
        
    } catch (CriticalVitalException $e) {
        $errors[] = ["type" => "critical", "message" => "CRITICAL ALARM: " . $e->getMessage()];
    } catch (InvalidVitalException $e) {
        $errors[] = ["type" => "warning", "message" => $e->getMessage()];
    } catch (MedicalException $e) {
        $errors[] = ["type" => "info", "message" => $e->getMessage()];
    } catch (Exception $e) {
        $errors[] = ["type" => "info", "message" => "General Error: " . $e->getMessage()];
    } finally {
        // Logging action in finally block
        $timestamp = date("Y-m-d H:i:s");
        $status = $success ? "SUCCESS" : "FAILED";
        $auditLog = "[$timestamp] Validation $status for patient ID: " . htmlspecialchars($_POST['patientId'] ?? 'UNKNOWN');
        $_SESSION['last_audit'] = $auditLog;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Report</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Medical Center - Patient Report</h1>
    </header>
    <div class="container">
        
        <?php if (!empty($errors)): ?>
            <?php foreach ($errors as $error): ?>
                <div class="error-card error-<?= htmlspecialchars($error['type']) ?>">
                    <strong>Error:</strong> <?= htmlspecialchars($error['message']) ?>
                </div>
            <?php endforeach; ?>
            <a href="index.php" class="btn">Go Back</a>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success-card">
                <strong>Success!</strong> Patient data processed and validated successfully.
            </div>
            
            <?php if (!empty($warnings)): ?>
                <?php foreach ($warnings as $warn): ?>
                    <div class="error-card error-warning">
                        <strong>Warning:</strong> <?= htmlspecialchars($warn) ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <div class="card">
                <h2>Patient: <?= htmlspecialchars($patientName) ?> (<?= htmlspecialchars($patientId) ?>)</h2>
                
                <?php
                // BMI Calculation
                $heightM = $height / 100;
                $bmi = $weight / ($heightM * $heightM);
                $bmiCategory = match(true) {
                    $bmi < 18.5 => 'Underweight',
                    $bmi < 25 => 'Normal',
                    $bmi < 30 => 'Overweight',
                    default => 'Obese'
                };
                ?>
                <p><strong>Age:</strong> <?= $age ?> yrs | <strong>BMI:</strong> <?= number_format($bmi, 1) ?> (<?= $bmiCategory ?>)</p>
                
                <table>
                    <thead>
                        <tr>
                            <th>Vital Sign</th>
                            <th>Value</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Blood Pressure</td>
                            <td><?= $sysBp ?>/<?= $diaBp ?> mmHg</td>
                            <td>
                                <span class="badge <?= ($sysBp > 140 || $sysBp < 90) ? 'badge-abnormal' : 'badge-normal' ?>">
                                    <?= ($sysBp > 140 || $sysBp < 90) ? 'Needs Attention' : 'Normal' ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>Blood Sugar</td>
                            <td><?= $bloodSugar ?> mg/dL</td>
                            <td>
                                <span class="badge <?= ($bloodSugar > 125 || $bloodSugar < 70) ? 'badge-abnormal' : 'badge-normal' ?>">
                                    <?= ($bloodSugar > 125 || $bloodSugar < 70) ? 'Needs Attention' : 'Normal' ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>SpO2</td>
                            <td><?= $spo2 ?>%</td>
                            <td>
                                <span class="badge <?= ($spo2 < 95) ? 'badge-abnormal' : 'badge-normal' ?>">
                                    <?= ($spo2 < 95) ? 'Needs Attention' : 'Normal' ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>Heart Rate</td>
                            <td><?= $heartRate ?> bpm</td>
                            <td>
                                <span class="badge <?= ($heartRate < 60 || $heartRate > 100) ? 'badge-abnormal' : 'badge-normal' ?>">
                                    <?= ($heartRate < 60 || $heartRate > 100) ? 'Needs Attention' : 'Normal' ?>
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <a href="index.php" class="btn" style="text-decoration: none; text-align: center; display: inline-block;">New Patient</a>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <h3>Audit Log</h3>
            <p><?= htmlspecialchars($auditLog) ?></p>
        </div>
    </div>
</body>
</html>
