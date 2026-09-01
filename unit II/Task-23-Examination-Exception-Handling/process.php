<?php
// Custom Exceptions for Examination
class ExaminationException extends Exception {}

class InvalidRegisterNumberException extends ExaminationException {
    public function __construct($regNo){
        parent::__construct("Invalid Register Number: '$regNo'. Expected: 23CS0001 format.");
    }
}

class InvalidMarksException extends ExaminationException {
    private string $subject;
    public function __construct($subject, $marks){
        $this->subject = $subject;
        parent::__construct("Invalid marks for '$subject': $marks. Must be 0-100.");
    }
    public function getSubject(): string { return $this->subject; }
}

class SubjectNotFoundException extends ExaminationException {}

class ArrearsException extends ExaminationException {
    private array $failedSubjects;
    public function __construct(array $failed){
        $this->failedSubjects = $failed;
        parent::__construct('Arrears in '.count($failed).' subject(s): '.implode(', ', $failed));
    }
    public function getFailedSubjects(): array { return $this->failedSubjects; }
}

// Function to calculate grade using match expression (PHP 8+)
function calculateGrade($marks): string {
    return match(true) {
        $marks >= 90 => 'O',
        $marks >= 80 => 'A+',
        $marks >= 70 => 'A',
        $marks >= 60 => 'B+',
        $marks >= 50 => 'B',
        default => 'U'
    };
}

$error = null;
$arrears = null;
$results = [];
$total = 0;
$average = 0;
$failedSubjects = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $regNo = trim($_POST['regNo']);
        $studentName = htmlspecialchars(trim($_POST['studentName']));
        $dept = htmlspecialchars($_POST['department']);
        $sem = htmlspecialchars($_POST['semester']);
        $subjects = $_POST['subjects'];
        $marks = $_POST['marks'];
        
        // Validate Register Number
        if (!preg_match('/^\d{2}[A-Z]{2}\d{4}$/', $regNo)) {
            throw new InvalidRegisterNumberException($regNo);
        }
        
        // Process subjects
        for ($i = 0; $i < 6; $i++) {
            $subj = trim($subjects[$i]);
            $mark = trim($marks[$i]);
            
            if (empty($subj)) {
                throw new SubjectNotFoundException("Subject name cannot be empty for entry " . ($i+1));
            }
            if ($mark === '' || $mark < 0 || $mark > 100) {
                throw new InvalidMarksException($subj, $mark);
            }
            
            $mark = (int)$mark;
            $grade = calculateGrade($mark);
            $total += $mark;
            
            $results[] = [
                'subject' => htmlspecialchars($subj),
                'mark' => $mark,
                'grade' => $grade
            ];
            
            if ($mark < 50) {
                $failedSubjects[] = htmlspecialchars($subj);
            }
        }
        
        $average = $total / 6;
        
        if (count($failedSubjects) > 0) {
            throw new ArrearsException($failedSubjects);
        }
        
    } catch (ArrearsException $e) {
        $arrears = $e->getMessage();
    } catch (ExaminationException $e) {
        $error = $e->getMessage();
    } catch (Exception $e) {
        $error = "System Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Examination Results</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Examination Result</h1>
    </header>
    
    <div class="container">
        <?php if ($error): ?>
            <div class="error-card">
                <strong>Error!</strong> <?= htmlspecialchars($error) ?>
            </div>
            <a href="index.php" class="btn">Go Back</a>
        <?php elseif (!empty($results)): ?>
            
            <?php if ($arrears): ?>
                <div class="warning-card">
                    <strong>Notice:</strong> <?= htmlspecialchars($arrears) ?>
                </div>
            <?php else: ?>
                <div class="result-card">
                    <strong>Congratulations!</strong> You have successfully cleared all subjects.
                </div>
            <?php endif; ?>
            
            <div class="card">
                <h2><?= htmlspecialchars($studentName) ?> (<?= htmlspecialchars($regNo) ?>) - <?= $dept ?> Sem <?= $sem ?></h2>
                
                <table>
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Marks</th>
                            <th>Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($results as $res): ?>
                            <tr class="<?= ($res['mark'] < 50) ? 'failed-subject' : '' ?>">
                                <td><?= $res['subject'] ?></td>
                                <td><?= $res['mark'] ?></td>
                                <td><?= $res['grade'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div style="margin-top:20px; font-size:18px;">
                    <strong>Total Marks:</strong> <?= $total ?> / 600 <br>
                    <strong>Average:</strong> <?= number_format($average, 2) ?>% <br>
                    <strong>Overall Result:</strong> <?= $arrears ? 'FAIL' : 'PASS' ?>
                </div>
                
                <div class="grade-scale">
                    <strong>Grade Scale:</strong>
                    <div class="grade-item">O: >= 90</div>
                    <div class="grade-item">A+: 80-89</div>
                    <div class="grade-item">A: 70-79</div>
                    <div class="grade-item">B+: 60-69</div>
                    <div class="grade-item">B: 50-59</div>
                    <div class="grade-item">U: < 50</div>
                </div>
                
                <br>
                <a href="index.php" class="btn" style="text-decoration:none; display:inline-block; text-align:center;">Enter Another Result</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
