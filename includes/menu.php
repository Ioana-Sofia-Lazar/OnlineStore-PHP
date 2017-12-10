<div id="logo-img-div">
        <img id="logo-img" src="images/logo3.jpg">
</div>
<ul id="meniu">
    <li><a href="index.php">Home</a></li>
    <li><a href="all_products.php">All Products</a></li>
    <li><a href="about_us.php">About Us</a></li>
    <li><a href="contact_us.php">Contact Us</a></li>
</ul>
</nav>
<main>
    <?php addToCart(); ?>
    <div id="cart">
        <span id="money">
            Cart: <?php echo showTotalItems();?> items 
            &ensp;
            Total price: <?php echo "$" . showTotalPrice(); ?>
            &ensp;
            <a href="cart.php">Go to Cart</a>
        </span>
        <form id="searchBox" method="post">
            <button id="searchButton" type="submit" name="submit_search" id="submit_search" >Search</button>
            <input id="search" type="text" name="search_input" placeholder="Search a Product" />
        </form>
        
    </div>
    <div id="productMain">
        
<?php
if (isset($_POST['submit_search'])) {
    $search = $_POST['search_input'];
    echo "<script>window.open('index.php?submit_search=&search_input=$search','_self')</script>";

}
        
?>