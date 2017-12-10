<?php
$connection = new mysqli("localhost", "root", "root", "online_store");

// Check for MySql error
if ($connection->connect_error) {
    die("$mysqli->connect_errno: $mysqli->connect_error");
}

$stmt = $connection->stmt_init();

if (isset($_GET['email']) && !empty($_GET['email']) && isset($_GET['hash']) && !empty($_GET['hash'])) {
    $email = $_GET['email']; 
    $hash = $_GET['hash']; 
    
    // check validity of email and hash
    global $connection;
    $query = "SELECT * FROM customer WHERE email = ? AND hash = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("ss", $email, $hash);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if (!$res->num_rows) {
	    echo "<script>alert('This link is not valid')</script>";
        return;
	}
    
    // make this email verified
    $query = "UPDATE customer SET verified = 1 WHERE email = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    
    $stmt->free_result();
    $stmt->close();
    
    echo "<script>alert('Your e-mail has been verified!')</script>";
    
    // redirect to login
    echo "<script>window.open('customer_login.php','_self')</script>";
    
}