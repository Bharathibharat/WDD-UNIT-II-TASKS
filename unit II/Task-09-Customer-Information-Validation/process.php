<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validation Results</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Validation Results</h1>
        <p><a href="index.php" style="color:white;">&larr; Back to Form</a></p>
    </header>

    <div class="container">
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $validationResults = [];
            $isValidForm = true;

            function validateField($name, $value, $rule, $errorMsg) {
                $isValid = preg_match($rule, $value);
                return [
                    'name' => $name,
                    'value' => $value,
                    'isValid' => $isValid,
                    'message' => $isValid ? 'Valid' : $errorMsg
                ];
            }

            function calculateAge($dob) {
                $bday = new DateTime($dob);
                $today = new DateTime();
                return $today->diff($bday)->y;
            }

            function maskAadhar($a) {
                return str_repeat('*', 8) . substr($a, -4);
            }

            function maskPAN($p) {
                return substr($p, 0, 3) . str_repeat('*', 4) . substr($p, -3);
            }

            // Rules
            $rules = [
                'fullName' => ['regex' => '/^[A-Za-z]+(\s[A-Za-z]+){1,3}$/', 'msg' => 'Must contain 2-4 words, alphabets only'],
                'dob' => ['regex' => '/^\d{4}-\d{2}-\d{2}$/', 'msg' => 'Invalid Date format'],
                'email' => ['regex' => '/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/', 'msg' => 'Invalid email address'],
                'mobile' => ['regex' => '/^[6-9]\d{9}$/', 'msg' => 'Must be a 10-digit Indian number'],
                'pincode' => ['regex' => '/^[1-9][0-9]{5}$/', 'msg' => 'Must be a valid 6-digit Pincode'],
                'aadhar' => ['regex' => '/^[2-9]{1}[0-9]{11}$/', 'msg' => 'Must be a valid 12-digit Aadhar number'],
                'pan' => ['regex' => '/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/', 'msg' => 'Invalid PAN format (e.g. ABCDE1234F)'],
            ];

            foreach ($rules as $field => $rule) {
                $val = $data[$field] ?? '';
                $res = validateField(ucfirst($field), $val, $rule['regex'], $rule['msg']);
                $validationResults[] = $res;
                if (!$res['isValid']) $isValidForm = false;
            }

            // Additional Gender Check
            $gender = $data['gender'] ?? '';
            $genderValid = in_array($gender, ['Male', 'Female', 'Other']);
            $validationResults[] = [
                'name' => 'Gender', 'value' => $gender, 'isValid' => $genderValid, 'message' => $genderValid ? 'Valid' : 'Select a valid gender'
            ];
            if (!$genderValid) $isValidForm = false;

            // Age check
            $age = 0;
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['dob'])) {
                $age = calculateAge($data['dob']);
                if ($age < 18) {
                    $isValidForm = false;
                    $validationResults[] = ['name' => 'Age Verification', 'value' => "$age years", 'isValid' => false, 'message' => 'Must be at least 18 years old'];
                } else {
                    $validationResults[] = ['name' => 'Age Verification', 'value' => "$age years", 'isValid' => true, 'message' => 'Valid'];
                }
            }

            ?>
            
            <div class="card">
                <h3 class="section-title">Field Validation Details</h3>
                <table class="validation-table">
                    <tr>
                        <th>Field</th>
                        <th>Entered Value</th>
                        <th>Status</th>
                        <th>Message</th>
                    </tr>
                    <?php foreach ($validationResults as $res): ?>
                        <tr>
                            <td><?= htmlspecialchars($res['name']) ?></td>
                            <td><?= htmlspecialchars($res['value']) ?></td>
                            <td class="status-icon <?= $res['isValid'] ? 'status-valid' : 'status-invalid' ?>">
                                <?= $res['isValid'] ? '&#10004;' : '&#10008;' ?>
                            </td>
                            <td><?= htmlspecialchars($res['message']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>

            <?php if ($isValidForm): ?>
                <div class="customer-card">
                    <h2>Registration Successful</h2>
                    <div class="detail"><strong>Name:</strong> <?= htmlspecialchars($data['fullName']) ?></div>
                    <div class="detail"><strong>Email:</strong> <?= htmlspecialchars($data['email']) ?></div>
                    <div class="detail"><strong>Mobile:</strong> <?= htmlspecialchars($data['mobile']) ?></div>
                    <div class="detail"><strong>Aadhar:</strong> <?= maskAadhar($data['aadhar']) ?></div>
                    <div class="detail"><strong>PAN:</strong> <?= maskPAN($data['pan']) ?></div>
                </div>
            <?php else: ?>
                <div class="card" style="border-left: 5px solid #c62828;">
                    <h3 style="color:#c62828;">Validation Failed</h3>
                    <p>Please fix the errors above and try again.</p>
                </div>
            <?php endif; ?>

            <?php
        }
        ?>
    </div>
</body>
</html>
