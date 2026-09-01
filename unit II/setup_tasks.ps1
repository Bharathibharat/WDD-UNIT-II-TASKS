$tasks = @(
  'Task-01-Student-Performance-Analysis',
  'Task-02-Branch-wise-Sales-Analysis',
  'Task-03-Employee-Password-Validation',
  'Task-04-Employee-Salary-Analysis',
  'Task-05-Course-Enrolment-Analysis',
  'Task-06-Patient-Records-Analysis',
  'Task-07-Music-Search-Playlist',
  'Task-08-Email-Address-Extraction',
  'Task-09-Customer-Information-Validation',
  'Task-10-E-Commerce-Registration-Validation',
  'Task-11-Customer-Support-Queue',
  'Task-12-Loan-Repayment-Calculator',
  'Task-13-Railway-Waiting-List',
  'Task-14-Package-Stack-Queue',
  'Task-15-Browser-History-Stack',
  'Task-16-Player-Score-Analysis',
  'Task-17-Sales-Trend-Analysis',
  'Task-18-Stock-Performance-Analysis',
  'Task-19-Banking-Exception-Handling',
  'Task-20-Payroll-Exception-Handling',
  'Task-21-Product-Sorting',
  'Task-22-Patient-Validation-Exception',
  'Task-23-Examination-Exception-Handling',
  'Task-24-Library-Book-Search',
  'Task-25-Student-Placement-Statistics',
  'Task-26-Digital-Marketing-Analysis'
)

# Tasks that genuinely need process.php (form submission & server-side processing)
$needsProcess = @(
  'Task-03-Employee-Password-Validation',
  'Task-04-Employee-Salary-Analysis',
  'Task-05-Course-Enrolment-Analysis',
  'Task-06-Patient-Records-Analysis',
  'Task-08-Email-Address-Extraction',
  'Task-09-Customer-Information-Validation',
  'Task-10-E-Commerce-Registration-Validation',
  'Task-12-Loan-Repayment-Calculator',
  'Task-19-Banking-Exception-Handling',
  'Task-20-Payroll-Exception-Handling',
  'Task-22-Patient-Validation-Exception',
  'Task-23-Examination-Exception-Handling',
  'Task-24-Library-Book-Search'
)

$base = 'C:\xampp\htdocs\unit II'

foreach ($task in $tasks) {
  $dir = Join-Path $base $task
  New-Item -ItemType Directory -Force -Path $dir | Out-Null
  New-Item -ItemType File -Force -Path (Join-Path $dir 'index.php') | Out-Null
  New-Item -ItemType File -Force -Path (Join-Path $dir 'style.css') | Out-Null
  if ($needsProcess -contains $task) {
    New-Item -ItemType File -Force -Path (Join-Path $dir 'process.php') | Out-Null
    Write-Host "  Created: $task  [index.php, style.css, process.php]"
  } else {
    Write-Host "  Created: $task  [index.php, style.css]"
  }
}

Write-Host ""
Write-Host "All 26 task folders and files created successfully!"
