<?php
session_start();
include("./includes/db.php");

$orderId = $_SESSION['orderId'];
global $connection;

// get order details
$query = "SELECT o.id, o.date, o.amount, c.name, c.country, c.address, c.postal_code FROM orders o, customer c WHERE o.customer_id = c.id AND o.id = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $orderId);
$stmt->execute();
$orders = $stmt->get_result();

$row = $orders->fetch_assoc();

$id = $row['id'];
$date = $row['date'];
$amount = $row['amount'];
$cName = $row['name'];
$cCountry = $row['country'];
$cAddress = $row['address'];
$cCode = $row['postal_code'];

// get products and quantity for this order
$query = "SELECT p.title, i.quantity, p.price, p.description FROM order_item i, product p WHERE order_id = ? AND i.product_id = p.id";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $id);
$stmt->execute();
$items = $stmt->get_result();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Print Invoice</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            font-family: Arial;
            font-size: 10pt;
            color: #000;
        }

        body {
            width: 100%;
            font-family: Arial;
            font-size: 10pt;
            margin: 0;
            padding: 0;
        }

        p {
            margin: 0;
            padding: 0;
        }

        #wrapper {
            width: 180mm;
            margin: 0 15mm;
        }

        .page {
            height: 297mm;
            width: 210mm;
            page-break-after: always;
        }

        table {
            border-left: 1px solid #ccc;
            border-top: 1px solid #ccc;

            border-spacing: 0;
            border-collapse: collapse;

        }

        table td {
            border-right: 1px solid #ccc;
            border-bottom: 1px solid #ccc;
            padding: 2mm;
        }

        table.heading {
            height: 50mm;
        }

        h1.heading {
            font-size: 14pt;
            color: #000;
            font-weight: normal;
        }

        h2.heading {
            font-size: 9pt;
            color: #000;
            font-weight: normal;
        }

        hr {
            color: #ccc;
            background: #ccc;
        }

        #invoice_body {
            height: 149mm;
        }

        #invoice_body,
        #invoice_total {
            width: 100%;
        }

        #invoice_body table,
        #invoice_total table {
            width: 100%;
            border-left: 1px solid #ccc;
            border-top: 1px solid #ccc;

            border-spacing: 0;
            border-collapse: collapse;

            margin-top: 5mm;
        }

        #invoice_body table td,
        #invoice_total table td {
            text-align: center;
            font-size: 9pt;
            border-right: 1px solid #ccc;
            border-bottom: 1px solid #ccc;
            padding: 2mm 0;
        }

        #invoice_body table td.mono,
        #invoice_total table td.mono {
            font-family: monospace;
            text-align: right;
            padding-right: 3mm;
            font-size: 10pt;
        }

        #footer {
            width: 180mm;
            margin: 0 15mm;
            padding-bottom: 3mm;
        }

        #footer table {
            width: 100%;
            border-left: 1px solid #ccc;
            border-top: 1px solid #ccc;

            background: #eee;

            border-spacing: 0;
            border-collapse: collapse;
        }

        #footer table td {
            width: 25%;
            text-align: center;
            font-size: 9pt;
            border-right: 1px solid #ccc;
            border-bottom: 1px solid #ccc;
        }

    </style>
</head>

<body>
    <div id="wrapper">

        <p style="text-align:center; font-weight:bold; padding-top:5mm;">INVOICE</p>
        <br />
        <table class="heading" style="width:100%;">
            <tr>
                <td style="width:80mm;">
                    <h1 class="heading">Online Store</h1>
                    <h2 class="heading">
                        12 Awesome Street<br /> AwesomeCity<br /> AwesomeCounty, Country<br /> Website : www.onlinestore.com<br /> E-mail : info@onlinestore.com
                    </h2>
                </td>
                <td rowspan="2" valign="top" align="right" style="padding:3mm;">
                    <table>
                        <tr>
                            <td>Invoice No: </td>
                            <td><?php echo $id; ?></td>
                        </tr>
                        <tr>
                            <td>Date: </td>
                            <td><?php echo $date; ?></td>
                        </tr>
                        <tr>
                            <td>Currency: </td>
                            <td>USD</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <b>Buyer</b>:<br /> <?php echo $cName; ?><br /> <?php echo $cAddress . ', ' . $cCode; ?> 
                    <br /> <?php echo $cCountry; ?> <br />
                </td>
            </tr>
        </table>


        <div id="content">

            <div id="invoice_body">
                <table>
                    <tr style="background:#eee;">
                        <td style="width:8%;"><b>Sl. No.</b></td>
                        <td><b>Product</b></td>
                        <td style="width:15%;"><b>Quantity</b></td>
                        <td style="width:15%;"><b>Price</b></td>
                        <td style="width:15%;"><b>Total</b></td>
                    </tr>
                </table>

                <table>
                    <?php
                    $num = 1;
                    while ($item = $items->fetch_assoc()) {

                        $title = $item['title'];
                        $quantity = $item['quantity'];
                        $price = $item['price'];
                        $description = $item['description'];
        
                    ?>
                    <tr>
                        <td style="width:8%;"><?php echo $num; $num += 1; ?></td>
                        <td style="text-align:left; padding-left:10px;"><?php echo $title; ?><br />Description: <?php echo $description; ?></td>
                        <td class="mono" style="width:15%;"><?php echo $quantity; ?></td>
                        <td style="width:15%;" class="mono">$<?php echo $price; ?></td>
                        <td style="width:15%;" class="mono">$<?php echo $price * $quantity; ?></td>
                    </tr>
                    <?php
                    }
                    ?>
                    <tr>
                        <td colspan="3"></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td colspan="3"></td>
                        <td>Shipping:</td>
                        <td class="mono">$10</td>
                    </tr>
                    
                    <tr>
                        <td colspan="3"></td>
                        <td>Total:</td>
                        <td class="mono">$<?php echo $amount + 10; ?></td>
                    </tr>
                </table>
            </div>  
        </div>

    </div>

    <htmlpagefooter name="footer">
        <hr />
        <div id="footer">
            <p>Thank you for choosing us!</p>
            <p>Online Store</p>
        </div>
    </htmlpagefooter>
    <sethtmlpagefooter name="footer" value="on" />

</body>

</html>
