<?php
include 'includes/head.php';
include 'includes/log.php';
include 'includes/menu.php';
include 'includes/admin-nav-left.php';
?>

<?php

$query = "SELECT p.id, p.price, p.title title, b.title category, c.title brand, p.stock, p.description FROM `product` p, brand b, category c WHERE p.brand = b.id AND p.category = c.id ORDER BY p.id";

// we will display search result instead of all products
if (isset($_GET['search-input'])) {
    $search = $_GET['search-input'];
    
    $query = "SELECT p.id, p.price, p.title title, b.title category, c.title brand, p.stock, p.description FROM `product` p, brand b, category c WHERE p.brand = b.id AND p.category = c.id AND (p.title LIKE '%$search%' OR p.description LIKE '%$search%' OR p.id LIKE '%$search%') ORDER BY p.id";
}

$products = mysqli_query($connection, $query); 

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
            <button type="submit" name="add-product">Add product</button>
            <button type="submit" name="remove-selected">Remove Selected</button>
        </span>

        <table class="prod-table">
            <caption>Manage Products </caption>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Remove</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
<?php
while ($row = mysqli_fetch_array($products)) {

    $id = $row['id'];
    $categ = $row['category'];
    $brand = $row['brand'];
    $title = $row['title'];
    $price = $row['price'];
    $stock = $row['stock'];
?>
                <tr>
                    <td><?php echo $id; ?></td>
                    <td><?php echo $title; ?></td>
                    <td><?php echo $categ; ?></td>
                    <td><?php echo $brand; ?></td>
                    <td><?php echo $price; ?></td>
                    <td>
                        <p><?php echo $stock; ?></p>
                    </td>
                    <td>
                        <input type="checkbox" name="remove[]" value="<?php echo $id; ?>">
                    </td>
                    <td>
                        <button><a href="edit_product.php?product-id=<?php echo $id; ?>">Edit</a></button>
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
 * Adding to stock for product
 */
if (isset($_GET['add-for'])) {
    $productId = $_GET['add-for'];
    $addStock = $_POST['add-stock'];
    
    global $connection;
    
    $query = "UPDATE product SET stock = stock + $addStock WHERE id = $productId";  
    mysqli_query($connection, $query); 
    
    // reload
    echo "<script>window.open('products.php','_self')</script>";
}

/**
 * "Remove Selected" button was clicked
 */
if (isset($_POST['remove-selected'])) {

    if(!empty($_POST['remove'])) {
        // iterate all checkboxes and remove products
        foreach($_POST['remove'] as $removeId) {
            $query = "DELETE FROM product WHERE id = $removeId";
            mysqli_query($connection, $query); 
        }
    }
    
    // reload
    echo "<script>window.open('products.php','_self')</script>";
    
}

/**
 * "Add product" button was clicked
 */
if (isset($_POST['add-product'])) {
    echo "<script>window.open('add_product.php','_self')</script>";
}

?>


















