<?php
$connection = mysqli_connect("localhost", "root", "root", "online_store");

if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

/**
 * Show graph
 */
require_once ('../../../jpgraph-4.1.0/src/jpgraph.php');
require_once ('../../../jpgraph-4.1.0/src/jpgraph_line.php');
require_once ('../../../jpgraph-4.1.0/src/jpgraph_date.php');
require_once ('../../../jpgraph-4.1.0/src/jpgraph_scatter.php');

// create an array of strings with all dates between the given range
$fromDate = $_GET["from-date"];
$toDate = $_GET["to-date"];
$period = new DatePeriod(
     new DateTime($fromDate),
     new DateInterval('P1D'),
     new DateTime($toDate)
);
$period = getPeriod($period);

// get numbers of visitors/views
$datay1 = getDataArray();

// Setup the graph
$graph = new Graph(850, 450);
$graph->SetMargin(40,40,30,100);

$graph->SetScale("intlin", 0, max($datay1) + 5, 0, count($period) - 1);

$graph->SetBox(false);
$graph->yaxis->HideLine(false);
$graph->yaxis->HideTicks(false,false);
$graph->yaxis->HideZeroLabel();

$graph->xaxis->SetTickLabels($period);
$graph->xaxis->SetLabelAngle(90);

// Create the plot
$p1 = new LinePlot($datay1, range(0, count($period) - 1));
$graph->Add($p1);

// Circle marker
$p1->SetColor('#CD5C5C');
$p1->mark->SetType(MARK_FILLEDCIRCLE,'',1.0);

$graph->Stroke();

/**
 * Gets an array of numbers that represents the number of visitors/views
 */
function getDataArray() {
    $type = $_GET["type"];
    $visitors = array();
    $dates = array();
    
    if ($type == "unique") {
        $visitors = getUnique();
    } else if ($type == "total") {
        $visitors = getTotal();
    }
    
    return $visitors;
}

function getUnique() {
    global $connection, $period;
    $visitors = array();
    $dates = array();
    
    foreach ($period as $date) {
        $query = "SELECT count(*) num FROM unique_visitors WHERE date_field = STR_TO_DATE(?, '%Y-%m-%d');";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("s", $date);
        $stmt->execute();
        $rows = $stmt->get_result();
        $row = $rows->fetch_assoc();
        
        $visitors[] = $row["num"];        
    }
    
    return $visitors;
}

function getTotal() {
   global $connection, $period;
    $visitors = array();
    $dates = array();
    
    foreach ($period as $date) {
        $query = "SELECT IFNULL(SUM(views), 0) total FROM unique_visitors WHERE date_field = STR_TO_DATE(?, '%Y-%m-%d');";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("s", $date);
        $stmt->execute();
        $rows = $stmt->get_result();
        $row = $rows->fetch_assoc();
        
        $visitors[] = $row["total"];        
    }
    
    return $visitors;
}

function getPeriod($period) {
    $dates = array();
    foreach($period as $date) {
        $dates[] = $date->format('Y-m-d');
    }
   
    return $dates;
}