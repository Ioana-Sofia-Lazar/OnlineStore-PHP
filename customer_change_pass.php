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
                <h2>Change password</h2>
                <p>
                    <label>Old Password</label>
                    <input type="password" name="old-pass" required />
                </p>
                <p>
                    <label>New password</label>
                    <input type="password" name="new-pass" pattern="[0-9a-zA-Z]{4,}" title="Can contain numbers, uppercase and lowercase letters, and must have at least 4 characters" required />
                </p>
                <p>
                    <label>Confirm password</label>
                    <input type="password" name="conf-pass" required />
                </p>

                <button type="submit" name="update-password"> Update password </button>

            </form>
        </div>

<?php
include 'includes/footer.php'; 
?>

<?php

/**
 * "Update password" button was clicked
 */
if (isset($_POST['update-password'])) {
    global $connection;
    
    // retrieve fields 
    $oldPass = $_POST['old-pass'];
    $newPass = $_POST['new-pass'];
    $confPass = $_POST['conf-pass'];
   
    // check if password is correct
    $query = "SELECT * FROM customer WHERE id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $customerId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $password = $row['password'];
    
    if ($oldPass != $password) {
        echo "<script>alert('Old password is incorrect!');</script>";
        return;
    }
    
    // check if new password and confirm password match
    if ($newPass != $confPass) {
        echo "<script>alert('Confirm password field does not match!');</script>";
        return;
    }
    
    // entered data is ok -- change password
    $query = "UPDATE customer SET password = ? WHERE id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("ss", $newPass, $customerId);

	if (!$stmt->execute()) {
	    echo "<script>alert('Error updating password!');</script>";
        return;
	}
    
    $stmt->free_result();
    $stmt->close();
    
    echo "<script>alert('Password successfully updated!');</script>";
    
}

?>
