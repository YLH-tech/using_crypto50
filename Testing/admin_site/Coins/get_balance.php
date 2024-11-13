<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

// Get user's USDT balance using PDO
$user_id = $_SESSION['user_id'];
$query = "SELECT usdt FROM user_balances WHERE user_id = :user_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user_balance = $stmt->fetch(PDO::FETCH_ASSOC);
$available_usdt = $user_balance['usdt'] ?? 0.00;

echo json_encode(['status' => 'success', 'balance' => number_format($available_usdt, 6)]);
?>
