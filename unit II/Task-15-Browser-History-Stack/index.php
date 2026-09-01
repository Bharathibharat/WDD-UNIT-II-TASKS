<?php
session_start();
date_default_timezone_set('Asia/Kolkata');

if (!isset($_SESSION['historyStack'])) {
    $_SESSION['historyStack'] = [
        ['url' => 'https://www.google.com', 'title' => 'Google Search', 'category' => 'Work', 'time' => date('H:i')],
        ['url' => 'https://www.github.com', 'title' => 'GitHub', 'category' => 'Work', 'time' => date('H:i')],
        ['url' => 'https://www.youtube.com', 'title' => 'YouTube', 'category' => 'Entertainment', 'time' => date('H:i')],
    ];
    $_SESSION['forwardStack'] = [];
}

function visitPage(&$hist, &$fwd, $page) { array_push($hist, $page); $fwd = []; }
function goBack(&$hist, &$fwd) {
    if (count($hist) > 1) {
        $cur = array_pop($hist);
        array_push($fwd, $cur);
        return end($hist);
    }
    return null;
}
function goForward(&$hist, &$fwd) {
    if (!empty($fwd)) {
        $p = array_pop($fwd);
        array_push($hist, $p);
        return $p;
    }
    return null;
}
function currentPage($hist) { return end($hist); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['visit'])) {
        $page = [
            'url' => htmlspecialchars($_POST['url']),
            'title' => htmlspecialchars($_POST['title']),
            'category' => htmlspecialchars($_POST['category']),
            'time' => date('H:i')
        ];
        visitPage($_SESSION['historyStack'], $_SESSION['forwardStack'], $page);
    } elseif (isset($_POST['back'])) {
        goBack($_SESSION['historyStack'], $_SESSION['forwardStack']);
    } elseif (isset($_POST['forward'])) {
        goForward($_SESSION['historyStack'], $_SESSION['forwardStack']);
    } elseif (isset($_POST['home'])) {
        $page = ['url' => 'https://www.google.com', 'title' => 'Google Search', 'category' => 'Work', 'time' => date('H:i')];
        visitPage($_SESSION['historyStack'], $_SESSION['forwardStack'], $page);
    } elseif (isset($_POST['clear'])) {
        $_SESSION['historyStack'] = [['url' => 'https://www.google.com', 'title' => 'Google Search', 'category' => 'Work', 'time' => date('H:i')]];
        $_SESSION['forwardStack'] = [];
    }
    header("Location: index.php");
    exit();
}

$current = currentPage($_SESSION['historyStack']);
$historyReversed = array_reverse($_SESSION['historyStack']);
$categoryDist = [];
foreach ($_SESSION['historyStack'] as $p) {
    $categoryDist[$p['category']] = ($categoryDist[$p['category']] ?? 0) + 1;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browser History Stack</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="browser">
        <div class="browser-header">
            <form method="POST" class="nav-controls">
                <button type="submit" name="back" <?= count($_SESSION['historyStack']) <= 1 ? 'disabled' : '' ?>>&#8592;</button>
                <button type="submit" name="forward" <?= empty($_SESSION['forwardStack']) ? 'disabled' : '' ?>>&#8594;</button>
                <button type="submit" name="home">&#8962;</button>
            </form>
            <div class="url-bar"><?= htmlspecialchars($current['url'] ?? '') ?></div>
        </div>
        
        <div class="browser-content">
            <div class="current-page">
                <h2><?= htmlspecialchars($current['title'] ?? '') ?></h2>
                <p><?= htmlspecialchars($current['url'] ?? '') ?></p>
                <span class="badge"><?= htmlspecialchars($current['category'] ?? '') ?></span>
            </div>

            <div class="controls">
                <h3>Visit New Page</h3>
                <form method="POST" class="visit-form">
                    <input type="url" name="url" placeholder="https://..." required>
                    <input type="text" name="title" placeholder="Page Title" required>
                    <select name="category" required>
                        <option value="Work">Work</option>
                        <option value="Entertainment">Entertainment</option>
                        <option value="Social">Social</option>
                        <option value="News">News</option>
                    </select>
                    <button type="submit" name="visit">Visit</button>
                    <button type="submit" name="clear" class="btn-clear">Clear History</button>
                </form>
            </div>

            <div class="dashboard">
                <div class="history-list">
                    <h3>History Stack (Top is Newest)</h3>
                    <?php foreach ($historyReversed as $item): ?>
                        <div class="history-card">
                            <div class="time"><?= htmlspecialchars($item['time']) ?></div>
                            <div class="details">
                                <h4><?= htmlspecialchars($item['title']) ?></h4>
                                <a href="#"><?= htmlspecialchars($item['url']) ?></a>
                            </div>
                            <span class="badge"><?= htmlspecialchars($item['category']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="stats">
                    <h3>Category Distribution</h3>
                    <ul>
                        <?php foreach ($categoryDist as $cat => $count): ?>
                            <li><?= htmlspecialchars($cat) ?>: <?= $count ?></li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <h3>Forward Stack (<?= count($_SESSION['forwardStack']) ?>)</h3>
                    <ul>
                        <?php foreach (array_reverse($_SESSION['forwardStack']) as $f): ?>
                            <li><?= htmlspecialchars($f['title']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
