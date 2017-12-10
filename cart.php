<?php
include 'includes/head.php';
include 'includes/log.php';
include 'includes/menu.php';
include 'includes/nav-left.php';

$totalItems = showTotalItems();
?>

    <div id="products">
        <div class="cart-container">
            <div class="block">
                <div class="span12">
                    <div class="wrapper-no-padding shopping-cart">
                        <table class="cart-table">

                            <caption class="slab-font">You Have <?php echo $totalItems; ?> item(s) In Your Cart</caption>

                            <tr class="heading heading-font">
                                <th class="cart-item pl">Cart Items</th>
                                <th class="cart-item-price">Price</th>
                                <th class="cart-item-quantity">Quantity</th>
                                <th class="cart-item-total pr">Total</th>
                            </tr>

                            <tr class="item-row">
                            </tr>

<?php 

    global $connection;
                        
    // if customer is logged in we retrieve his items from cart
    if(isset($_SESSION['customer_id'])) {
        $customerId = $_SESSION['customer_id'];
        $query = "SELECT p.id, p.stock, p.title, b.title brand, p.price, p.image, categ.title category, c.quantity, SUM(c.quantity * p.price) total FROM cart c, product p, category categ, brand b WHERE c.product_id = p.id AND p.category = categ.id AND p.brand = b.id AND c.customer_id = ? GROUP BY p.category, p.title, p.brand, p.price, p.image, categ.title";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("s", $customerId);
    } else {
        // if not, we retrieve items corresponding to this ip address
        $ip = getIp();
        $query = "SELECT p.id, p.stock, p.title, b.title brand, p.price, p.image, categ.title category, c.quantity, SUM(c.quantity * p.price) total FROM cart c, product p, category categ, brand b WHERE c.product_id = p.id AND p.category = categ.id AND p.brand = b.id AND c.ip_address = ? GROUP BY p.category, p.title, p.brand, p.price, p.image, categ.title";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("s", $ip);
    }   

    $stmt->execute();
    $products = $stmt->get_result(); 

    // if there are no products in the cart
    if (!$products->num_rows) {
        return;
    }
  
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
        
        correctQuantity($quantity, $stock, $id);

?>
                                <tr class="item-row">
                                    <td class="cart-item">
                                        <table>
                                            <tr>
                                                <td><img width="200px" height="200px" src="<?php echo $image; ?>" alt="<?php echo $title; ?>" /></td>
                                                <td>
                                                    <a href=details.php?product_id=<?php echo $id; ?>><p class="item-name"><?php echo $title; ?></p></a>                                        
                                                    <p><a href="cart.php?remove=<?php echo $id; ?>">Remove Item</a></p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td class="cart-item-price">
                                        <p>$<?php echo $price; ?></p>
                                    </td>
                                    <td class="cart-item-quantity">
                                        <form method="post" action="?update-for=<?php echo $id; ?>">
                                            <input name="quantity" type="number" class="span1" value="<?php echo $quantity; ?>" min="1" max="<?php echo $stock; ?>" title="<?php echo $stock; ?> available" />
                                            <button name="update-quantity">Update</button> 
                                        </form>
                                    </td>
                                    <td class="cart-item-total slab-font pr">
                                        <p>$<?php echo $total; ?></p>
                                    </td>
                                </tr>
<?php
    }
?>

                        </table>
                    </div>

                </div>

            </div>

<?php
// Show total if there are items in the cart
if ($totalItems > 0) {
?>
            <div class="span6">

                <div class="wrapper-no-padding">
                    <table class="cart-table">
                        <caption class="slab-font">Cart Totals</caption>
                        <tr>
                            <td class="key">Cart Subtotal</td>
                            <td class="value">$<?php echo showTotalPrice(); ?></td>
                        </tr>
                        <tr>
                            <td class="key">Shipping</td>
                            <td class="value">$10</td>
                        </tr>
                        <tr class="total">
                            <td class="key">Order Total</td>
                            <td class="value slab-font">$<?php echo showTotalPrice() + 10;?></td>
                        </tr>
                    </table>
                    <div class="clearfix pa"><a href="?checkout=true" id="checkout">Checkout<i class="fa fa-caret-right"></i></a></div>
                </div>

            </div>
<?php
}
?>
        </div>

    </div>


<?php 

/**
 * If "Checkout" was clicked
 */
if (isset($_GET['checkout'])) {
    // if customer is logged in 
    if (isset($_SESSION['customer_email'])) { 
        // redirect him to payment page
        echo "<script>window.open('checkout.php','_self');</script>";
        
    } else {
        // redirect him to login
        echo "<script>window.open('customer_login.php','_self');</script>";
    }
}

/**
 * Customer clicked "Update" for quantity of item from cart
 */
if (isset($_POST['quantity']) && isset($_GET['update-for'])) {
    $quantity = $_POST['quantity'];
    $productId = $_GET['update-for'];
    
    global $connection;
    
    // if customer is logged in 
    if(isset($_SESSION['customer_id'])) {
        $customerId = $_SESSION['customer_id'];
        $query = "UPDATE cart SET quantity = ? WHERE customer_id = ? AND product_id = ?";  
        $stmt = $connection->prepare($query);
        $stmt->bind_param("sss", $quantity, $customerId, $productId);
    } else {
        // if not, use ip address
        $ip = getIp();
        $query = "UPDATE cart SET quantity = ? WHERE ip_address = ? AND product_id = ?"; 
        $stmt = $connection->prepare($query);
        $stmt->bind_param("sss", $quantity, $ip, $productId);
    }
    
    $stmt->execute(); 
    $stmt->free_result();
    $stmt->close();
    
    // go back to cart
    echo "<script>window.open('cart.php','_self')</script>";
           
}

/**
 * Checks if quantity that is in the cart for a product is greater than the available stock
 */
function correctQuantity($quantity, $stock, $productId) {
    if ($quantity <= $stock) {
        return;
    }
    
    global $connection;
    
    // if customer is logged in
    if(isset($_SESSION['customer_id'])) {
        $customerId = $_SESSION['customer_id'];
        $query = "UPDATE cart SET quantity = ? WHERE customer_id = ? AND product_id = ?";  
        $stmt = $connection->prepare($query);
        $stmt->bind_param("sss", $stock, $customerId, $productId);
    } else {
        // if not, use ip address
        $ip = getIp();
        $query = "UPDATE cart SET quantity = ? WHERE ip_address = ? AND product_id = ?"; 
        $stmt = $connection->prepare($query);
        $stmt->bind_param("sss", $stock, $ip, $productId);
    }
    
    $stmt->execute();
    $stmt->free_result();
    $stmt->close();
}

/**
 * If "Remove item" was clicked for an item
 */
if (isset($_GET['remove'])) {
    
    $id = $_GET['remove'];
    
    // remove product with id from cart
    removeFromCart($id);
    
    // go back to cart
    echo "<script>window.open('cart.php','_self')</script>";
    
}

/**
 * Remove product with id from cart
 */
function removeFromCart($id) {
    
    $ip = getIp();  
    global $connection;
    
    // if customer is logged in we remove from his cart
    if(isset($_SESSION['customer_id'])) {
        $customerId = $_SESSION['customer_id'];
        $query = "DELETE FROM cart WHERE customer_id = ? AND product_id = ?";  
        $stmt = $connection->prepare($query);
        $stmt->bind_param("ss", $customerId, $id);
    } else {
        // if not, we remove from the cart corresponding to this ip address
        $ip = getIp();
        $query = "DELETE FROM cart WHERE ip_address = ? AND product_id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("ss", $ip, $id);
    }
     
    $stmt->execute();    
    $stmt->free_result();
    $stmt->close();
    
}

?>

<?php include 'includes/footer.php'; ?>
