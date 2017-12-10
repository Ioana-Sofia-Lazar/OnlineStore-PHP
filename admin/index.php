<!DOCTYPE html>

<?php 
session_start();
include("includes/db.php");
?>

<html>

<head>
    <meta charset="UTF-8">
    <title>Login & Register form</title>
    <link rel="stylesheet" href="styles/login.css">
    <link href="https://fonts.googleapis.com/css?family=Barlow+Semi+Condensed:100,200,300,400,500,600,700" rel="stylesheet">
</head>

<body>
    <div id="logo-img">
        <p id="admin-panel-title">
            Admin Panel
        </p>
    </div>
    <div class="login-wrap">
        <h2>Login</h2>

        <form class="form" action="" method=POST>
            <input type="email" placeholder="E-mail" name="email" />
            <input type="password" placeholder="Password" name="password" />
            <button type="submit" name="login"> Sign in </button>
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

    $query = "SELECT * FROM admin WHERE password = '$password' AND email = '$email'";
    $result = mysqli_query($connection, $query);

    // if email password combination is incorrect (no rows were returned)
    if (!mysqli_num_rows($result)) {
        echo "<script>alert('Password or email incorrect!')</script>";
        return;
    }

    // proceed to login
    // check if there were items in the cart for this ip and move them to the newly logged in customer

    $_SESSION['admin_email'] = $email; 

    echo "<script> window.open('products.php','_self') </script>";

}	
	
?>





