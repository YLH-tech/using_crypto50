<?php
// Database connection
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role'] === 'admin' ? 'admin' : 'user';

    // Insert new user
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    if ($stmt->execute([$username, $password, $role])) {

        // Retrieve the new user's ID
        $user_id = $pdo->lastInsertId();

        // Initialize balance of USDT, ETH, BTC, BND to 0 in user_balances
        $balance_stmt = $pdo->prepare("INSERT INTO user_balances (user_id, usdt, eth, btc, bnd) VALUES (?, 0, 0, 0, 0)");
        $balance_stmt->execute([$user_id]);

        echo "User registered successfully!";
    } else {
        echo "Error: Could not register user.";
    }
}
?>