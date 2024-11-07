<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);
$profit_loss = floatval($data['profit_loss']);

// Update user balance in the database
$query = "UPDATE user_balances SET usdt = usdt + :profit_loss WHERE user_id = :user_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':profit_loss', $profit_loss, PDO::PARAM_STR);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

if ($stmt->execute()) {
    // Fetch the updated balance
    $stmt = $pdo->prepare("SELECT usdt FROM user_balances WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user_balance = $stmt->fetch(PDO::FETCH_ASSOC);
    $updated_balance = $user_balance['usdt'] ?? 0.00;

    echo json_encode(['status' => 'success', 'message' => 'Balance updated successfully.', 'balance' => $updated_balance]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update balance.']);
}
?>
