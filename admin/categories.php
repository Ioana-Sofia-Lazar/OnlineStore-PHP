<?php
include 'includes/head.php';
include 'includes/log.php';
include 'includes/menu.php';
include 'includes/admin-nav-left.php';
?>

<?php

$query = "SELECT * FROM category";

// we will display search result instead of all categories
if (isset($_GET['search-input'])) {
    $search = $_GET['search-input'];
    
    $query = "SELECT * FROM category WHERE id LIKE '%$search%' OR title LIKE '%$search%'";
}

$categories = mysqli_query($connection, $query); 

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
            <button type="button" onClick="window.location.href='categories.php';">Cancel</button>
            <button title="Click to save changes made on categories titles and to remove selected." type="submit" name="update-changes">Save Changes</button>
        </span>

        <table class="prod-table">
            <caption>Manage Categories</caption>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody>

<?php
while ($row = mysqli_fetch_array($categories)) {

    $id = $row['id'];
    $title = $row['title'];
    
?>
                <tr>
                    <td><?php echo $id; ?></td>
                    <td>
                         <input type="text" name="titles['<?php echo $id; ?>']" value="<?php echo $title; ?>" required />
                    </td>
                    <td>
                        <input type="checkbox" name="remove[]" value="<?php echo $id; ?>">
                    </td>
                </tr>
                
<?php
}
?>
                <tr height="80px">
                    <td>Add a new Category</td>
                    <td>
                        <input type="text" name="new-title" />
                    </td>
                    <td>
                        <button title="Click to add a new Category" type="submit" name="add-category">Add Category</button>
                    </td>
                </tr>
                
            </tbody>
        </table>
   </form>
</div>

<?php include 'includes/footer.php'; ?>
    
<?php

/**
 * "Add Category" button was clicked
 */
if (isset($_POST['add-category'])) {
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
    
    // add category to the db
    $query = "INSERT INTO category(title) VALUES('$title')";
    mysqli_query($connection, $query); 
    
    // reload
    echo "<script>window.open('categories.php','_self')</script>";
   
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
            $query = "UPDATE category SET title = '$value' WHERE id = $key";
            mysqli_query($connection, $query); 
        }
    }
    
    // remove selected categories
    if(!empty($_POST['remove'])) {
        // iterate all checkboxes and remove products
        foreach($_POST['remove'] as $removeId) {
            $query = "DELETE FROM category WHERE id = $removeId";
            mysqli_query($connection, $query); 
        }
    }
    
    // reload
    echo "<script>window.open('categories.php','_self')</script>";
    
}

?>













