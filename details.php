<?php

include 'includes/head.php';
include 'includes/log.php';
include 'includes/menu.php';
include 'includes/nav-left.php';

?>
<div id="products">
    <?php showProductDetails(); ?>
</div>

<?php
/**
 * Show details for the selected product.
 */
function showProductDetails() {
    
    global $connection;
    
    if(isset($_GET['product_id'])) {

        $productId = $_GET['product_id'];

        $query = "SELECT p.*, b.title brand_title FROM product p, brand b WHERE p.id = ? AND b.id = p.brand";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("s", $productId);
        $stmt->execute();
        $products = $stmt->get_result();

        while($row = $products->fetch_assoc()) {

            $id = $row['id'];
            $categ = $row['category'];
            $brand = $row['brand_title'];
            $title = $row['title'];
            $price = $row['price'];
            $image = $row['image'];
            $stock = $row['stock'];
            $description = $row['description'];

            echo "
                    <div id='product-details'>

                        <h3 align='center'>$title</h3>

                        <img src='$image' height='300' />

                        <p><b> $$price </b></p>
                        <p>Brand: $brand </p>
                        <p>In stock: $stock</p>
                        <p>Description: $description </p>                     

                        <button onclick='history.go(-1);'>Go Back</button>

                        <button><a href='index.php?add_to_cart=$id'>Add to Cart</a></button>

                    </div>

            ";

        }
    }
}

include 'includes/footer.php'; 
?>