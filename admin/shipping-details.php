<?php require_once('../Connections/orek.php'); ?>
<?php require_once('session.php'); ?>
<?php
$flag = false;
if ( isset( $_GET[ 'shipping_id' ] ) ) {
    $shipping_id = $_GET[ 'shipping_id' ];

    $query_Recordset2 = "SELECT A.*, B.phone_no,(SELECT COUNT(item_id) FROM cart_item WHERE cart_id =(SELECT cart_id FROM payment WHERE payment_id = A.payment_id)) AS total_items FROM shipping AS A INNER JOIN user AS B ON A.user_id = B.user_id WHERE shipping_id = '$shipping_id'";
    $Recordset2 = mysqli_query( $orek, $query_Recordset2 )or die( mysqli_error( $orek ) );
    $row_Recordset2 = mysqli_fetch_assoc( $Recordset2 );
    $totalRows_Recordset2 = mysqli_num_rows( $Recordset2 );
    $flag = true;
}

$editFormAction = $_SERVER[ 'PHP_SELF' ];
if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
    $editFormAction .= "?" . htmlentities( $_SERVER[ 'QUERY_STRING' ] );
}

if ( ( isset( $_POST[ "MM_update" ] ) ) && ( $_POST[ "MM_update" ] == "form1" ) ) {
    $updateSQL = sprintf( "UPDATE `shipping` SET `shipping_date` = %s, `consignment_no` = %s, `status` = 'Shipped' WHERE shipping_id = %s",
        GetSQLValueString( $_POST[ 'shipping_date' ], "text" ),
        GetSQLValueString( $_POST[ 'consignment_no' ], "text" ),
        GetSQLValueString( $_POST[ 'shipping_id' ], "int" ) );
    $Result = mysqli_query( $orek, $updateSQL )or die( mysqli_error( $orek ) );

    //		bulk sms
    $url = 'http://sms.bulksmsind.in/sendSMS';

    $fields = array(
        'username' => "yashshaw",
        'message' => "Your order with " . $_POST['total_items'] . " items are shipped. Track your order at https://carsparemart.com. Regards Suraj Distributors",
        'sendername' => "SURDIS",
        'smstype' => "TRANS",
        'numbers' => $_POST[ 'phone_no' ],
        'apikey' => "63776471-232d-4e42-858a-dbb2684122af"
    );

    //open connection
    $ch = curl_init();

    //set the url, number of POST vars, POST data
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_POST, count( $fields ) );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $fields ) );

    //execute post
    $result = curl_exec( $ch );

    //close connection
    curl_close( $ch );

    $insertGoTo = "shipping-details.php?success=Updated";
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

    <title>Shipping Details - Orek</title>
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

    <!-- =======================================================
  * Template Name: NiceAdmin
  * Updated: Mar 09 2023 with Bootstrap v5.2.3
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>

    <?php require_once('menu.php'); ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Update Shipping</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active">Update Shipping</li>
                </ol>
            </nav>
        </div>
        <!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <a href="shipping.php" class="btn btn-secondary">Back</a>
                </div>
                <!-- Left side columns -->
                <?php
        if ( isset( $_GET[ 'success' ] ) ) {
            echo '<div class="col-md-12">';
            echo '<div class="alert alert-success"> Shipping ' . $_GET[ 'success' ] . ' Successfully</div>';
            echo '</div>';
        }
        ?>
                <div class="col-lg-12">
                    <div class="row">
                        <!-- Vertical Form -->
                        <form method="POST" class="row g-3" name="form1" role="form"
                            action="<?php echo $editFormAction; ?>" enctype="multipart/form-data">
                            <div class="col-12">
                                <label for="shipping_date" class="form-label">Shipping Date</label>
                                <input type="date" name="shipping_date" class="form-control"
                                    value="<?php if($flag && !empty($row_Recordset2['shipping_date'])) echo date('Y-m-d', strtotime($row_Recordset2['shipping_date'])); ?>"
                                    required>
                            </div>
                            <div class="col-12">
                                <label for="consignment_no" class="form-label">Consignment No</label>
                                <input type="text" name="consignment_no" class="form-control"
                                    value="<?php if($flag) echo $row_Recordset2['consignment_no']; ?>" required>
                            </div>
                            <div class="col-12">
                                <label for="total_items" class="form-label">Total Order Item</label>
                                <input type="text" name="total_items" class="form-control"
                                    value="<?php if ($flag) echo $row_Recordset2['total_items']; ?>" required readonly>
                            </div>
                            <div class="col-12">
                                <label for="status" class="form-label">Status</label>
                                <input type="text" name="status" class="form-control"
                                    value="<?php if ($flag) echo $row_Recordset2['status']; ?>" required readonly>
                            </div>
                            <?php if($row_Recordset2['status'] == "Pending") { ?>
                            <div class="col-12 text-center">
                                <button type="submit" id="save" class="btn btn-primary">Save</button>
                                <input type="hidden" name="MM_update" value="form1">
                                <input type="hidden" name="shipping_id" value="<?php echo $shipping_id; ?>">
                                <input type="hidden" name="phone_no"
                                    value="<?php echo $row_Recordset2['phone_no'] ; ?>">
                                <button type="reset" class="btn btn-secondary">Reset</button>
                            </div>
                            <?php } ?>
                        </form>

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