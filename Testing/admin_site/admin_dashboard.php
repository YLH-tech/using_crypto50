<?php
session_start();
include 'db.php';

// Redirect if not logged in or not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
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
        .thumbnail {
            width: 100px;
            /* Small size for the thumbnail */
            cursor: pointer;
            /* Change cursor to pointer on hover */
        }

        .large-image {
            display: none;
            /* Initially hidden */
            position: fixed;
            /* Fix it to the viewport */
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
            /* Keep it on top */
        }

        .large-image img {
            max-width: 90%;
            /* Responsive size */
            height: auto;
            /* Maintain aspect ratio */
        }

        .overlay {
            display: none;
            /* Initially hidden */
            position: fixed;
            /* Fix it to the viewport */
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            /* Semi-transparent background */
            z-index: 500;
            /* Below the large image */
        }
    </style>
</head>

<body>
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



                            <label for="notes">
                                <h1>Note for user</h1>:
                            </label>
                            <select id="notes" name="admin_note"> <!-- The name attribute allows the selected value to be submitted -->
                                <option value="Try Again">Try Again</option>
                                <option value="No,Faker">
                                    <h2>No,Faker</h2>
                                </option>
                                <option value="You is bad Liar">
                                    <h1>You is bad Liar</h1>
                                </option>
                                <option value="No, Your photo is not same for amount">
                                    <h1>No, Your photo is not same for amount</h1>
                                </option>
                                <option value="Good Job,I add coins" selected>Good Job,I add coins</option>
                            </select>



                    <td><button type="submit" name="action" value="approve">Approve</button>
                        <button type="submit" name="action" value="reject">Reject</button>
                    </td>
                    </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No pending requests.</p>
    <?php endif; ?>

    <!-- Display Transaction and Approval History Including Admin Notes -->
    <h2>Transaction and Approval History</h2>
    <?php
    // Fetch transaction and approval history
    $history_stmt = $pdo->query("SELECT t.*, u.username FROM transactions t JOIN users u ON t.user_id = u.id ORDER BY t.timestamp DESC");
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
                <td><?= nl2br($entry['admin_note']) ?></td> <!-- Display admin note -->
                <td><?= $entry['timestamp'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>