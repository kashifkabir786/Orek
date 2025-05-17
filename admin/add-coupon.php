<?php require_once('../Connections/orek.php'); ?>
<?php require_once('session.php'); ?>
<?php
$flag = false;
if ( isset( $_GET[ 'success' ] ) && $_GET[ 'success' ] == "Added" ) {
    $query_Recordset2 = "SELECT * FROM coupon ORDER BY coupon_id DESC LIMIT 1";
    $Recordset2 = mysqli_query( $orek, $query_Recordset2 )or die( mysqli_error( $orek ) );
    $row_Recordset2 = mysqli_fetch_assoc( $Recordset2 );
    $totalRows_Recordset2 = mysqli_num_rows( $Recordset2 );

    $coupon_id = $row_Recordset2[ 'coupon_id' ];
    $flag = true;
}

$editFormAction = $_SERVER[ 'PHP_SELF' ];
if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
    $editFormAction .= "?" . htmlentities( $_SERVER[ 'QUERY_STRING' ] );
}
if ( ( isset( $_POST[ "MM_insert" ] ) ) && ( $_POST[ "MM_insert" ] == "form1" ) ) {

    $insertSQL = sprintf( "INSERT INTO `coupon`(`coupon_code`, `percentage`, `start_date`, `end_date`) VALUES (%s, %s, %s, %s)",
        GetSQLValueString( $_POST[ 'coupon_code' ], "text" ),
        GetSQLValueString( $_POST[ 'percentage' ], "text" ),
        GetSQLValueString( $_POST[ 'start_date' ], "text" ),
        GetSQLValueString( $_POST[ 'end_date' ], "text" ) );
    $Result = mysqli_query( $orek, $insertSQL )or die( mysqli_error( $orek ) );

    $insertGoTo = "add-coupon.php?success=Added";
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

    <title>Add Brand - Orek</title>
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
            <h1>Add Coupon</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active">Add Coupon</li>
                </ol>
            </nav>
        </div>
        <!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <a href="coupon.php" class="btn btn-secondary">Back</a>
                </div>
                <!-- Left side columns -->
                <?php
        if ( isset( $_GET[ 'success' ] ) ) {
            echo '<div class="col-md-12">';
            echo '<div class="alert alert-success"> Coupon ' . $_GET[ 'success' ] . ' Successfully</div>';
            echo '</div>';
        }
        ?>
                <div class="col-lg-12">
                    <div class="row">

                        <!-- Vertical Form -->
                        <form method="POST" class="row g-3" name="form1" role="form"
                            action="<?php echo $editFormAction; ?>" enctype="multipart/form-data">
                            <div class="col-12">
                                <label for="coupon_code" class="form-label">Coupon Code</label>
                                <input type="text" class="form-control" name="coupon_code"
                                    value="<?php if($flag) echo $row_Recordset2['coupon_code'] ?>" />
                            </div>
                            <div class="col-12">
                                <label for="percentage" class="form-label">Percentage</label>
                                <input type="text" class="form-control" name="percentage"
                                    value="<?php if($flag) echo $row_Recordset2['percentage'] ?>" />
                            </div>
                            <div class="col-12">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" name="start_date"
                                    value="<?php if($flag) echo $row_Recordset2['start_date'] ?>" />
                            </div>
                            <div class="col-12">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" name="end_date"
                                    value="<?php if($flag) echo $row_Recordset2['end_date'] ?>" />
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <input type="hidden" name="MM_insert" value="form1">
                                <button type="reset" class="btn btn-secondary">Reset</button>
                            </div>
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