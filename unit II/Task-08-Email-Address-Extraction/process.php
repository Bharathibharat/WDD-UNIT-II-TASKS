<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Extraction Results</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Extraction Results</h1>
        <p><a href="index.php" style="color:white;">&larr; Back to Extractor</a></p>
    </header>

    <div class="container">
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $raw_text = $_POST['raw_text'] ?? '';
            
            function extractEmails($text) {
                // Extracts potential email strings based on pattern
                $pattern = '/[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}/';
                preg_match_all($pattern, $text, $matches);
                return $matches[0];
            }

            function validateEmails($emails) {
                $valid = [];
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

            function analyzeByDomain($emails) {
                $domains = [];
                foreach ($emails as $email) {
                    $parts = explode('@', $email);
                    if (count($parts) === 2) {
                        $domains[] = strtolower($parts[1]);
                    }
                }
                return array_count_values($domains);
            }

            // Processing
            $extracted = extractEmails($raw_text);
            $total_found = count($extracted);
            
            // Deduplicate
            $unique_emails = array_unique($extracted);
            $unique_count = count($unique_emails);
            
            // Validate
            $validation = validateEmails($unique_emails);
            $valid_emails = $validation['valid'];
            $invalid_emails = $validation['invalid'];
            
            // Analyze Domains
            $domain_stats = analyzeByDomain($valid_emails);
            arsort($domain_stats); // Sort by count descending
            
            ?>
            
            <div class="stats-grid">
                <div class="stat-badge">
                    <h3><?= htmlspecialchars($total_found) ?></h3>
                    <p>Total Found</p>
                </div>
                <div class="stat-badge">
                    <h3><?= htmlspecialchars($unique_count) ?></h3>
                    <p>Unique Emails</p>
                </div>
                <div class="stat-badge" style="border-color: #2e7d32;">
                    <h3 style="color: #2e7d32;"><?= htmlspecialchars(count($valid_emails)) ?></h3>
                    <p>Valid</p>
                </div>
                <div class="stat-badge" style="border-color: #c62828;">
                    <h3 style="color: #c62828;"><?= htmlspecialchars(count($invalid_emails)) ?></h3>
                    <p>Invalid</p>
                </div>
            </div>

            <div class="card">
                <h2>Extracted Emails</h2>
                <div class="chips-container">
                    <?php foreach ($valid_emails as $email): ?>
                        <span class="chip"><?= htmlspecialchars($email) ?></span>
                    <?php endforeach; ?>
                    <?php foreach ($invalid_emails as $email): ?>
                        <span class="chip invalid"><?= htmlspecialchars($email) ?> (Invalid)</span>
                    <?php endforeach; ?>
                </div>
                
                <?php if (count($valid_emails) > 0): ?>
                    <textarea readonly style="min-height: 100px;"><?= htmlspecialchars(implode("\n", $valid_emails)) ?></textarea>
                    <small>Copy valid emails from above</small>
                <?php endif; ?>
            </div>

            <div class="card">
                <h2>Domain Analysis (Valid Emails)</h2>
                <?php if (!empty($domain_stats)): ?>
                    <table class="domain-table">
                        <thead>
                            <tr>
                                <th>Domain Name</th>
                                <th>Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($domain_stats as $domain => $count): ?>
                                <tr>
                                    <td><?= htmlspecialchars($domain) ?></td>
                                    <td><?= htmlspecialchars($count) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No valid domains found.</p>
                <?php endif; ?>
            </div>

            <?php
        } else {
            echo "<div class='card'><p>No data submitted.</p></div>";
        }
        ?>
    </div>
</body>
</html>
