<?php

include 'includes/head.php';
include 'includes/log.php';
include 'includes/menu.php';
include 'includes/nav-left.php';

?>
    <div id="products">
        <div id="order-shipped">
            <img height="150px" src="images/denied.png">
            <h2>You need to be logged in to access this page!</h2>
            <div id="register-login">
                <a href="customer_login.php">Login</a>
                <a href="customer_register.php">Register</a>
            </div>
        </div>
    </div>

    <?php
include 'includes/footer.php'; 
?>
