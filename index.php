<?php
include 'includes/head.php';
include 'includes/log.php';
include 'includes/menu.php';
include 'includes/nav-left.php';
?>
        
<div id="products">
    <?php getNewestProducts(); ?>
    <?php getProductsByCategory(); ?>
    <?php getProductsByBrand(); ?>
    <?php getProductsByPrice(); ?>
    <?php getSearchResults(); ?>
</div>

<?php include 'includes/footer.php'; ?>
        