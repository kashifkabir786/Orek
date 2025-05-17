<?php require_once('../Connections/orek.php'); ?>
<?php require_once('session.php'); ?>
<?php
$flag = false;
if ( isset( $_GET[ 'user_id' ] ) ) {
    $user_id = $_GET[ 'user_id' ];

    $query_Recordset2 = "SELECT * FROM user WHERE user_id='$user_id'";
    $Recordset2 = mysqli_query( $orek, $query_Recordset2 )or die( mysqli_error( $orek ) );
    $row_Recordset2 = mysqli_fetch_assoc( $Recordset2 );
    $totalRows_Recordset2 = mysqli_num_rows( $Recordset2 );
    $flag = true;
}

if ( isset( $_GET[ 'success' ] ) && $_GET[ 'success' ] == "Added" ) {
    $query_Recordset2 = "SELECT * FROM user ORDER BY user_id DESC LIMIT 1";
    $Recordset2 = mysqli_query( $orek, $query_Recordset2 )or die( mysqli_error( $orek ) );
    $row_Recordset2 = mysqli_fetch_assoc( $Recordset2 );
    $totalRows_Recordset2 = mysqli_num_rows( $Recordset2 );

    $user_id = $row_Recordset2[ 'user_id' ];
    $flag = true;
}

$editFormAction = $_SERVER[ 'PHP_SELF' ];
if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
    $editFormAction .= "?" . htmlentities( $_SERVER[ 'QUERY_STRING' ] );
}

if ( ( isset( $_POST[ "MM_insert" ] ) ) && ( $_POST[ "MM_insert" ] == "form1" ) ) {

    $password = $_POST[ 'password' ];
    $hash = password_hash( $password, PASSWORD_DEFAULT );

    $insertSQL = sprintf( "INSERT INTO `user`(`password`, `fname`, `lname`, `email`, `phone_no`) VALUES (%s, %s, %s, %s, %s)",
        GetSQLValueString( $hash, "text" ),
        GetSQLValueString( $_POST[ 'fname' ], "text" ),
        GetSQLValueString( $_POST[ 'lname' ], "text" ),
        GetSQLValueString( $_POST[ 'email' ], "text" ),
        GetSQLValueString( $_POST[ 'phone_no' ], "text" ) );
    $Result = mysqli_query( $orek, $insertSQL )or die( mysqli_error( $orek ) );

    $insertGoTo = "add-user.php?success=Added";
    if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
        $insertGoTo .= ( strpos( $insertGoTo, '?' ) ) ? "&" : "?";
        $insertGoTo .= $_SERVER[ 'QUERY_STRING' ];
    }
    header( sprintf( "Location: %s", $insertGoTo ) );
}

if ( ( isset( $_POST[ "MM_update" ] ) ) && ( $_POST[ "MM_update" ] == "form1" ) ) {

    $updateSQL = sprintf( "UPDATE `user` SET `fname` = %s, `lname` = %s, `email` = %s, `phone_no` = %s WHERE user_id = %s",
        GetSQLValueString( $_POST[ 'fname' ], "text" ),
        GetSQLValueString( $_POST[ 'lname' ], "text" ),
        GetSQLValueString( $_POST[ 'email' ], "text" ),
        GetSQLValueString( $_POST[ 'phone_no' ], "text" ),
        GetSQLValueString( $_POST[ 'user_id' ], "text" ) );
    $Result = mysqli_query( $orek, $updateSQL )or die( mysqli_error( $orek ) );

    $insertGoTo = "add-user.php?success=Updated";
    if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
        $insertGoTo .= ( strpos( $insertGoTo, '?' ) ) ? "&" : "?";
        $insertGoTo .= $_SERVER[ 'QUERY_STRING' ];
    }
    header( sprintf( "Location: %s", $insertGoTo ) );
}
//Reset password
$errpass1 = $errpass2 = $errpass = $errpass3 = "";

if ( ( isset( $_POST[ "MM_password" ] ) ) && ( $_POST[ "MM_password" ] == "form3" ) ) {

    $query_Recordset3 = "SELECT password FROM user WHERE user_id = '{$_POST[ 'user_id' ]}'";
    $Recordset3 = mysqli_query( $orek, $query_Recordset3 )or die( mysqli_error( $orek ) );
    $row_Recordset3 = mysqli_fetch_assoc( $Recordset3 );

    $hash = $row_Recordset3[ 'password' ];
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

        $updateSQL = sprintf( "UPDATE `user` SET `password` = '$hash' WHERE `user_id` = %s", GetSQLValueString( $_POST[ 'user_id' ], "text" ) );

        $Result1 = mysqli_query( $orek, $updateSQL )or die( mysqli_error( $orek ) );

        $insertGoTo = "add-user.php?sucess=Password Updated";
        if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
            $insertGoTo .= ( strpos( $insertGoTo, '?' ) ) ? "&" : "?";
            $insertGoTo .= $_SERVER[ 'QUERY_STRING' ];
        }
        header( sprintf( "Location: %s", $insertGoTo ) );
    }
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
            <h1>Add User</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active">Add User</li>
                </ol>
            </nav>
        </div>
        <!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <a href="user.php" class="btn btn-secondary">Back</a>
                </div>
                <!-- Left side columns -->
                <?php
        if ( isset( $_GET[ 'success' ] ) ) {
            echo '<div class="col-md-12">';
            echo '<div class="alert alert-success"> User ' . $_GET[ 'success' ] . ' Successfully</div>';
            echo '</div>';
        }
        ?>
                <div class="col-lg-12">
                    <div class="row">
                        <!-- User Registration Form -->
                        <div class="col-lg-12">
                            <form method="POST" class="row g-3" name="form1" role="form"
                                action="<?php echo $editFormAction; ?>">

                                <?php if (!$flag) { ?>
                                <div class="col-12">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                                <?php } ?>

                                <div class="col-md-6">
                                    <label for="fname" class="form-label">First Name</label>
                                    <input type="text" name="fname" class="form-control"
                                        value="<?php if($flag) echo $row_Recordset2['fname']; ?>" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="lname" class="form-label">Last Name</label>
                                    <input type="text" name="lname" class="form-control"
                                        value="<?php if($flag) echo $row_Recordset2['lname']; ?>" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control"
                                        value="<?php if($flag) echo $row_Recordset2['email']; ?>" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="phone_no" class="form-label">Phone No</label>
                                    <input type="text" name="phone_no" class="form-control"
                                        value="<?php if($flag) echo $row_Recordset2['phone_no']; ?>" required>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                    <?php if ($flag) { ?>
                                    <input type="hidden" name="MM_update" value="form1">
                                    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                                    <?php } else { ?>
                                    <input type="hidden" name="MM_insert" value="form1">
                                    <?php } ?>
                                    <button type="reset" class="btn btn-secondary">Reset</button>
                                </div>
                            </form>
                        </div>

                        <?php if ($flag) { ?>
                        <!-- Reset Password Section -->
                        <div class="col-lg-12 mt-4">

                            <form name="form3" action="<?php echo $editFormAction; ?>" method="post" role="form">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <input type="password" name="old-password" class="form-control"
                                            placeholder="Enter Old Password" required>
                                    </div>

                                    <div class="col-md-4">
                                        <input type="password" name="password" class="form-control"
                                            placeholder="Enter New Password..." required>
                                    </div>

                                    <div class="col-md-4">
                                        <input type="password" name="rpassword" class="form-control"
                                            placeholder="Confirm New Password..." required>
                                    </div>
                                </div>

                                <div class="text-center mt-3">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                    <input type="hidden" name="MM_password" value="form3">
                                    <input type="hidden" name="user_id"
                                        value="<?php echo $row_Recordset2['user_id']; ?>">
                                </div>
                            </form>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <!-- End Right side columns -->
            </div>
        </section>
    </main>
    <!-- End #main -->

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