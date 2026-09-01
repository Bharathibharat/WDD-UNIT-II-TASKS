<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Vitals System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Medical Center - Patient Vitals</h1>
    </header>
    
    <div class="container">
        <div class="card">
            <h2>Record Patient Vitals</h2>
            <form action="process.php" method="POST">
                <div class="grid-2">
                    <div class="form-group">
                        <label for="patientName">Patient Name</label>
                        <input type="text" id="patientName" name="patientName" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="patientId">Patient ID (e.g., PAT1234)</label>
                        <input type="text" id="patientId" name="patientId" class="form-control" required>
                    </div>
                </div>
                
                <div class="grid-3">
                    <div class="form-group">
                        <label for="age">Age</label>
                        <input type="number" id="age" name="age" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="height">Height (cm)</label>
                        <input type="number" step="0.1" id="height" name="height" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="weight">Weight (kg)</label>
                        <input type="number" step="0.1" id="weight" name="weight" class="form-control" required>
                    </div>
                </div>

                <h3>Vital Signs</h3>
                <div class="grid-2">
                    <div class="form-group">
                        <label for="sysBp">Systolic BP (mmHg)</label>
                        <input type="number" id="sysBp" name="sysBp" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="diaBp">Diastolic BP (mmHg)</label>
                        <input type="number" id="diaBp" name="diaBp" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="bloodSugar">Blood Sugar (mg/dL)</label>
                        <input type="number" id="bloodSugar" name="bloodSugar" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="temperature">Temperature (°F)</label>
                        <input type="number" step="0.1" id="temperature" name="temperature" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="spo2">SpO2 (%)</label>
                        <input type="number" id="spo2" name="spo2" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="heartRate">Heart Rate (bpm)</label>
                        <input type="number" id="heartRate" name="heartRate" class="form-control" required>
                    </div>
                </div>

                <button type="submit" class="btn">Process Vitals</button>
            </form>
        </div>
    </div>
</body>
</html>
