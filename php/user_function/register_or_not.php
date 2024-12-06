<?php 
include "../database/db_connection.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}
print($_SESSION['user_id']);

?>