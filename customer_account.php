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

$query = "SELECT * FROM customer WHERE id = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $customerId);
$stmt->execute();
$res = $stmt->get_result();

$customer = $res->fetch_assoc();

$stmt->free_result();
$stmt->close();

$name = $customer['name'];
$email = $customer['email'];
$country = $customer['country'];
$address = $customer['address'];
$postalCode = $customer['postal_code'];
    
?>

        <div id="products">
            <form class="account-wrap" method="post" action="">
                <h2>Account information</h2>
                <p>
                    <label>Name</label>
                    <input type="text" value="<?php echo $name; ?>" placeholder="Name" name="name" required />
                </p>
                <p>
                    <label>E-mail</label>
                    <input type="email" value="<?php echo $email; ?>" placeholder="E-mail" name="email" readonly />
                </p>
                <p>
                    <label>Country</label>
                    <select name="country" selected="<?php echo $country; ?>" required>
                        <?php showCountriesSelected($country); ?>
                    </select>
                </p>
                <p>
                    <label>Address</label>
                    <input type="text" value="<?php echo $address;?>" placeholder="Address" name="address" required />
                </p>
                <p>
                    <label>Postal Code</label>
                    <input type="number" value="<?php echo $postalCode;?>" placeholder="Postal Code" name="postal-code" required />
                </p>

                <button type="submit" name="update-account"> Save changes </button>

            </form>
        </div>

<?php
include 'includes/footer.php'; 
?>

<?php

/**
 * "Update account" button was clicked
 */
if (isset($_POST['update-account'])) {
    global $connection;

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
    
    // entered data is ok -- insert new customer in the db
    $query = "UPDATE customer SET name = ?, country = ?, address = ?, postal_code = ? WHERE id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("sssss", $name, $country, $address, $postalCode, $customerId);
    $stmt->execute();
    
    $stmt->free_result();
    $stmt->close();
    
    // refresh page
    echo "<meta http-equiv='refresh' content='0'>";
    
}

?>
