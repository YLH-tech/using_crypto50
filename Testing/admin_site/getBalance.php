<?php
session_start();

// Include the database connection file
require_once 'db.php'; // Make sure the path is correct

// Ensure the user ID is stored in the session
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT usdt FROM user_balances WHERE user_id = ?");
    $stmt->bind_param("i", $userId); // 'i' denotes the type is integer

    // Execute the statement
    $stmt->execute();
    $stmt->bind_result($usdtBalance);
    $stmt->fetch();

    // Return the balance as JSON
    if ($usdtBalance !== null) {
        echo json_encode(['usdt' => $usdtBalance]);
    } else {
        echo json_encode(['error' => 'User balance not found.']);
    }

    // Close the statement
    $stmt->close();
} else {
    echo json_encode(['error' => 'User not logged in.']);
}

// Close the connection (if not managed in db.php)
$conn->close();
?>
