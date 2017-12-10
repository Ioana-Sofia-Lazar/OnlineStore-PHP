<?php

$connection = new mysqli("localhost", "root", "root", "online_store");

// Check for MySql error
if ($connection->connect_error) {
    die("$mysqli->connect_errno: $mysqli->connect_error");
}

$stmt = $connection->stmt_init();

/**
 * Gets most recently added products.
 */
function getNewestProducts(){

    if (!$_GET) {
        
        global $connection; 

        $query = "SELECT * FROM product ORDER BY RAND() LIMIT 0,8";

        $products = mysqli_query($connection, $query); 

        while ($row = mysqli_fetch_array($products)) {

            $id = $row['id'];
            $categ = $row['category'];
            $brand = $row['brand'];
            $title = $row['title'];
            $price = $row['price'];
            $image = $row['image'];

            displayProductBox($id, $title, $price, $image);

        }
	}

}

/**
 * Gets all product categories from the db
 */
function getCategories(){
	
	global $connection; // global makes it work inside the function
	
	$getCategs = "SELECT * FROM category";	
	$runCategs = mysqli_query($connection, $getCategs);
	
	while ($categ = mysqli_fetch_array($runCategs)){
		$id = $categ['id']; 
		$title = $categ['title'];
        
	    echo "<li><a href='index.php?category=$id'>$title</a></li>";
	
	}
}

/**
 * Gets all brands from the db
 */
function getBrands(){
	
	global $connection; // global makes it work inside the function
	
	$query = "SELECT * FROM brand";	
	$brands = mysqli_query($connection, $query);
	
	while ($brand = mysqli_fetch_array($brands)) {
		$id = $brand['id']; 
		$title = $brand['title'];
        
	    echo "<li><a href='index.php?brand=$id'>$title</a></li>";
	
	}
}

/**
 * Get all products that are in the selected category.
 */
function getProductsByCategory() {

	if (!isset($_GET['category'])) {
        return;
    }
		
    $categId = $_GET['category'];

	global $connection; 
	$query = "SELECT * FROM product WHERE category = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $categId);
    $stmt->execute();
    $products = $stmt->get_result(); 

    // if no products were found for this category
	if (!$products->num_rows) {
	    echo "<h2>No products were found for this category!</h2>";
        return;
	}
	
	while ($row = $products->fetch_assoc()) {
        
        $id = $row['id'];
        $categ = $row['category'];
        $brand = $row['brand'];
        $title = $row['title'];
        $price = $row['price'];
        $image = $row['image'];
	
		displayProductBox($id, $title, $price, $image);
	
	}
    
    $stmt->free_result();
    $stmt->close();

}

/**
 * Displays a single product.
 */
function displayProductBox($id, $title, $price, $image) {
    
    echo "
        
          <div class='product-container'>
            <img src='$image' class='product-image' />

            <p class='product-price'>$<br>$price</p>

            <div class='product-information'>

              <p class='product-title'>$title</p>

              <a href='index.php?add_to_cart=$id' class='btn-add-to-cart'>Add to Cart</a> <br>
              <a href='details.php?product_id=$id' class='btn-view-details'>View Details</a>

            </div>
          </div>
        
		
    ";	
}

/**
 * Get all products from the selected brand.
 */
function getProductsByBrand() {
    
    if (!isset($_GET['brand'])) {
        return;
    }
		
    $brandId = $_GET['brand'];

	global $connection; 
	
	$query = "SELECT * FROM product WHERE brand = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $brandId);
    $stmt->execute();
    $products = $stmt->get_result();

    // if no products were found for this brand
	if (!$products->num_rows) {
	    echo "<h2>No products were found for this brand!</h2>";
        return;
	}
	
	while ($row = $products->fetch_assoc()) {
        
        $id = $row['id'];
        $categ = $row['category'];
        $brand = $row['brand'];
        $title = $row['title'];
        $price = $row['price'];
        $image = $row['image'];
	
		displayProductBox($id, $title, $price, $image);
	
	}
    
    $stmt->free_result();
    $stmt->close();
}

/**
 * Get all products between the min price and the max price set.
 */
function getProductsByPrice() {
    
    if (!isset($_GET['min-price']) || !isset($_GET['max-price'])) {
        return;
    }
		
    $min = $_GET['min-price'];
    $max = $_GET['max-price'];

	global $connection; 
	
	$query = "SELECT * FROM product WHERE price >= ? AND price <= ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("ss", $min, $max);
    $stmt->execute();
    $products = $stmt->get_result();

    // if no products were found for this brand
	if (!$products->num_rows) {
	    echo "<h2>No products were found in this price range!</h2>";
        return;
	}
	
	while ($row = $products->fetch_assoc()) {
        
        $id = $row['id'];
        $categ = $row['category'];
        $brand = $row['brand'];
        $title = $row['title'];
        $price = $row['price'];
        $image = $row['image'];
	
		displayProductBox($id, $title, $price, $image);
	
	}
    
    $stmt->free_result();
    $stmt->close();
}


/**
 * Display products corresponding to what user entered in the search box (search for match in title and keywords).
 */
function getSearchResults() {
    
    if (!isset($_GET['search_input']) || !isset($_GET['submit_search'])) {
        return;
    }
    
    global $connection;
    
    $searchInput = $_GET['search_input'];
    $searchInput = "%".$searchInput."%";
    
    $query = "SELECT * FROM product WHERE keywords LIKE ? OR title LIKE ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("ss", $searchInput, $searchInput);
    $stmt->execute();
    $products = $stmt->get_result();

    // if no products were found for this search
	if (!$products->num_rows) {
	    echo "<h2>No products where found!</h2>";
        return;
	}
	
	while ($row = $products->fetch_assoc()) {
        
        $id = $row['id'];
        $categ = $row['category'];
        $brand = $row['brand'];
        $title = $row['title'];
        $price = $row['price'];
        $image = $row['image'];
	
		displayProductBox($id, $title, $price, $image);
	
	}
    
    $stmt->free_result();
    $stmt->close();
    
}

/**
 * When user clicks "Add to Cart" button the product is added to the cart
 */ 
function addToCart(){

    if (!isset($_GET['add_to_cart'])) {
        return;
    }
    
	global $connection; 

	$ip = getIp();
	$productId = $_GET['add_to_cart'];
    
    // check if stock for this product is > 0
    $query = "SELECT stock FROM product WHERE id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $productId); 
    $stmt->execute();
    $stmt->bind_result($stock);
    $stmt->fetch();  
    $stmt->free_result();
    $stmt->close();
    if ($stock == 0) {
        echo "<script>alert('The product is currently not in stock.')</script>";
        echo "<script>window.open('index.php','_self')</script>";
        return;
    }

    // if customer is logged in use his id
    if (isset($_SESSION['customer_id'])) { 
        $customerId = $_SESSION['customer_id'];
        $query = "SELECT * FROM cart WHERE customer_id = ? AND product_id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("ss", $customerId, $productId);
    } else {
        // use ip address
        $query = "SELECT * FROM cart WHERE ip_address = ? AND product_id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("ss", $ip, $productId);
    }
	
    $stmt->execute();
    $result = $stmt->get_result(); 
	
	if ($result->num_rows > 0) {  
        // this product is already added to the cart
        // increase quantity
        
        // if customer is logged in use his id
        if (isset($_SESSION['customer_id'])) {
            $customerId = $_SESSION['customer_id'];
            $query = "UPDATE cart SET quantity = quantity + 1 WHERE customer_id = ? AND product_id = ?";
            $stmt = $connection->prepare($query);
            $stmt->bind_param("ss", $customerId, $productId);
        } else {
            // use ip address
            $query = "UPDATE cart SET quantity = quantity + 1 WHERE product_id = ? AND ip_address = ?";
            $stmt = $connection->prepare($query);
            $stmt->bind_param("ss", $productId, $ip);
        }
                                       
        $stmt->execute();
        $stmt->free_result();
        $stmt->close();
        
	}
	else {
        // insert new product
        
        // if customer is logged in use his id
        if (isset($_SESSION['customer_id'])) {
            $customerId = $_SESSION['customer_id'];
            $query = "INSERT INTO cart (product_id, ip_address, customer_id, quantity) VALUES (?, '', ?, 1)";
            $stmt = $connection->prepare($query);
            $stmt->bind_param("ss", $productId, $customerId);
        } else {
            // use ip address
            $query = "INSERT INTO cart (product_id, ip_address, quantity, customer_id) VALUES (?, ?, 1, 0)";
            $stmt = $connection->prepare($query);
            $stmt->bind_param("ss", $productId, $ip);
        }

        $stmt->execute();
        $stmt->free_result();
        $stmt->close();
  
	}
    
    $stmt->free_result();
    $stmt->close();
    
    // take user back to home
    echo "<script>window.open('index.php','_self')</script>";

}

/**
 * Shows the total number of items from the cart
 */
function showTotalItems(){
    global $connection; 

    // if customer is logged in use his id
    if (isset($_SESSION['customer_id'])) {
        $customerId = $_SESSION['customer_id'];
        $query = "SELECT SUM(quantity) total FROM cart WHERE customer_id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("s", $customerId);
    } else {
        // use ip address
        $ip = getIp(); 
        $query = "SELECT SUM(quantity) total FROM cart WHERE ip_address = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("s", $ip);
    }

    $stmt->execute();
    $stmt->bind_result($items);
    $stmt->fetch(); 
    
    $stmt->free_result();
    $stmt->close();
    
    if (is_null($items)) {
        return 0;
    }
    
    return $items;
}
  

/**
 * Shows the total price of the items in the cart.
 */ 
function showTotalPrice(){
		
    global $connection; 
    
    // if customer is logged in use his id
    if (isset($_SESSION['customer_id'])) {
        $customerId = $_SESSION['customer_id'];
        $query = "SELECT SUM(c.quantity * p.price) FROM cart c, product p WHERE c.product_id = p.id AND c.customer_id = ?;";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("s", $customerId);
    } else {
        // use ip address
        $ip = getIp(); 
        $query = "SELECT SUM(c.quantity * p.price) FROM cart c, product p WHERE c.product_id = p.id AND c.ip_address = ?;";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("s", $ip);
    }
    
    $stmt->execute();
    $stmt->bind_result($total);
    $stmt->fetch(); 
    
    $stmt->free_result();
    $stmt->close(); 
    
    if (is_null($total)) {
        return 0;
    }
    
    return $total;

}

/**
 * Returns maximum price existing in the database
 */
function getMaxPrice() {    
    global $connection;
    
    $query = "SELECT MAX(price) FROM product";
    $stmt = $connection->prepare($query);
    $stmt->execute();
    $stmt->bind_result($max);
    $stmt->fetch();
    
    $stmt->free_result();
    $stmt->close();
    
    if (is_null($max)) {
        return 0;
    }
    
    return $max;
}

?>