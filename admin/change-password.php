<?php require_once('../Connections/orek.php'); ?>
<?php require_once('session.php'); ?>
<?php
$editFormAction = $_SERVER[ 'PHP_SELF' ];
if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
    $editFormAction .= "?" . htmlentities( $_SERVER[ 'QUERY_STRING' ] );
}
$flag = true;

$errpass1 = $errpass2 = $errpass = $errpass3 = "";

if ( ( isset( $_POST[ "MM_update" ] ) ) && ( $_POST[ "MM_update" ] == "form1" ) ) {

    $query_Recordset2 = "SELECT password FROM admin WHERE uname = '{$row_Recordset1['uname']}'";
    $Recordset2 = mysqli_query( $orek, $query_Recordset2 )or die( mysqli_error( $orek ) );
    $row_Recordset2 = mysqli_fetch_assoc( $Recordset2 );

    $hash = $row_Recordset2[ 'password' ];
    if ( !password_verify( $_POST[ 'old-password' ], $hash ) )
        $errpass3 = "Wrong Password Entered";
    if ( empty( $_POST[ 'password' ] ) )
        $errpass1 = "Please Enter Password";
    if ( empty( $_POST[ 'rpassword' ] ) )
        $errpass2 = "Please Retype Password";
    if ( $_POST[ 'password' ] != $_POST[ 'rpassword' ] )
        $errpass = "Passwords Don't Match";

    if ( empty( $errpass3 ) && empty( $errpass1 ) && empty( $errpass2 ) && empty( $errpass ) ) {
        $password = $_POST[ 'password' ];
        $hash = password_hash( $password, PASSWORD_DEFAULT );

        $updateSQL = sprintf( "UPDATE `admin` SET `password` = '$hash' WHERE `uname` = %s", GetSQLValueString( $_POST[ 'subscriber_id' ], "text" ) );

        $Result1 = mysqli_query( $orek, $updateSQL )or die( mysqli_error( $orek ) );

        unset( $_SESSION[ 'uname' ] );

        $insertGoTo = "index.php?sucess=yes";
        if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
            $insertGoTo .= ( strpos( $insertGoTo, '?' ) ) ? "&" : "?";
            $insertGoTo .= $_SERVER[ 'QUERY_STRING' ];
        }
        header( sprintf( "Location: %s", $insertGoTo ) );
    }

}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />

    <title>Category - Orek</title>
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
            <h1>Change Password</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item">Change Password</li>
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
                                  <form id="form1" name="form1" action="<?php echo $editFormAction; ?>" method="POST" role="form">
                                    <div class="row">
                                      <div class="col-md-12">
                                        <h4>Update Password</h4>
                                        <hr/>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="form-group">
                                          <label for="old-password">Old Password:<span class="text-warning">* <?php echo $errpass3; ?></span></label>
                                          <input type="password" class="form-control" name="old-password" id="old-password">
                                        </div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="form-group">
                                          <label for="password">New Password:<span class="text-warning">* <?php echo $errpass1; ?></span></label>
                                          <input type="password" class="form-control" name="password" id="password">
                                        </div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="form-group">
                                          <label for="rpassword">Repeat Password:<span class="text-warning">* <?php echo $errpass2 . $errpass; ?></span></label>
                                          <input type="password" class="form-control" name="rpassword" id="rpassword">
                                        </div>
                                      </div>
                                    </div>
                                    <div class="form-group mt-3">
                                      <button type="submit" id="save" class="btn btn-primary"><i class="bi bi-save"></i> Update</button>
                                      <input type="hidden" name="subscriber_id" value="<?php echo $row_Recordset1['uname'] ?>"/>
                                      <input type="hidden" name="MM_update" value="form1">
                                    </div>
                                  </form>
                                </div>
                            </div>
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
