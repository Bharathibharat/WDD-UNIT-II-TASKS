<?php
session_start();

function validateEmployeeId($id) {
    return preg_match('/^EMP[0-9]{4}$/', $id);
}

function validateName($name) {
    return preg_match('/^[A-Za-z ]{3,50}$/', $name);
}

function validatePassword($pwd) {
    return [
        'length' => preg_match('/.{8,}/', $pwd),
        'upper' => preg_match('/[A-Z]/', $pwd),
        'lower' => preg_match('/[a-z]/', $pwd),
        'number' => preg_match('/[0-9]/', $pwd),
        'special' => preg_match('/[!@#$%^&*()]/', $pwd)
    ];
}

function checkPasswordStrength($pwd) {
    $rules = validatePassword($pwd);
    return array_sum($rules);
}

$errors = [];
$success = false;
$pwdRules = [];
$strength = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $empId = $_POST['empId'] ?? '';
    $name = $_POST['name'] ?? '';
    $dept = $_POST['department'] ?? '';
    $pwd = $_POST['password'] ?? '';
    $confirmPwd = $_POST['confirm_password'] ?? '';

    if (!validateEmployeeId($empId)) {
        $errors[] = "Employee ID must be in the format EMP followed by 4 digits.";
    }
    
    if (!validateName($name)) {
        $errors[] = "Name must be between 3 and 50 characters, containing only letters and spaces.";
    }
    
    if (empty($dept)) {
        $errors[] = "Please select a department.";
    }
    
    $pwdRules = validatePassword($pwd);
    $strength = checkPasswordStrength($pwd);
    
    if ($strength < 5) {
        $errors[] = "Password does not meet all requirements.";
    }
    
    if ($pwd !== $confirmPwd) {
        $errors[] = "Passwords do not match.";
    }
    
    if (empty($errors)) {
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Result</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="header">
        <h1>Registration Status</h1>
    </header>
    <div class="container">
        <div class="card result-card">
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <h2>Success!</h2>
                    <p>Employee <?= htmlspecialchars($name) ?> (<?= htmlspecialchars($empId) ?>) registered successfully.</p>
                </div>
            <?php else: ?>
                <div class="alert alert-error">
                    <h2>Registration Failed</h2>
                    <ul>
                        <?php foreach($errors as $err): ?>
                            <li><?= htmlspecialchars($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <div class="validation-details">
                    <h3>Password Validation Details:</h3>
                    <ul>
                        <li><?= $pwdRules['length'] ? '✅' : '❌' ?> At least 8 characters</li>
                        <li><?= $pwdRules['upper'] ? '✅' : '❌' ?> Uppercase letter</li>
                        <li><?= $pwdRules['lower'] ? '✅' : '❌' ?> Lowercase letter</li>
                        <li><?= $pwdRules['number'] ? '✅' : '❌' ?> Number</li>
                        <li><?= $pwdRules['special'] ? '✅' : '❌' ?> Special character</li>
                    </ul>
                    <div class="strength-bar-container">
                        <div class="strength-bar strength-<?= $strength ?>" style="width: <?= ($strength/5)*100 ?>%"></div>
                    </div>
                    <p>Strength Score: <?= $strength ?>/5</p>
                </div>
            <?php endif; ?>
            
            <a href="index.php" class="btn btn-secondary">Go Back</a>
        </div>
    </div>
</body>
</html>
