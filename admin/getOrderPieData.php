<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require 'db.php'; // Include your database connection

// Get the selected time range from the GET request
$timeRange = isset($_GET['time_range']) ? $_GET['time_range'] : 'today'; // Default to 'today' if not selected

// Calculate the date range based on the selected option
switch ($timeRange) {
    case '7days':
        $dateCondition = "AND orders.created_at >= CURDATE() - INTERVAL 7 DAY";
        break;
    case '30days':
        $dateCondition = "AND orders.created_at >= CURDATE() - INTERVAL 30 DAY";
        break;
    case 'today':
    default:
        $dateCondition = "AND DATE(orders.created_at) = CURDATE()";
        break;
}

// Fetch buy and sell order counts
$buySellQuery = $pdo->prepare("
    SELECT order_type, COUNT(*) as count
    FROM orders
    WHERE 1 $dateCondition
    GROUP BY order_type
");
$buySellQuery->execute();
$buySellData = $buySellQuery->fetchAll(PDO::FETCH_ASSOC);

$buyCount = 0;
$sellCount = 0;
foreach ($buySellData as $row) {
    if ($row['order_type'] === 'Buy') {
        $buyCount = $row['count'];
    } elseif ($row['order_type'] === 'Sell') {
        $sellCount = $row['count'];
    }
}

// Fetch profit and loss order counts
$profitLossQuery = $pdo->prepare("
    SELECT CASE 
               WHEN (end_price > starting_price) THEN 'profit' 
               ELSE 'loss' 
           END as result, COUNT(*) as count
    FROM orders
    WHERE 1 $dateCondition
    GROUP BY result
");
$profitLossQuery->execute();
$profitLossData = $profitLossQuery->fetchAll(PDO::FETCH_ASSOC);

$profitCount = 0;
$lossCount = 0;
foreach ($profitLossData as $row) {
    if ($row['result'] === 'profit') {
        $profitCount = $row['count'];
    } elseif ($row['result'] === 'loss') {
        $lossCount = $row['count'];
    }
}

// Return the data as JSON for use in pie charts
echo json_encode([
    'buyCount' => $buyCount,
    'sellCount' => $sellCount,
    'profitCount' => $profitCount,
    'lossCount' => $lossCount
]);
?>
