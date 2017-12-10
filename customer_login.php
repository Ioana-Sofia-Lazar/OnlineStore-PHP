<!DOCTYPE html>

<?php 
session_start();
include("includes/db.php");
?>

<html>

<head>
    <meta charset="UTF-8">
    <title>Login & Register form</title>
    <link rel="stylesheet" href="styles/customer_login.css">
</head>

<body>
    <div id="logo-img">
        <a href="index.php"> <img title="Go Back to our Home Page" src="images/logo3.jpg"> </a>
    </div>
    <div class="login-wrap">
        <h2>Login</h2>

        <form class="form" action="" method=POST>
            <input type="email" placeholder="E-mail" name="email" />
            <input type="password" placeholder="Password" name="password" />
            <button type="submit" name="login"> Sign in </button>
            <a href="customer_register.php">
                <p> Don't have an account? Register </p>
            </a>
        </form>
    </div>

</body>

</html>

<?php

?>

<?php 

if(isset($_POST['login'])){

    global $connection;

    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM customer WHERE password = ? AND email = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("ss", $password, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->free_result();
    $stmt->close();

    // if email password combination is incorrect (no rows were returned)
    if (!$result->num_rows) {
        echo "<script>alert('Password or email incorrect!')</script>";
        return;
    }
    
    // check if email if verified
    $customer = $result->fetch_assoc();
    $verified = $customer['verified'];
    if ($verified == 0) {
        echo "<script>alert('You have to verify your e-mail before logging in!')</script>";
        return;
    }

    // proceed to login
    // check if there were items in the cart for this ip and move them to the newly logged in customer
    $ip = getIp(); 
    $customerId = $customer['id'];
    
    $query = "UPDATE cart SET customer_id = ?, ip_address = '' WHERE ip_address = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("ss", $customerId, $ip);
    $stmt->execute();
    $stmt->free_result();
    $stmt->close();

    $_SESSION['customer_email'] = $email; 
    $_SESSION['customer_id'] = $customerId; 
    
    echo "<script> window.open('cart.php','_self') </script>";

}

/**
 * Gets the user IP address
 */ 
function getIp() {
    $ip = $_SERVER['REMOTE_ADDR'];
 
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
 
    return $ip;
}
	
	
?>





