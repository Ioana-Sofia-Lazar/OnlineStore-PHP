<?php
ob_start();
session_start();

if (!isset($_GET["order_id"])) {
    // take customer back to orders table
    echo "<script>window.open('customer_orders.php','_self')</script>";
} 
   
$invoiceId = $_GET["order_id"];
$_SESSION["orderId"] = $invoiceId;

include 'MPDF/mpdf60/mpdf.php';
//ob_start();  // start output buffering
include 'invoice.php';

$content = ob_get_clean(); // get content of the buffer and clean the buffer
$mpdf = new mPDF();
$mpdf->SetDisplayMode('fullpage');
$mpdf->WriteHTML($content);

$invoiceName = "invoice-" . $invoiceId . ".pdf"; 
$mpdf->Output($invoiceName);
ob_end_clean();

// take customer back to orders table
echo "<script>window.open('customer_orders.php','_self')</script>";




