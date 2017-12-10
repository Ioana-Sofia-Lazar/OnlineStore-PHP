<!DOCTYPE html>

<?php 
session_start();
include("includes/db.php");
include("functions/common_functions.php");
?>

<html>

<head>
    <meta charset="UTF-8">
    <title>Login & Register form</title>
    <link rel="stylesheet" href="styles/customer_register.css">
</head>

<body>
    <div id="logo-img">
        <a href="index.php"> <img height="120px" title="Go Back to our Home Page" src="images/logo3.jpg"> </a>
    </div>
    <div class="login-wrap">
        <h2>Register</h2>

        <form class="form" method="post">
            <input type="email" value="<?php echo (isset($_POST['email']))?$_POST['email']:'';?>" placeholder="E-mail" name="email" required />
            
            <input type="password" pattern="[0-9a-zA-Z]{4,}" title="Can contain numbers, uppercase and lowercase letters, and must have at least 4 characters" placeholder="Password" name="password" required />
            
            <input type="text" value="<?php echo (isset($_POST['name']))?$_POST['name']:'';?>" placeholder="Name" name="name" required />
            
            <select name="country" value="<?php echo (isset($_POST['country']))?$_POST['country']:'';?>" required>
                <?php showCountries(); ?>
            </select>
            
            <input type="text" value="<?php echo (isset($_POST['address']))?$_POST['address']:'';?>" placeholder="Address" name="address" required />
            
            <input type="number" value="<?php echo (isset($_POST['postal-code']))?$_POST['postal-code']:'';?>" placeholder="Postal Code" name="postal-code" required />
            
            <button type="submit" name="register"> Register </button>
            
            <a href="customer_login.php">
                <p> Already have an account? Login </p>
            </a>
        </form>
    </div>

</body>

</html>
			
<?php 

/**
 * "Register" button has been clicked
 */
if (isset($_POST['register'])) {
    global $connection;
    
    $password = $_POST['password'];
    $email = $_POST['email'];
    $name = $_POST['name'];
    $country = $_POST['country'];
    $address = $_POST['address'];
    $postalCode = $_POST['postal-code'];
    
    // trim inputs and display error if empty
    $name = trim($name);
    $address = trim($address);
    if (empty($name) || empty($address)) {
        echo "<script>alert('Please enter non-empty values!');</script>";
        return;
    }
    
    // test validity of e-mail
    $email = filter_var($email, FILTER_SANITIZE_EMAIL); // remove illegal characters
    // validate e-mail
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('E-mail not valid!');</script>";
        return;
    } 
    
    // check if account with this email already exists
    $query = "SELECT * FROM customer WHERE email = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();
	if ($res->num_rows > 0) {
	    echo "<script>alert('E-mail already in use!');</script>";
        return;
	}
    
    // entered data is ok -- insert new customer in the db
    $ip = getIp();
    // generate random 32 character hash
    $hash = md5(rand(0, 1000));
    $query = "INSERT INTO customer(name, email, password, country, address, postal_code, hash) VALUES(?, ?, ?, ?, ?, ?, ?)";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("sssssss", $name, $email, $password, $country, $address, $postalCode, $hash);
    
	if (!$stmt->execute()) {
	    echo "<script>alert('Error registering customer!');</script>";
        return;
	}
    
    // customer was registered
    // get id of inserted customer
    $customerId = $connection->insert_id;
    
    // if there are items in the cart for this ip, save them for the newly registered customer
    $query = "UPDATE cart SET ip_address = '', customer_id = ? WHERE ip_address = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("ss", $customerId, $ip);
    $stmt->execute();
    
    $stmt->free_result();
    $stmt->close();
    
    // send verification email
    sendVerificationEmail($email, $hash);
    
    // redirect customer to login
    echo "<script>alert('Please check your e-mail for a verification link');</script>";
    
    // redirect to login
    echo "<script>window.open('customer_login.php','_self')</script>";
}

/**
 * Sends verification link to given email
 */
function sendVerificationEmail($email, $hash) {
    $to      = $email;
    $subject = 'Verify e-mail for Online Store';
    $message = '

    Thank you for signing up!

    Please click this link to activate your account:
    http://localhost/onlinestore/verify_email.php?email='.$email.'&hash='.$hash.'

    '; 

    $headers = 'From:noreply@onlinestore.com' . "\r\n";
    mail($to, $subject, $message, $headers);
}

?>










