<?php
include 'includes/head.php';
include 'includes/log.php';
include 'includes/menu.php';
include 'includes/nav-left.php';
?>
        
<div id="products">
    <?php
    // Display all products from the database
    global $connection; 
	
	$query = "SELECT * FROM product";

	$products = mysqli_query($connection, $query); 

    // if no products were found
	if (!mysqli_num_rows($products)) {
	    echo "<h2>No products where found!</h2>";
        return;
	}
	
	while ($row = mysqli_fetch_array($products)) {
        
        $id = $row['id'];
        $categ = $row['category'];
        $brand = $row['brand'];
        $title = $row['title'];
        $price = $row['price'];
        $image = $row['image'];
	
		displayProductBox($id, $title, $price, $image);
	
	}    
    ?>
</div>

<?php include 'includes/footer.php'; ?>