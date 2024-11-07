<?php
session_start();
require 'db.php';

$userId = $_SESSION['user_id'];

// Check if the user is logged in
if (!$userId) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_fund_password'])) {
    $fundPassword = $_POST['fund_password'];
    $fundPasswordConfirm = $_POST['fund_password_confirm'];

    // Validate fund password length
    if (strlen($fundPassword) !== 6) {
        $error = "Fund password must be 6 digits long.";
    } elseif ($fundPassword !== $fundPasswordConfirm) {
        $error = "Fund passwords do not match.";
    } else {
        // Hash the fund password before storing it
        $hashedFundPassword = password_hash($fundPassword, PASSWORD_DEFAULT);

        // Update the fund password in the fund_passwords table
        $stmt = $pdo->prepare("UPDATE fund_passwords SET fund_password = ? WHERE user_id = ?");
        $stmt->execute([$hashedFundPassword, $userId]);

        $success = "Fund password has been updated successfully.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Fund Password</title>
</head>
<body>
    <h2>Update Fund Password</h2>
    <form method="POST">
        <input type="text" name="fund_password" placeholder="New Fund Password (6 digits)" required pattern="\d{6}" maxlength="6">
        <input type="text" name="fund_password_confirm" placeholder="Confirm New Fund Password" required pattern="\d{6}" maxlength="6">
        <button type="submit" name="update_fund_password">Update Password</button>
    </form>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <?php if (isset($success)): ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php endif; ?>
</body>
</html>
