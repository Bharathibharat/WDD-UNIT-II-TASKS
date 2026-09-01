<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Information Validation - Task 09</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Customer Information form</h1>
        <p>Complete validation with regular expressions</p>
    </header>

    <div class="container">
        <div class="card">
            <form action="process.php" method="POST">
                <h3 class="section-title">Personal Information</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="fullName" placeholder="John Doe" required>
                    </div>
                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" name="dob" required>
                    </div>
                    <div class="form-group">
                        <label>Gender</label>
                        <select name="gender" required>
                            <option value="">Select...</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>

                <h3 class="section-title">Contact Information</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" placeholder="john@example.com" required>
                    </div>
                    <div class="form-group">
                        <label>Mobile Number</label>
                        <input type="text" name="mobile" placeholder="9876543210" required>
                    </div>
                </div>

                <h3 class="section-title">Address & Identity</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Pincode</label>
                        <input type="text" name="pincode" placeholder="110001" required>
                    </div>
                    <div class="form-group">
                        <label>Aadhar Number</label>
                        <input type="text" name="aadhar" placeholder="123456789012" required>
                    </div>
                    <div class="form-group">
                        <label>PAN Card Number</label>
                        <input type="text" name="pan" placeholder="ABCDE1234F" required>
                    </div>
                </div>

                <button type="submit" class="btn">Validate & Submit</button>
            </form>
        </div>
    </div>
</body>
</html>
