<?php
session_start();
require '../database/db.php';

$userId = $_SESSION['user_id'];

// Check if the user is logged in
if (!$userId) {
    header('Location: ../../login.php'); // Redirect to login if not logged in
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['set_fund_password'])) {
    $fundPassword = $_POST['fund_password'];
    $fundPasswordConfirm = $_POST['fund_password_confirm'];

    // Check if fund password already exists for the user
    $stmt = $pdo->prepare("SELECT fund_password FROM fund_passwords WHERE user_id = ?");
    $stmt->execute([$userId]);
    $existingPassword = $stmt->fetchColumn();

    if ($existingPassword) {
        header("Location: fundpw.php?error=Your fund password already exists.");
        exit();
    }

    // Validate fund password length
    if (strlen($fundPassword) !== 6) {
        header("Location: fundpw.php?error=Fund password must be 6 digits long.");
        exit();
    } elseif ($fundPassword !== $fundPasswordConfirm) {
        header("Location: fundpw.php?error=Fund passwords do not match.");
        exit();
    } else {
        // Hash the fund password before storing it
        $hashedFundPassword = password_hash($fundPassword, PASSWORD_DEFAULT);

        // Insert the fund password into the fund_passwords table
        $stmt = $pdo->prepare("INSERT INTO fund_passwords (user_id, fund_password) VALUES (?, ?)
                                ON DUPLICATE KEY UPDATE fund_password = ?");
        $stmt->execute([$userId, $hashedFundPassword, $hashedFundPassword]);
        header("Location: fundpw.php?error=Fund password has been set successfully.");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-translate="title_fund">Set Fund Password</title>

    <!-- Style link -->
    <link rel="stylesheet" href="../../style/login-signup.css">
    <script src="../../js/eye.js" defer></script>
    <script src="../../js/translate.js"></script>

    <!-- Fontawesome link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .error {
            background: #F2DEDE;
            color: green;
            padding: 10px;
            width: 95%;
            border-radius: 5px;
            margin: 20px auto;
        }
    </style>
</head>

<body>
    <main>
        <!-- <img src="./assets/images/Sign up-amico.svg" alt="Background"> -->
        <form action="" method="post">
            <h1 data-translate="title_fund">Set Fund Password</h1>
            <h4> <?php if (isset($_GET['error'])) { ?>
                    <p class="error"> <?php echo $_GET['error']; ?></p>
                <?php } ?>
            </h4>
            <!-- Email input -->
            <fieldset>
                <legend data-translate="password">Password</legend>
                <span>
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="fund_password" id="password" data-translate="fund_password_placeholder" placeholder="Fund Password (6 digits)" required pattern="\d{6}" maxlength="6">
                    <i class="fas fa-eye toggle-password" data-toggle="#password"></i>

                </span>
            </fieldset>

            <!-- Password input -->
            <fieldset>
                <legend data-translate="confirm_password">Comfirm Password</legend>
                <span>
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="fund_password_confirm" id="confirmPassword" data-translate="confirm_fund_password_placeholder" placeholder="Confirm Fund Password" required pattern="\d{6}" maxlength="6">
                    <i class="fas fa-eye toggle-password" data-toggle="#confirmPassword"></i>
                </span>
            </fieldset>

            <span class="btn-container"> <!-- For align the buttons -->
                <span class="btn-gp">
                    <input type="submit" name="set_fund_password" data-translate="set_password" class="active-btn" value="Set Password">

                </span>
                <a href="update_password.php" data-translate="forgot_password_fund">Fogot fund password</a>
            </span>
        </form>
    </main>
</body>

</html>
