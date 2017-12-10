<?php

include 'includes/head.php';
include 'includes/log.php';
include 'includes/menu.php';
include 'includes/nav-left.php';

checkAccessDenied();

?>

<?php
global $connection;

$customerId = $_SESSION['customer_id'];

$query = "SELECT * FROM orders WHERE customer_id = ? ORDER BY date DESC";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $customerId);
$stmt->execute();
$orders = $stmt->get_result();
    
?>

    <div id="products">

        <table class="prod-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Products</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>
                
<?php

while ($row = $orders->fetch_assoc()) {

    $id = $row['id'];
    $date = $row['date'];
    $amount = $row['amount'];
    $status = $row['status'];

?>              
                
                <tr>                      
                    <td><?php echo $id; ?></td>
                    <td><?php echo $date; ?></td>
                    <td>
<?php
    
    // display products and quantity for this order
    $query = "SELECT p.title, i.quantity FROM order_item i, product p WHERE order_id = ? AND i.product_id = p.id";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $items = $stmt->get_result();

    while ($item = $items->fetch_assoc()) {

        $title = $item['title'];
        $quantity = $item['quantity'];

        echo "<p>$quantity  x  $title</p>";

    }

?>
                    
                    </td>
                    <td><?php echo $amount; ?></td>
                    <td><?php echo $status; ?></td>
<?php
    
    // if order was not received show button to mark it as received
    if ($status != "Received") {
        echo "<td><button><a href='?received=$id'>Mark Order Received</a></button></td>";
    }
    
    ?>
                    <td><button><a href='download_invoice.php?order_id=<?php echo $id; ?>'>Download Invoice</a></button></td>
                    
                </tr>
<?php
}
                
$stmt->free_result();
$stmt->close();                
?>

            </tbody>
        </table>

    </div>

<?php
include 'includes/footer.php'; 
?>

<?php

/**
 * An order was marked as received
 */
if (isset($_GET['received'])) {
    $orderId = $_GET['received'];
    
    global $connection;
    $query = "UPDATE orders SET status = 'Received' WHERE id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $orderId);
    $stmt->execute();
    
    // take customer back to orders table
    echo "<script>window.open('customer_orders.php','_self')</script>";
   
}
?>
