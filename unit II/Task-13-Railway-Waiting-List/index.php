<?php
session_start();
if (!isset($_SESSION['waitingList'])) $_SESSION['waitingList'] = [];
if (!isset($_SESSION['confirmedList'])) $_SESSION['confirmedList'] = [];
if (!isset($_SESSION['availableSeats'])) $_SESSION['availableSeats'] = 5;
if (!isset($_SESSION['cancelledList'])) $_SESSION['cancelledList'] = [];

function addToWaiting(&$wl, $p) {
    array_push($wl, $p); // FIFO
}

function confirmPassenger(&$confirmed, &$seats, $p) {
    $p['pnr'] = 'PNR' . rand(1000000, 9999999);
    $p['seat'] = 'S' . str_pad(count($confirmed) + 1, 2, '0', STR_PAD_LEFT);
    array_push($confirmed, $p);
    $seats--;
}

function cancelTicket(&$confirmed, &$wl, &$seats, &$cancelled, $pnr) {
    $found = false;
    foreach ($confirmed as $key => $p) {
        if ($p['pnr'] === $pnr) {
            $cancelled[] = $p;
            unset($confirmed[$key]);
            $confirmed = array_values($confirmed); // reindex
            $seats++;
            $found = true;
            break;
        }
    }
    
    if ($found && !empty($wl)) {
        $promoted = array_shift($wl); // Dequeue first waiting list person
        confirmPassenger($confirmed, $seats, $promoted);
    }
    return $found;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['book'])) {
        $passenger = [
            'name' => $_POST['name'],
            'age' => $_POST['age'],
            'gender' => $_POST['gender'],
            'class' => $_POST['class'],
            'source' => $_POST['source'],
            'dest' => $_POST['dest']
        ];
        
        if ($_SESSION['availableSeats'] > 0) {
            confirmPassenger($_SESSION['confirmedList'], $_SESSION['availableSeats'], $passenger);
        } else {
            addToWaiting($_SESSION['waitingList'], $passenger);
        }
    } elseif (isset($_POST['cancel'])) {
        cancelTicket($_SESSION['confirmedList'], $_SESSION['waitingList'], $_SESSION['availableSeats'], $_SESSION['cancelledList'], trim($_POST['pnr']));
    } elseif (isset($_POST['clear'])) {
        session_destroy();
        session_start();
        $_SESSION['waitingList'] = [];
        $_SESSION['confirmedList'] = [];
        $_SESSION['availableSeats'] = 5;
        $_SESSION['cancelledList'] = [];
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
    <title>Railway Waiting List - Task 13</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Indian Railways Ticket Booking</h1>
        <p>Dynamic Waiting List Management</p>
    </header>

    <div class="container">
        <!-- Left Panel: Actions -->
        <div>
            <div class="train-info">
                <div>
                    <h2>Tamil Nadu Express</h2>
                    <p>No: 12622 | Chennai → Delhi</p>
                </div>
                <div class="seats-badge">
                    Seats: <?= $_SESSION['availableSeats'] ?>
                </div>
            </div>

            <div class="card">
                <h2>Book Ticket</h2>
                <form method="POST">
                    <div class="form-group"><label>Passenger Name</label><input type="text" name="name" required></div>
                    <div style="display:flex; gap:10px;">
                        <div class="form-group" style="flex:1;"><label>Age</label><input type="number" name="age" required></div>
                        <div class="form-group" style="flex:1;"><label>Gender</label>
                            <select name="gender" required><option value="M">M</option><option value="F">F</option></select>
                        </div>
                    </div>
                    <div class="form-group"><label>Class</label>
                        <select name="class" required><option value="Sleeper">Sleeper (SL)</option><option value="3AC">3 Tier AC (3A)</option></select>
                    </div>
                    <div style="display:flex; gap:10px;">
                        <div class="form-group" style="flex:1;"><label>Source</label><input type="text" name="source" value="MAS" required></div>
                        <div class="form-group" style="flex:1;"><label>Dest</label><input type="text" name="dest" value="NDLS" required></div>
                    </div>
                    <button type="submit" name="book" class="btn">Book Ticket</button>
                </form>
            </div>

            <div class="card">
                <h2>Cancel Ticket</h2>
                <form method="POST">
                    <div class="form-group"><label>PNR Number</label><input type="text" name="pnr" placeholder="Enter PNR" required></div>
                    <button type="submit" name="cancel" class="btn btn-cancel">Cancel Ticket</button>
                </form>
            </div>
            
            <form method="POST">
                <button type="submit" name="clear" class="btn btn-clear">Clear System</button>
            </form>
        </div>

        <!-- Right Panel: Data -->
        <div>
            <div class="card">
                <h2>Confirmed Passengers (<?= count($_SESSION['confirmedList']) ?>)</h2>
                <?php if(empty($_SESSION['confirmedList'])): ?>
                    <p style="color:#666;">No confirmed passengers.</p>
                <?php else: ?>
                    <table class="data-table">
                        <tr><th>PNR</th><th>Seat</th><th>Name</th><th>Class</th></tr>
                        <?php foreach($_SESSION['confirmedList'] as $p): ?>
                        <tr>
                            <td><strong><?= $p['pnr'] ?></strong></td>
                            <td><?= $p['seat'] ?></td>
                            <td><?= htmlspecialchars($p['name']) ?> (<?= $p['age'] ?>/<?= $p['gender'] ?>)</td>
                            <td><?= $p['class'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>
            </div>

            <div class="card">
                <h2>Waiting List (WL <?= count($_SESSION['waitingList']) ?>)</h2>
                <?php if(empty($_SESSION['waitingList'])): ?>
                    <p style="color:#666;">Waiting list is empty.</p>
                <?php else: ?>
                    <div class="wl-queue">
                        <?php foreach($_SESSION['waitingList'] as $i => $p): ?>
                        <div class="wl-item">
                            <div>
                                <strong><?= htmlspecialchars($p['name']) ?></strong> (<?= $p['age'] ?>/<?= $p['gender'] ?>) - <?= $p['class'] ?><br>
                                <small><?= $p['source'] ?> &rarr; <?= $p['dest'] ?></small>
                            </div>
                            <div class="wl-pos">WL <?= $i + 1 ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
