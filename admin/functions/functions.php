<?php 

$connection = mysqli_connect("localhost", "root", "root", "online_store");

if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

/**
 * Gets all product categories from the db and display them as options
 */
function getCategories(){
	
	global $connection; // global makes it work inside the function
	
	$getCategs = "SELECT * FROM category";	
	$categs = mysqli_query($connection, $getCategs);
    
    echo "<option value=''>Choose a Category...</option>";
	
	while ($categ = mysqli_fetch_array($categs)) {
		$id = $categ['id']; 
		$title = $categ['title'];
        
	    echo "<option value='$id'>$title</option>";
	
	}
}

/**
 * Given value is the id of the category that must be selected
 */
function getCategoriesSelected($value){
	
	global $connection; // global makes it work inside the function
	
	$getCategs = "SELECT * FROM category";	
	$categs = mysqli_query($connection, $getCategs);
    
    echo "<option value=''>Choose a Category...</option>";
	
	while ($categ = mysqli_fetch_array($categs)) {
		$id = $categ['id']; 
		$title = $categ['title'];
        
        // it this category is the one from database make it selected
        if ($value == $id) {
            echo "<option selected='selected' value='$id'>$title</option>";
        } else {
            echo "<option value='$id'>$title</option>";
        }
	
	}
}

/**
 * Gets all brands from the db and display them as options
 */
function getBrands(){
	
	global $connection; 
	
	$getBrands = "SELECT * FROM brand";	
	$brands = mysqli_query($connection, $getBrands);
    
    echo "<option value=''>Choose a Brand...</option>";
	
	while ($brand = mysqli_fetch_array($brands)) {
		$id = $brand['id']; 
		$title = $brand['title'];
        
	    echo "<option value='$id'>$title</option>";
	}
}

/**
 * Given value is the id of the brand that must be selected
 */
function getBrandsSelected($value){
	
	global $connection; 
	
	$getBrands = "SELECT * FROM brand";	
	$brands = mysqli_query($connection, $getBrands);
    
    echo "<option value=''>Choose a Brand...</option>";
	
	while ($brand = mysqli_fetch_array($brands)) {
		$id = $brand['id']; 
		$title = $brand['title'];
        
        // it this brand is the one from database make it selected
        if ($value == $id) {
            echo "<option selected='selected' value='$id'>$title</option>";
        } else {
            echo "<option value='$id'>$title</option>";
        }

	}
}

/**
 * Given value is the status that must be selected
 */
function getStatusSelected($value){
	
	$statuses = array("Processing",
                    "Awaiting Shipment",
                    "Shipped");
	
	foreach ($statuses as $status) {
        if ($value == $status) {
            echo "<option selected='selected' value='$status'>$status</option>";
        } else {
            echo "<option value='$status'>$status</option>";
        }

	}
}

/**
 * Show error if anyone who if not signed in as admin tries to access this page.
 */
function checkAccessDenied() {
    if (!isset($_SESSION['admin_email'])) {
        echo "<script>window.open('access_denied.php','_self')</script>";
    }
}



?>
