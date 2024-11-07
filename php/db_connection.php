<?php
  
  $conn = mysqli_connect("localhost", "root", "", "admin_page");

  if($conn == false){
    die("Error:".mysqli_connect_error($conn));
  }

?>