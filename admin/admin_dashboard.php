<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../php/database/db.php';

// Redirect if not logged in or not an admin
// if (!isset($_SESSION['user_id'])) {
//      header("Location: adminlogin.php");
//      exit();
// }

// Clear history if the admin clicks "Clear"
if (isset($_POST['clear_history_admin'])) {
    $pdo->query("UPDATE transactions SET hidden_admin = 1 "); // Only hide for admin, keep visible for user
    header("Location: admin_dashboard.php"); // Refresh the page after clearing
    exit();
}

// Fetch pending coin requests
$requests_stmt = $pdo->query("SELECT cr.*, u.username FROM coin_requests cr JOIN users u ON cr.user_id = u.id WHERE cr.status = 'pending'");
$requests = $requests_stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        .thumbnail { width: 100px; cursor: pointer; }
        .large-image { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1000; }
        .large-image img { max-width: 90%; height: auto; }
        .overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 500; }
    </style>
</head>
<body>
    

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
                                <option value="Try Again">Try Again</option>
                                <option value="No, Faker">No, Faker</option>
                                <option value="You are a bad liar">You are a bad liar</option>
                                <option value="No, Your photo doesn't match the amount">No, Your photo doesn't match the amount</option>
                                <option value="Good Job, I added coins" selected>Good Job, I added coins</option>
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
</body>
</html>
