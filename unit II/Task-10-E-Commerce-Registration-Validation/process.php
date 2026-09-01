<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Result</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Registration Status</h1>
        <p><a href="index.php" style="color:white;">&larr; Back to form</a></p>
    </header>

    <div class="container">
        <div class="card">
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $errors = [];

            $validationRules = [
                'username' => ['pattern'=>'/^[a-zA-Z][a-zA-Z0-9_]{4,19}$/','message'=>'Username: 5-20 chars, start with letter, alphanumeric+underscore'],
                'fullName' => ['pattern'=>'/^[A-Za-z]+(\s[A-Za-z]+)+$/','message'=>'Full name: at least 2 words'],
                'email' => ['pattern'=>'/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/','message'=>'Invalid email format'],
                'mobile' => ['pattern'=>'/^[6-9]\d{9}$/','message'=>'Invalid Indian mobile number'],
            ];

            foreach ($validationRules as $field => $rule) {
                if (!preg_match($rule['pattern'], $data[$field] ?? '')) {
                    $errors[$field] = $rule['message'];
                }
            }

            // Password check
            $pwd = $data['password'] ?? '';
            $cpwd = $data['confirmPassword'] ?? '';
            $pwdErrors = [];
            if (strlen($pwd) < 8) $pwdErrors[] = "Minimum 8 characters";
            if (!preg_match('/[A-Z]/', $pwd)) $pwdErrors[] = "At least one uppercase letter";
            if (!preg_match('/[a-z]/', $pwd)) $pwdErrors[] = "At least one lowercase letter";
            if (!preg_match('/[0-9]/', $pwd)) $pwdErrors[] = "At least one digit";
            if (!preg_match('/[^a-zA-Z0-9]/', $pwd)) $pwdErrors[] = "At least one special character";
            
            if (!empty($pwdErrors)) {
                $errors['password'] = "Password weak: " . implode(", ", $pwdErrors);
            }
            if ($pwd !== $cpwd) {
                $errors['confirmPassword'] = "Passwords do not match";
            }

            // Age Check
            $bday = new DateTime($data['dob']);
            $today = new DateTime();
            $age = $today->diff($bday)->y;
            if ($age < 16) {
                $errors['dob'] = "Must be at least 16 years old";
            }

            // Referral
            $ref = $data['referral'] ?? '';
            if (!empty($ref) && !preg_match('/^[A-Z0-9]{6,10}$/', $ref)) {
                $errors['referral'] = "Invalid referral code (6-10 alphanumeric)";
            }

            // Terms
            if (!isset($data['terms']) || $data['terms'] !== 'yes') {
                $errors['terms'] = "You must agree to the terms";
            }

            if (empty($errors)) {
                echo "<h2 style='color:#2e7d32; text-align:center;'>Welcome, " . htmlspecialchars($data['fullName']) . "!</h2>";
                echo "<p style='text-align:center;'>Your registration was successful.</p>";
            } else {
                echo "<h2 style='color:#c62828;'>Registration Failed</h2>";
                echo "<table class='val-table'>";
                echo "<tr><th>Field</th><th>Error</th></tr>";
                foreach ($errors as $f => $e) {
                    echo "<tr><td><strong>" . htmlspecialchars(ucfirst($f)) . "</strong></td><td class='error-msg'>" . htmlspecialchars($e) . "</td></tr>";
                }
                echo "</table>";
            }

            echo "<br><h3>Password Requirements Analysis:</h3>";
            echo "<ul class='checklist'>";
            echo "<li class='" . (strlen($pwd)>=8 ? "done" : "fail") . "'>Length >= 8</li>";
            echo "<li class='" . (preg_match('/[A-Z]/', $pwd) ? "done" : "fail") . "'>Uppercase Letter</li>";
            echo "<li class='" . (preg_match('/[a-z]/', $pwd) ? "done" : "fail") . "'>Lowercase Letter</li>";
            echo "<li class='" . (preg_match('/[0-9]/', $pwd) ? "done" : "fail") . "'>Digit</li>";
            echo "<li class='" . (preg_match('/[^a-zA-Z0-9]/', $pwd) ? "done" : "fail") . "'>Special Character</li>";
            echo "</ul>";

        }
        ?>
        </div>
    </div>
</body>
</html>
