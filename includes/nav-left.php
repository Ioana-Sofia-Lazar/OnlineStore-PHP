<div id="categoriesAll">
    <ul id="categories">
        <h1>
            Categories
        </h1>
        <?php getCategories(); ?>
    </ul>
    <ul id="brands">
        <h1 style="border-top: 1px solid #bf9649; padding-top: 10px;">
            Brands
        </h1>
        <?php getBrands(); ?>
    </ul>
    <ul id="brands">
        <h1 style="border-top: 1px solid #bf9649; padding-top: 10px;">
            Search By Price
        </h1>
        <li>
            <form action="" method="get">
                <div id="div-left">
                    From <br>
                    to
                </div>
                <div id="div-middle">
                    <input type="number" step="0.01" min=0 name="min-price"> <br> 
                    <input type="number" step="0.01" min=0 name="max-price">
                </div>
                <div id="div-right">
                    <button type="submit" name="search-price">Search </button>
                </div>
            </form>
        </li>
    </ul>
</div>

<?php

if (isset($_GET['search-price'])) {
    $min = 0;
    $max = getMaxPrice();
    
    if (!empty($_GET['min-price'])) {
        $min = $_GET['min-price'];
    }
    
    if (!empty($_GET['max-price'])) {
        $max = $_GET['max-price'];
    }
    
    echo "<script> window.open('index.php?min-price=$min&max-price=$max','_self') </script>";
}

?>
