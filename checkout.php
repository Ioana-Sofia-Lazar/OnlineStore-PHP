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

$query = "SELECT * FROM customer WHERE id = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $customerId);
$stmt->execute();
$res = $stmt->get_result();
$customer = $res->fetch_assoc();

$name = $customer['name'];
$email = $customer['email'];
$country = $customer['country'];
$address = $customer['address'];
$postalCode = $customer['postal_code'];
    
?>

    <div id="products">
        <form method="post" action="">
            <div id="left-col">
                <p class="caption-p">Shipping information</p>
                <p>
                    <label>Name</label>
                    <input type="text" value="<?php echo $name;?>" placeholder="Name" name="name" readonly />
                </p>
                <p>
                    <label>E-mail</label>
                    <input type="email" value="<?php echo $email;?>" placeholder="E-mail" name="email" readonly />
                </p>
                <p>
                    <label>Country</label>
                    <input type="text" value="<?php echo $country;?>" placeholder="Country" name="country" readonly />
                </p>
                <p>
                    <label>Address</label>
                    <input type="text" value="<?php echo $address;?>" placeholder="Address" name="address" readonly />
                </p>
                <p>
                    <label>Postal Code</label>
                    <input type="number" value="<?php echo $postalCode;?>" placeholder="Postal Code" name="postal-code" readonly />
                </p>

            </div>
            <div id="right-col">
                <p>
                    <p class="caption-p">Order Notes</p>
                    <textarea placeholder="Any additional information here..." rows="20" name="notes" autocomplete="off" aria-autocomplete="list"></textarea>
                </p>
                <table>
                    <p class="caption-p">Order Summary</p>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
  
<?php 

$query = "SELECT p.id, p.stock, p.title, b.title brand, p.price, p.image, categ.title category, c.quantity, SUM(c.quantity * p.price) total FROM cart c, product p, category categ, brand b WHERE c.product_id = p.id AND p.category = categ.id AND p.brand = b.id AND c.customer_id = ? GROUP BY p.category, p.title, p.brand, p.price, p.image, categ.title";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $customerId);
                        
$stmt->execute();
$products = $stmt->get_result();

while ($row = $products->fetch_assoc()) {

    $id = $row['id'];
    $categ = $row['category'];
    $brand = $row['brand'];
    $title = $row['title'];
    $price = $row['price'];
    $image = $row['image'];
    $stock = $row['stock'];
    $quantity = $row['quantity'];
    $total = $row['total'];
?>                     
                        
                        <tr>
                            <td><?php echo $title; ?></td>
                            <td><?php echo $quantity; ?></td>
                            <td><?php echo $total; ?></td>
                        </tr>
<?php
}
                        
$stmt->free_result();
$stmt->close();
    
$totalPrice = showTotalPrice();
?>
                    </tbody>
                </table>

                <table id="total-table">
                    <tbody>
                        <tr>
                            <td class="td-bold">Subtotal</td>
                            <td>$<?php echo $totalPrice; ?></td>
                        </tr>
                        <tr>
                            <td>Shipping</td>
                            <td>$10</td>
                        </tr>
                        <tr>
                            <td class="td-bold">Total</td>
                            <td>$<?php echo $totalPrice + 10; ?></td>
                        </tr>
                    </tbody>
                </table>

                <button type="submit" name="place-order">Place Order</button>
            </div>
        </form>
    </div>



<?php include 'includes/footer.php'; ?>

<?php

/**
 * "Place Order" button was clicked
 */
if (isset($_POST['place-order'])) {
    global $connection;

    $notes = $_POST['notes'];
    
    // correct quantities (products for which customer wants to order more than the current stock)
    $corrected = correctQuantities();

    if (!empty($corrected)) {
        $urlcorrected = urlencode($corrected);
        echo "<script type='text/javascript' language='javascript'> window.open('cart.php?corrected=$urlcorrected','_self');</script>";
        //echo "<script>window.location.href='cart.php';</script>";
        //echo "<script type='text/javascript' language='javascript'> window.open('cart.php','_self');</script>";
    } else {
        // create new order 
        $query = "INSERT INTO orders(customer_id, amount, date, notes, status) VALUES(?, ?, SYSDATE(), ?, 'Processing')";

        $totalPrice = showTotalPrice();
        if ($totalPrice == 0) {
            echo "<script>alert('You cannot place an order with 0 items!')</script>";
            echo "<script>window.open('cart.php','_self')</script>";
            return;
        }

        $stmt = $connection->prepare($query);
        $stmt->bind_param("sss", $customerId, $totalPrice, $notes);
        $stmt->execute();
        $stmt->free_result();
        $stmt->close();

        // get id of inserted order
        $orderId = $connection->insert_id;

        // select items from cart
        $query = "SELECT * FROM cart WHERE customer_id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("s", $customerId);
        $stmt->execute();
        $products = $stmt->get_result();
        $stmt->free_result();
        $stmt->close();

        while ($row = $products->fetch_assoc()) {
            $productId = $row['product_id'];
            $quantity = $row['quantity'];

            // move item from cart to order_item table, and mark it as belonging to the order
            $query = "INSERT INTO order_item(order_id, product_id, quantity) VALUES(?, ?, ?)";
            $stmt = $connection->prepare($query);
            $stmt->bind_param("sss", $orderId, $productId, $quantity);
            $stmt->execute();
            $stmt->free_result();
            $stmt->close();

            // decrease stock for product
            $query = "UPDATE product SET stock = stock - ? WHERE id = ?";
            $stmt = $connection->prepare($query);
            $stmt->bind_param("ss", $quantity, $productId);
            $stmt->execute();
            $stmt->free_result();
            $stmt->close();

        }

        // delete items from cart
        $query = "DELETE FROM cart WHERE customer_id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("s", $customerId);
        $stmt->execute();

        $stmt->free_result();
        $stmt->close();

        // redirect customer 
        echo "<script>window.location.href='order_shipped.php';</script>";
    }    
 
}

/**
 * Checks if quantity that customer wants to order for any product is greater than the available stock 
 */
function correctQuantities() {
    global $connection;
    $customerId = $_SESSION['customer_id'];
    
    // get products from cart
    $query = "SELECT c.id, c.quantity, p.stock, p.title FROM cart c, product p WHERE c.product_id = p.id AND c.customer_id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $customerId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->free_result();
    $stmt->close();
    
    $corrected = "";
    while($row = $result->fetch_assoc()) {
        $cartId = $row['id'];
        $quantity = $row['quantity'];
        $stock = $row['stock'];
        $title = $row['title'];
        
        if ($quantity == 0) {
            // remove item from cart
            
            $query = "DELETE FROM cart WHERE id = ?";
            $stmt = $connection->prepare($query);
            $stmt->bind_param("s", $cartId);
            $stmt->execute();
            $stmt->free_result();
            $stmt->close();
        }
        
        if ($quantity > $stock) {
            // modify quantity in the cart
            if ($stock == 0) {
                $query = "DELETE FROM cart WHERE id = ?";
                $stmt = $connection->prepare($query);
                $stmt->bind_param("s", $cartId);
            } else {
                $query = "UPDATE cart SET quantity = ? WHERE id = ?";
                $stmt = $connection->prepare($query);
                $stmt->bind_param("ss", $stock, $cartId);
            }
            $stmt->execute();
            $stmt->free_result();
            $stmt->close();
            
            $corrected = $corrected . "\n" . $title . " x " . $stock;
        }
    }
    
    return $corrected;
    
}

?>























