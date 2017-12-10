<?php

include 'includes/head.php';
include 'includes/log.php';
include 'includes/menu.php';
include 'includes/nav-left.php';

checkAccessDenied();

?>
    <div id="products">
        <div id="order-shipped">
            <h1>Your order has been placed!</h1>
            <img src="images/shipped.gif">
            <p><small>You can download the invoice from <a href="customer_orders.php">ORDERS</a> page.</small></p>
        </div>
    </div>

<?php
include 'includes/footer.php'; 
?>