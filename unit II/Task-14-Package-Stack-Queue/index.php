<?php
session_start();
if (!isset($_SESSION['stack'])) $_SESSION['stack'] = [];
if (!isset($_SESSION['queue'])) $_SESSION['queue'] = [];
if (!isset($_SESSION['opLog'])) $_SESSION['opLog'] = [];

function logOp($msg) {
    array_unshift($_SESSION['opLog'], "[" . date('H:i:s') . "] " . $msg);
    if (count($_SESSION['opLog']) > 15) array_pop($_SESSION['opLog']);
}

// Stack Functions (LIFO)
function push(&$stack, $item) { array_push($stack, $item); }
function pop(&$stack) { return array_pop($stack); }
function peekStack($stack) { return end($stack); }
function stackSize($stack) { return count($stack); }

// Queue Functions (FIFO)
function enqueue(&$queue, $item) { array_push($queue, $item); }
function dequeue(&$queue) { return array_shift($queue); }
function peekQueue($queue) { return $queue[0] ?? null; }
function queueSize($queue) { return count($queue); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Stack Ops
    if (isset($_POST['push'])) {
        $pkg = ['id' => $_POST['s_id'], 'name' => $_POST['s_name']];
        push($_SESSION['stack'], $pkg);
        logOp("STACK PUSH: Package {$pkg['id']} ({$pkg['name']})");
    } elseif (isset($_POST['pop'])) {
        $popped = pop($_SESSION['stack']);
        if ($popped) logOp("STACK POP: Removed {$popped['id']}");
        else logOp("STACK POP FAILED: Stack is empty");
    }
    
    // Queue Ops
    elseif (isset($_POST['enqueue'])) {
        $pkg = ['id' => $_POST['q_id'], 'dest' => $_POST['q_dest']];
        enqueue($_SESSION['queue'], $pkg);
        logOp("QUEUE ENQUEUE: Package {$pkg['id']} to {$pkg['dest']}");
    } elseif (isset($_POST['dequeue'])) {
        $dq = dequeue($_SESSION['queue']);
        if ($dq) logOp("QUEUE DEQUEUE: Removed {$dq['id']}");
        else logOp("QUEUE DEQUEUE FAILED: Queue is empty");
    }

    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Package Stack & Queue - Task 14</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Warehouse Package Management</h1>
        <p>Demonstrating LIFO (Stack) and FIFO (Queue) concepts</p>
    </header>

    <div class="container">
        <!-- Stack Panel -->
        <div>
            <div class="card">
                <h2>Stack Operations (LIFO)</h2>
                <form method="POST">
                    <div style="display:flex; gap:10px;">
                        <div class="form-group" style="flex:1"><label>Package ID</label><input type="text" name="s_id" required></div>
                        <div class="form-group" style="flex:2"><label>Item Name</label><input type="text" name="s_name" required></div>
                    </div>
                    <div class="btn-group">
                        <button type="submit" name="push" class="btn">Push</button>
                        <button type="submit" name="pop" class="btn btn-pop" formnovalidate>Pop</button>
                    </div>
                </form>
                <div style="margin-top:1rem; text-align:center;">
                    Size: <?= stackSize($_SESSION['stack']) ?> | Top: <?= peekStack($_SESSION['stack']) ? peekStack($_SESSION['stack'])['id'] : 'None' ?>
                </div>
            </div>
            <div class="card" style="text-align:center;">
                <h3>Stack Visual (Top to Bottom)</h3>
                <div class="stack-visual">
                    <?php foreach($_SESSION['stack'] as $pkg): ?>
                        <div class="stack-item"><?= htmlspecialchars($pkg['id']) ?><br><small><?= htmlspecialchars($pkg['name']) ?></small></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Queue Panel -->
        <div>
            <div class="card">
                <h2>Queue Operations (FIFO)</h2>
                <form method="POST">
                    <div style="display:flex; gap:10px;">
                        <div class="form-group" style="flex:1"><label>Package ID</label><input type="text" name="q_id" required></div>
                        <div class="form-group" style="flex:2"><label>Destination</label><input type="text" name="q_dest" required></div>
                    </div>
                    <div class="btn-group">
                        <button type="submit" name="enqueue" class="btn">Enqueue</button>
                        <button type="submit" name="dequeue" class="btn btn-pop" formnovalidate>Dequeue</button>
                    </div>
                </form>
                <div style="margin-top:1rem; text-align:center;">
                    Size: <?= queueSize($_SESSION['queue']) ?> | Front: <?= peekQueue($_SESSION['queue']) ? peekQueue($_SESSION['queue'])['id'] : 'None' ?>
                </div>
            </div>
            <div class="card">
                <h3>Queue Visual (Front &rarr; Rear)</h3>
                <div class="queue-visual">
                    <?php if(empty($_SESSION['queue'])): ?>
                        <div style="color:#999; margin:auto;">Queue empty</div>
                    <?php else: ?>
                        <?php foreach($_SESSION['queue'] as $pkg): ?>
                            <div class="queue-item"><?= htmlspecialchars($pkg['id']) ?><br><small><?= htmlspecialchars($pkg['dest']) ?></small></div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Bottom Panel -->
        <div class="bottom-panel">
            <div class="card">
                <h2>Operation Log</h2>
                <div class="log-box">
                    <?php foreach($_SESSION['opLog'] as $log): ?>
                        <p><?= htmlspecialchars($log) ?></p>
                    <?php endforeach; ?>
                    <?php if(empty($_SESSION['opLog'])) echo "<p>No operations yet.</p>"; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
