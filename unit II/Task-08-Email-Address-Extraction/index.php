<?php
/**
 * Task 08 - Email Address Extraction
 * CS23C10 - Web Design and Development - Unit II
 * Demonstrates: Regular expressions (preg_match_all, preg_quote),
 *               array functions (array_unique, array_filter, array_count_values),
 *               string functions (explode, filter_var)
 */

// ─── Functions ───────────────────────────────────────────────────────────────

/**
 * Extract all email-like strings from raw text using preg_match_all
 */
function extractEmails(string $text): array {
    // Regex to match email patterns in text
    preg_match_all(
        '/[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}/',
        $text,
        $matches
    );
    return $matches[0];
}

/**
 * Separate emails into valid and invalid using filter_var
 */
function validateEmails(array $emails): array {
    $valid   = [];
    $invalid = [];
    foreach ($emails as $email) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $valid[] = $email;
        } else {
            $invalid[] = $email;
        }
    }
    return ['valid' => $valid, 'invalid' => $invalid];
}

/**
 * Group emails by domain and count occurrences
 */
function analyzeByDomain(array $emails): array {
    // Extract domain part (after @) using explode
    $domains = array_map(fn($e) => strtolower(explode('@', $e)[1] ?? ''), $emails);
    // Count each domain using array_count_values
    $counts  = array_count_values($domains);
    arsort($counts); // Sort by count descending
    return $counts;
}

// ─── Process form submission ──────────────────────────────────────────────────

$rawText        = '';
$extracted      = [];
$unique         = [];
$valid          = [];
$invalid        = [];
$domainAnalysis = [];
$processed      = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['raw_text'])) {
    $rawText   = $_POST['raw_text'];
    $extracted = extractEmails($rawText);

    // Deduplicate using array_unique
    $unique = array_values(array_unique($extracted));

    // Validate each unique email
    $validation = validateEmails($unique);
    $valid      = $validation['valid'];
    $invalid    = $validation['invalid'];

    // Domain analysis on valid emails
    $domainAnalysis = analyzeByDomain($valid);

    $processed = true;
}

$sampleText = 'Dear Team,\n\nPlease send your reports to reports@company.com and cc manager@office.org.\nFor support queries, contact support@helpdesk.in or support@helpdesk.in (same).\nIntern applications: hr.intern@startup.co.in and hiring@jobs.net.\nInvalid ones: user@, @nodomain.com, notanemail.\nExternal partners: john.doe@gmail.com, jane_smith99@yahoo.com, alice.bob@outlook.com.\n\nThanks,\nadmin@example.com';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task 08 – Email Address Extraction | CS23C10 Unit II</title>
    <meta name="description" content="Extract, validate and analyze email addresses from raw text using PHP regular expressions.">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<header class="header">
    <div class="header-inner">
        <div class="header-badge">Task 08</div>
        <h1>Email Address Extractor</h1>
        <p>Extract, validate &amp; analyse email addresses from any text using PHP Regular Expressions</p>
        <div class="concepts">
            <span>preg_match_all</span>
            <span>array_unique</span>
            <span>filter_var</span>
            <span>array_count_values</span>
        </div>
    </div>
</header>

<main class="container">

    <!-- Input Form -->
    <div class="card">
        <h2 class="card-title">📋 Paste Your Text</h2>
        <p class="card-desc">Paste any block of text that may contain email addresses. The extractor will find, deduplicate, validate and categorise them by domain.</p>
        <form action="index.php" method="POST">
            <textarea name="raw_text" id="raw_text" rows="8"
                placeholder="Paste text containing email addresses here..."
                required><?= htmlspecialchars($rawText) ?></textarea>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">🔍 Extract Emails</button>
                <button type="reset" class="btn btn-secondary">↺ Clear</button>
            </div>
        </form>

        <div class="sample-box">
            <strong>📌 Sample Text (copy and paste above):</strong>
            <pre><?= htmlspecialchars(str_replace('\n', "\n", $sampleText)) ?></pre>
        </div>
    </div>

    <?php if ($processed): ?>

    <!-- Summary Stats -->
    <div class="stats-grid">
        <div class="stat-card stat-blue">
            <div class="stat-num"><?= count($extracted) ?></div>
            <div class="stat-label">Total Found</div>
        </div>
        <div class="stat-card stat-purple">
            <div class="stat-num"><?= count($unique) ?></div>
            <div class="stat-label">Unique Emails</div>
        </div>
        <div class="stat-card stat-green">
            <div class="stat-num"><?= count($valid) ?></div>
            <div class="stat-label">Valid Emails</div>
        </div>
        <div class="stat-card stat-red">
            <div class="stat-num"><?= count($invalid) ?></div>
            <div class="stat-label">Invalid Emails</div>
        </div>
        <div class="stat-card stat-orange">
            <div class="stat-num"><?= count($domainAnalysis) ?></div>
            <div class="stat-label">Unique Domains</div>
        </div>
    </div>

    <?php if (count($valid) > 0): ?>

    <!-- Valid Emails as Chips -->
    <div class="card">
        <h2 class="card-title">✅ Valid Extracted Emails (<?= count($valid) ?>)</h2>
        <div class="chip-container">
            <?php foreach ($valid as $email): ?>
                <span class="chip chip-green"><?= htmlspecialchars($email) ?></span>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Domain Analysis -->
    <div class="card">
        <h2 class="card-title">🌐 Domain Analysis</h2>
        <p class="card-desc">Top domain: <strong><?= htmlspecialchars(array_key_first($domainAnalysis)) ?></strong>
            with <?= reset($domainAnalysis) ?> email(s).</p>
        <table class="data-table">
            <thead>
                <tr><th>#</th><th>Domain</th><th>Count</th><th>Percentage</th><th>Bar</th></tr>
            </thead>
            <tbody>
                <?php
                $rank = 1;
                foreach ($domainAnalysis as $domain => $count):
                    $pct = round($count / count($valid) * 100, 1);
                ?>
                <tr>
                    <td><?= $rank++ ?></td>
                    <td><strong><?= htmlspecialchars($domain) ?></strong></td>
                    <td><?= $count ?></td>
                    <td><?= $pct ?>%</td>
                    <td>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width:<?= $pct ?>%"></div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Copyable output -->
    <div class="card">
        <h2 class="card-title">📋 Copy All Valid Emails</h2>
        <textarea class="copy-area" readonly><?= htmlspecialchars(implode("\n", $valid)) ?></textarea>
    </div>

    <?php endif; ?>

    <?php if (count($invalid) > 0): ?>
    <!-- Invalid Emails Warning -->
    <div class="card">
        <h2 class="card-title">⚠️ Invalid / Suspicious Emails (<?= count($invalid) ?>)</h2>
        <p class="card-desc">These were matched by the regex pattern but failed PHP's <code>filter_var(FILTER_VALIDATE_EMAIL)</code> check.</p>
        <div class="chip-container">
            <?php foreach ($invalid as $email): ?>
                <span class="chip chip-red"><?= htmlspecialchars($email) ?></span>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (empty($extracted)): ?>
    <div class="alert alert-info">
        ℹ️ No email addresses were found in the submitted text. Try the sample text above.
    </div>
    <?php endif; ?>

    <?php endif; ?>

    <!-- Concept Explanation -->
    <div class="card concept-card">
        <h2 class="card-title">💡 PHP Concepts Used</h2>
        <div class="concept-grid">
            <div class="concept-item">
                <h4>preg_match_all()</h4>
                <p>Finds ALL occurrences of a regex pattern in a string and stores them in an array.</p>
            </div>
            <div class="concept-item">
                <h4>array_unique()</h4>
                <p>Removes duplicate values from an array, keeping the first occurrence of each value.</p>
            </div>
            <div class="concept-item">
                <h4>filter_var()</h4>
                <p>Validates data using built-in PHP filters. <code>FILTER_VALIDATE_EMAIL</code> checks RFC 5322 compliance.</p>
            </div>
            <div class="concept-item">
                <h4>array_count_values()</h4>
                <p>Counts how many times each value appears in an array, returning an associative count array.</p>
            </div>
        </div>
    </div>

</main>

<footer class="footer">
    <p>CS23C10 – Web Design and Development | Unit II | Task 08 – Email Address Extraction</p>
</footer>
</body>
</html>
