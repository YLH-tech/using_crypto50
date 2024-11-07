<?php
require "./php/db_connection.php";

// Prepare and execute the SQL query to fetch user data
$sql = "SELECT profile FROM user WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", "1");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    
    $userData = $result->fetch_assoc();

   
    $_SESSION['profile'] = $userData['profile'];

    echo "User data stored in session variables successfully.";
} else {
    echo "No user found with the given ID.";
}


$stmt->close();
$conn->close();
?>