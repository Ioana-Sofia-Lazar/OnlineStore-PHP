<?php
include 'includes/head.php';
include 'includes/log.php';
include 'includes/menu.php';
include 'includes/admin-nav-left.php';
?>

<?php

if (!isset($_GET['product-id'])) {
    echo "<script>window.open('products.php','_self')</script>";
}

$productId = $_GET['product-id'];

$query = "SELECT * FROM product WHERE id = $productId";  
$res = mysqli_query($connection, $query);
$row = mysqli_fetch_array($res);

$title = $row['title'];
$category = $row['category'];
$brand = $row['brand'];
$price = $row['price'];
$stock = $row['stock'];
$image = $row['image'];
$desc = $row['description'];
$keywords = $row['keywords'];

// check if image path is given (and not url)
if (substr($image, 0, 5) === "admin") {
    // correct path for admin will be "product_images/.." instead of "admin/product_images/.."
    // remove the "admin"
    $image = strstr($image, "/");
    // remove the "/"
    $image = substr($image, 1);
}

// if image is saved as url display it in the url field
$imageUrl = "";
if (substr($image, 0, 4) === "http") {
    $imageUrl = $image;
}

?>

    <div id="products">
        <form action="" method="post" class="action-wrap" enctype="multipart/form-data">
            <h2>Edit Product Information</h2>
            <p>
                <label>Title:</label> <br>
                <input type="text" name="product-title" value="<?php echo $title; ?>" required />
            </p>

            <p>
                <label>Category:</label> <br>
                <select name="product-category" value="<?php echo $category; ?>" required >
                <?php getCategoriesSelected($category); ?>
            </select>
            </p>

            <p>
                <label>Brand:</label> <br>
                <select name="product-brand" value="<?php echo $brand; ?>" required >
                <?php getBrandsSelected($brand); ?>
            </select>
            </p>

            <p>
                <label>Price ($):</label> <br>
                <input type="number" name="product-price" step="0.01" min=0 value="<?php echo $price; ?>" required />
            </p>

            <p>
                <label>Stock:</label> <br>
                <input type="number" name="product-stock" step="1" min=0 value="<?php echo $stock; ?>" required />
            </p>

            <p>
                <label>Image:</label> <br>
                <img width="300px" src="<?php echo $image; ?>" alt="<?php echo $title; ?>">
                <input type="file" name="product-image" />
                <label>or enter URL:</label>
                <input type="text" name="product-image-url" value="<?php echo $imageUrl; ?>" />
            </p>

            <p>
                <label>Description:</label> <br>
                <textarea name="product-desc" cols="20" rows="5" required><?php echo $desc; ?></textarea>
            </p>

            <p>
                <label>Keywords:</label> <br>
                <input type="text" name="product-keywords" size="50" value="<?php echo $keywords; ?>" required />
            </p>

            <button type="submit" name="edit-product">Update Changes</button>
            
            <button class="cancel-btn" type="button" name="cancel-btn" onClick="window.location.href='products.php';">Cancel</button>

        </form>
    </div>

    <?php include 'includes/footer.php'; ?>

    <?php

/**
 * Update product in the db
 */
if (isset($_POST['edit-product'])) {
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
        $image2 = $_FILES['product-image']['name'];
        $imageTmp = $_FILES['product-image']['tmp_name'];
        // save photo to directory
        move_uploaded_file($imageTmp, "product_images/$image2");
        $image2 = "admin/product_images/" . $image2;
    } else if (!empty($image_url)) {
        // save url
        $image2 = $image_url;
    } else {
        // no new image was selected -- keep the old one
        $image2 = $image;
    }
    
    // update product in the db
    global $connection;
    
    $query = "UPDATE product SET title = '$title', category = $category, brand = $brand, price = $price, stock = $stock, image = '$image2', description = '$desc', keywords = '$keywords' WHERE id = $productId";  
    $res = mysqli_query($connection, $query); 
    
    if($res) {
        echo "<script>alert('Product successfully edited!')</script>";
        echo "<script>window.open('products.php', '_self')</script>";
    } else {
        echo "<script>alert('Error editing product!')</script>";
    }
}


?>