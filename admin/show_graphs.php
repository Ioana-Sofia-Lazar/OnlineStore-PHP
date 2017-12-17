<?php
include 'includes/head.php';
include 'includes/log.php';
include 'includes/menu.php';
include 'includes/admin-nav-left.php';
?>

<?php
$fromDate = $_GET["from-date"];
$toDate = $_GET["to-date"];
?>
<div id="products">
    <h2>Showing statistics: <?php echo $fromDate; ?> to <?php echo $toDate; ?></h2><hr>
    
    <h3>Unique visitors</h3>
    <img src="graph.php?from-date=<?php echo $fromDate; ?>&to-date=<?php echo $toDate; ?>&type=unique" />
    
    <h3>Total page views</h3>
    <img src="graph.php?from-date=<?php echo $fromDate; ?>&to-date=<?php echo $toDate; ?>&type=total" />



















