<?php require_once('../Connections/orek.php'); ?>
<?php require_once('session.php'); ?>
<?php
//show in table
if ( isset( $_GET[ 'cart_id' ] ) ) {
    $cart_id = $_GET[ 'cart_id' ];
}
$query_Recordset2 = "SELECT A.*, B.item_id, B.item_name, B.image_1 FROM cart_item AS A INNER JOIN item AS B ON A.item_id = B.item_id WHERE A.cart_id = '$cart_id'";
$Recordset2 = mysqli_query( $orek, $query_Recordset2 )or die( mysqli_error( $orek ) );
$row_Recordset2 = mysqli_fetch_assoc( $Recordset2 );
$totalRows_Recordset2 = mysqli_num_rows( $Recordset2 );
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />

    <title>Add Cart Item - Orek</title>
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
            <h1>Add Cart Item</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active">Add Cart Item</li>
                </ol>
            </nav>
        </div>
        <!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <a href="cart.php" class="btn btn-secondary">Back</a>
                </div>
                <!-- Left side columns -->
                <div class="col-lg-12">
                    <!-- Table -->
                    <div class="card mt-5">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="example">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Cart Id</th>
                                        <th>Item Name</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($totalRows_Recordset2 > 0) {
                                    do { ?>
                                    <tr>
                                        <td><?php echo $row_Recordset2['cart_id'] ?></td>
                                        <td><a href="add-item.php?item_id=<?php echo $row_Recordset2['item_id'] ?>"><img src="../assets/img/item/<?php echo $row_Recordset2['image_1'] ?>" alt="Product Image" style="width: 50px; height: 50px;"> <?php echo $row_Recordset2['item_name'] ?></a></td>
                                        <td><?php echo $row_Recordset2['qnty'] ?></td>
                                        <td>Rs <?php echo $row_Recordset2['amount'] ?></td>
                                    </tr>
                                    <?php } while ($row_Recordset2 = mysqli_fetch_assoc($Recordset2)); } ?>
                                </tbody>    
                            </table>
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