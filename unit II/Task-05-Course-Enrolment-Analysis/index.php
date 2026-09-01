<?php
$courses=[
  ['id'=>'CS101','name'=>'PHP Programming','dept'=>'CS','credits'=>4,'capacity'=>60,'enrolled'=>58,'duration'=>'6 months','fee'=>5000,'instructor'=>'Dr. Kumar'],
  ['id'=>'CS102','name'=>'Web Design','dept'=>'CS','credits'=>3,'capacity'=>50,'enrolled'=>45,'duration'=>'4 months','fee'=>4000,'instructor'=>'Prof. Priya'],
  ['id'=>'IT101','name'=>'Database Management','dept'=>'IT','credits'=>4,'capacity'=>55,'enrolled'=>55,'duration'=>'5 months','fee'=>4500,'instructor'=>'Dr. Ravi'],
  ['id'=>'IT102','name'=>'Network Security','dept'=>'IT','credits'=>3,'capacity'=>40,'enrolled'=>22,'duration'=>'4 months','fee'=>6000,'instructor'=>'Prof. Meena'],
  ['id'=>'EC101','name'=>'Digital Electronics','dept'=>'EC','credits'=>4,'capacity'=>65,'enrolled'=>60,'duration'=>'6 months','fee'=>4200,'instructor'=>'Dr. Suresh'],
  ['id'=>'ME101','name'=>'AutoCAD Design','dept'=>'ME','credits'=>2,'capacity'=>45,'enrolled'=>30,'duration'=>'3 months','fee'=>3500,'instructor'=>'Prof. Kavitha'],
  ['id'=>'CS103','name'=>'Python ML','dept'=>'CS','credits'=>5,'capacity'=>50,'enrolled'=>50,'duration'=>'8 months','fee'=>8000,'instructor'=>'Dr. Muthu'],
  ['id'=>'BA101','name'=>'Business Analytics','dept'=>'BA','credits'=>3,'capacity'=>60,'enrolled'=>12,'duration'=>'4 months','fee'=>7000,'instructor'=>'Prof. Saranya']
];

function getCourseStatus($enrolled, $capacity) {
    $percent = ($enrolled / $capacity) * 100;
    if ($percent >= 100) return 'Full';
    if ($percent >= 80) return 'Limited';
    return 'Open';
}

function sortCourses($courses, $sortBy) {
    usort($courses, function($a, $b) use ($sortBy) {
        if ($sortBy === 'name') return strcmp($a['name'], $b['name']);
        if ($sortBy === 'dept') return strcmp($a['dept'], $b['dept']);
        if ($sortBy === 'fill') {
            $fillA = $a['enrolled'] / $a['capacity'];
            $fillB = $b['enrolled'] / $b['capacity'];
            return $fillB <=> $fillA;
        }
        if ($sortBy === 'seats') {
            $availA = $a['capacity'] - $a['enrolled'];
            $availB = $b['capacity'] - $b['enrolled'];
            return $availA <=> $availB;
        }
        return 0;
    });
    return $courses;
}

function filterCourses($courses, $filter) {
    if ($filter === 'all') return $courses;
    return array_filter($courses, function($c) use ($filter) {
        $status = getCourseStatus($c['enrolled'], $c['capacity']);
        return strtolower($status) === $filter;
    });
}

// Compute extra stats
$processedCourses = array_map(function($c) {
    $c['available'] = $c['capacity'] - $c['enrolled'];
    $c['fill_percent'] = ($c['enrolled'] / $c['capacity']) * 100;
    $c['status'] = getCourseStatus($c['enrolled'], $c['capacity']);
    return $c;
}, $courses);

// Apply filters and sort from GET
$sortBy = $_GET['sort'] ?? 'name';
$filterBy = $_GET['filter'] ?? 'all';

$filtered = filterCourses($processedCourses, $filterBy);
$finalCourses = sortCourses($filtered, $sortBy);

$totalCourses = count($courses);
$totalEnrolled = array_sum(array_column($courses, 'enrolled'));
$totalCapacity = array_sum(array_column($courses, 'capacity'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Enrolment Analysis</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="header">
        <h1>Course Enrolment Analysis</h1>
    </header>
    <div class="container">
        <div class="summary-cards">
            <div class="card summary-card">
                <h3>Total Courses</h3>
                <p><?= $totalCourses ?></p>
            </div>
            <div class="card summary-card">
                <h3>Total Capacity</h3>
                <p><?= $totalCapacity ?></p>
            </div>
            <div class="card summary-card">
                <h3>Total Enrolled</h3>
                <p><?= $totalEnrolled ?></p>
            </div>
        </div>

        <div class="controls card">
            <form action="index.php" method="GET" class="control-form">
                <div class="form-group">
                    <label>Sort By:</label>
                    <select name="sort" onchange="this.form.submit()">
                        <option value="name" <?= $sortBy==='name'?'selected':'' ?>>Name</option>
                        <option value="dept" <?= $sortBy==='dept'?'selected':'' ?>>Department</option>
                        <option value="fill" <?= $sortBy==='fill'?'selected':'' ?>>Highest Fill %</option>
                        <option value="seats" <?= $sortBy==='seats'?'selected':'' ?>>Available Seats (Low to High)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Filter By Status:</label>
                    <select name="filter" onchange="this.form.submit()">
                        <option value="all" <?= $filterBy==='all'?'selected':'' ?>>All</option>
                        <option value="open" <?= $filterBy==='open'?'selected':'' ?>>Open</option>
                        <option value="limited" <?= $filterBy==='limited'?'selected':'' ?>>Limited</option>
                        <option value="full" <?= $filterBy==='full'?'selected':'' ?>>Full</option>
                    </select>
                </div>
            </form>
        </div>

        <div class="courses-grid">
            <?php foreach($finalCourses as $c): ?>
                <div class="card course-card">
                    <div class="course-header">
                        <h2><?= htmlspecialchars($c['name']) ?></h2>
                        <span class="badge status-<?= strtolower($c['status']) ?>"><?= $c['status'] ?></span>
                    </div>
                    <p class="course-meta">
                        <span><?= $c['id'] ?></span> | 
                        <span>Dept: <?= $c['dept'] ?></span> | 
                        <span>Credits: <?= $c['credits'] ?></span>
                    </p>
                    
                    <div class="capacity-section">
                        <div class="capacity-stats">
                            <span>Enrolled: <?= $c['enrolled'] ?>/<?= $c['capacity'] ?></span>
                            <span><?= number_format($c['fill_percent'], 1) ?>%</span>
                        </div>
                        <div class="capacity-bar-bg">
                            <?php 
                                $barClass = 'bg-green';
                                if($c['fill_percent'] >= 100) $barClass = 'bg-red';
                                elseif($c['fill_percent'] >= 80) $barClass = 'bg-yellow';
                            ?>
                            <div class="capacity-bar <?= $barClass ?>" style="width: <?= min(100, $c['fill_percent']) ?>%"></div>
                        </div>
                        <p class="available-seats">Available Seats: <strong><?= $c['available'] ?></strong></p>
                    </div>

                    <div class="course-footer">
                        <p><strong>Instructor:</strong> <?= htmlspecialchars($c['instructor']) ?></p>
                        <p><strong>Fee:</strong> ₹<?= number_format($c['fee']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if(empty($finalCourses)): ?>
                <p>No courses match the criteria.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
