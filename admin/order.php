<?php require_once('../Connections/orek.php'); ?>
<?php require_once('session.php'); ?>
<?php
    $query_Recordset2 = "SELECT cart.*, payment.payment_date FROM cart INNER JOIN payment ON cart.cart_id = payment.cart_id WHERE cart.status = 'Paid' OR cart.status = 'Ordered' ORDER BY payment.payment_date DESC";
    $Recordset2 = mysqli_query( $orek, $query_Recordset2 )or die( mysqli_error( $orek ) );
    $row_Recordset2 = mysqli_fetch_assoc( $Recordset2 );
    $totalRows_Recordset2 = mysqli_num_rows( $Recordset2 );

    // $query_Recordset3 = "SELECT A.*, B.* FROM cart_item AS A INNER JOIN item AS B ON A.item_id = B.item_id";
    // $Recordset3 = mysqli_query( $orek, $query_Recordset3 )or die( mysqli_error( $orek ) );
    // $row_Recordset3 = mysqli_fetch_assoc( $Recordset3 );
    // $totalRows_Recordset3 = mysqli_num_rows( $Recordset3 );
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />

    <title>Order - Orek</title>
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
            <h1>Order</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item">Order</li>
                </ol>
            </nav>
        </div>
        <!-- End Page Title -->

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <!-- Order Filter Form -->
                            <form action="download-order.php" method="post" class="row g-3 mt-3">
                                <div class="col-md-5">
                                    <label for="start_date" class="form-label">Start Date:</label>
                                    <input type="date" name="start_date" class="form-control" required>
                                </div>
                                <div class="col-md-5">
                                    <label for="end_date" class="form-label">End Date:</label>
                                    <input type="date" name="end_date" class="form-control" required>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-success w-100"><i class="fas fa-download"></i>
                                        Download</button>
                                    <input type="hidden" name="MM_download" value="form1">
                                </div>
                            </form>

                            <hr>

                            <!-- Orders Table -->
                            <div class="table-responsive">
                                <table class="table datatable">
                                    <thead class="table">
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <!-- <th>Position</th> -->
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($totalRows_Recordset2 > 0) { ?>
                                        <?php do { ?>
                                        <tr>
                                            <td><?php echo $row_Recordset2['cart_id']; ?></td>
                                            <td>
                                                <span
                                                    class="badge bg-<?php echo ($row_Recordset2['status'] == 'Completed') ? 'success' : 'warning'; ?>">
                                                    <?php echo $row_Recordset2['status']; ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d F, Y', strtotime($row_Recordset2['payment_date'])); ?></td>
                                            <!-- <td><?php echo $row_Recordset3['position']; ?></td> -->
                                            <td>
                                                <a href="order-details.php?cart_id=<?php echo $row_Recordset2['cart_id']; ?>"
                                                    class="">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php } while ($row_Recordset2 = mysqli_fetch_assoc($Recordset2));} ?>
                                    </tbody>
                                </table>
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