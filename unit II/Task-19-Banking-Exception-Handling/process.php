<?php
session_start();

class BankingException extends Exception {}

class InsufficientFundsException extends BankingException {
    private float $available;
    private float $requested;
    public function __construct(float $avail, float $req) {
        $this->available = $avail;
        $this->requested = $req;
        parent::__construct('Insufficient funds.');
    }
    public function getShortfall(): float {
        return $this->requested - $this->available;
    }
    public function getDetails(): string {
        return "Available: ₹" . number_format($this->available, 2) . ", Requested: ₹" . number_format($this->requested, 2) . " (Shortfall: ₹" . number_format($this->getShortfall(), 2) . ")";
    }
}

class InvalidAmountException extends BankingException {
    public function __construct() {
        parent::__construct('Invalid transaction amount. Amount must be greater than zero.');
    }
}

class FrozenAccountException extends BankingException {
    public function __construct($accId) {
        parent::__construct("Account $accId is frozen. Transactions are not allowed.");
    }
}

class InvalidAccountException extends BankingException {
    public function __construct($accId) {
        parent::__construct("Account $accId does not exist.");
    }
}

function processTransaction($type, $source, $amount, $target = null) {
    if (!isset($_SESSION['accounts'][$source])) {
        throw new InvalidAccountException($source);
    }
    if ($amount <= 0) {
        throw new InvalidAmountException();
    }
    
    $srcAcc = &$_SESSION['accounts'][$source];
    
    if ($srcAcc['status'] === 'Frozen') {
        throw new FrozenAccountException($source);
    }

    if ($type === 'Deposit') {
        $srcAcc['balance'] += $amount;
    } elseif ($type === 'Withdrawal') {
        if ($srcAcc['balance'] < $amount) {
            throw new InsufficientFundsException($srcAcc['balance'], $amount);
        }
        $srcAcc['balance'] -= $amount;
    } elseif ($type === 'Transfer') {
        if (!isset($_SESSION['accounts'][$target])) {
            throw new InvalidAccountException($target);
        }
        $tgtAcc = &$_SESSION['accounts'][$target];
        if ($tgtAcc['status'] === 'Frozen') {
            throw new FrozenAccountException($target);
        }
        if ($srcAcc['balance'] < $amount) {
            throw new InsufficientFundsException($srcAcc['balance'], $amount);
        }
        $srcAcc['balance'] -= $amount;
        $tgtAcc['balance'] += $amount;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? '';
    $source = $_POST['source'] ?? '';
    $amount = floatval($_POST['amount'] ?? 0);
    $target = $_POST['target'] ?? '';
    
    $status = 'Failed';
    try {
        processTransaction($type, $source, $amount, $target);
        $_SESSION['msg'] = "Transaction Successful: $type of ₹" . number_format($amount, 2);
        $_SESSION['msg_type'] = 'success-msg';
        $status = 'Success';
    } catch (InsufficientFundsException $e) {
        $_SESSION['msg'] = "Transaction Failed: " . $e->getMessage();
        $_SESSION['msg_detail'] = $e->getDetails();
        $_SESSION['msg_type'] = 'error-msg fund-error';
    } catch (BankingException $e) {
        $_SESSION['msg'] = "Transaction Failed: " . $e->getMessage();
        $_SESSION['msg_type'] = 'error-msg';
    } catch (Exception $e) {
        $_SESSION['msg'] = "System Error: " . $e->getMessage();
        $_SESSION['msg_type'] = 'error-msg sys-error';
    } finally {
        $_SESSION['transactions'][] = [
            'time' => date('Y-m-d H:i:s'),
            'type' => $type,
            'source' => $source . ($type === 'Transfer' ? " -> $target" : ""),
            'amount' => $amount,
            'status' => $status
        ];
        header("Location: index.php");
        exit();
    }
}
