<?php
session_start();
include 'db.php';

// Redirect if not logged in or not a user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Clear history if the user clicks "Clear"
if (isset($_POST['clear_history_user'])) {
    $stmt = $pdo->prepare("UPDATE transactions SET hidden_user = 1 WHERE user_id = ? "); // Only hide for user, keep visible for admin
    $stmt->execute([$user_id]);
    header("Location: user_dashboard.php"); // Refresh the page after clearing
    exit();
}

// Fetch user balance
$stmt = $pdo->prepare("SELECT * FROM user_balances WHERE user_id = ?");
$stmt->execute([$user_id]);
$balance = $stmt->fetch();

// Fetch transaction history (only records visible to user)
$history_stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? AND hidden_user = 0 ORDER BY timestamp DESC");
$history_stmt->execute([$user_id]);
$transactions = $history_stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <style>
.camera-box {
    display: inline-block;
    width: 100px;
    height: 100px;
    background-color: #f0f0f0;
    border: 2px dashed #ccc;
    border-radius: 10px;
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    font-size: 24px;
    color: #555;
    background-size: cover; /* Ensure background image covers the box */
    background-position: center; /* Center the image */
}
.camera-box:hover {
    background-color: #e0e0e0;
}
</style>

</head>
<body>
    <h1>Welcome, User</h1>

    <h2>Your Coin Balances</h2>
    <ul>
        <li>BTC: <?= $balance['BTC'] ?></li>
        <li>ETH: <?= $balance['ETH'] ?></li>
        <li>USDT: <?= $balance['USDT'] ?></li>
        <li>USDC: <?= $balance['USDC'] ?></li>
    </ul>

    <h2>Request More Coins</h2>
    <form action="request_coin.php" method="post" enctype="multipart/form-data">
        <label>Coin Type:
            <select name="coin_type" required>
                <option value="BTC">BTC</option>
                <option value="ETH">ETH</option>
                <option value="USDT">USDT</option>
                <option value="USDC">USDC</option>
            </select>
        </label>
        <label>Amount: <input type="number" step="0.0001" name="amount" required></label>
        <br><br>
        <label for="imageInput" class="camera-box" id="cameraBox">
        <span>📷</span> <!-- Initial camera icon -->
        <input type="file" name="image" id="imageInput" accept="image/*" style="display: none;" required>
    </label>
    <br>
    <img id="imagePreview" src="#" alt="Image Preview" style="display: none; max-width: 200px; margin-top: 10px;">
    
        <button type="submit">Request</button>
    </form>
    <h2>Transaction History</h2>
    <form method="post">
        <button type="submit" name="clear_history_user" onclick="return confirm('Are you sure you want to clear your transaction history?');">Clear History</button>
    </form><br>

    
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
                <td><?= nl2br($transaction['admin_note']) ?></td>
                <td><?= $transaction['timestamp'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <script>
document.getElementById('imageInput').onchange = function (event) {
    const [file] = event.target.files;
    if (file) {
        const cameraBox = document.getElementById('cameraBox');
        const reader = new FileReader();

        reader.onload = function (e) {
            cameraBox.style.backgroundImage = `url(${e.target.result})`; // Set the image as background
            cameraBox.querySelector('span').style.display = 'none'; // Hide the camera icon
            cameraBox.style.backgroundColor = 'transparent'; // Optional: Make background transparent
        };

        reader.readAsDataURL(file);
    }
};
</script>



</body>
</html>
