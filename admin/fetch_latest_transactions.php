<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

// Fetch the latest 10 transactions with the associated user name and allow status
$latestTransactionsStmt = $pdo->query("
    SELECT o.id, o.symbol, o.order_type, o.amount, o.starting_price, o.end_price, o.expected_pl, o.created_at, u.username, u.allow
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
    LIMIT 10
");
$latestTransactions = $latestTransactionsStmt->fetchAll();
echo json_encode($latestTransactions);
?>
