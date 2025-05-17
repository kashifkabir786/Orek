<?php require_once('../Connections/orek.php'); ?>
<?php require_once('session.php'); ?>
<?php
$where = "";
if(isset($_GET['category_id']) && !empty($_GET['category_id'])) {
    $category_id = mysqli_real_escape_string($orek, $_GET['category_id']);
    $where = " WHERE category_id = '$category_id'";
}
$query_Recordset2 = "SELECT * FROM item" . $where;
$Recordset2 = mysqli_query( $orek, $query_Recordset2 )or die( mysqli_error( $orek ) );
$row_Recordset2 = mysqli_fetch_assoc( $Recordset2 );
$totalRows_Recordset2 = mysqli_num_rows( $Recordset2 );

$query_Categories = "SELECT * FROM category";
$Categories = mysqli_query($orek, $query_Categories) or die(mysqli_error($orek));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />

    <title>Item - Orek</title>
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
            <h1>Item</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item">Item</li>
                </ol>
            </nav>
        </div>
        <!-- End Page Title -->

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mt-3 mb-3">
                                    <a href="add-item.php" class="btn btn-primary me-2">ADD ITEM</a>
                                    <button class="btn btn-primary me-2" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#filterSection">
                                        FILTER <i class="bi bi-funnel"></i>
                                    </button>
                                    <a href="import-item.php" class="btn btn-primary me-2">IMPORT</a>
                                    <a href="download-excel.php?type=item" class="btn btn-primary me-2">EXPORT</a>
                                </div>
                            </div>
                            <!-- Add this filter section -->
                            <div class="collapse mb-3" id="filterSection">
                                <div class="card card-body">
                                    <form method="GET" action="" id="filterForm">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label class="form-label">Category</label>
                                                <select name="category_id" class="form-select"
                                                    onchange="this.form.submit()">
                                                    <option value="">All Categories</option>
                                                    <?php while($cat = mysqli_fetch_assoc($Categories)) { ?>
                                                    <option value="<?php echo $cat['category_id']; ?>"
                                                        <?php echo (isset($_GET['category_id']) && $_GET['category_id'] == $cat['category_id']) ? 'selected' : ''; ?>>
                                                        <?php echo $cat['category_name']; ?>
                                                    </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <!-- Table with stripped rows -->
                            <table class="table datatable">
                                <thead>
                                    <tr>
                                        <th>Item Id</th>
                                        <th>Image</th>
                                        <th>Item Name</th>
                                        <th>Mood</th>
                                        <th>Price</th>
                                        <th>Discount</th>
                                        <th>Stock</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
              if ( $totalRows_Recordset2 > 0 ) {
                $stock = 0;
                do {
                  $query_Recordset3 = "SELECT A.qnty AS `cart_qnty`, A.item_id, B.qnty AS `grn_qnty` FROM cart_item AS A RIGHT JOIN grn_item AS B ON A.item_id = B.item_id WHERE B.item_id = '{$row_Recordset2['item_id']}'";
                  $Recordset3 = mysqli_query( $orek, $query_Recordset3 )or die( mysqli_error( $orek ) );
                  $row_Recordset3 = mysqli_fetch_assoc( $Recordset3 );
                  $totalRows_Recordset3 = mysqli_num_rows( $Recordset3 );
                  if ( $totalRows_Recordset3 > 0 ) {
                    if ( $row_Recordset3[ 'cart_qnty' ] == null && $row_Recordset3[ 'grn_qnty' ] != null ) {
                      $stock = $row_Recordset3[ 'grn_qnty' ];
                    } else if ( $row_Recordset3[ 'cart_qnty' ] != null && $row_Recordset3[ 'grn_qnty' ] != null ) {
                      $stock = ( $row_Recordset3[ 'grn_qnty' ] - $row_Recordset3[ 'cart_qnty' ] );
                    } else {
                      $stock = 'NA';
                    }
                  } else {
                    $stock = 'NA';
                  }
                  ?>
                                    <tr>
                                        <td><?php echo $row_Recordset2['item_id'] ?></td>
                                        <td><a href="../assets/img/item/<?php echo $row_Recordset2['image_1'] ?>"
                                                target="_blank"><img
                                                    src="../assets/img/item/<?php echo $row_Recordset2['image_1'] ?>"
                                                    width="20%"></a></td>
                                        <td><?php echo $row_Recordset2['item_name']?></td>
                                        <td><?php echo $row_Recordset2['mood'] ?></td>
                                        <td>&#8377;<?php echo $row_Recordset2['price'] ?></td>
                                        <td><?php echo $row_Recordset2['discount'] ?>%</td>
                                        <td><?php echo $stock ?></td>
                                        <td><a
                                                href="add-item.php?item_id=<?php echo $row_Recordset2['item_id'] ?> & category_id=<?php echo $row_Recordset2['category_id']; ?>"><i
                                                    class="bi bi-pencil-square me-2"></i></a> <a
                                                href="delete-item.php?item_id=<?php echo $row_Recordset2['item_id']; ?>"><i
                                                    class="bi bi-trash3-fill text-danger"></i></a></td>
                                    </tr>
                                    <?php }while($row_Recordset2 = mysqli_fetch_assoc($Recordset2));
                                    }?>
                                </tbody>
                            </table>
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