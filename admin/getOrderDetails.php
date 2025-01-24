<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'db.php';

// Get the selected symbol and time range
$symbol = isset($_GET['symbol']) ? $_GET['symbol'] : '';
$timeRange = isset($_GET['time_range']) ? $_GET['time_range'] : 'today'; // Default to today if not selected

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

// Modify query based on whether we are querying for total orders or a specific symbol
if ($symbol) {
    $query = "SELECT SUM(amount * end_price) AS totalUSDT, 
                     COUNT(DISTINCT user_id) AS totalUsers,
                     SUM(CASE WHEN expected_pl > 0 THEN expected_pl ELSE 0 END) AS totalProfit,
                     SUM(CASE WHEN expected_pl < 0 THEN expected_pl ELSE 0 END) AS totalLoss
              FROM orders
              WHERE symbol = ? $dateCondition";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$symbol]);
} else {
    // Total orders query for all symbols
    $query = "SELECT SUM(amount * end_price) AS totalUSDT, 
                     COUNT(DISTINCT user_id) AS totalUsers,
                     SUM(CASE WHEN expected_pl > 0 THEN expected_pl ELSE 0 END) AS totalProfit,
                     SUM(CASE WHEN expected_pl < 0 THEN expected_pl ELSE 0 END) AS totalLoss
              FROM orders
              WHERE 1=1 $dateCondition"; // No symbol filter for total
    $stmt = $pdo->prepare($query);
    $stmt->execute();
}

$data = $stmt->fetch(PDO::FETCH_ASSOC);

$response = [
    'totalUSDT' => $data['totalUSDT'] ?? 0,
    'totalUsers' => $data['totalUsers'] ?? 0,
    'totalProfit' => $data['totalProfit'] ?? 0,
    'totalLoss' => $data['totalLoss'] ?? 0
];

header('Content-Type: application/json');
echo json_encode($response);
?>
