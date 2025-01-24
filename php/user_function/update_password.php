<?php
session_start();
require '../database/db.php';

$userId = $_SESSION['user_id'];

// Check if the user is logged in
if (!$userId) {
    header('Location: ../../login.php'); // Redirect to login if not logged in
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_fund_password'])) {
    $fundPassword = $_POST['fund_password'];
    $fundPasswordConfirm = $_POST['fund_password_confirm'];

    // Validate fund password length
    if (strlen($fundPassword) !== 6) {
       
        header("Location: update_password.php?error=Fund password must be 6 digits long.");

    } elseif ($fundPassword !== $fundPasswordConfirm) {

        header("Location: update_password.php?error=Fund passwords do not match.");

    } else {
        // Hash the fund password before storing it
        $hashedFundPassword = password_hash($fundPassword, PASSWORD_DEFAULT);

        // Update the fund password in the fund_passwords table
        $stmt = $pdo->prepare("UPDATE fund_passwords SET fund_password = ? WHERE user_id = ?");
        $stmt->execute([$hashedFundPassword, $userId]);
        header("Location: update_password.php?error=Fund password has been updated successfully.");

    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-translate="title_new">Update Fund Password</title>

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
            <h1 data-translate="title_new">Update Your Fund Password</h1>
            <h4> <?php if (isset($_GET['error'])) { ?>
                    <p class="error"> <?php echo $_GET['error']; ?></p>
                <?php } ?>
            </h4>
            <!-- Email input -->
            <fieldset>
                <legend data-translate="password">Password</legend>
                <span>
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="fund_password" id="password" data-translate="fund_password_placeholder_new" placeholder="New Fund Password (6 digits)" required pattern="\d{6}" maxlength="6">
                    <i class="fas fa-eye toggle-password" data-toggle="#password"></i>

                </span>
            </fieldset>

            <!-- Password input -->
            <fieldset>
                <legend data-translate="confirm_password">Comfirm Password</legend>
                <span>
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="fund_password_confirm" id="confirmPassword" data-translate="confirm_fund_password_placeholder_new" placeholder="New Confirm Fund Password" required pattern="\d{6}" maxlength="6">
                    <i class="fas fa-eye toggle-password" data-toggle="#confirmPassword"></i>
                </span>
            </fieldset>

            <span class="btn-container"> <!-- For align the buttons -->
                <span class="btn-gp">    
                 <input type="submit" name="update_fund_password" data-translate="update_password" class="active-btn" value="Update Password">

                </span>
            </span>
        </form>
    </main>
</body>

</html>
