<?php
session_start();
include 'db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit();
}

// Get the profit/loss value from the request
$data = json_decode(file_get_contents('php://input'), true);
$profit_loss = $data['profit_loss'] ?? 0;

// Ensure profit/loss is a numeric value
if (!is_numeric($profit_loss)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid profit/loss value.']);
    exit();
}

// Get the user's ID from the session
$user_id = $_SESSION['user_id'];

// Update user's balance in the user_balances table
$query = "UPDATE user_balances SET usdt = usdt + :profit_loss WHERE user_id = :user_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':profit_loss', $profit_loss, PDO::PARAM_STR); // You can change this to PDO::PARAM_INT if you ensure your input is an integer
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Balance updated successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update balance.']);
}
?>
