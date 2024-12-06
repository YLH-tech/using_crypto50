<?php
session_start();
require '../database/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch user balance
$stmt = $pdo->prepare("SELECT * FROM user_balances WHERE user_id = ?");
$stmt->execute([$userId]);
$balance = $stmt->fetch();

// Fetch withdrawal history
$stmt = $pdo->prepare("SELECT * FROM withdrawal_requests WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$userId]);
$withdrawalHistory = $stmt->fetchAll();

$error = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $coin = $_POST['coin'];
    $amount = (float)$_POST['amount'];
    $walletAddress = $_POST['wallet_address'];
    $fundPasswordInput = $_POST['fund_password'];

    if ($amount <= 0) {
        $error = "Withdrawal amount cannot be zero.";
    } else {
        // Calculate service charge and net amount
        $serviceCharge = 0.01 * $amount;
        $netAmount = $amount - $serviceCharge;

        // Check for sufficient balance
        if ($balance[strtoupper($coin)] < $amount) {
            $error = "Insufficient balance for this withdrawal.";
        } else {
            // Fetch stored fund password
            $stmt = $pdo->prepare("SELECT fund_password FROM fund_passwords WHERE user_id = ?");
            $stmt->execute([$userId]);
            $fundPasswordHash = $stmt->fetchColumn();

            // Verify fund password
            if (password_verify($fundPasswordInput, $fundPasswordHash)) {

                // Update user's balance
                $stmt = $pdo->prepare("UPDATE user_balances SET $coin = $coin - ? WHERE user_id = ?");
                $stmt->execute([$amount, $userId]);
                // Insert withdrawal request
                $stmt = $pdo->prepare("INSERT INTO withdrawal_requests (user_id, coin, amount, wallet_address, service_charge, net_amount) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$userId, $coin, $amount, $walletAddress, $serviceCharge, $netAmount]);
                $_SESSION['successMsg'] = "Your request is successfully";
                header('location:withdraw_eth.php');
               
            } else {
                $_SESSION['successMsg'] = "Invalid fund password.Please check you create fund password or You forgot this.You can go to update fund password";
                header('location:withdraw_eth.php');
            }
        }
    }
}

// Output success or error messages
if ($error) {
    echo "Error: " . $error;
}