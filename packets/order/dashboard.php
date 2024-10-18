<?php
// Include database connection
include('db.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch user data
$user_id = $_SESSION['user_id'];
$user_query = $conn->prepare("SELECT * FROM users WHERE id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user_data = $user_query->get_result()->fetch_assoc();

// Handle buy/sell order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_type = $_POST['order_type'];
    $trading_pair = $_POST['trading_pair'];
    $amount = $_POST['amount'];
    $price = $_POST['price'];
    $fee = 0.1; // For example, a fixed 0.1% fee

    $stmt = $conn->prepare("INSERT INTO orders (user_id, order_type, trading_pair, amount, price, fee) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issddd", $user_id, $order_type, $trading_pair, $amount, $price, $fee);
    $stmt->execute();
}

// Fetch orders
$order_query = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$order_query->bind_param("i", $user_id);
$order_query->execute();
$order_result = $order_query->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <style>
        /* Add some basic styles */
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .balance {
            margin-bottom: 20px;
        }
        .orders {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>

    <div class="balance">
        <h2>Your Balances</h2>
        <p>USDT Balance: <?php echo $user_data['balance_usdt']; ?></p>
        <p>BTC Balance: <?php echo $user_data['balance_btc']; ?></p>
        <p>ETH Balance: <?php echo $user_data['balance_eth']; ?></p>
    </div>

    <h2>Place an Order</h2>
    <form method="POST">
        <label for="trading_pair">Trading Pair:</label>
        <select name="trading_pair" id="trading_pair">
            <option value="BTC/USDT">BTC/USDT</option>
            <option value="ETH/USDT">ETH/USDT</option>
        </select>

        <label for="amount">Amount:</label>
        <input type="number" name="amount" id="amount" placeholder="Enter amount" required>

        <label for="price">Price (USDT):</label>
        <input type="number" name="price" id="price" placeholder="Enter price" required>

        <button type="submit" name="order_type" value="Buy">Buy</button>
        <button type="submit" name="order_type" value="Sell">Sell</button>
    </form>

    <div class="orders">
        <h2>Your Orders</h2>
        <table>
            <thead>
                <tr>
                    <th>Order Type</th>
                    <th>Trading Pair</th>
                    <th>Amount</th>
                    <th>Price</th>
                    <th>Fee</th>
                    <th>Status</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $order_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $order['order_type']; ?></td>
                        <td><?php echo $order['trading_pair']; ?></td>
                        <td><?php echo $order['amount']; ?></td>
                        <td><?php echo $order['price']; ?></td>
                        <td><?php echo $order['fee']; ?></td>
                        <td><?php echo $order['status']; ?></td>
                        <td><?php echo $order['created_at']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
