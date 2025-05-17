<?php require_once('../Connections/orek.php'); ?>
<?php require_once('session.php'); ?>
<?php
$flag = false;
if ( isset( $_GET[ 'blog_id' ] ) ) {
  $blog_id = $_GET[ 'blog_id' ];

  $query_Recordset2 = "SELECT * FROM blogs WHERE blog_id='$blog_id'";
  $Recordset2 = mysqli_query( $orek, $query_Recordset2 )or die( mysqli_error( $orek ) );
  $row_Recordset2 = mysqli_fetch_assoc( $Recordset2 );
  $totalRows_Recordset2 = mysqli_num_rows( $Recordset2 );
  $flag = true;
}

if ( isset( $_GET[ 'success' ] ) && $_GET[ 'success' ] == "Added" ) {
  $query_Recordset2 = "SELECT * FROM blogs ORDER BY blog_id DESC LIMIT 1";
  $Recordset2 = mysqli_query( $orek, $query_Recordset2 )or die( mysqli_error( $orek ) );
  $row_Recordset2 = mysqli_fetch_assoc( $Recordset2 );
  $totalRows_Recordset2 = mysqli_num_rows( $Recordset2 );

  $blog_id = $row_Recordset2[ 'blog_id' ];
  $flag = true;
}

$editFormAction = $_SERVER[ 'PHP_SELF' ];
if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
  $editFormAction .= "?" . htmlentities( $_SERVER[ 'QUERY_STRING' ] );
}
if ( ( isset( $_POST[ "MM_insert" ] ) ) && ( $_POST[ "MM_insert" ] == "form1" ) ) {

  $target = "../assets/img/blog/";
  $randno = rand( 100, 1000 );
  $target = $target . $randno . "-" . basename( $_FILES[ 'image_1' ][ 'name' ] );
  $imageFileType = pathinfo( $target, PATHINFO_EXTENSION );
  // Allow certain file formats
  if ( $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
    $error[] = "Sorry, File is not an image. Please upload jpg, png or gif files only";
  } else if ( strpos( strtolower( $_FILES[ 'image_1' ][ 'name' ] ), 'php' ) !== false || strpos( strtolower( $_FILES[ 'image_1' ][ 'name' ] ), 'js' ) !== false ) {
    $error[] = "Sorry, File is not an image. Please upload jpg, png or gif files only";
  } else
    $pic = $randno . "-" . ( $_FILES[ 'image_1' ][ 'name' ] );
  if ( move_uploaded_file( $_FILES[ 'image_1' ][ 'tmp_name' ], $target ) ) {

    $insertSQL = sprintf( "INSERT INTO `blogs`(`blog_name`, `image_1`, `blog_description`) VALUES (%s, %s, %s)",
      GetSQLValueString( $_POST[ 'blog_name' ], "text" ),
      GetSQLValueString( $pic, "text" ),
      GetSQLValueString( $_POST[ 'blog_description' ], "text" ) );
    $Result = mysqli_query( $orek, $insertSQL )or die( mysqli_error( $orek ) );

    $insertGoTo = "add-blog.php?success=Added";
    if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
      $insertGoTo .= ( strpos( $insertGoTo, '?' ) ) ? "&" : "?";
      $insertGoTo .= $_SERVER[ 'QUERY_STRING' ];
    }
    header( sprintf( "Location: %s", $insertGoTo ) );
  }
}

if ( ( isset( $_POST[ "MM_update" ] ) ) && ( $_POST[ "MM_update" ] == "form1" ) ) {
  $updateSQL = sprintf( "UPDATE `blogs` SET `blog_name` = %s, `blog_description` = %s WHERE blog_id = %s",
    GetSQLValueString( $_POST[ 'blog_name' ], "text" ),
    GetSQLValueString( $_POST[ 'blog_description' ], "text" ),
    GetSQLValueString( $_POST[ 'blog_id' ], "int" ) );
  $Result = mysqli_query( $orek, $updateSQL )or die( mysqli_error( $orek ) );

  $insertGoTo = "add-blog.php?success=Updated";
  if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
    $insertGoTo .= ( strpos( $insertGoTo, '?' ) ) ? "&" : "?";
    $insertGoTo .= $_SERVER[ 'QUERY_STRING' ];
  }
  header( sprintf( "Location: %s", $insertGoTo ) );
}
//update image_1
if ( ( isset( $_POST[ "MM_photo" ] ) ) && ( $_POST[ "MM_photo" ] == "form2" ) ) {

  $target = "../assets/img/blog/";
  $randno = rand( 100, 1000 );
  $target = $target . $randno . "-" . basename( $_FILES[ 'image_1' ][ 'name' ] );
  $imageFileType = pathinfo( $target, PATHINFO_EXTENSION );
  // Allow certain file formats
  if ( $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
    $error[] = "Sorry, File is not an image. Please upload jpg, png or gif files only";
  } else if ( strpos( strtolower( $_FILES[ 'image_1' ][ 'name' ] ), 'php' ) !== false || strpos( strtolower( $_FILES[ 'image_1' ][ 'name' ] ), 'js' ) !== false ) {
    $error[] = "Sorry, File is not an image. Please upload jpg, png or gif files only";
  } else {
    $pic = $randno . "-" . ( $_FILES[ 'image_1' ][ 'name' ] );

    //Writes the photo to the server
    if ( move_uploaded_file( $_FILES[ 'image_1' ][ 'tmp_name' ], $target ) ) {
      $insertSQL = sprintf( "UPDATE `blogs` SET `image_1` = %s WHERE blog_id = %s",
        GetSQLValueString( $pic, "text" ),
        GetSQLValueString( $_POST[ 'blog_id' ], "int" ) );
      $Result = mysqli_query( $orek, $insertSQL )or die( mysqli_error( $orek ) );

      $insertGoTo = "add-blog.php?success=Image Updated";
      if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
        $insertGoTo .= ( strpos( $insertGoTo, '?' ) ) ? "&" : "?";
        $insertGoTo .= $_SERVER[ 'QUERY_STRING' ];
      }
      header( sprintf( "Location: %s", $insertGoTo ) );
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />

    <title>Add Blog - Orek</title>
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
            <h1>Add Blog</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active">Add Blog</li>
                </ol>
            </nav>
        </div>
        <!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <a href="blog.php" class="btn btn-secondary">Back</a>
                </div>
                <!-- Left side columns -->
                <?php
        if ( isset( $_GET[ 'success' ] ) ) {
            echo '<div class="col-md-12">';
            echo '<div class="alert alert-success"> Blog ' . $_GET[ 'success' ] . ' Successfully</div>';
            echo '</div>';
        }
        ?>
                <div class="col-lg-12">
                    <div class="row">
                        <?php
                        if (isset($_GET['success']) || $flag) {
                        ?>
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Blog Image</h5>
                                <div class="row">
                                    <!-- Display Brand Image -->
                                    <div class="col-md-4">
                                        <img src="../assets/img/Blog/<?php echo $row_Recordset2['image_1']; ?>"
                                            class="img-fluid img-thumbnail" style="max-width: 100%; height: auto;">
                                    </div>

                                    <!-- Upload New Image Form -->
                                    <div class="col-md-8">
                                        <form action="<?php echo $editFormAction; ?>" method="POST"
                                            enctype="multipart/form-data" name="form2" role="form">
                                            <div class="mb-3">
                                                <label for="image_1" class="form-label">Select New Blog
                                                    Image</label><br>
                                                <input type="file" name="image_1" id="image_1" />
                                            </div>
                                            <div class="text-start">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="bi bi-upload"></i> Upload
                                                </button>
                                                <input type="hidden" name="blog_id"
                                                    value="<?php echo $row_Recordset2['blog_id']; ?>" />
                                                <input type="hidden" name="MM_photo" value="form2">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>

                        <!-- Vertical Form -->
                        <form method="POST" class="row g-3" name="form1" role="form"
                            action="<?php echo $editFormAction; ?>" enctype="multipart/form-data">
                            <div class="col-12">
                                <label for="group_code" class="form-label">Blog Name</label>
                                <input type="text" class="form-control" name="blog_name"
                                    value="<?php if($flag) echo $row_Recordset2['blog_name'] ?>" />
                            </div>
                            <?php if(!$flag) { ?>
                            <div class="col-12">
                                <label for="client_name" class="form-label">Add Blog Image</label>
                                <input type="file" class="form-control" name="image_1"
                                    value="<?php if($flag) echo $row_Recordset2['image_1'] ?>" />
                            </div>
                            <?php } ?>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="control-label"> Description </label>
                                    <textarea id="mytextarea" name="blog_description"><?php if($flag) echo $row_Recordset2['blog_description']; ?>
</textarea>
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <?php
                                if ( $flag ) {
                                  ?>
                                <input type="hidden" name="MM_update" value="form1">
                                <input type="hidden" name="blog_id" value="<?php echo $blog_id; ?>">
                                <?php } else { ?>
                                <input type="hidden" name="MM_insert" value="form1">
                                <?php } ?>
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
    <script>
    tinymce.init({
        selector: '#mytextarea'
    });
    </script>
</body>

</html>