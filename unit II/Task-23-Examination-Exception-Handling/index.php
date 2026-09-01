<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Examination Results Portal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>University Examination Results</h1>
    </header>
    
    <div class="container">
        <div class="card">
            <h2>Student Details & Marks Entry</h2>
            <form action="process.php" method="POST">
                <div class="grid-2">
                    <div class="form-group">
                        <label for="studentName">Student Name</label>
                        <input type="text" id="studentName" name="studentName" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="regNo">Register Number (e.g., 23CS0001)</label>
                        <input type="text" id="regNo" name="regNo" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="department">Department</label>
                        <select id="department" name="department" class="form-control" required>
                            <option value="CSE">CSE</option>
                            <option value="IT">IT</option>
                            <option value="ECE">ECE</option>
                            <option value="MECH">MECH</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="semester">Semester</label>
                        <select id="semester" name="semester" class="form-control" required>
                            <option value="I">I</option>
                            <option value="II">II</option>
                            <option value="III">III</option>
                            <option value="IV">IV</option>
                            <option value="V">V</option>
                            <option value="VI">VI</option>
                            <option value="VII">VII</option>
                            <option value="VIII">VIII</option>
                        </select>
                    </div>
                </div>

                <h3>Subject Marks</h3>
                <?php for($i=1; $i<=6; $i++): ?>
                <div class="subject-row">
                    <div class="form-group">
                        <label>Subject <?= $i ?> Name</label>
                        <input type="text" name="subjects[]" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Marks (0-100)</label>
                        <input type="number" name="marks[]" class="form-control" required>
                    </div>
                </div>
                <?php endfor; ?>

                <button type="submit" class="btn">Calculate Results</button>
            </form>
        </div>
    </div>
</body>
</html>
