<?php
session_start();
include 'db.php';

// Redirect if not logged in or not a user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user balance
$stmt = $pdo->prepare("SELECT * FROM user_balances WHERE user_id = ?");
$stmt->execute([$user_id]);
$balance = $stmt->fetch();

// Fetch transaction history
$history_stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY timestamp DESC");
$history_stmt->execute([$user_id]);
$transactions = $history_stmt->fetchAll();
?>

<!DOCTYPE html>
<html>

<head>
    <title>User Dashboard</title>
</head>

<body>
    <h1>Welcome, User</h1>

    <h2>Your Coin Balances</h2>
    <ul>
        <li>USDT: <?= $balance['usdt'] ?></li>
        <li>ETH: <?= $balance['eth'] ?></li>
        <li>BTC: <?= $balance['btc'] ?></li>
        <li>BND: <?= $balance['bnd'] ?></li>
    </ul>

    <h2>Request More Coins</h2>
    <form action="request_coin.php" method="post" enctype="multipart/form-data">
        <label>Coin Type:
            <select name="coin_type" required>
                <option value="usdt">USDT</option>
                <option value="eth">ETH</option>
                <option value="btc">BTC</option>
                <option value="bnd">BND</option>
            </select>
        </label>
        <label>Amount: <input type="number" step="0.0001" name="amount" required></label>
        <label>Image: <input type="file" name="image" required></label>
        <button type="submit">Request</button>
    </form>

    <h2>Transaction History</h2>
    <table border="1">
        <tr>
            <th>Action</th>
            <th>Coin Type</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Admin Note</th>
            <th>Date</th>
        </tr>
        <?php foreach ($transactions as $transaction): ?>
            <tr>
                <td><?= ucfirst($transaction['action']) ?></td>
                <td><?= strtoupper($transaction['coin_type']) ?></td>
                <td><?= $transaction['amount'] ?></td>
                <td><?= ucfirst($transaction['status']) ?></td>
                <td><?= nl2br($transaction['admin_note']) ?></td> <!-- Shows note if present -->
                <td><?= $transaction['timestamp'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>