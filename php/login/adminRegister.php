<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require "../database/db_connection.php";
session_start(); // Start the session
?>

<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if (isset($_POST["register"])) {
    $name = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $retype_password = $_POST["retype_password"];

    // Check if passwords match
    if ($password !== $retype_password) {
        header("Location: ../../admin/admin_signup.php?error=Passwords do not match!");
        exit();
    }

    // Check if there is already an admin in the database
    $sql_check_admin = "SELECT COUNT(*) AS admin_count FROM admin_users";
    $result_check_admin = mysqli_query($conn, $sql_check_admin);
    $row = mysqli_fetch_assoc($result_check_admin);

    if ($row['admin_count'] > 0) {
        header("Location: ../../admin/admin_signup.php?error=An admin account already exists. Please log in.");
        exit();
    }

    // Check if email already exists in the database
    $sql_check_email = "SELECT * FROM admin_users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql_check_email);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        header("Location: ../../admin/admin_signup.php?error=Email already exists!");
        exit();
    }

    // Instantiate and configure PHPMailer
    $mail = new PHPMailer(true);

    try {
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'bithumbnnofficial@gmail.com';
        $mail->Password = 'dwtegfpxwvnasqtz';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('bithumbnnofficial@gmail.com', 'Bithumbnn.com');
        $mail->addAddress($email, $name);

        $mail->isHTML(true);

        $verification_code = substr(number_format(time() * rand(), 0, '', ''), 0, 6);

        $mail->Subject = 'Email verification';
        $mail->Body    = '<p>Your verification code is: <b style="font-size: 30px;">' . $verification_code . '</b></p>';

        $mail->send();

        $encrypted_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert into users table
        $sql_insert_user = "INSERT INTO admin_users(username, email, password, verification_code, email_verified_at) 
                            VALUES (?, ?, ?, ?, NULL)";
        $stmt_insert = mysqli_prepare($conn, $sql_insert_user);
        mysqli_stmt_bind_param($stmt_insert, "ssss", $name, $email, $encrypted_password, $verification_code);
    
        if (mysqli_stmt_execute($stmt_insert)) {

            // Store the user_id in the session
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $name;
            $_SESSION['email'] = $email;
        
            header("Location: admin_email_verfication.php?email=" . $email);
            exit();
        }
        
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
