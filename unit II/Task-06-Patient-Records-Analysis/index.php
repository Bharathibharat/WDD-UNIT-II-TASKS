<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Records Analysis</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="header">
        <h1>Patient Vitals & Records System</h1>
    </header>
    <div class="container">
        <div class="card form-card">
            <h2>Add New Patient Reading</h2>
            <form action="process.php" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Age</label>
                        <input type="number" name="age" min="0" max="120" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Gender</label>
                        <select name="gender" required>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Height (cm)</label>
                        <input type="number" name="height" min="50" max="300" step="0.1" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Weight (kg)</label>
                        <input type="number" name="weight" min="2" max="500" step="0.1" required>
                    </div>
                    <div class="form-group">
                        <label>Systolic BP</label>
                        <input type="number" name="sys" min="50" max="250" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Diastolic BP</label>
                        <input type="number" name="dia" min="30" max="150" required>
                    </div>
                    <div class="form-group">
                        <label>Blood Sugar (mg/dL)</label>
                        <input type="number" name="sugar" min="20" max="600" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Temperature (°F)</label>
                        <input type="number" name="temp" min="90" max="110" step="0.1" required>
                    </div>
                    <div class="form-group">
                        <label>Cholesterol (mg/dL)</label>
                        <input type="number" name="chol" min="50" max="500" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Generate Report</button>
            </form>
        </div>
    </div>
</body>
</html>
