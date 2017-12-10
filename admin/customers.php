<?php
include 'includes/head.php';
include 'includes/log.php';
include 'includes/menu.php';
include 'includes/admin-nav-left.php';
?>

<?php

$query = "SELECT * FROM customer";

// we will display search result instead of all brands
if (isset($_GET['search-input'])) {
    $search = $_GET['search-input'];
    
    $query = "SELECT * FROM customer WHERE id LIKE '%$search%' OR name LIKE '%$search%'";
}

$customers = mysqli_query($connection, $query); 

?>

    <div id="products">
        <span id="searchBox">
            <form action="" method="get">
                <input name="search-input" id="search">
                <button id="searchButton" type="submit" name="search-button">Search</button>
            </form>
        </span>
        
        <form action="" method="post">

        <table class="prod-table">
            <caption>Customers</caption>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>E-mail</th>
                    <th>Country</th>
                    <th>Address</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>

<?php
while ($row = mysqli_fetch_array($customers)) {

    $id = $row['id'];
    $name = $row['name'];
    $email = $row['email'];
    $country = $row['country'];
    $address = $row['address'] . ", " . $row['postal_code'];
    
?>
                <tr>
                    <td><?php echo $id; ?></td>
                    <td><?php echo $name; ?></td>
                    <td><?php echo $email; ?></td>
                    <td><?php echo $country; ?></td>
                    <td><?php echo $address; ?></td>
                    <td>
                        <button><a href="orders.php?search-input=<?php echo $name; ?>&search-button=">View Orders</a></button>
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
 * "Add Brand" button was clicked
 */
if (isset($_POST['add-brand'])) {
    if (!isset($_POST['new-title'])){
        echo "<script>alert('Please enter a title')</script>";
        return;  
    }
    
    // if title is made only of spaces
    $title = $_POST['new-title'];
    $title = trim($title);
    if (empty($title)) {
        echo "<script>alert('Please enter a title')</script>";
        return;  
    }
    
    // add brand to the db
    $query = "INSERT INTO brand(title) VALUES('$title')";
    mysqli_query($connection, $query); 
    
    // reload
    echo "<script>window.open('brands.php','_self')</script>";
   
}

/**
 * "Update changes" button was clicked
 */
if (isset($_POST['update-changes'])) {
    // save modified titles
    if(!empty($_POST['titles'])) {
        // iterate all titles and update in the db
        $titles = $_POST['titles'];
        foreach($titles as $key => $value) {
            $query = "UPDATE brand SET title = '$value' WHERE id = $key";
            mysqli_query($connection, $query); 
        }
    }
    
    // remove selected brands
    if(!empty($_POST['remove'])) {
        // iterate all checkboxes and remove products
        foreach($_POST['remove'] as $removeId) {
            $query = "DELETE FROM brand WHERE id = $removeId";
            mysqli_query($connection, $query); 
        }
    }
    
    // reload
    echo "<script>window.open('brands.php','_self')</script>";
    
}

?>













