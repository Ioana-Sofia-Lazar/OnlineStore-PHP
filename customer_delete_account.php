<?php

include 'includes/head.php';
include 'includes/log.php';
include 'includes/menu.php';
include 'includes/customer-nav-left.php';

checkAccessDenied();

?>

<?php
global $connection;

$customerId = $_SESSION['customer_id'];
    
?>

        <div id="products">
            <form class="account-wrap" method="post" action="">
                <h2>Are you sure you want to delete your account?</h2>
                <p align="center">Enter your password in the field below</p>
                <p>
                    <input type="password" name="pass" required />
                </p>

                <button type="submit" name="delete-account"> Delete account</button>

            </form>
        </div>

<?php
include 'includes/footer.php'; 
?>

<?php

/**
 * "Delete account" button was clicked
 */
if (isset($_POST['delete-account'])) {
    global $connection;
    
    // retrieve entered password 
    $pass = $_POST['pass'];
   
    // check if password is correct
    $query = "SELECT * FROM customer WHERE id = $customerId";
    $result = mysqli_query($connection, $query);
    $row = mysqli_fetch_array($result);
    $password = $row['password'];
    
    if ($pass != $password) {
        echo "<script>alert('Password is incorrect!');</script>";
        return;
    }
    
    // password is ok 
    // delete customer's cart items
    $query = "DELETE FROM cart WHERE customer_id = $customerId";
    mysqli_query($connection, $query); 
    
    // delete customer's order items that were received
    $query = "DELETE order_item FROM order_item INNER JOIN orders ON orders.id = order_item.order_id WHERE orders.customer_id = $customerId AND orders.status = 'Received'";
    mysqli_query($connection, $query); 
    
    // delete customer's orders that were received
    $query = "DELETE FROM orders WHERE customer_id = $customerId AND status = 'Received'";
    mysqli_query($connection, $query); 
    
    // delete customer from the database
    $query = "DELETE FROM customer WHERE id = $customerId";
    mysqli_query($connection, $query); 
	
    echo "<script>alert('Account successfully deleted!');</script>";
    
    // destroy session
    session_destroy();
    
    // redirect to home page
    echo "<script>window.open('index.php','_self')</script>";
    
}

?>
