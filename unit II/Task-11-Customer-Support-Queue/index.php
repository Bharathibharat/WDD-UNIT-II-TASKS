<?php
session_start();
if (!isset($_SESSION['queue'])) $_SESSION['queue'] = [];
if (!isset($_SESSION['served'])) $_SESSION['served'] = null;

// Queue operations
function enqueue(&$queue, $customer) {
    $customer['ticketId'] = 'TKT' . str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT);
    $customer['timestamp'] = date('H:i:s');
    
    // Priority queue logic (High priority jumps to front)
    if ($customer['priority'] === 'High') {
        array_unshift($queue, $customer);
    } else {
        array_push($queue, $customer); // FIFO for others
    }
    
    // Update positions
    foreach ($queue as $i => &$item) {
        $item['position'] = $i + 1;
    }
}

function dequeue(&$queue) {
    // FIFO behavior: remove from front
    return array_shift($queue);
}

function clearQueue(&$queue) {
    $queue = [];
}

// Handle requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $customer = [
            'name' => $_POST['name'],
            'category' => $_POST['category'],
            'priority' => $_POST['priority'],
            'contact' => $_POST['contact']
        ];
        enqueue($_SESSION['queue'], $customer);
    } elseif (isset($_POST['serve'])) {
        $_SESSION['served'] = dequeue($_SESSION['queue']);
    } elseif (isset($_POST['clear'])) {
        clearQueue($_SESSION['queue']);
        $_SESSION['served'] = null;
    }
    
    // Redirect to prevent form resubmission
    header("Location: index.php");
    exit;
}

$queue = $_SESSION['queue'];
$total = count($queue);
$highCount = count(array_filter($queue, fn($q) => $q['priority'] === 'High'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Support Queue - Task 11</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Customer Support Queue</h1>
        <p>FIFO operations with Priority override</p>
    </header>

    <div class="container">
        <!-- Left Panel: Form -->
        <div>
            <div class="card">
                <h2>Add Customer</h2>
                <form method="POST">
                    <div class="form-group">
                        <label>Customer Name</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Issue Category</label>
                        <select name="category" required>
                            <option value="Billing">Billing</option>
                            <option value="Technical">Technical</option>
                            <option value="Sales">Sales</option>
                            <option value="General">General</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Priority</label>
                        <select name="priority" required>
                            <option value="Low">Low</option>
                            <option value="Medium">Medium</option>
                            <option value="High">High</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Contact Info</label>
                        <input type="text" name="contact" required>
                    </div>
                    <button type="submit" name="add" class="btn">Add to Queue</button>
                </form>
            </div>
            
            <div class="card">
                <h2>Queue Controls</h2>
                <form method="POST">
                    <button type="submit" name="serve" class="btn btn-success" <?= empty($queue) ? 'disabled' : '' ?>>Serve Next Customer</button>
                    <button type="submit" name="clear" class="btn btn-danger">Clear Queue</button>
                </form>
            </div>
        </div>

        <!-- Right Panel: Queue Visualization -->
        <div>
            <div class="queue-stats">
                <div class="stat">Waiting: <?= $total ?></div>
                <div class="stat">High Priority: <?= $highCount ?></div>
            </div>

            <?php if ($_SESSION['served']): ?>
            <div class="served-card">
                <h3 style="color:#2e7d32; margin-bottom:5px;">Currently Serving</h3>
                <strong><?= htmlspecialchars($_SESSION['served']['ticketId']) ?> - <?= htmlspecialchars($_SESSION['served']['name']) ?></strong><br>
                <small><?= htmlspecialchars($_SESSION['served']['category']) ?> | <?= htmlspecialchars($_SESSION['served']['priority']) ?></small>
            </div>
            <?php endif; ?>

            <div class="card">
                <h2>Current Queue</h2>
                <div class="queue-visualization">
                    <?php if (empty($queue)): ?>
                        <p style="text-align:center; color:#777; padding:20px;">Queue is empty.</p>
                    <?php else: ?>
                        <?php foreach ($queue as $ticket): ?>
                        <div class="ticket-card priority-<?= $ticket['priority'] ?>">
                            <div class="ticket-info">
                                <h4><?= htmlspecialchars($ticket['ticketId']) ?> - <?= htmlspecialchars($ticket['name']) ?></h4>
                                <p>Issue: <?= htmlspecialchars($ticket['category']) ?> | Added: <?= $ticket['timestamp'] ?></p>
                                <span class="badge <?= $ticket['priority'] ?>"><?= $ticket['priority'] ?> Priority</span>
                            </div>
                            <div class="ticket-pos">#<?= $ticket['position'] ?></div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
