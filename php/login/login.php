<?php
// Include the database connection
require "../database/db_connection.php";

// Start the session at the beginning of your script
session_start();

if (isset($_POST["login"])) {   
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Validate input
    if (empty($email) || empty($password)) {
        header("Location: ../../login.php?error=Please enter both email and password.");
        exit();
    }

    // Query the database for the user
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        header("Location: ../../login.php?error=Email not found.");
        exit();
    }

    $user = $result->fetch_object();

    // Verify password
    if (!password_verify($password, $user->password)) {
        header("Location: ../../login.php?error=Incorrect password.");
        exit();
    }

    // Check if email is verified
    if (is_null($user->email_verified_at)) {
        header("Location: ../../email_verification.php?email=" . urlencode($email));
        exit();
    }

    // Set session variables for the logged-in user
    $_SESSION['user_id'] = $user->id;
    $_SESSION['user_email'] = $user->email;
    $_SESSION['username'] = $user->username;


    // Redirect to home page
    header("Location: ../../index.php");
    exit();
}
?>
