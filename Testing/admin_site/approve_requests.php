<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = $_POST['request_id'];
    $user_id = $_POST['user_id'];
    $coin_type = $_POST['coin_type'];
    $amount = $_POST['amount'];
    $action = $_POST['action'];
    $admin_note = $_POST['admin_note'];

    try {
        $pdo->beginTransaction();

        if ($action === 'approve') {
            
            // Update user's balance
            $update_balance = $pdo->prepare("UPDATE user_balances SET $coin_type = $coin_type + ? WHERE user_id = ?");
            $update_balance->execute([$amount, $user_id]);

            // Update request status to 'approved'
            $update_request = $pdo->prepare("UPDATE coin_requests SET status = 'approved', admin_note = ? WHERE id = ?");
            $update_request->execute([$admin_note, $request_id]);

            // Log transaction as approved
            $log_transaction = $pdo->prepare("INSERT INTO transactions (user_id, action, coin_type, amount, status, admin_note) VALUES (?, 'request', ?, ?, 'approved', ?)");
            $log_transaction->execute([$user_id, $coin_type, $amount, $admin_note]);
        } elseif ($action === 'reject') {
            // Update request status to 'rejected'
            $update_request = $pdo->prepare("UPDATE coin_requests SET status = 'rejected', admin_note = ? WHERE id = ?");
            $update_request->execute([$admin_note, $request_id]);

            // Log transaction as rejected
            $log_transaction = $pdo->prepare("INSERT INTO transactions (user_id, action, coin_type, amount, status, admin_note) VALUES (?, 'request', ?, ?, 'rejected', ?)");
            $log_transaction->execute([$user_id, $coin_type, $amount, $admin_note]);
        }

        $pdo->commit();
        header("Location:admin_dashboard.php");
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error processing request: " . $e->getMessage();
    }
} else {
    header("Location: admin_dashboard.php");
}
?>
