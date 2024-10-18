<?php
  
  $conn = mysqli_connect("localhost", "root", "", "crypto-order");

  if($conn == false){
    die("Error:".mysqli_connect_error($conn));
  }

?>