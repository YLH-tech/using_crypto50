<?php
require "../database/db_connection.php";
session_start(); // Start the session to handle session variables

// Include PHPMailer and the send_mail function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';

// Function to generate a random 6-digit verification code
function generateVerificationCode() {
    return rand(100000, 999999); // Generates a 6-digit number
}

// Send email using PHPMailer
function send_mail($recipient, $subject, $message)
{
    $mail = new PHPMailer();
    $mail->IsSMTP();

    $mail->SMTPDebug  = 0;  
    $mail->SMTPAuth   = TRUE;
    $mail->SMTPSecure = "tls";
    $mail->Port       = 587;
    $mail->Host       = "smtp.gmail.com";
    $mail->Username   = "lighting177036@gmail.com";
    $mail->Password   = "gjomowgrlsbnkuhw";

    $mail->IsHTML(true);
    $mail->AddAddress($recipient, "Esteemed Customer");
    $mail->SetFrom("lighting177036@gmail.com", "Bithumbnn.com");
    $mail->Subject = $subject;
    $content = $message;

    $mail->MsgHTML($content); 
    if (!$mail->Send()) {
        return false;
    } else {
        return true;
    }
}

if (isset($_POST["verify_email"])) {
    $email = $_POST["email"];
    $verification_code = $_POST["verification_code"];

    // Connect with the database
    //$conn = mysqli_connect("localhost", "root", "", "test");

    // Mark email as verified
    $sql = "UPDATE users SET email_verified_at = NOW() WHERE email = '" . $email . "' AND verification_code = '" . $verification_code . "'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_affected_rows($conn) == 0) {
        // Set an error message in the session
        $_SESSION['error'] = "Verification code failed. Check your email.";
        header("Location: email_verfication.php?email=" . urlencode($email)); // Redirect to the same page with the email
        exit();
    }

    // Clear the error message on successful verification
    unset($_SESSION['error']);
    
    header("Location: ../../home.html"); // Redirect to the homepage after successful verification
    exit();
}

if (isset($_POST["resend_code"])) {
    $email = $_POST["email"];
    
    // Generate a new 6-digit verification code
    $new_verification_code = generateVerificationCode();

    // Update the database with the new verification code
    $sql = "UPDATE users SET verification_code = '$new_verification_code' WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        // Send the new verification code via email
        $subject = "Your New Verification Code";
        $message = "Your new verification code is: $new_verification_code";

        if (send_mail($email, $subject, $message)) {
            $_SESSION['error'] = "A new verification code has been sent to your email.";
            header("Location: email_verfication.php?email=" . urlencode($email)); // Redirect back to the form
            exit();
        } else {
            $_SESSION['error'] = "Failed to resend the verification code. Please try again.";
            header("Location: email_verfication.php?email=" . urlencode($email)); // Redirect back to the form
            exit();
        }
    } else {
        $_SESSION['error'] = "Failed to resend the verification code. Please try again.";
        header("Location: email_verfication.php?email=" . urlencode($email)); // Redirect back to the form
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Page</title>

    <!-- Style link -->
    <link rel="stylesheet" href="../../style/login-signup.css">

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
        <form method="POST">
            <h1>Welcome to CoinEX!</h1>
            <p>Glad to see you!</p>
            
            <!-- Display error message if available -->
            <?php if (isset($_SESSION['error'])) { ?>
                <p class="error"><?php echo $_SESSION['error']; ?></p>
            <?php } ?>

            <!-- Email input -->
            <fieldset>
                <legend>Email Verification</legend>
                <span>
                    <i class="fa-solid fa-envelope"></i>
                    <input type="hidden" name="email" value="<?php echo $_GET['email']; ?>" required>
                    <input type="text" name="verification_code" placeholder="Enter verification code">
                </span>
            </fieldset>

            <span class="btn-container"> <!-- For align the buttons -->
                <input type="submit" class="active-btn" name="verify_email" value="Verify Email">
                <input type="submit" class="active-btn" name="resend_code" value="Resend Code">
            </span>
        </form>
    </main>

    <?php
    // Clear error message from session after displaying it
    unset($_SESSION['error']);
    ?>
</body>

</html>
