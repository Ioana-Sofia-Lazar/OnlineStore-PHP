<?php
// check if customer is logged in
if (!isset($_SESSION['customer_email'])) {
?>

    <ul id="log">
        <li id="floatRight"><a href="customer_login.php">Login</a></li>
        <li id="floatRight"><a href="customer_register.php">Register</a></li>
    </ul>

<?php
} else {
?>

    <ul id="log">
        <li id="floatLeft"><a href="customer_account.php">Account</a></li>
        <li id="floatLeft"><a href="customer_orders.php">My Orders</a></li>
        <li id="floatRight"><a href="?logout=true">Logout</a></li>
    </ul>

<?php
}
?>

<?php
if (isset($_GET['logout'])) {
    session_destroy();
    // redirect to login
    echo "<script>window.open('customer_login.php','_self');</script>";
}
?>