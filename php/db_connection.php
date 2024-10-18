<?php
  $db_hostname = "localhost";
  $db_username = "root";
  $db_pwd = "";
  $db_name = "cryptoweb";
  $conn = mysqli_connect($db_hostname, $db_username, $db_pwd, $db_name);

  if($conn == false){
    die("Error:".mysqli_connect_error($conn));
  }

?>