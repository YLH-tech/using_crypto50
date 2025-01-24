<?php
session_start();
// Database connection
$conn = mysqli_connect("localhost", "root", "", "project");

if ($conn === false) {
    die("Error: " . mysqli_connect_error());
}

// // Check if the user is logged in as admin
// if ($_SESSION['role'] !== 'admin') {
//     die("Access Denied");
// }

// Fetch all users from the database
$query = "SELECT id, username FROM users";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
        .update-btn { padding: 10px 20px; }
    </style>
</head>
<body>
    <h1>User Management</h1>

    <!-- User List -->
    <h2>Users:</h2>
    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $user['username']; ?></td>
                    <td><a href="admin_manage_balance.php?user_id=<?php echo $user['id']; ?>">Edit Balances</a></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
