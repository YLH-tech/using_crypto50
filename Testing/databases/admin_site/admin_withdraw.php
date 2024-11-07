<?php
session_start();
require 'db.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if the admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Clear history if the admin clicks "Clear"
if (isset($_POST['clear_history_admin'])) {
    $stmt = $pdo->prepare("UPDATE withdrawal_requests SET hidden_admin = 1"); // Only hide for admin, keep visible for user
    if ($stmt->execute()) {
        // If the update was successful, redirect to the admin page
        header("Location: admin_withdraw.php"); // Refresh the page after clearing
        exit();
    } else {
        // Handle any errors
        $errorMessage = "Failed to clear history.";
    }
}

// Fetch all withdrawal requests
$stmt = $pdo->prepare("SELECT * FROM withdrawal_requests WHERE hidden_admin = 0 ORDER BY created_at DESC");
$stmt->execute();
$withdrawalHistory = $stmt->fetchAll();

// Success message initialization
$successMessage = "";

// Process approval or rejection of withdrawal requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requestId = $_POST['request_id'] ?? null;
    $action = $_POST['action'] ?? null;

    if ($requestId && $action) {
        // Fetch the specific withdrawal request and the user's current balance for the specified coin
        $stmt = $pdo->prepare("SELECT wr.*, ub.btc, ub.eth, ub.usdt, ub.usdc 
                               FROM withdrawal_requests wr 
                               JOIN user_balances ub ON wr.user_id = ub.user_id 
                               WHERE wr.id = ?");
        $stmt->execute([$requestId]);
        $request = $stmt->fetch();

        if ($request) {
            if ($action === 'approve') {
                // Calculate new balance after deduction if approved
                $coin = strtolower($request['coin']);
                $newBalance = $request[$coin] - $request['amount'];

                // Update the user balance for the specific coin
                $stmt = $pdo->prepare("UPDATE user_balances 
                                       SET $coin = ? 
                                       WHERE user_id = ?");
                $stmt->execute([$newBalance, $request['user_id']]);

                // Mark the withdrawal request as approved
                $stmt = $pdo->prepare("UPDATE withdrawal_requests 
                                       SET status = 'Approved and Please check your Wallet' 
                                       WHERE id = ?");
                $stmt->execute([$requestId]);

                header('location:admin_withdraw.php');
            } elseif ($action === 'reject') {
                // Mark the withdrawal request as rejected
                $stmt = $pdo->prepare("UPDATE withdrawal_requests 
                                       SET status = 'Rejected and Server Maintaining' 
                                       WHERE id = ?");
                $stmt->execute([$requestId]);

                header('location:admin_withdraw.php');
            }
        } else {
            $errorMessage = "Request not found.";
        }
    } else {
        $errorMessage = "Invalid request parameters.";
    }
}

// Fetch pending withdrawal requests
$stmt = $pdo->prepare("SELECT wr.*, u.username, ub.btc, ub.eth, ub.usdt, ub.usdc 
                       FROM withdrawal_requests wr 
                       JOIN users u ON wr.user_id = u.id 
                       JOIN user_balances ub ON wr.user_id = ub.user_id 
                       WHERE wr.status = 'Waiting a few movement'");
$stmt->execute();
$requests = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        button {
            margin-left: 10px;
            background-color: #4CAF50; /* Green */
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>
    <title>Admin - Approve Withdrawals</title>
</head>
<body>
    <h1>Pending Withdrawal Requests</h1>

    <?php if (isset($successMessage)) : ?>
        <p style="color: green;"><?= $successMessage; ?></p>
    <?php elseif (isset($errorMessage)) : ?>
        <p style="color: red;"><?= $errorMessage; ?></p>
    <?php endif; ?>

    <table border="1">
        <thead>
            <tr>
                <th>User</th>
                <th>Coin</th>
                <th>Amount</th>
                <th>Wallet address</th>
                <th>Service Charge</th>
                <th>Net Amount</th>
                <th>Current Balance</th>
                <th>Balance After Deduction</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($requests as $request): ?>
                <?php 
                $currentBalance = $request[strtolower($request['coin'])];
                $balanceAfterDeduction = $currentBalance - $request['amount'];
                ?>
                <tr>
                    <td><?= htmlspecialchars($request['username']); ?></td>
                    <td><?= htmlspecialchars($request['coin']); ?></td>
                    <td><?= htmlspecialchars($request['amount']); ?></td>
                    <td>
                        <span><?= htmlspecialchars($request['wallet_address']); ?></span>
                        <button onclick="copyToClipboard('<?= htmlspecialchars($request['wallet_address']); ?>')">Copy</button>
                    </td>
                    <td><?= htmlspecialchars($request['service_charge']); ?></td>
                    <td><?= htmlspecialchars($request['net_amount']); ?></td>
                    <td><?= htmlspecialchars($currentBalance); ?></td>
                    <td><?= htmlspecialchars($balanceAfterDeduction); ?></td>
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="request_id" value="<?= $request['id']; ?>">
                            <button type="submit" name="action" value="approve">Approve</button>
                            <button type="submit" name="action" value="reject">Reject</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <script>
        function copyToClipboard(text) {
            const input = document.createElement('input');
            input.setAttribute('value', text);
            document.body.appendChild(input);
            input.select();
            document.execCommand('copy');
            document.body.removeChild(input);
            alert('Wallet address copied to clipboard: ' + text);
        }
    </script>
    <h1>All Withdraw History</h1>
    <form method="post">
        <button type="submit" name="clear_history_admin" onclick="return confirm('Are you sure you want to clear the transaction history from your view?');">Clear History</button>
    </form>
    <br>
    <table border="1">
        <tr>
            <th>Date</th>
            <th>User ID</th>
            <th>Coin</th>
            <th>Amount</th>
            <th>Wallet Address</th>
            <th>Service Charge</th>
            <th>Net Amount</th>
            <th>Status</th>
        </tr>
        <?php foreach ($withdrawalHistory as $request): ?>
            <tr>
                <td><?php echo $request['created_at']; ?></td>
                <td><?php echo htmlspecialchars($request['user_id']); ?></td>
                <td><?php echo htmlspecialchars($request['coin']); ?></td>
                <td><?php echo number_format($request['amount'], 4); ?></td>
                <td><?php echo htmlspecialchars($request['wallet_address']); ?></td>
                <td><?php echo number_format($request['service_charge'], 4); ?></td>
                <td><?php echo number_format($request['net_amount'], 4); ?></td>
                <td><?php echo htmlspecialchars($request['status']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
