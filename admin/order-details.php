<?php require_once('../Connections/orek.php'); ?>
<?php require_once('session.php'); ?>
<?php
if ( isset( $_GET[ 'cart_id' ] ) ) {
    $cart_id = $_GET[ 'cart_id' ];
    //for showing details
    $query_Recordset2 = "SELECT A.*, B.*, C.fname, C.lname, P.payment_id, P.amount AS payment_amount, P.coupon_discount FROM cart AS A INNER JOIN cart_item AS B ON A.cart_id = B.cart_id INNER JOIN user AS C ON A.user_id = C.user_id INNER JOIN payment AS P ON A.cart_id = P.cart_id WHERE A.cart_id = '$cart_id'";
    $Recordset2 = mysqli_query( $orek, $query_Recordset2 )or die( mysqli_error( $orek ) );
    $row_Recordset2 = mysqli_fetch_assoc( $Recordset2 );
    $totalRows_Recordset2 = mysqli_num_rows( $Recordset2 );

    //for getting shipping_id
    $query_Recordset3 = "SELECT * FROM user_shipping WHERE shipping_id = (SELECT user_shipping_id FROM payment WHERE cart_id = '$cart_id')";
    $Recordset3 = mysqli_query( $orek, $query_Recordset3 )or die( mysqli_error( $orek ) );
    $row_Recordset3 = mysqli_fetch_assoc( $Recordset3 );
    $totalRows_Recordset3 = mysqli_num_rows( $Recordset3 );

}

    $query_Recordset4 = "SELECT A.*, B.*, P.amount AS payment_amount FROM cart_item AS A INNER JOIN item AS B ON A.item_id = B.item_id INNER JOIN payment AS P ON A.cart_id = P.cart_id WHERE A.cart_id = '$cart_id'";
    $Recordset4 = mysqli_query( $orek, $query_Recordset4 )or die( mysqli_error( $orek ) );
    $row_Recordset4 = mysqli_fetch_assoc( $Recordset4 );
    $totalRows_Recordset4 = mysqli_num_rows( $Recordset4 );

$editFormAction = $_SERVER[ 'PHP_SELF' ];
if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
    $editFormAction .= "?" . htmlentities( $_SERVER[ 'QUERY_STRING' ] );
}
if ( ( isset( $_POST[ "MM_update" ] ) ) && ( $_POST[ "MM_update" ] == "form1" ) ) {
    $updateSQL = sprintf( "UPDATE `shipping` SET `shipping_date` = %s, `status` = %s WHERE shipping_id = %s",
        GetSQLValueString( $_POST[ 'shipping_date' ], "text" ),
        GetSQLValueString( 'Shipped', "text" ),
        GetSQLValueString( $_POST[ 'shipping_id' ], "int" ) );
    $Result = mysqli_query( $orek, $updateSQL )or die( mysqli_error( $orek ) );

    $insertGoTo = "order-details.php?success=Updated";
    if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
        $insertGoTo .= ( strpos( $insertGoTo, '?' ) ) ? "&" : "?";
        $insertGoTo .= $_SERVER[ 'QUERY_STRING' ];
    }
    header( sprintf( "Location: %s", $insertGoTo ) );
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />

    <title>Order Details - Orek</title>
    <meta content="" name="description" />
    <meta content="" name="keywords" />

    <!-- Favicons -->
    <link href="assets/img/logo.png" rel="icon" />
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon" />

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect" />
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet" />

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet" />
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet" />
    <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet" />
    <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet" />
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet" />
    <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet" />

    <!-- Template Main CSS File -->
    <link href="assets/css/style.css" rel="stylesheet" />
</head>

<body>

    <?php require_once('menu.php'); ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Order Details</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active">Order Details</li>
                </ol>
            </nav>
        </div>
        <!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <a href="order.php" class="btn btn-secondary">Back</a>
                    <button class="btn btn-primary" onclick="window.open('print-shipping-label.php?cart_id=<?php echo $cart_id; ?>', '_blank')"><i class="bi bi-printer"></i> Print Shipping Label</button>
                </div>
                <!-- Left side columns -->
                <?php
        if ( isset( $_GET[ 'success' ] ) ) {
            echo '<div class="col-md-12">';
            echo '<div class="alert alert-success"> Order ' . $_GET[ 'success' ] . ' Successfully</div>';
            echo '</div>';
        }
        ?>
                <div class="col-lg-12">
                    <div class="row">
                        
                        <div class="col-md-12">
                            <div class="card shadow-lg border-0 rounded">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="p-3">
                                                <h5 class="text-primary"><i class="fas fa-shopping-cart"></i> Order
                                                    Details
                                                </h5>
                                                <p><strong>🛒 Order ID:</strong>
                                                    <?php echo $row_Recordset2['cart_id']; ?>
                                                </p>
                                                <p><strong>👤 User Name:</strong>
                                                    <?php echo $row_Recordset2['fname'] . " " . $row_Recordset2['lname']; ?>
                                                </p>
                                                <p><strong>📅 Date:</strong>
                                                    <?php echo date('d F, Y', strtotime($row_Recordset2['date'])); ?>
                                                </p>
                                                <p><strong>💰 Amount:</strong>
                                                    ₹<?php echo $row_Recordset2['payment_amount']; ?></p>
                                                <p><strong>📦 Quantity:</strong> <?php echo $row_Recordset2['qnty']; ?>
                                                </p>
                                                <p><strong>📦 Status:</strong>
                                                    <span
                                                        class="badge bg-<?php echo ($row_Recordset2['status'] == 'Completed') ? 'success' : 'warning'; ?>">
                                                        <?php echo $row_Recordset2['status']; ?>
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="p-3">
                                                <h5 class="text-primary"><i class="fas fa-box"></i> Shipping Details
                                                </h5>
                                                <p><strong>📦 Contact Person:</strong>
                                                    <?php echo $row_Recordset3['recipient_name']; ?></p>
                                                <p><strong>📦 Shipping Type:</strong>
                                                    <?php echo $row_Recordset3['address_name']; ?>
                                                </p>
                                                <p><strong>📦 Shipping Address:</strong>
                                                    <?php echo $row_Recordset3['address']; ?></p>
                                                <p><strong>📦 Shipping City:</strong>
                                                    <?php echo $row_Recordset3['city']; ?></p>
                                                <p><strong>📦 Shipping State:</strong>
                                                    <?php echo $row_Recordset3['state']; ?></p>
                                                <p><strong>📦 Shipping Pin Code:</strong>
                                                    <?php echo $row_Recordset3['pin_code']; ?></p>
                                                <p><strong>📦 Shipping Phone:</strong>
                                                    <?php echo $row_Recordset3['phone']; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="card shadow-lg border-0 rounded">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="table-responsive">
                                            <table class="table datatable">
                                                <thead class="table">
                                                    <tr>
                                                        <th>Item ID</th>
                                                        <th>Item Name</th>
                                                        <th>Image</th>
                                                        <th>Amount</th>
                                                        <th>Position</th>
                                                        <th>Quantity</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if ($totalRows_Recordset4 > 0) { ?>
                                                    <?php do { ?>
                                                    <tr>
                                                        <td><?php echo $row_Recordset4['item_id']; ?></td>
                                                        <td><?php echo $row_Recordset4['item_name']; ?></td>
                                                        <td><a href="../assets/img/item/<?php echo $row_Recordset4['image_1'] ?>"
                                                                target="_blank"><img
                                                                    src="../assets/img/item/<?php echo $row_Recordset4['image_1'] ?>"
                                                                    width="50"></a></td>
                                                        <td><?php echo $row_Recordset4['payment_amount']; ?></td>
                                                        <td><?php echo $row_Recordset4['position']; ?></td>
                                                        <td><?php echo $row_Recordset4['qnty']; ?></td>
                                                    </tr>
                                                    <?php } while ($row_Recordset4 = mysqli_fetch_assoc($Recordset4));} ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Vertical Form -->
                    </div>
                </div>
                <!-- End Right side columns -->
            </div>
        </section>
    </main>
    <!-- End #main -->

    <!-- ======= Footer ======= -->
    <footer id="footer" class="footer">
        <div class="copyright">
            &copy; Copyright <strong><span>Orek</span></strong>. All Rights Reserved
        </div>
        <div class="credits">
            <!-- All the links in the footer should remain intact. -->
            <!-- You can delete the links only if you purchased the pro version. -->
            <!-- Licensing information: https://bootstrapmade.com/license/ -->
            <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
            Designed by <a href="https://xwaydesigns.com/website-application.html">X Way Design</a>
        </div>
    </footer><!-- End Footer -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/chart.js/chart.umd.js"></script>
    <script src="assets/vendor/echarts/echarts.min.js"></script>
    <script src="assets/vendor/quill/quill.min.js"></script>
    <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>

    <!-- Template Main JS File -->
    <script src="assets/js/main.js"></script>
</body>

</html>