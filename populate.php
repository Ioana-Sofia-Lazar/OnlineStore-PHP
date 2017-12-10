<?php

/*
 * Populates the database with products from th given furniture website.
 */

$connection = new mysqli("localhost", "root", "root", "online_store");

// Check for MySql error
if ($connection->connect_error) {
    die("$mysqli->connect_errno: $mysqli->connect_error");
}

$stmt = $connection->stmt_init();

loadProductsFrom("https://www.amara.com/shop/cushions", 1);
loadProductsFrom("https://www.amara.com/shop/throws", 2);
loadProductsFrom("https://www.amara.com/shop/mirrors", 3);
loadProductsFrom("https://www.amara.com/shop/curtains", 4);
loadProductsFrom("https://www.amara.com/shop/rugs", 5);
loadProductsFrom("https://www.amara.com/shop/storage/sort/popularity", 6);
loadProductsFrom("https://www.amara.com/shop/wallpaper/sort/popularity", 7);
loadProductsFrom("https://www.amara.com/shop/vases", 9);
loadProductsFrom("https://www.amara.com/shop/candle-holders", 10);
loadProductsFrom("https://www.amara.com/shop/clocks/sort/popularity", 11);
loadProductsFrom("https://www.amara.com/shop/wall-plates", 12);
loadProductsFrom("https://www.amara.com/shop/indoor-pots", 13);
loadProductsFrom("https://www.amara.com/shop/door-mats", 14);

function getStock() {
    $maxStock = 50;
     
    return rand(0, $maxStock);
}

function getBrand() {
    $nbBrands = 12;
     
    return rand(1, $nbBrands);
}

function getNbProducts() {
    return rand(5, 15);
}

function loadProductsFrom($url, $categId) {
    // loading html content from remote url
    $html = file_get_contents($url);
    $dom = new domDocument; 
    $dom->loadHTML($html);  
    $finder = new DomXPath($dom);

    // getting all divs with class "product-container"
    $divs = $finder -> query("//div[@class='product-container']");

    // insert random amount of products in this category
    $number = getNbProducts();
    
    $count = 0;
    foreach ($divs as $div) { 
        // get product image
        $div1 = $div->getElementsByTagName("div")->item(1);
        $a1 = $div1->getElementsByTagName("a")->item(0);
        $img = $a1->getElementsByTagName("img")->item(0);
        $imgSrc = $img->getAttribute("src");

        // get product title
        $div2 = $div->getElementsByTagName("div")->item(2);
        $a2 = $div2->getElementsByTagName("a")->item(0);
        $span = $a2->getElementsByTagName("span")->item(1);
        $title = $span->textContent;

        // get product price
        $span1 = $div2->getElementsByTagName("span")->item(2);
        // keep only digits (remove spaces and currency) 
        // some items have a price range (ex: $135 to $175)
        $price = strtok($span1->textContent, "to");
        $price = preg_replace('/[^0-9.]+/', '', $price);
        $price = (float) $price;
        
        $brand = getBrand();
        $stock = getStock();
        
        echo $imgSrc . " " . $title . " " . $price . " " . $brand . " " . $stock . "<br><br>";
        addProduct($imgSrc, $title, $price, $brand, $stock, $categId);
        
        $count++;
        if ($count == $number)
            break;

    } 
}

function addProduct($imgSrc, $title, $price, $brand, $stock, $categ) {
    global $connection; 
    $keywords = $title . " " . $brand;
    
    $query = "INSERT INTO product(title, price, image, brand, category, stock, description, keywords) VALUES(?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("ssssssss", $title, $price, $imgSrc, $brand, $categ, $stock, $title, $keywords);
    $stmt->execute();
    
    $stmt->free_result();
    $stmt->close();

}

?>