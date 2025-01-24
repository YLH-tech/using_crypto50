<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);


if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

require 'db.php'; // Include your database connection
require 'db_connection.php';  // Include the MySQLi connection file

$currentPasswordChangeTime = $_SESSION['last_password_change'];  // Store the last password change timestamp in session

// Query to fetch the last password change timestamp from the database
$query = "SELECT last_password_change FROM admin_users";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

if ($row) {
    // Compare the stored timestamp with the one from the database
    if ($currentPasswordChangeTime !== $row['last_password_change']) {
        // If the timestamps don't match, the password has been changed, so force login again
        session_unset();  // Unset all session variables
        session_destroy();  // Destroy the session
        header("Location: admin_login.php?error=Password has been changed. Please log in again.");
        exit;  // Stop further execution
    }
}



// Clear history if the admin clicks "Clear"
if (isset($_POST['clear_history_admin'])) {
    $stmt = $pdo->prepare("UPDATE withdrawal_requests SET hidden_admin = 1"); // Only hide for admin, keep visible for user
    if ($stmt->execute()) {
        header("Location: withdraw_management.php"); // Refresh the page after clearing
        exit();
    } else {
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
        $stmt = $pdo->prepare("SELECT wr.*, ub.btc, ub.eth, ub.usdt, ub.usdc
                               FROM withdrawal_requests wr 
                               JOIN user_balances ub ON wr.user_id = ub.user_id 
                               WHERE wr.id = ?");
        $stmt->execute([$requestId]);
        $request = $stmt->fetch();

        if ($request) {
            if ($action === 'approve') {
                $coin = strtolower($request['coin']);
                $newBalance = $request[$coin] - $request['amount'];

                $stmt = $pdo->prepare("UPDATE user_balances SET $coin = ? WHERE user_id = ?");
                $stmt->execute([$newBalance, $request['user_id']]);

                $stmt = $pdo->prepare("UPDATE withdrawal_requests SET status = 'Approved and Please check your Wallet' WHERE id = ?");
                $stmt->execute([$requestId]);

                header('location:withdraw_management.php');
            } elseif ($action === 'reject') {
                $stmt = $pdo->prepare("UPDATE withdrawal_requests SET status = 'Rejected and Server Maintaining' WHERE id = ?");
                $stmt->execute([$requestId]);

                header('location:withdraw_management.php');
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

// Fetch the count of pending coin requests
$pending_requests_count_stmt = $pdo->query("SELECT COUNT(*) as count FROM coin_requests WHERE status = 'pending'");
$pending_requests_count = $pending_requests_count_stmt->fetch()['count'];

// Fetch the count of pending coin requests
$waiting_requests_count_stmt = $pdo->query("SELECT COUNT(*) as count FROM withdrawal_requests WHERE status = 'Waiting a few movement'");
$waiting_requests_count = $waiting_requests_count_stmt->fetch()['count'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="withdraw_management.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Admin Dashboard: Withdrawal Requests</title>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>Admin Dashboard</h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="user_management.php"><i class="fa fa-users"></i> User Management</a></li>
            <li><a href="order_dashboard.php"><i class="fa fa-shopping-cart"></i> Orders</a></li>
            <li>
                <a href="deposit_management.php">
                    <i class="fa fa-wallet"></i> Deposits
                    <span class="badge"><?= $pending_requests_count > 0 ? $pending_requests_count : 0 ?></span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fa fa-money-check-alt"></i> Withdraws
                    <span class="badge"><?= $waiting_requests_count > 0 ? $waiting_requests_count : 0 ?></span>
                </a>
            </li>
            <li><a href="settings.php"><i class="fa fa-cog"></i> Settings</a></li>
            <!-- Logout Confirmation Modal -->
            <div id="logoutModal" class="modal">
                <div class="modal-content">
                    <h3>Are you sure you want to log out?</h3>
                    <div class="modal-buttons">
                        <button id="confirmLogout">OK</button>
                        <button id="cancelLogout">Cancel</button>
                    </div>
                </div>
            </div>

            <!-- Logout Button -->
            <li><a href="#" id="logoutBtn"><i class="fa fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <h1>Welcome, Admin</h1>
        <div class="container">
            <!-- Pending Requests Table -->
            <h2>Pending Requests</h2>
            <?php if (empty($requests)): ?>
                <p class="no-data-message">No pending requests</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Coin</th>
                            <th>Amount</th>
                            <th>Wallet Address</th>
                            <th>Service Charge</th>
                            <th>Net Amount</th>
                            <th>Current Balance</th>
                            <th>After Deduction</th>
                            <th>Actions</th>
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
            <?php endif; ?>

            <!-- All Withdrawal History Table -->
            <h2>All Withdrawal History</h2>
            <?php if (empty($withdrawalHistory)): ?>
                <p class="no-data-message">No transaction history</p>
            <?php else: ?>
                <a class="clear-btn" href="#" onclick="return confirm('Are you sure you want to clear the transaction history?');">Clear History</a>
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
            <?php endif; ?>

        </div>
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
    <!-- script for logout  -->
<!-- script for logout  -->
<script>
    // Get modal and buttons
const logoutModal = document.getElementById('logoutModal');
const logoutBtn = document.getElementById('logoutBtn');
const confirmLogoutBtn = document.getElementById('confirmLogout');
const cancelLogoutBtn = document.getElementById('cancelLogout');

// Show the modal when the "Logout" button is clicked
logoutBtn.addEventListener('click', function(event) {
    event.preventDefault(); // Prevent default link behavior
    logoutModal.style.display = 'block'; // Show the modal
});

// Close the modal when the "Cancel" button is clicked
cancelLogoutBtn.addEventListener('click', function() {
    logoutModal.style.display = 'none'; // Hide the modal
});

// Logout and redirect to admin_login.php when the "OK" button is clicked
confirmLogoutBtn.addEventListener('click', function() {
    window.location.href = 'logout.php'; // Redirect to logout.php
});
</script>
</body>
</html>
