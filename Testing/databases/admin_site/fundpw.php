<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['set_fund_password'])) {
    $userId = $_SESSION['user_id'];
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

        // Insert the fund password into the fund_passwords table
        $stmt = $pdo->prepare("INSERT INTO fund_passwords (user_id, fund_password) VALUES (?, ?)
                                ON DUPLICATE KEY UPDATE fund_password = ?");
        $stmt->execute([$userId, $hashedFundPassword, $hashedFundPassword]);

        $success = "Fund password has been set successfully.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Fund Password</title>
</head>
<body>
    <h2>Set Fund Password</h2>
    <form method="POST">
        <input type="text" name="fund_password" placeholder="Fund Password (6 digits)" required pattern="\d{6}" maxlength="6">
        <input type="text" name="fund_password_confirm" placeholder="Confirm Fund Password" required pattern="\d{6}" maxlength="6">
        <button type="submit" name="set_fund_password">Set Password</button>
    </form>
    <br>
    <button><a href="update_password.php">Fogot fund password</a></button>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <?php if (isset($success)): ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php endif; ?>
</body>
</html>
