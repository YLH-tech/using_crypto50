<?php
require 'db.php';

$response = [];

// Fetch total users
$totalUsersStmt = $pdo->query("SELECT COUNT(*) FROM users");
$response['totalUsers'] = (int)$totalUsersStmt->fetchColumn();

// Fetch total transactions
$totalTransactionsStmt = $pdo->query("SELECT COUNT(*) FROM transactions");
$response['totalTransactions'] = (int)$totalTransactionsStmt->fetchColumn();

// Fetch total balance (USDT)
$totalBalanceStmt = $pdo->query("SELECT SUM(usdt) FROM user_balances");
$response['totalBalance'] = (float)($totalBalanceStmt->fetchColumn() ?: 0);

header('Content-Type: application/json');
echo json_encode($response);
?>
