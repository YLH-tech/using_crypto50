<?php
session_start();
require '../php/database/db.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if the admin is logged in
// if (!isset($_SESSION['user_id'])) {
//     header('Location: adminlogin.php');
//     exit;
// }

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
                

                // Mark the withdrawal request as approved
                $stmt = $pdo->prepare("UPDATE withdrawal_requests 
                                       SET status = 'Approved and Please check your Wallet' 
                                       WHERE id = ?");
                $stmt->execute([$requestId]);

                header('location:admin_withdraw.php');
            } elseif ($action === 'reject') {
                
                // Calculate new balance after deduction if approved
                $coin = strtolower($request['coin']);
                $newBalance = $request[$coin] + $request['amount'];

                // Update the user balance for the specific coin
                $stmt = $pdo->prepare("UPDATE user_balances 
                                       SET $coin = ? 
                                       WHERE user_id = ?");
                $stmt->execute([$newBalance, $request['user_id']]);

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
    <!-- <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet"> -->
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
        }
        h1 {
            text-align: center;
            color: #444;
            margin: 20px 0;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        thead {
            background-color: #4CAF50;
            color: #fff;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            text-transform: uppercase;
            font-weight: 700;
        }
        tbody tr:hover {
            background-color: #f1f1f1;
        }
        button {
            padding: 8px 12px;
            margin: 5px;
            font-size: 14px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button.approve {
            background-color: #4CAF50;
            color: #fff;
        }
        button.approve:hover {
            background-color: #45a049;
        }
        button.reject {
            background-color: #e74c3c;
            color: #fff;
        }
        button.reject:hover {
            background-color: #c0392b;
        }
        button.copy {
            background-color: #3498db;
            color: #fff;
        }
        button.copy:hover {
            background-color: #2980b9;
        }
        .clear-btn {
            display: inline-block;
            margin: 10px 0;
            background-color: #f39c12;
            color: #fff;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .clear-btn:hover {
            background-color: #d35400;
        }
        .info {
            text-align: center;
            margin-top: 20px;
            color: #777;
        }
    </style>
    <title>Admin - Withdrawal Management</title>
</head>
<body>
    <h1>Admin Dashboard: Withdrawal Requests</h1>
    <div class="container">
        <h2>Pending Requests</h2>
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Coin</th>
                    <th>Amount</th>
                    <th>Wallet Address</th>
                    <th>Service Charge</th>
                    <th>Net Amount</th>
                    <th>Remaining Balance</th>
                    <th>Before Deduction</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $request): ?>
                    <?php 
                    $currentBalance = $request[strtolower($request['coin'])];
                    $balanceAfterDeduction = $currentBalance + $request['amount'];
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($request['username']); ?></td>
                        <td><?= htmlspecialchars($request['coin']); ?></td>
                        <td><?= htmlspecialchars($request['amount']); ?></td>
                        <td>
                            <span><?= htmlspecialchars($request['wallet_address']); ?></span>
                            <button class="copy" onclick="copyToClipboard('<?= htmlspecialchars($request['wallet_address']); ?>')">Copy</button>
                        </td>
                        <td><?= htmlspecialchars($request['service_charge']); ?></td>
                        <td><?= htmlspecialchars($request['net_amount']); ?></td>
                        <td><?= htmlspecialchars($currentBalance); ?></td>
                        <td><?= htmlspecialchars($balanceAfterDeduction); ?></td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="request_id" value="<?= $request['id']; ?>">
                                <button type="submit" name="action" value="approve" class="approve">Approve</button>
                                <button type="submit" name="action" value="reject" class="reject">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>All Withdrawal History</h2>
        <form method="post">
          <button type="submit" name="clear_history_admin" onclick="return confirm('Are you sure you want to clear the transaction history from your view?');">Clear History</button>
        </form><br>
            <table>
            <thead>
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
            </thead>
            <tbody>
                <?php foreach ($withdrawalHistory as $request): ?>
                    <tr>
                        <td><?= $request['created_at']; ?></td>
                        <td><?= htmlspecialchars($request['user_id']); ?></td>
                        <td><?= htmlspecialchars($request['coin']); ?></td>
                        <td><?= number_format($request['amount'], 4); ?></td>
                        <td><?= htmlspecialchars($request['wallet_address']); ?></td>
                        <td><?= number_format($request['service_charge'], 4); ?></td>
                        <td><?= number_format($request['net_amount'], 4); ?></td>
                        <td><?= htmlspecialchars($request['status']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        function copyToClipboard(text) {
            const input = document.createElement('input');
            input.value = text;
            document.body.appendChild(input);
            input.select();
            document.execCommand('copy');
            document.body.removeChild(input);
            alert('Wallet address copied: ' + text);
        }
    </script>
</body>
</html>

