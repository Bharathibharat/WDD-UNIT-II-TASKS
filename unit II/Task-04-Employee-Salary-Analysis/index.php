<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Salary Analysis</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="header">
        <h1>Salary Processing System</h1>
    </header>
    <div class="container">
        <div class="card form-card">
            <h2>Enter Employee Details</h2>
            <form action="process.php" method="POST">
                <div class="form-group">
                    <label>Employee Name</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Employee ID</label>
                    <input type="text" name="empId" required>
                </div>
                <div class="form-group">
                    <label>Department</label>
                    <select name="department" required>
                        <option value="">Select Department</option>
                        <option value="Engineering">Engineering</option>
                        <option value="Marketing">Marketing</option>
                        <option value="Sales">Sales</option>
                        <option value="HR">HR</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Designation</label>
                    <select name="designation" required>
                        <option value="">Select Designation</option>
                        <option value="Junior Engineer">Junior Engineer</option>
                        <option value="Senior Engineer">Senior Engineer</option>
                        <option value="Manager">Manager</option>
                        <option value="Director">Director</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Basic Salary (Min ₹10,000)</label>
                    <input type="number" name="basic" min="10000" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Years of Experience</label>
                    <input type="number" name="experience" min="0" step="1" required>
                </div>
                <button type="submit" class="btn btn-primary">Generate Payslip</button>
            </form>
        </div>
    </div>
</body>
</html>
