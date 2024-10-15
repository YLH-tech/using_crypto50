<?php
  
  $conn = mysqli_connect("localhost", "root", "", "project");

  if($conn == false){
    die("Error:".mysqli_connect_error($conn));
  }

?>