<?php
include 'includes/head.php';
include 'includes/log.php';
include 'includes/menu.php';
include 'includes/admin-nav-left.php';
?>
<div id="products">

<form action="" method="post"> 

    <p>
        <label>From: </label>
        <input type="date" min="2017-01-01" max=" <?php echo date('Y-m-d'); ?>" name="from-date" required>
    </p>
    <p>
        <label>To: </label>
        <input type="date" min="2017-01-01" max=" <?php echo date('Y-m-d'); ?>" name="to-date" required>
    </p>
    <button type="submit" name="btn-stats">Show Stats</button>
    
</form>

</div>

<?php
if (isset($_POST["btn-stats"])) {
    $fromDate = $_POST["from-date"];
    $toDate = $_POST["to-date"];
    
    if ($fromDate > $toDate) {
        echo "<script>alert('Please enter valid dates!');</script>";
        return;
    }
    
    header("Location: show_graphs.php?from-date=$fromDate&to-date=$toDate");
    //header("Location: graph.php?from-date=$fromDate&to-date=$toDate&type=unique");
}
?>

















