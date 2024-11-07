<?php
session_start();
include 'db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Get order data from POST request
$data = json_decode(file_get_contents('php://input'), true);

$symbol = $data['symbol'] ?? '';
$amount = $data['amount'] ?? 0.0;
$starting_price = $data['starting_price'] ?? 0.0;
$end_price = $data['end_price'] ?? 0.0;
$expected_pl = $data['expected_pl'] ?? 0.0;

// Prepare the SQL statement
$query = "INSERT INTO orders (user_id, symbol, amount, starting_price, end_price, expected_pl, created_at) 
          VALUES (:user_id, :symbol, :amount, :starting_price, :end_price, :expected_pl, NOW())";

$stmt = $pdo->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindParam(':symbol', $symbol);
$stmt->bindParam(':amount', $amount);
$stmt->bindParam(':starting_price', $starting_price);
$stmt->bindParam(':end_price', $end_price);
$stmt->bindParam(':expected_pl', $expected_pl);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Order saved successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to save order']);
}
?>
