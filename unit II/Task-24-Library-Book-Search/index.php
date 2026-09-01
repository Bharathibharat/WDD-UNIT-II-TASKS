<?php
$library=[
  ['isbn'=>'978-0-13-468599-1','title'=>'PHP and MySQL Web Development','author'=>'Luke Welling','category'=>'Programming','publisher'=>'Addison-Wesley','year'=>2017,'copies'=>3,'available'=>2,'shelf'=>'A-12'],
  ['isbn'=>'978-0-596-51774-8','title'=>'Learning PHP MySQL JavaScript','author'=>'Robin Nixon','category'=>'Programming','publisher'=>'OReilly','year'=>2018,'copies'=>2,'available'=>1,'shelf'=>'A-13'],
  ['isbn'=>'978-0-13-235088-4','title'=>'The Pragmatic Programmer','author'=>'David Thomas','category'=>'Software Engineering','publisher'=>'Addison-Wesley','year'=>2019,'copies'=>2,'available'=>2,'shelf'=>'B-05'],
  ['isbn'=>'978-0-20-163361-4','title'=>'Design Patterns','author'=>'Gang of Four','category'=>'Software Engineering','publisher'=>'Addison-Wesley','year'=>1994,'copies'=>3,'available'=>0,'shelf'=>'B-08'],
  ['isbn'=>'978-1-49-195016-0','title'=>'Eloquent JavaScript','author'=>'Marijn Haverbeke','category'=>'Programming','publisher'=>'No Starch Press','year'=>2018,'copies'=>4,'available'=>3,'shelf'=>'A-20'],
  ['isbn'=>'978-0-13-110362-7','title'=>'The C Programming Language','author'=>'Kernighan and Ritchie','category'=>'Programming','publisher'=>'Prentice Hall','year'=>1988,'copies'=>2,'available'=>1,'shelf'=>'A-01'],
  ['isbn'=>'978-0-59-651798-4','title'=>'Python Cookbook','author'=>'David Beazley','category'=>'Programming','publisher'=>'OReilly','year'=>2013,'copies'=>3,'available'=>2,'shelf'=>'A-25'],
  ['isbn'=>'978-0-13-597880-1','title'=>'Clean Code','author'=>'Robert C. Martin','category'=>'Software Engineering','publisher'=>'Prentice Hall','year'=>2008,'copies'=>4,'available'=>4,'shelf'=>'B-02'],
  ['isbn'=>'978-0-20-161622-4','title'=>'Refactoring','author'=>'Martin Fowler','category'=>'Software Engineering','publisher'=>'Addison-Wesley','year'=>2018,'copies'=>2,'available'=>0,'shelf'=>'B-10'],
  ['isbn'=>'978-0-13-235088-5','title'=>'Introduction to Algorithms','author'=>'Cormen et al','category'=>'Algorithms','publisher'=>'MIT Press','year'=>2009,'copies'=>5,'available'=>3,'shelf'=>'C-01'],
  ['isbn'=>'978-1-59-327584-6','title'=>'The Linux Command Line','author'=>'William Shotts','category'=>'Operating Systems','publisher'=>'No Starch Press','year'=>2019,'copies'=>2,'available'=>2,'shelf'=>'D-05'],
  ['isbn'=>'978-0-32-154361-7','title'=>'Domain-Driven Design','author'=>'Eric Evans','category'=>'Software Engineering','publisher'=>'Addison-Wesley','year'=>2003,'copies'=>1,'available'=>1,'shelf'=>'B-15'],
  ['isbn'=>'978-1-49-192032-3','title'=>'Learning SQL','author'=>'Alan Beaulieu','category'=>'Databases','publisher'=>'OReilly','year'=>2020,'copies'=>3,'available'=>2,'shelf'=>'E-03'],
  ['isbn'=>'978-0-13-448108-5','title'=>'Artificial Intelligence: A Modern Approach','author'=>'Russell and Norvig','category'=>'AI/ML','publisher'=>'Pearson','year'=>2020,'copies'=>4,'available'=>1,'shelf'=>'F-01'],
  ['isbn'=>'978-1-09-181221-4','title'=>'Hands-On Machine Learning','author'=>'Aurelien Geron','category'=>'AI/ML','publisher'=>'OReilly','year'=>2022,'copies'=>3,'available'=>3,'shelf'=>'F-05'],
  ['isbn'=>'978-0-13-601970-7','title'=>'Computer Networks','author'=>'Tanenbaum and Wetherall','category'=>'Networking','publisher'=>'Pearson','year'=>2010,'copies'=>2,'available'=>0,'shelf'=>'G-02'],
  ['isbn'=>'978-0-07-352161-9','title'=>'Operating System Concepts','author'=>'Silberschatz et al','category'=>'Operating Systems','publisher'=>'Wiley','year'=>2018,'copies'=>5,'available'=>4,'shelf'=>'D-01'],
  ['isbn'=>'978-8-17-758095-8','title'=>'Let Us C','author'=>'Yashavant Kanetkar','category'=>'Programming','publisher'=>'BPB Publications','year'=>2019,'copies'=>6,'available'=>5,'shelf'=>'A-05'],
  ['isbn'=>'978-0-13-216935-2','title'=>'Software Engineering','author'=>'Ian Sommerville','category'=>'Software Engineering','publisher'=>'Pearson','year'=>2015,'copies'=>4,'available'=>2,'shelf'=>'B-20'],
  ['isbn'=>'978-0-07-640272-8','title'=>'Database System Concepts','author'=>'Silberschatz et al','category'=>'Databases','publisher'=>'McGraw-Hill','year'=>2019,'copies'=>3,'available'=>3,'shelf'=>'E-01'],
  ['isbn'=>'978-0-13-487498-5','title'=>'Compiler Design','author'=>'Aho Lam Sethi Ullman','category'=>'Computer Science','publisher'=>'Pearson','year'=>2006,'copies'=>2,'available'=>1,'shelf'=>'H-03'],
  ['isbn'=>'978-0-13-439250-6','title'=>'Computer Organization and Design','author'=>'Patterson and Hennessy','category'=>'Computer Science','publisher'=>'Morgan Kaufmann','year'=>2020,'copies'=>3,'available'=>2,'shelf'=>'H-01'],
  ['isbn'=>'978-1-60-309047-4','title'=>'JavaScript: The Good Parts','author'=>'Douglas Crockford','category'=>'Programming','publisher'=>'OReilly','year'=>2008,'copies'=>2,'available'=>2,'shelf'=>'A-18'],
  ['isbn'=>'978-1-78-862404-3','title'=>'PHP 8 Objects Patterns Practice','author'=>'Matt Zandstra','category'=>'Programming','publisher'=>'Apress','year'=>2021,'copies'=>3,'available'=>2,'shelf'=>'A-15'],
  ['isbn'=>'978-0-59-652068-7','title'=>'Web Design with HTML CSS JavaScript','author'=>'Jon Duckett','category'=>'Web Development','publisher'=>'Wiley','year'=>2011,'copies'=>4,'available'=>3,'shelf'=>'A-30']
];

function searchBooks($library, $query, $searchType, $category='all', $availability='all') {
  $query = trim($query);
  $pattern = !empty($query) ? '/'.preg_quote($query,'/').'/'.'i' : null;
  return array_values(array_filter($library, function($book) use ($pattern, $searchType, $category, $availability, $query) {
    if ($availability==='available' && $book['available']===0) return false;
    if ($availability==='issued' && $book['available']>0) return false;
    if ($category!=='all' && $book['category']!==$category) return false;
    if ($pattern===null) return true;
    return match($searchType) {
      'title' => (bool)preg_match($pattern,$book['title']),
      'author' => (bool)preg_match($pattern,$book['author']),
      'isbn' => str_contains($book['isbn'], $query),
      default => preg_match($pattern,$book['title'])||preg_match($pattern,$book['author'])||preg_match($pattern,$book['category'])
    };
  }));
}

// Handling form submission
$query = $_POST['search'] ?? '';
$searchType = $_POST['searchType'] ?? 'all';
$category = $_POST['category'] ?? 'all';
$availability = $_POST['availability'] ?? 'all';

$results = searchBooks($library, $query, $searchType, $category, $availability);

// Calculating stats
$totalBooks = count($results);
$availableCount = count(array_filter($results, fn($b) => $b['available'] > 0));
$issuedCount = $totalBooks - $availableCount;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Library Book Search</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Central Library Catalog</h1>
    </header>
    
    <div class="container">
        <div class="search-card">
            <form action="index.php" method="POST">
                <div class="flex-row" style="margin-bottom: 15px;">
                    <div class="flex-item" style="flex: 2;">
                        <input type="text" name="search" class="form-control" placeholder="Search books..." value="<?= htmlspecialchars($query) ?>">
                    </div>
                    <div class="flex-item">
                        <button type="submit" class="btn" style="width: 100%;">Search Library</button>
                    </div>
                </div>
                
                <div class="flex-row">
                    <div class="flex-item">
                        <label>Search By:</label>
                        <div>
                            <input type="radio" name="searchType" value="all" <?= $searchType=='all' ? 'checked' : '' ?>> All
                            <input type="radio" name="searchType" value="title" <?= $searchType=='title' ? 'checked' : '' ?>> Title
                            <input type="radio" name="searchType" value="author" <?= $searchType=='author' ? 'checked' : '' ?>> Author
                            <input type="radio" name="searchType" value="isbn" <?= $searchType=='isbn' ? 'checked' : '' ?>> ISBN
                        </div>
                    </div>
                    <div class="flex-item">
                        <label>Category:</label>
                        <select name="category" class="form-control">
                            <option value="all" <?= $category=='all' ? 'selected' : '' ?>>All Categories</option>
                            <option value="Programming" <?= $category=='Programming' ? 'selected' : '' ?>>Programming</option>
                            <option value="Software Engineering" <?= $category=='Software Engineering' ? 'selected' : '' ?>>Software Engineering</option>
                            <option value="Algorithms" <?= $category=='Algorithms' ? 'selected' : '' ?>>Algorithms</option>
                            <option value="Operating Systems" <?= $category=='Operating Systems' ? 'selected' : '' ?>>Operating Systems</option>
                            <option value="Databases" <?= $category=='Databases' ? 'selected' : '' ?>>Databases</option>
                            <option value="AI/ML" <?= $category=='AI/ML' ? 'selected' : '' ?>>AI/ML</option>
                            <option value="Networking" <?= $category=='Networking' ? 'selected' : '' ?>>Networking</option>
                            <option value="Computer Science" <?= $category=='Computer Science' ? 'selected' : '' ?>>Computer Science</option>
                            <option value="Web Development" <?= $category=='Web Development' ? 'selected' : '' ?>>Web Development</option>
                        </select>
                    </div>
                    <div class="flex-item">
                        <label>Availability:</label>
                        <select name="availability" class="form-control">
                            <option value="all" <?= $availability=='all' ? 'selected' : '' ?>>All Books</option>
                            <option value="available" <?= $availability=='available' ? 'selected' : '' ?>>Available Only</option>
                            <option value="issued" <?= $availability=='issued' ? 'selected' : '' ?>>Issued Only</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="stats-panel">
            <div><strong>Total Results:</strong> <?= $totalBooks ?></div>
            <div><strong>Available:</strong> <?= $availableCount ?></div>
            <div><strong>Issued:</strong> <?= $issuedCount ?></div>
        </div>
        
        <div class="books-grid">
            <?php if(empty($results)): ?>
                <p>No books found matching your criteria.</p>
            <?php else: ?>
                <?php foreach($results as $book): ?>
                    <div class="book-card">
                        <div class="book-title"><?= htmlspecialchars($book['title']) ?></div>
                        <div class="book-author">By <?= htmlspecialchars($book['author']) ?> (<?= $book['year'] ?>)</div>
                        <div class="book-meta"><strong>ISBN:</strong> <?= htmlspecialchars($book['isbn']) ?></div>
                        <div class="book-meta"><strong>Publisher:</strong> <?= htmlspecialchars($book['publisher']) ?></div>
                        <div class="book-meta"><strong>Shelf:</strong> <?= htmlspecialchars($book['shelf']) ?></div>
                        <div class="book-meta"><strong>Copies:</strong> <?= $book['available'] ?> / <?= $book['copies'] ?> available</div>
                        
                        <div>
                            <span class="badge badge-category"><?= htmlspecialchars($book['category']) ?></span>
                            <?php if($book['available'] > 0): ?>
                                <span class="badge badge-available">Available</span>
                            <?php else: ?>
                                <span class="badge badge-issued">Issued</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
