<?php
session_start();
require 'db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Coin pairs list
$coinPairs = [
    'BTCUSDT', 'ETHUSDT', 'BNBUSDT', 'XRPUSDT', 'DOGEUSDT', 'SOLUSDT', 'ADAUSDT', 'TRXUSDT', 
    'DOTUSDT', 'LTCUSDT', 'BCHUSDT', 'ETCUSDT', 'UNIUSDT', 'LINKUSDT', 'AVAXUSDT', 'NEOUSDT', 
    'EOSUSDT', 'ARBUSDT', 'APTUSDT', 'TONUSDT'
];

// Prepare SQL query to get order counts, profit/loss, and total USDT for each coin pair
$stmt = $pdo->prepare("
    SELECT SUM(amount * end_price) AS totalUSDT, 
           symbol, 
           COUNT(*) AS orderCount,
           SUM(CASE WHEN expected_pl > 0 THEN expected_pl ELSE 0 END) AS totalProfit,
           SUM(CASE WHEN expected_pl < 0 THEN expected_pl ELSE 0 END) AS totalLoss
    FROM orders
    WHERE symbol IN (" . implode(',', array_fill(0, count($coinPairs), '?')) . ")
    GROUP BY symbol
");
$stmt->execute($coinPairs);

// Fetch results for the order counts, total USDT, profit, and loss
$orderCounts = [];
$totalOrders = 0;
$totalUSDT = 0;
$totalProfit = 0;
$totalLoss = 0;
$orderIDs = [];

foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $orderCounts[$row['symbol']] = $row['orderCount'];
    $totalOrders += $row['orderCount'];
    $totalUSDT += $row['totalUSDT'];
    $totalProfit += $row['totalProfit'];
    $totalLoss += $row['totalLoss'];

    // Collect the order IDs for these orders to count distinct users later
    $query = $pdo->prepare("SELECT id FROM orders WHERE symbol = ? LIMIT ?");
    $query->execute([$row['symbol'], $row['orderCount']]);
    $orderIDs = array_merge($orderIDs, array_column($query->fetchAll(PDO::FETCH_ASSOC), 'id'));
}

// Now, fetch the total number of distinct users for all these orders
// We fetch distinct user IDs for the orders collected above
if (!empty($orderIDs)) {
    $userStmt = $pdo->prepare("
        SELECT COUNT(DISTINCT user_id) AS totalUsers
        FROM orders
        WHERE id IN (" . implode(',', $orderIDs) . ")
    ");
    $userStmt->execute();
    $userData = $userStmt->fetch(PDO::FETCH_ASSOC);
    $totalUsers = $userData['totalUsers'];
} else {
    $totalUsers = 0;
}

// Prepare final response with profit and loss information
$response = [
    'orderCounts' => $orderCounts,
    'totalOrders' => $totalOrders,
    'totalUSDT' => $totalUSDT,
    'totalProfit' => $totalProfit,
    'totalLoss' => $totalLoss,
    'totalUsers' => $totalUsers
];

header('Content-Type: application/json');
echo json_encode($response);
?>
