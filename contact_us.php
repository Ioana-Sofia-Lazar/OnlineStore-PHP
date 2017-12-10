<?php

include 'includes/head.php';
include 'includes/log.php';
include 'includes/menu.php';
include 'includes/nav-left.php';

?>

<?php
$name = "";
$email = "";

// if customer is logged in display his name and email
if (isset($_SESSION['customer_email'])) { 
    $customerId = $_SESSION['customer_id'];
    
    $query = "SELECT email, name FROM customer WHERE id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $customerId);
    $stmt->execute();
    $stmt->bind_result($email, $name);
    $customer = $stmt->fetch();
    
    $stmt->free_result();
    $stmt->close();

}

?>

    <div id="products">
        <form class="account-wrap" method="post" action="">
                <h2>Contact us</h2>
                <p>
                    <label>Name</label>
                    <input type="text" placeholder="Name" pattern="[a-zA-Z- ]{2,}" name="name" value="<?php echo $name;?>" required />
                </p>
                <p>
                    <label>E-mail</label>
                    <input type="email" placeholder="E-mail" name="email" value="<?php echo $email;?>" required />
                </p>
                <p>
                    <label>Message</label>
                    <textarea name="message" required></textarea>
                </p>

                <button type="submit" name="submit-contact"> Send </button>

            </form>
    </div>

<?php
include 'includes/footer.php'; 
?>

<?php
/**
 * "Send" button was clicked
 */
if (isset($_POST['submit-contact'])) {
    global $connection;
    
    // get email of admin that has HR role
    $query = "SELECT email FROM admin WHERE role = 'hr'";
    $stmt = $connection->prepare($query);
    $stmt->execute();
    $stmt->bind_result($adminEmail);
    $stmt->fetch(); 
    
    $stmt->free_result();
    $stmt->close();
    
    // get customer info
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    
    // send e-mail
    $to = $adminEmail;
    $subject = "Online Store -- Message from " . $name;
    $headers = "From: " . $email . "\r\n";
    $res = mail($to, $subject, $message, $headers);
    var_dump($res);
    echo "<script>alert('Your message was sent!')</script>";
}
?>
