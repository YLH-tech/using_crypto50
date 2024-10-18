<?php
session_start();
require 'db.php';
require 'User.php';
require 'Transaction.php';
require 'utils.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user = new User();
$transaction = new Transaction();
$currencyRates = getStaticCurrencyRates();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fromCurrency = $_POST['from_currency'];
    $toCurrency = $_POST['to_currency'];
    $amount = $_POST['amount'];
    $userId = $_SESSION['user_id'];

    $fromRate = $currencyRates[$fromCurrency];
    $toRate = $currencyRates[$toCurrency];
    $convertedAmount = ($amount * $fromRate) / $toRate;

    if ($transaction->recordTransaction($userId, $fromCurrency, $toCurrency, $amount, $convertedAmount)) {
        $message = "Converted $amount $fromCurrency to $convertedAmount $toCurrency.";
    } else {
        $message = "Transaction failed.";
    }
}

$transactions = $transaction->getTransactionHistory($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Exchange</title>
</head>
<body>
    <h1>Currency Exchange</h1>
    <form method="post">
        <select name="from_currency">
            <option value="BTC">BTC</option>
            <option value="ETH">ETH</option>
            <option value="USDT">USDT</option>
            <option value="BND">BND</option>
        </select>
        <select name="to_currency">
            <option value="BTC">BTC</option>
            <option value="ETH">ETH</option>
            <option value="USDT">USDT</option>
            <option value="BND">BND</option>
        </select>
        <input type="number" name="amount" step="any">
        <button type="submit">Convert</button>
    </form>

    <!-- Display result message -->
    <?php if (isset($message)) { ?>
        <p><?php echo $message; ?></p>
    <?php } ?>

    <h2>Transaction History</h2>
    <table border="1">
        <thead>
            <tr>
                <th>From Currency</th>
                <th>To Currency</th>
                <th>Amount</th>
                <th>Converted Amount</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $transaction) { ?>
                <tr>
                    <td><?php echo $transaction['from_currency']; ?></td>
                    <td><?php echo $transaction['to_currency']; ?></td>
                    <td><?php echo $transaction['amount']; ?></td>
                    <td><?php echo $transaction['converted_amount']; ?></td>
                    <td><?php echo $transaction['timestamp']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>
