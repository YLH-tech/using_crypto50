<?php
session_start();
include 'db.php';
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


if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

// Clear history if the admin clicks "Clear"
if (isset($_POST['clear_history_admin'])) {
    $pdo->query("UPDATE transactions SET hidden_admin = 1 "); // Only hide for admin, keep visible for user
    header("Location: admin_dashboard.php"); // Refresh the page after clearing
    exit();
}

// Fetch pending coin requests
$requests_stmt = $pdo->query("SELECT cr.*, u.username FROM coin_requests cr JOIN users u ON cr.user_id = u.id WHERE cr.status = 'pending'");
$requests = $requests_stmt->fetchAll();
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
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="deposit_management.css">
    <!-- <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
                <a href="#">
                    <i class="fa fa-wallet"></i> Deposits
                    <span class="badge"><?= $pending_requests_count > 0 ? $pending_requests_count : 0 ?></span>
                </a>
            </li>
            <li>
                <a href="withdraw_management.php">
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

        <!-- Display Pending Coin Requests -->
        <h2>Pending Coin Requests</h2>
        <?php if (count($requests) > 0): ?>
            <table border="1">
                <tr>
                    <th>User</th>
                    <th>Coin Type</th>
                    <th>Amount</th>
                    <th>Image</th>
                    <th>Admin Note</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($requests as $request): ?>
                    <tr>
                        <td><?= $request['username'] ?></td>
                        <td><?= strtoupper($request['coin_type']) ?></td>
                        <td><?= $request['amount'] ?></td>
                        <td><img src="<?= $request['image_path'] ?>" alt="Thumbnail" class="thumbnail" onclick="showImage('<?= $request['image_path'] ?>')">
                            <div class="overlay" id="overlay" onclick="hideImage()"></div>
                            <div class="large-image" id="largeImage">
                                <img src="<?= $request['image_path'] ?>" alt="Large Image" id="imgLarge">
                            </div>
                        </td>
                        <td>
                            <form action="approve_requests.php" method="post">
                                <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                                <input type="hidden" name="user_id" value="<?= $request['user_id'] ?>">
                                <input type="hidden" name="coin_type" value="<?= $request['coin_type'] ?>">
                                <input type="hidden" name="amount" value="<?= $request['amount'] ?>">
                                <label for="notes">Note for user:</label>
                                <select id="notes" name="admin_note">
                                    <option value="deposit_rejected">Deposit rejected! The input data does not meet our requirements. Please check and resubmit.</option>
                                    <option value="deposit_approved" selected>Deposit Approved! Coins have been added successfully</option>
                                </select>
                        </td>
                        <td>
                            <button type="submit" name="action" value="approve">Approve</button>
                            <button type="submit" name="action" value="reject">Reject</button>
                        </td>
                            </form>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No pending requests.</p>
        <?php endif; ?>

        <!-- Display Transaction and Approval History Including Admin Notes -->
        <h2>Transaction and Approval History</h2>
        <form method="post">
            <button type="submit" name="clear_history_admin" onclick="return confirm('Are you sure you want to clear the transaction history from your view?');">Clear History</button>
        </form><br>
        <?php
        // Fetch transaction and approval history (only records visible to admin)
        $history_stmt = $pdo->query("SELECT t.*, u.username FROM transactions t JOIN users u ON t.user_id = u.id WHERE t.hidden_admin = 0 ORDER BY t.timestamp DESC");
        $history = $history_stmt->fetchAll();
        ?>
        <table border="1">
            <tr>
                <th>User</th>
                <th>Action</th>
                <th>Coin Type</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Admin Note</th>
                <th>Date</th>
            </tr>
            <?php foreach ($history as $entry): ?>
                <tr>
                    <td><?= $entry['username'] ?></td>
                    <td><?= ucfirst($entry['action']) ?></td>
                    <td><?= strtoupper($entry['coin_type']) ?></td>
                    <td><?= $entry['amount'] ?></td>
                    <td><?= ucfirst($entry['status']) ?></td>
                    <td><?= nl2br($entry['admin_note']) ?></td>
                    <td><?= $entry['timestamp'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <script>
        function showImage(imageSrc) {
            document.getElementById('imgLarge').src = imageSrc;
            document.getElementById('largeImage').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        }

        function hideImage() {
            document.getElementById('largeImage').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        }
    </script>
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
