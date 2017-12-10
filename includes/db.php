<?php 
$connection = mysqli_connect("localhost", "root", "root", "online_store");

// Check for MySql error
if (mysqli_connect_errno()) {
  echo "The Connection was not established: " . mysqli_connect_error();
}

?>
