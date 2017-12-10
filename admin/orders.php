<?php
include 'includes/head.php';
include 'includes/log.php';
include 'includes/menu.php';
include 'includes/admin-nav-left.php';
?>

<?php

$query = "SELECT c.country, c.address, c.postal_code, o.id, o.date, o.notes, o.status, c.id customer_id, c.name, o.amount FROM orders o, customer c WHERE o.customer_id = c.id";

// we will display search result instead of all categories
if (isset($_GET['search-input'])) {
    $search = $_GET['search-input'];
    
    $query = "SELECT c.country, c.address, c.postal_code, o.id, o.date, o.notes, o.status, c.id customer_id, c.name, o.amount FROM orders o, customer c WHERE o.customer_id = c.id AND (o.id LIKE '%$search%' OR o.date LIKE '%$search%' OR o.date LIKE '%$search%' OR c.name LIKE '%$search%')";
}

$orders = mysqli_query($connection, $query); 

?>

    <div id="products">
        <span id="searchBox">
            <form action="" method="get">
                <input name="search-input" id="search">
                <button id="searchButton" type="submit" name="search-button">Search</button>
            </form>
        </span>
        
        <form action="" method="post">

        <span id="buttons">
            <button type="button" onClick="window.location.href='orders.php';">Cancel</button>
            <button title="Click to save changes made on orders statuses." type="submit" name="update-changes">Save Changes</button>
        </span>

        <table class="prod-table">
            <caption>Manage Orders</caption>
            <thead>
                <tr>
                    <th width="30px">ID</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Address</th>
                    <th>Products</th>
                    <th>Amount</th>
                    <th width="120px">Status</th>
                </tr>
            </thead>
            <tbody>

<?php
while ($row = mysqli_fetch_array($orders)) {

    $id = $row['id'];
    $date = $row['date'];
    $notes = $row['notes'];
    $status = $row['status'];
    $customerName = $row['name'];
    $customerId = $row['customer_id'];
    $amount = $row['amount'];
    $country = $row['country'];
    $address = $row['address'];
    $postaCode = $row['postal_code'];  
    
?>
                <tr>
                    <td><?php echo $id; ?></td>
                    <td><?php echo $date; ?></td>
                    <td>
                        #<?php echo $customerId . '--' . $customerName; ?>
                    </td>
                    <td>
                        <?php echo $country . ', ' . $address . ', ' . $postaCode; ?>
                    </td>
                    <td>
                    
<?php
    // display products and quantities for this order
    $query = "SELECT p.title, i.quantity FROM order_item i, product p WHERE order_id = $id AND i.product_id = p.id";
    $items = mysqli_query($connection, $query);

    while ($item = mysqli_fetch_array($items)) {

        $title = $item['title'];
        $quantity = $item['quantity'];

        echo "<p>$quantity  x  $title</p>";

    }
?>
                    </td>
                    <td>$<?php echo $amount; ?></td>
                    <td>
                        <?php
                        if ($status == "Received")
                            echo $status;
                        else {
                        ?>
                    
                        <select name="status-select['<?php echo $id; ?>']">
                            <?php getStatusSelected($status); ?>
                        </select>
                        <?php
                        }
                        ?>
                    </td>
                </tr>
                
<?php
}
?>
                
            </tbody>
        </table>
   </form>
</div>

<?php include 'includes/footer.php'; ?>
    
<?php

/**
 * "Update changes" button was clicked
 */
if (isset($_POST['update-changes'])) {
    // save modified statuses
    if(!empty($_POST['status-select'])) {
        // iterate all statuses and update in the db
        $statuses = $_POST['status-select'];
        foreach($statuses as $key => $value) {
            $query = "UPDATE orders SET status = '$value' WHERE id = $key";
            mysqli_query($connection, $query); 
        }
    }
    
    // reload
    echo "<script>window.open('orders.php','_self')</script>";
    
}

?>













