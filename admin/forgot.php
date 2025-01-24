<?php
session_start();
$error = array();

require "db_connection.php";

$mode = "enter_password";
if (isset($_GET['mode'])) {
    $mode = $_GET['mode'];
}

//something is posted
if (count($_POST) > 0) {

    switch ($mode) {
        case 'enter_password':
            // code...
            $password = $_POST['password'];
            $password2 = $_POST['password2'];

            if ($password !== $password2) {
                $error[] = "Passwords do not match";
            }elseif (is_current_password($email, $password)) {
                $error[] = "The new password cannot be the same as the current password.";
            } else {

                save_password($password);
                if (isset($_SESSION['forgot'])) {
                    unset($_SESSION['forgot']);
                }

                header("Location: admin_login.php?error=Password has been changed. Please log in again.");
                die;
            }
            break;

        default:
            // code...
            break;
    }
}
function is_current_password($email, $new_password)
{
    global $conn;

    // Escape the email to prevent SQL injection
    $email = addslashes($email);

    // Query to fetch the current password hash from the database
    $query = "SELECT password FROM admin_users";
    $result = mysqli_query($conn, $query);

    // Check if the query was successful
    if ($result) {
        $user = mysqli_fetch_assoc($result);
        if ($user && password_verify($new_password, $user['password'])) {
            return true;  // The new password is the same as the current password
        }
    }

    return false;  // The new password is different from the current password
}

function save_password($password)
{

    global $conn;

    $password = password_hash($password, PASSWORD_DEFAULT);

    $query = "UPDATE admin_users SET password = '$password', last_password_change = CURRENT_TIMESTAMP";
    mysqli_query($conn, $query);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Page</title>

    <!-- Style link -->
    <link rel="stylesheet" href="forgot.css">
    <script src="eye.js" defer></script>

    <!-- Fontawesome link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>


<body>
    <main>
        <?php

        switch ($mode) {
            case 'enter_password':
                // code...
            ?>
                <form method="post" action="forgot.php?mode=enter_password">
                    <h1>Forgot Password</h1>
                    <br>
                    <h3>Enter your new password</h3>
                    <span style="font-size: 12px;color:red;">
                        <?php
                        foreach ($error as $err) {
                            // code...
                            echo $err . "<br>";
                        }
                        ?>
                    </span>

                    <fieldset>
                        <legend>Password</legend>
                        <span>
                            <i class="fa-solid fa-lock"></i>
                            <input class="textbox" type="password" name="password" id="password" placeholder="Password" required>
                            <i class="fas fa-eye toggle-password" data-toggle="#password"></i>
                        </span>
                    </fieldset>
                    <fieldset>
                        <legend>Retype Password</legend>
                        <span>
                            <i class="fa-solid fa-lock"></i>
                            <input class="textbox" type="password" name="password2" id="confirmPassword" placeholder="Retype Password" required>
                            <i class="fas fa-eye toggle-password" data-toggle="#confirmPassword"></i>
                        </span>
                    </fieldset>
                    
                    <span class="btn-container">
                        <span class="btn-gp">

                            <a href="forgot.php">
                                <input type="button" class="active-btn" value="Start Over">
                            </a>

                        </span>
                         <input type="submit" class="active-btn" value="Next" style="float: right;">

                    </span>
                </form>
        <?php
                break;

            default:
                // code...
                break;
        }

        ?>
    </main>
</body>

</html>