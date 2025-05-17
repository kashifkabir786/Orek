<?php require_once('../Connections/orek.php'); ?>
<?php require_once('session.php'); ?>
<?php
if (isset($_GET['cart_id'])) {
    $cart_id = $_GET['cart_id'];
    //for showing details
    $query_Recordset2 = "SELECT A.*, B.*, C.fname, C.lname, P.payment_id, P.amount AS payment_amount, P.coupon_discount FROM cart AS A INNER JOIN cart_item AS B ON A.cart_id = B.cart_id INNER JOIN user AS C ON A.user_id = C.user_id INNER JOIN payment AS P ON A.cart_id = P.cart_id WHERE A.cart_id = '$cart_id'";
    $Recordset2 = mysqli_query($orek, $query_Recordset2) or die(mysqli_error($orek));
    $row_Recordset2 = mysqli_fetch_assoc($Recordset2);
    $totalRows_Recordset2 = mysqli_num_rows($Recordset2);

    //for getting shipping_id
    $query_Recordset3 = "SELECT * FROM user_shipping WHERE shipping_id = (SELECT user_shipping_id FROM payment WHERE cart_id = '$cart_id')";
    $Recordset3 = mysqli_query($orek, $query_Recordset3) or die(mysqli_error($orek));
    $row_Recordset3 = mysqli_fetch_assoc($Recordset3);
    $totalRows_Recordset3 = mysqli_num_rows($Recordset3);

    $query_Recordset4 = "SELECT A.*, B.*, P.amount AS payment_amount FROM cart_item AS A INNER JOIN item AS B ON A.item_id = B.item_id INNER JOIN payment AS P ON A.cart_id = P.cart_id WHERE A.cart_id = '$cart_id'";
    $Recordset4 = mysqli_query($orek, $query_Recordset4) or die(mysqli_error($orek));
    $row_Recordset4 = mysqli_fetch_assoc($Recordset4);
    $totalRows_Recordset4 = mysqli_num_rows($Recordset4);
} else {
    header("Location: order.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />

    <title>Shipping Label - Orek</title>
    <meta content="" name="description" />
    <meta content="" name="keywords" />

    <!-- Favicons -->
    <link href="assets/img/logo.png" rel="icon" />

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                margin: 0;
                padding: 0;
                background-color: white;
            }
            #shipping-label {
                width: 210mm !important;
                height: auto !important;
                padding: 10mm !important;
                box-shadow: none !important;
                margin: 0 !important;
                position: relative !important;
            }
            .row {
                display: flex !important;
                flex-wrap: wrap !important;
            }
            .col-md-6 {
                width: 48% !important;
                float: left !important;
                margin-right: 2% !important;
            }
            table, tr, td, th {
                page-break-inside: avoid !important;
            }
        }
        body {
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .actions {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        #shipping-label {
            width: 210mm; /* A4 width */
            height: auto; /* Auto height to accommodate content */
            padding: 15mm;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative;
            font-family: 'Open Sans', sans-serif;
        }
        .label-header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .company-logo {
            max-width: 120px;
        }
        .label-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 10px 0;
            color: #2c3e50;
        }
        .order-details, .shipping-details {
            margin-bottom: 15px;
        }
        .section-title {
            font-weight: bold;
            font-size: 16px;
            color: #3498db;
            margin-bottom: 10px;
            border-bottom: 1px dashed #e0e0e0;
            padding-bottom: 5px;
        }
        .detail-row {
            display: flex;
            margin-bottom: 5px;
        }
        .detail-label {
            font-weight: 600;
            width: 150px;
        }
        .detail-value {
            flex: 1;
        }
        .barcode-section {
            text-align: center;
            margin: 20px 0;
        }
        .barcode {
            max-width: 100%;
            height: 70px;
        }
        .footer-note {
            margin-top: 20px;
            font-size: 12px;
            text-align: center;
            color: #7f8c8d;
        }
        .qr-code {
            text-align: center;
            margin-top: 15px;
        }
        .from-address, .to-address {
            margin-bottom: 15px;
        }
        .address-box {
            border: 1px solid #e0e0e0;
            padding: 10px;
            border-radius: 5px;
            margin-top: 5px;
        }
        .priority-label {
            position: absolute;
            top: 15mm;
            right: 15mm;
            background-color: #e74c3c;
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            font-weight: bold;
            transform: rotate(15deg);
        }
        /* Additional styles to ensure proper layout */
        .row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
        }
        .col-md-6 {
            position: relative;
            width: 50%;
            padding-right: 15px;
            padding-left: 15px;
        }
        @media screen and (max-width: 768px) {
            .col-md-6 {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="actions no-print">
        <button class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
        <button class="btn btn-secondary" onclick="window.close()">Close</button>
    </div>

    <div id="shipping-label">
        <div class="priority-label">PRIORITY</div>
        <div class="label-header">
            <img src="assets/img/logo.png" alt="Orek Logo" class="company-logo">
            <div>
                <h2>OREK</h2>
                <p>Shipping Label</p>
            </div>
        </div>
        
        <div class="label-title">
            Order ID: <?php echo $row_Recordset2['cart_id']; ?>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="from-address">
                    <div class="section-title">FROM (Sender):</div>
                    <div class="address-box">
                        <strong>OREK</strong><br>
                        Headquarters,<br>
                        Jamshedpur, Jharkhand<br>
                        PIN: 831001<br>
                        Email: care@orek.in
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="to-address">
                    <div class="section-title">TO (Customer):</div>
                    <div class="address-box">
                        <strong><?php echo $row_Recordset3['recipient_name']; ?></strong><br>
                        <?php echo $row_Recordset3['address']; ?><br>
                        <?php echo $row_Recordset3['city']; ?>, <?php echo $row_Recordset3['state']; ?><br>
                        PIN: <?php echo $row_Recordset3['pin_code']; ?><br>
                        Phone: <?php echo $row_Recordset3['phone']; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="order-details">
                    <div class="section-title">ORDER DETAILS</div>
                    <div class="detail-row">
                        <div class="detail-label">Order Date:</div>
                        <div class="detail-value"><?php echo date('d F, Y', strtotime($row_Recordset2['date'])); ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Payment Amount:</div>
                        <div class="detail-value">₹<?php echo $row_Recordset2['payment_amount']; ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Total Items:</div>
                        <div class="detail-value"><?php echo $totalRows_Recordset4; ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Total Quantity:</div>
                        <div class="detail-value"><?php echo $row_Recordset2['qnty']; ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="barcode-section">
                    <div class="text-center mt-3 mb-2">
                        <img src="https://barcode.tec-it.com/barcode.ashx?data=<?php echo $row_Recordset2['cart_id']; ?>&code=Code128&translate-esc=true" alt="Barcode" class="barcode">
                        <div class="mt-1"><?php echo $row_Recordset2['cart_id']; ?></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="shipping-details">
            <div class="section-title">PACKAGE CONTENTS</div>
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Item</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if ($totalRows_Recordset4 > 0) {
                        mysqli_data_seek($Recordset4, 0);
                        $count = 1;
                        while ($row_item = mysqli_fetch_assoc($Recordset4)) {
                    ?>
                    <tr>
                        <td><?php echo $count++; ?></td>
                        <td><?php echo $row_item['item_name']; ?></td>
                        <td><?php echo $row_item['qnty']; ?></td>
                    </tr>
                    <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
        
        <div class="footer-note">
            <p>This is an official shipping label issued by OREK.</p>
            <p>Thank you for shopping with OREK!</p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto print dialog when page loads
        window.onload = function() {
            // Small delay to ensure page is fully loaded
            setTimeout(function() {
                // Uncomment the line below to automatically show print dialog when page loads
                // window.print();
            }, 1000);
        }
    </script>
</body>

</html> 