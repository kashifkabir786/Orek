<?php require_once('../Connections/orek.php'); ?>
<?php require_once('session.php'); ?>
<?php
$editFormAction = $_SERVER[ 'PHP_SELF' ];
if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
    $editFormAction .= "?" . htmlentities( $_SERVER[ 'QUERY_STRING' ] );
}
if ( isset( $_GET[ 'submit' ] ) && $_GET[ 'submit' ] == 'filter' ) {

    $query_Recordset2 = "SELECT A.*, B.`fname`, B.`lname` FROM payment AS A INNER JOIN user AS B ON A.`user_id` = B.`user_id` WHERE A.`payment_date` BETWEEN '{$_GET['from']}' AND '{$_GET['to']}'";
    $Recordset2 = mysqli_query( $orek, $query_Recordset2 )or die( mysqli_error( $orek ) );
    $row_Recordset2 = mysqli_fetch_assoc( $Recordset2 );
    $totalRows_Recordset2 = mysqli_num_rows( $Recordset2 );

} else if ( isset( $_GET[ 'filter_download' ] ) && $_GET[ 'filter_download' ] == 'filter_download' ) {

    header( "Location: reports/sells-report.php?from=" . $_GET[ 'from' ] . "&to=" . $_GET[ 'to' ] );

} else {

    $query_Recordset2 = "SELECT A.*, B.`fname`, B.lname FROM payment AS A INNER JOIN user AS B ON A.`user_id` = B.`user_id`";
    $Recordset2 = mysqli_query( $orek, $query_Recordset2 )or die( mysqli_error( $orek ) );
    $row_Recordset2 = mysqli_fetch_assoc( $Recordset2 );
    $totalRows_Recordset2 = mysqli_num_rows( $Recordset2 );
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />

    <title>Sales Report - Orek</title>
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
    <link rel="stylesheet" href="https://cdn.materialdesignicons.com/5.4.55/css/materialdesignicons.min.css">

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
    <!-- ======= Header ======= -->
    <?php require_once('menu.php'); ?>

    <!-- End Sidebar-->

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Sales Report</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item">Sales Report</li>
                </ol>
            </nav>
        </div>
        <form name="form1" action="<?php echo $editFormAction ?>" method="get" role="form">
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">From:</label>
                <div class="col-sm-10">
                    <input type="date" name="from" class="form-control" required>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">To:</label>
                <div class="col-sm-10">
                    <input type="date" name="to" class="form-control" required>
                </div>
            </div>
            <div class="text-start mt-3">
                <button type="submit" class="btn btn-primary" name="submit" value="filter">
                    <i class="bi bi-filter"></i> Filter
                </button>
                <button type="submit" class="btn btn-success" name="filter_download" value="filter_download">
                    <i class="bi bi-download"></i> Filter and Download
                </button>
                <a href="<?php echo 'reports/sells-report.php?download=all'; ?>" class="btn btn-info">
                    <i class="bi bi-cloud-download"></i> Download All
                </a>
            </div>
        </form>

        <!-- End Page Title -->

        <section class="section">
            <div class="row mt-5">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <!-- Table with stripped rows -->
                            <table class="table datatable">
                                <thead>
                                    <tr>
                                        <th>Payment Id</th>
                                        <th>User Name</th>
                                        <th>Payment Date</th>
                                        <th>Amount</th>
                                        <th>Txn Id</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ( $totalRows_Recordset2 > 0 ) {
                                        do {
                                            ?>
                                    <tr>
                                        <td><?php echo $row_Recordset2['payment_id'] ?></td>
                                        <td><?php echo $row_Recordset2['fname'] ." " . $row_Recordset2['lname'] ?></td>
                                        <td><?php echo $row_Recordset2['payment_date'] ?></td>
                                        <td><?php echo $row_Recordset2['amount'] ?></td>
                                        <td><?php echo $row_Recordset2['txn_id'] ?></td>
                                    </tr>
                                    <?php }while($row_Recordset2 = mysqli_fetch_assoc($Recordset2)); 
                                    }?>
                                </tbody>
                            </table>
                            <!-- End Table with stripped rows -->
                        </div>
                    </div>
                </div>
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