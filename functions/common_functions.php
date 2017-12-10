<?php

/**
 * Parse html to get a list with all countries.
 */
function getCountries() {
    // loading html content from remote url
    $html = file_get_contents("http://www.nationsonline.org/oneworld/countries_of_the_world.htm");
    $dom = new domDocument; 
    $dom->loadHTML($html);  
    $finder = new DomXPath($dom);

    // getting all links within "td" with class "tdb"
    $links = $finder -> query("//td[@class='tdb']/a");
    
    $countries = array();
    
    foreach ($links as $link) { 
        $country = $link->textContent;
        $countries[] = $country;
    } 
    
    return $countries;
}

/**
 * Display countries as options for select.
 */
function showCountries() {
    
    $countries = getCountries();

    echo "<option value=''>Choose Country...</option>";
    
    foreach ($countries as $country) { 
        echo "<option value='$country'>$country</option>";
    } 
    
}

/**
 * Display countries as options for select and make the given value selected
 */
function showCountriesSelected($value) {
    
    $countries = getCountries();

    echo "<option value=''>Choose Country...</option>";
    
    foreach ($countries as $country) { 
        
        // it this country needs to be the selected one
        if ($value == $country) {
            echo "<option selected='selected' value='$country'>$country</option>";
        } else {
            echo "<option value='$country'>$country</option>";
        }
        
    } 
    
}

/**
 * Gets the user IP address
 */ 
function getIp() {
    $ip = $_SERVER['REMOTE_ADDR'];
 
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
 
    return $ip;
}

/**
 * 
 */
function removeGetVariable($varName) {
    
    // get current url
    $string = basename($_SERVER['REQUEST_URI']);;

    $parts = parse_url($string);

    $queryParams = array();
    parse_str($parts['query'], $queryParams);

    // remove parameter
    unset($queryParams[$varName]);
    
    // rebuild the url
    $queryString = http_build_query($queryParams);
    $url = $parts['path'] . '?' . $queryString;
    
    return $url;
    
}

/**
 * For a page that is only accessible for a logged in customer, show an error message if anyone else tries to access it.
 */
function checkAccessDenied() {
    if (!isset($_SESSION['customer_email'])) {
        echo "<script>window.open('access_denied.php','_self')</script>";
    }
}

?>

















