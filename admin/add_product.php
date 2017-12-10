<?php
include 'includes/head.php';
include 'includes/log.php';
include 'includes/menu.php';
include 'includes/admin-nav-left.php';

?>

    <div id="products">
        <form action="" method="post" class="action-wrap" enctype="multipart/form-data">
            <h2>New Product Information</h2>
            <p>
                <label>Title:</label> <br>
                <input type="text" name="product-title" required />
            </p>

            <p>
                <label>Category:</label> <br>
                <select name="product-category" required >
                <?php getCategories(); ?>
            </select>
            </p>

            <p>
                <label>Brand:</label> <br>
                <select name="product-brand" required >
                <?php getBrands(); ?>
            </select>
            </p>

            <p>
                <label>Price ($):</label> <br>
                <input type="number" name="product-price" step="0.01" min=0 required />
            </p>

            <p>
                <label>Stock:</label> <br>
                <input type="number" name="product-stock" step="1" min=0 required />
            </p>

            <p>
                <label>Image:</label> <br>
                <input type="file" name="product-image" />
                <label>or enter URL:</label>
                <input type="text" name="product-image-url"  />
            </p>

            <p>
                <label>Description:</label> <br>
                <textarea name="product-desc" cols="20" rows="5" required></textarea>
            </p>

            <p>
                <label>Keywords:</label> <br>
                <input type="text" name="product-keywords" size="50" required />
            </p>

            <button type="submit" name="add-product">Add Product</button>
            
            <button class="cancel-btn" type="button" name="cancel-btn" onClick="window.location.href='products.php';">Cancel</button>

        </form>
    </div>

    <?php include 'includes/footer.php'; ?>

    <?php

/**
 * Add product to db
 */
if (isset($_POST['add-product'])) {
    $title = $_POST['product-title'];
    $category = $_POST['product-category'];
    $brand = $_POST['product-brand'];
    $price = $_POST['product-price'];
    $stock = $_POST['product-stock'];
    $image_url = $_POST['product-image-url'];
    $desc = $_POST['product-desc'];
    $keywords = $_POST['product-keywords'];
    
    // if an image was chosen
    if (isset($_FILES['product-image']) && $_FILES['product-image']['tmp_name'] != "") {
        // get image from field
        $image = $_FILES['product-image']['name'];
        $imageTmp = $_FILES['product-image']['tmp_name'];
        // save photo to directory
        move_uploaded_file($imageTmp, "product_images/$image");
        $image = "admin/product_images/" . $image;
    } else {
        // save url
        $image = $image_url;
    }
    
    // add product to the db
    global $connection;
    
    $query = "INSERT INTO product(title, category, brand, price, stock, image, description, keywords) VALUES('$title', '$category', '$brand', $price, $stock, '$image', '$desc', '$keywords')";  
    $addedProduct = mysqli_query($connection, $query); 
    
    if($addedProduct) {
        echo "<script>alert('Product added successfully!')</script>";
    } else {
        echo "<script>alert('Error adding product!')</script>";
    }
}


?>
