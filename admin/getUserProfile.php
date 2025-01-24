<?php
// Include your database connection here
include('db.php');

if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    try {
        // Query to get user profile info (e.g., name, email, profile photo)
        $profileQuery = "SELECT id, username, email, profile_photo FROM users WHERE id = :user_id";
        $stmt = $pdo->prepare($profileQuery);
        $stmt->execute(['user_id' => $user_id]);
        $userProfile = $stmt->fetch();

        if (!$userProfile) {
            echo json_encode(['error' => 'User not found']);
            exit;
        }

        // Query to get user balances (USDT, USDC, BTC, ETH)
        $balanceQuery = "SELECT usdt, usdc, btc, eth FROM user_balances WHERE user_id = :user_id";
        $stmt = $pdo->prepare($balanceQuery);
        $stmt->execute(['user_id' => $user_id]);
        $userBalances = $stmt->fetch();

        if (!$userBalances) {
            echo json_encode(['error' => 'No balance data found']);
            exit;
        }

        // Query to get the total number of orders in the last month
        $ordersMonthQuery = "SELECT COUNT(*) FROM orders WHERE user_id = :user_id AND created_at > DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        $stmt = $pdo->prepare($ordersMonthQuery);
        $stmt->execute(['user_id' => $user_id]);
        $ordersMonth = $stmt->fetchColumn();

        // Query to get the total number of orders in the last week
        $ordersWeekQuery = "SELECT COUNT(*) FROM orders WHERE user_id = :user_id AND created_at > DATE_SUB(NOW(), INTERVAL 1 WEEK)";
        $stmt = $pdo->prepare($ordersWeekQuery);
        $stmt->execute(['user_id' => $user_id]);
        $ordersWeek = $stmt->fetchColumn();

        // Handle missing or empty profile photo
        $userProfile['profile_photo'] = !empty($userProfile['profile_photo']) 
        ? str_replace('./', '../', $userProfile['profile_photo']) 
        : '../assets/profile/default_pfp.png';
        // Prepare the response data
        $responseData = [
            'profile' => $userProfile,
            'balances' => $userBalances,
            'orders_month' => $ordersMonth,
            'orders_week' => $ordersWeek
        ];

        // Return all the fetched data as a JSON object
        echo json_encode($responseData);

    } catch (PDOException $e) {
        // Handle any database errors
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    // Handle case where 'user_id' is not provided
    echo json_encode(['error' => 'User ID is required']);
}
?>
