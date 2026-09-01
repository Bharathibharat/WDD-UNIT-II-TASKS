<?php
$songLibrary=[
  ['id'=>1,'title'=>'Enna Solla','artist'=>'Yuvan Shankar Raja','album'=>'Kaadhal','genre'=>'Melody','duration'=>'4:25','year'=>2004,'language'=>'Tamil','rating'=>4.8],
  ['id'=>2,'title'=>'Rowdy Baby','artist'=>'Yuvan Shankar Raja','album'=>'Maari 2','genre'=>'Folk','duration'=>'3:58','year'=>2018,'language'=>'Tamil','rating'=>4.9],
  ['id'=>3,'title'=>'Kannaana Kanney','artist'=>'D. Imman','album'=>'Viswasam','genre'=>'Melody','duration'=>'5:12','year'=>2019,'language'=>'Tamil','rating'=>4.7],
  ['id'=>4,'title'=>'Vaathi Coming','artist'=>'Anirudh Ravichander','album'=>'Master','genre'=>'Mass','duration'=>'3:45','year'=>2021,'language'=>'Tamil','rating'=>4.9],
  ['id'=>5,'title'=>'Enjoy Enjaami','artist'=>'Arivu','album'=>'Single','genre'=>'Folk','duration'=>'4:33','year'=>2021,'language'=>'Tamil','rating'=>4.6],
  ['id'=>6,'title'=>'Unna Nenachu','artist'=>'A.R. Rahman','album'=>'Roja','genre'=>'Classical','duration'=>'5:01','year'=>1992,'language'=>'Tamil','rating'=>4.8],
  ['id'=>7,'title'=>'Kolaveri Di','artist'=>'Dhanush','album'=>'3','genre'=>'Pop','duration'=>'3:39','year'=>2012,'language'=>'Tamil','rating'=>4.5],
  ['id'=>8,'title'=>'Mersal Arasan','artist'=>'A.R. Rahman','album'=>'Mersal','genre'=>'Mass','duration'=>'4:18','year'=>2017,'language'=>'Tamil','rating'=>4.7],
  ['id'=>9,'title'=>'Butterfly','artist'=>'Anirudh Ravichander','album'=>'Jigarthanda','genre'=>'Pop','duration'=>'3:55','year'=>2014,'language'=>'Tamil','rating'=>4.4],
  ['id'=>10,'title'=>'Thalli Pogathey','artist'=>'A.R. Rahman','album'=>'Achcham Yenbadhu','genre'=>'Melody','duration'=>'6:20','year'=>2016,'language'=>'Tamil','rating'=>4.9],
  ['id'=>11,'title'=>'Nenjukulle','artist'=>'A.R. Rahman','album'=>'Kadal','genre'=>'Classical','duration'=>'5:44','year'=>2013,'language'=>'Tamil','rating'=>4.6],
  ['id'=>12,'title'=>'Aalaporan Tamizhan','artist'=>'A.R. Rahman','album'=>'Mersal','genre'=>'Mass','duration'=>'4:02','year'=>2017,'language'=>'Tamil','rating'=>4.8],
  ['id'=>13,'title'=>'Oru Adaar Love','artist'=>'Omar Lulu','album'=>'Oru Adaar Love','genre'=>'Pop','duration'=>'3:30','year'=>2019,'language'=>'Malayalam','rating'=>4.3],
  ['id'=>14,'title'=>'Kaattu Payale','artist'=>'Anirudh Ravichander','album'=>'Remo','genre'=>'Folk','duration'=>'3:48','year'=>2016,'language'=>'Tamil','rating'=>4.5],
  ['id'=>15,'title'=>'Naatu Naatu','artist'=>'M.M. Keeravani','album'=>'RRR','genre'=>'Folk','duration'=>'4:11','year'=>2022,'language'=>'Telugu','rating'=>4.9],
  ['id'=>16,'title'=>'Petta Theme','artist'=>'Anirudh Ravichander','album'=>'Petta','genre'=>'Mass','duration'=>'2:58','year'=>2019,'language'=>'Tamil','rating'=>4.7],
  ['id'=>17,'title'=>'Veyil Mele','artist'=>'Yuvan Shankar Raja','album'=>'Vinnaithaandi','genre'=>'Melody','duration'=>'5:05','year'=>2010,'language'=>'Tamil','rating'=>4.6],
  ['id'=>18,'title'=>'Oh Manapenne','artist'=>'D. Imman','album'=>'Harihara Veera Mallu','genre'=>'Classical','duration'=>'4:45','year'=>2023,'language'=>'Tamil','rating'=>4.4],
  ['id'=>19,'title'=>'Butta Bomma','artist'=>'Armaan Malik','album'=>'Ala Vaikunthapurramuloo','genre'=>'Melody','duration'=>'3:52','year'=>2020,'language'=>'Telugu','rating'=>4.7],
  ['id'=>20,'title'=>'Srivalli','artist'=>'Sid Sriram','album'=>'Pushpa','genre'=>'Melody','duration'=>'4:28','year'=>2021,'language'=>'Telugu','rating'=>4.8]
];

function searchSongs($lib, $query) {
    if (empty($query)) return $lib;
    return array_filter($lib, function($s) use ($query) {
        return stripos($s['title'], $query) !== false || stripos($s['artist'], $query) !== false;
    });
}

function filterByGenre($lib, $genre) {
    if (empty($genre)) return $lib;
    return array_filter($lib, fn($s) => $s['genre'] === $genre);
}

function sortSongs($lib, $sort) {
    usort($lib, function($a, $b) use ($sort) {
        if ($sort === 'rating') return $b['rating'] <=> $a['rating'];
        if ($sort === 'title') return strcmp($a['title'], $b['title']);
        if ($sort === 'year') return $b['year'] <=> $a['year'];
        return 0;
    });
    return $lib;
}

function renderStars($rating) {
    $full = floor($rating);
    $half = ($rating - $full) >= 0.5 ? 1 : 0;
    $stars = str_repeat('★', $full) . ($half ? '⯪' : '') . str_repeat('☆', 5 - $full - $half);
    return $stars;
}

$genres = array_keys(array_count_values(array_column($songLibrary, 'genre')));

$search = $_GET['search'] ?? '';
$genreFilter = $_GET['genre'] ?? '';
$sortBy = $_GET['sort'] ?? 'rating';

$results = searchSongs($songLibrary, $search);
$results = filterByGenre($results, $genreFilter);
$results = sortSongs($results, $sortBy);

$top5 = array_slice(sortSongs($songLibrary, 'rating'), 0, 5);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music Search Playlist</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="header">
        <h1>MuzikStream</h1>
    </header>
    <div class="container layout">
        <aside class="sidebar">
            <div class="card">
                <h3>Top 5 Featured</h3>
                <ul class="featured-list">
                    <?php foreach($top5 as $t): ?>
                        <li>
                            <div class="feat-title"><?= htmlspecialchars($t['title']) ?></div>
                            <div class="feat-artist"><?= htmlspecialchars($t['artist']) ?></div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="card mt-1">
                <h3>Genres</h3>
                <div class="genre-pills">
                    <a href="?search=<?= urlencode($search) ?>&sort=<?= $sortBy ?>" class="pill <?= empty($genreFilter) ? 'active' : '' ?>">All</a>
                    <?php foreach($genres as $g): ?>
                        <a href="?genre=<?= urlencode($g) ?>&search=<?= urlencode($search) ?>&sort=<?= $sortBy ?>" class="pill <?= $genreFilter===$g ? 'active' : '' ?>">
                            <?= htmlspecialchars($g) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </aside>

        <main class="main-content">
            <div class="controls card">
                <form action="index.php" method="GET" class="search-form">
                    <input type="text" name="search" placeholder="Search by title or artist..." value="<?= htmlspecialchars($search) ?>">
                    <input type="hidden" name="genre" value="<?= htmlspecialchars($genreFilter) ?>">
                    
                    <select name="sort" onchange="this.form.submit()">
                        <option value="rating" <?= $sortBy==='rating'?'selected':'' ?>>Highest Rated</option>
                        <option value="title" <?= $sortBy==='title'?'selected':'' ?>>Title (A-Z)</option>
                        <option value="year" <?= $sortBy==='year'?'selected':'' ?>>Newest First</option>
                    </select>
                    
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>

            <div class="songs-grid">
                <?php foreach($results as $song): ?>
                    <div class="song-card">
                        <div class="song-info">
                            <h2><?= htmlspecialchars($song['title']) ?></h2>
                            <p class="artist"><?= htmlspecialchars($song['artist']) ?></p>
                            <p class="album"><?= htmlspecialchars($song['album']) ?> (<?= $song['year'] ?>)</p>
                        </div>
                        <div class="song-meta">
                            <span class="genre-tag"><?= htmlspecialchars($song['genre']) ?></span>
                            <span class="duration"><?= $song['duration'] ?></span>
                            <div class="rating" title="<?= $song['rating'] ?>/5">
                                <?= renderStars($song['rating']) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if(empty($results)): ?>
                    <div class="card"><p>No songs found.</p></div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
