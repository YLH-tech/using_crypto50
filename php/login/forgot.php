<?php
require "../database/db_connection.php";
?>

<?php
session_start();
$error = array();

require "mail.php";

//if(!$con = mysqli_connect("localhost","root","","test")){

//	die("could not connect");
//}

$mode = "enter_email";
if (isset($_GET['mode'])) {
    $mode = $_GET['mode'];
}

//something is posted
if (count($_POST) > 0) {

    switch ($mode) {
        case 'enter_email':
            // code...
            $email = $_POST['email'];
            //validate email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error[] = "Please enter a valid email";
            } elseif (!valid_email($email)) {
                $error[] = "That email was not found";
            } else {

                $_SESSION['forgot']['email'] = $email;
                send_email($email);
                header("Location: forgot.php?mode=enter_code");
                die;
            }
            break;

        case 'enter_code':
            // code...
            $code = $_POST['code'];
            $result = is_code_correct($code);

            if ($result == "the code is correct") {

                $_SESSION['forgot']['code'] = $code;
                header("Location: forgot.php?mode=enter_password");
                die;
            } else {
                $error[] = $result;
            }
            break;

        case 'enter_password':
            // code...
            $password = $_POST['password'];
            $password2 = $_POST['password2'];

            if ($password !== $password2) {
                $error[] = "Passwords do not match";
            } elseif (!isset($_SESSION['forgot']['email']) || !isset($_SESSION['forgot']['code'])) {
                header("Location: forgot.php");
                die;
            } else {

                save_password($password);
                if (isset($_SESSION['forgot'])) {
                    unset($_SESSION['forgot']);
                }

                header("Location: ../../login.php");
                die;
            }
            break;

        default:
            // code...
            break;
    }
}

function send_email($email)
{

    global $conn;

    $expire = time() + (60 * 1);
    $code = rand(10000, 99999);
    $email = addslashes($email);

    $query = "insert into codes (email,code,expire) value ('$email','$code','$expire')";
    mysqli_query($conn, $query);

    //send email here
    send_mail($email, 'Password reset', "Your code is " . $code);
}

function save_password($password)
{

    global $conn;

    $password = password_hash($password, PASSWORD_DEFAULT);
    $email = addslashes($_SESSION['forgot']['email']);

    $query = "update users set password = '$password' where email = '$email' limit 1";
    mysqli_query($conn, $query);
}

function valid_email($email)
{
    global $conn;

    $email = addslashes($email);

    $query = "select * from users where email = '$email' limit 1";
    $result = mysqli_query($conn, $query);
    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            return true;
        }
    }

    return false;
}

function is_code_correct($code)
{
    global $conn;

    $code = addslashes($code);
    $expire = time();
    $email = addslashes($_SESSION['forgot']['email']);

    $query = "select * from codes where code = '$code' && email = '$email' order by id desc limit 1";
    $result = mysqli_query($conn, $query);
    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            if ($row['expire'] > $expire) {

                return "the code is correct";
            } else {
                return "the code is expired";
            }
        } else {
            return "the code is incorrect";
        }
    }

    return "the code is incorrect";
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Page</title>

    <!-- Style link -->
    <link rel="stylesheet" href="../../style/login-signup.css">
    <script src="../../js/eye.js" defer></script>

    <!-- Fontawesome link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>


<body>
    <main>
        <?php

        switch ($mode) {
            case 'enter_email':
                // code...
        ?>
                <form method="post" action="forgot.php?mode=enter_email">
                    <h1>Forgot Password</h1>
                    <br>

                    <?php
                    foreach ($error as $err) {
                        // code...
                        echo $err . "<br>";
                    }
                    ?>
                    </span>
                    <fieldset>
                        <legend>Email</legend>
                        <span>
                            <i class="fa-solid fa-envelope"></i>
                            <input class="textbox" type="email" name="email" placeholder="Email">
                        </span>
                    </fieldset>
                    <span class="btn-container">
                        <span class="btn-gp">
                            <input type="submit" class="active-btn" value="Next">

                        </span>
                        <a href="../../login.php">Login</a></span>
                    </span>
                </form>
            <?php
                break;

            case 'enter_code':
                // code...
            ?>
                <form method="post" action="forgot.php?mode=enter_code">
                    <h1>Forgot Password</h1>
                    <br>
                    <h3>Enter your the code sent to your email</h3>
                    <span style="font-size: 12px;color:red;">
                        <?php
                        foreach ($error as $err) {
                            // code...
                            echo $err . "<br>";
                        }
                        ?>
                    </span>

                    <fieldset>
                        <legend>Code</legend>
                        <span>
                            <i class="fa-solid fa-lock"></i>
                            <input class="textbox" type="text" name="code" placeholder="Enter code" required>
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