<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $coin_type = $_POST['coin_type'];
    $amount = $_POST['amount'];

    // Handle image upload
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        // Insert coin request
        $stmt = $pdo->prepare("INSERT INTO coin_requests (user_id, coin_type, amount, image_path, status) VALUES (?, ?, ?, ?, 'pending')");
        if ($stmt->execute([$user_id, $coin_type, $amount, $target_file])) {
            header("Location:user_dashboard.php");
        } else {
            echo "Error submitting request.";
        }
    } else {
        echo "Error uploading image.";
    }
}
?>
