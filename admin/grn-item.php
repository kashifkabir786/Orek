<?php require_once('../Connections/orek.php'); ?>
<?php require_once('session.php'); ?>
<?php
//show in table
if ( isset( $_GET[ 'grn_id' ] ) ) {
    $grn_id = $_GET[ 'grn_id' ];
}
$query_Recordset2 = "SELECT A.*, B.item_name, B.position FROM grn_item AS A INNER JOIN item AS B ON A.item_id = B.item_id WHERE grn_id = '$grn_id'";
$Recordset2 = mysqli_query( $orek, $query_Recordset2 )or die( mysqli_error( $orek ) );
$row_Recordset2 = mysqli_fetch_assoc( $Recordset2 );
$totalRows_Recordset2 = mysqli_num_rows( $Recordset2 );
//item dropdown
$query_Recordset3 = "SELECT * FROM item";
$Recordset3 = mysqli_query( $orek, $query_Recordset3 )or die( mysqli_error( $orek ) );
$row_Recordset3 = mysqli_fetch_assoc( $Recordset3 );
$totalRows_Recordset3 = mysqli_num_rows( $Recordset3 );
//insert
$editFormAction = $_SERVER[ 'PHP_SELF' ];
if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
    $editFormAction .= "?" . htmlentities( $_SERVER[ 'QUERY_STRING' ] );
}
if ( ( isset( $_POST[ "MM_insert" ] ) ) && ( $_POST[ "MM_insert" ] == "form1" ) ) {

    $insertSQL = sprintf( "INSERT INTO `grn_item`(`grn_id`, `item_id`, `qnty`) VALUES (%s, %s, %s)",
        GetSQLValueString( $_POST[ 'grn_id' ], "text" ),
        GetSQLValueString( $_POST[ 'item_id' ], "text" ),
        GetSQLValueString( $_POST[ 'qnty' ], "text" ) );
    $Result = mysqli_query( $orek, $insertSQL )or die( mysqli_error( $orek ) );

    $updateSQL = sprintf( "UPDATE `item` SET `min_stock_alert` = %s WHERE `item_id` = %s",
        GetSQLValueString( $_POST[ 'min_stock_alert' ], "text" ),
        GetSQLValueString( $_POST[ 'item_id' ], "text" ) );
    $Result = mysqli_query( $orek, $updateSQL )or die( mysqli_error( $orek ) );

    $insertGoTo = "grn-item.php?success=Added";
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

    <title>Add GRN - Orek</title>
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
            <h1>Add GRN</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active">Add GRN</li>
                </ol>
            </nav>
        </div>
        <!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <a href="grn.php" class="btn btn-secondary">Back</a>
                </div>
                <!-- Left side columns -->
                <?php
        if ( isset( $_GET[ 'success' ] ) ) {
            echo '<div class="col-md-12">';
            echo '<div class="alert alert-success"> GRN Item ' . $_GET[ 'success' ] . ' Successfully</div>';
            echo '</div>';
        }
        ?>
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-md-12">
                            <form method="POST" name="form1" id="form1" role="form"
                                action="<?php echo $editFormAction; ?>" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="control-label" for="item_id">Item</label>
                                    <select name="item_id" class="form-control" required>
                                        <option value="" selected disabled>Select Item</option>
                                        <?php do { ?>
                                        <option value="<?php echo $row_Recordset3['item_id']; ?>">
                                            <?php echo $row_Recordset3['item_name']; ?>
                                        </option>
                                        <?php } while ($row_Recordset3 = mysqli_fetch_assoc($Recordset3)); ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="qnty" class="form-label">Quantity</label>
                                    <input type="text" name="qnty" class="form-control" required
                                        placeholder="Enter Quantity">
                                </div>
                                <div class="mb-3">
                                    <label for="min_stock_alert" class="form-label">Min Stock Alert</label>
                                    <input type="text" name="min_stock_alert" class="form-control" required
                                        placeholder="Min Stock Alert">
                                </div>

                                <div class="text-center">
                                    <button type="submit" id="save" class="btn btn-primary">
                                        <i class="bi bi-save"></i> Save
                                    </button>
                                    <input type="hidden" name="MM_insert" value="form1">
                                    <input type="hidden" name="grn_id" value="<?php echo $grn_id ?>">
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="card mt-5">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="example">
                                <thead class="table-primary">
                                    <tr>
                                        <th>GRN Id</th>
                                        <th>Item Name</th>
                                        <th>Quantity</th>
                                        <th>Position</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($totalRows_Recordset2 > 0) {
                        do { ?>
                                    <tr>
                                        <td><?php echo $row_Recordset2['grn_id'] ?></td>
                                        <td><?php echo $row_Recordset2['item_name'] ?></td>
                                        <td><?php echo $row_Recordset2['qnty'] ?></td>
                                        <td><?php echo $row_Recordset2['position'] ?></td>
                                        <td>
                                            <a href="delete-grn-item.php?grn_item_id=<?php echo $row_Recordset2['grn_item_id']; ?>&grn_id=<?php echo $row_Recordset2['grn_id']; ?>"
                                                class="btn btn-danger btn-sm">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
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