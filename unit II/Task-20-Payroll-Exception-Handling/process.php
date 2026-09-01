<?php
session_start();

class PayrollException extends Exception {}
class InvalidSalaryException extends PayrollException {}
class NegativeHoursException extends PayrollException {}
class ExcessiveLeaveException extends PayrollException {
    public function __construct($leaves, $max) {
        parent::__construct("Leave ($leaves days) exceeds max allowed ($max days).");
    }
}
class AdvanceExceedsSalaryException extends PayrollException {}
class InvalidEmployeeException extends PayrollException {}

function calculatePayroll($data) {
    if (!preg_match('/^EMP\d{4}$/', $data['empId'])) {
        throw new InvalidEmployeeException("Invalid Employee ID format. Must be EMP followed by 4 digits.");
    }
    if ($data['basicSalary'] < 10000 || $data['basicSalary'] > 500000) {
        throw new InvalidSalaryException("Basic salary must be between ₹10,000 and ₹5,00,000.");
    }
    if ($data['hoursWorked'] < 0 || $data['overtime'] < 0 || $data['hoursWorked'] > 200) {
        throw new NegativeHoursException("Hours worked and overtime must be non-negative. Max regular hours: 200.");
    }
    if ($data['leaves'] > 30) {
        throw new ExcessiveLeaveException($data['leaves'], 30);
    }
    if ($data['advance'] > ($data['basicSalary'] * 0.5)) {
        throw new AdvanceExceedsSalaryException("Advance cannot exceed 50% of basic salary.");
    }

    $basic = $data['basicSalary'];
    $hra = $basic * 0.40;
    $da = $basic * 0.15;
    $ta = 1500;
    $medical = 1250;
    
    $hourlyRate = $basic / 160;
    $overtimePay = $data['overtime'] * ($hourlyRate * 1.5);
    
    $gross = $basic + $hra + $da + $ta + $medical + $overtimePay;
    
    $pf = $basic * 0.12;
    $profTax = $gross > 15000 ? 200 : 0;
    $leaveDed = ($basic / 30) * $data['leaves'];
    $advance = $data['advance'];
    
    $totDeductions = $pf + $profTax + $leaveDed + $advance;
    $net = $gross - $totDeductions;

    return [
        'empId' => $data['empId'],
        'name' => $data['name'],
        'dept' => $data['dept'],
        'designation' => $data['designation'],
        'basic' => $basic,
        'hra' => $hra,
        'da' => $da,
        'ta' => $ta,
        'medical' => $medical,
        'overtimePay' => $overtimePay,
        'gross' => $gross,
        'pf' => $pf,
        'profTax' => $profTax,
        'leaveDed' => $leaveDed,
        'advance' => $advance,
        'totDeductions' => $totDeductions,
        'net' => $net
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $empId = $_POST['empId'] ?? '';
    
    if (!isset($_SESSION['payroll_log'])) {
        $_SESSION['payroll_log'] = [];
    }

    try {
        $payslip = calculatePayroll([
            'empId' => $empId,
            'name' => $_POST['name'] ?? '',
            'dept' => $_POST['dept'] ?? '',
            'designation' => $_POST['designation'] ?? '',
            'basicSalary' => floatval($_POST['basicSalary'] ?? 0),
            'hoursWorked' => floatval($_POST['hoursWorked'] ?? 0),
            'overtime' => floatval($_POST['overtime'] ?? 0),
            'leaves' => intval($_POST['leaves'] ?? 0),
            'advance' => floatval($_POST['advance'] ?? 0)
        ]);
        
        $_SESSION['payslip'] = $payslip;
        $status = 'Success';
    } catch (PayrollException $e) {
        $_SESSION['error'] = $e->getMessage();
        $status = 'Failed';
    } catch (Exception $e) {
        $_SESSION['error'] = "System Error: " . $e->getMessage();
        $status = 'Failed';
    } finally {
        $_SESSION['payroll_log'][] = [
            'empId' => $empId,
            'status' => $status,
            'time' => date('H:i:s')
        ];
        // keep only last 5 logs
        if (count($_SESSION['payroll_log']) > 5) {
            array_shift($_SESSION['payroll_log']);
        }
        header("Location: index.php");
        exit();
    }
}
