<?php require_once('../Connections/orek.php'); ?>
<?php require_once('session.php'); ?>
<?php
$flag = false;
  if ( isset( $_GET[ 'item_id' ] ) ) {
  $item_id = $_GET[ 'item_id' ];
  
    $query_Recordset2 = "SELECT * FROM item WHERE item_id = '$item_id'";
    $Recordset2 = mysqli_query( $orek, $query_Recordset2 )or die( mysqli_error( $orek ) );
    $row_Recordset2 = mysqli_fetch_assoc( $Recordset2 );
    $totalRows_Recordset2 = mysqli_num_rows( $Recordset2 );
    $flag = true;
}

    // $query_Recordset3 = "SELECT * FROM category_level1 WHERE category_level1_id = '{$row_Recordset2['category_level1_id']}'";
    // $Recordset3 = mysqli_query( $orek, $query_Recordset3 )or die( mysqli_error( $orek ) );
    // $row_Recordset3 = mysqli_fetch_assoc( $Recordset3 );
    // $totalRows_Recordset3 = mysqli_num_rows( $Recordset3 );

    // $query_Recordset4 = "SELECT * FROM category_level2 WHERE category_level2_id = '{$row_Recordset2['category_level2_id']}'";
    // $Recordset4 = mysqli_query( $orek, $query_Recordset4 )or die( mysqli_error( $orek ) );
    // $row_Recordset4 = mysqli_fetch_assoc( $Recordset4 );
    // $totalRows_Recordset4 = mysqli_num_rows( $Recordset4 );

    $query_Recordset5 = "SELECT * FROM category";
    $Recordset5 = mysqli_query( $orek, $query_Recordset5 )or die( mysqli_error( $orek ) );
    $row_Recordset5 = mysqli_fetch_assoc( $Recordset5 );
    $totalRows_Recordset5 = mysqli_num_rows( $Recordset5 );
    
if ( isset( $_GET[ 'success' ] ) && $_GET[ 'success' ] == "Added" ) {
  $query_Recordset2 = "SELECT * FROM item ORDER BY item_id DESC LIMIT 1";
  $Recordset2 = mysqli_query( $orek, $query_Recordset2 )or die( mysqli_error( $orek ) );
  $row_Recordset2 = mysqli_fetch_assoc( $Recordset2 );
  $totalRows_Recordset2 = mysqli_num_rows( $Recordset2 );

  $item_id = $row_Recordset2[ 'item_id' ];
  $flag = true;
}

$editFormAction = $_SERVER[ 'PHP_SELF' ];
if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
  $editFormAction .= "?" . htmlentities( $_SERVER[ 'QUERY_STRING' ] );
}
if ( ( isset( $_POST[ "MM_insert" ] ) ) && ( $_POST[ "MM_insert" ] == "form1" ) ) {

  //	image 1
  $target = "../assets/img/item/";
  $randno = rand( 100, 1000 );
  $target = $target . $randno . "-" . basename( $_FILES[ 'image_1' ][ 'name' ] );
  $imageFileType = pathinfo( $target, PATHINFO_EXTENSION );
  // Allow certain file formats
  if ( $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
    $error[] = "Sorry, File is not an image. Please upload jpg, png or gif files only";
  } else if ( strpos( strtolower( $_FILES[ 'image_1' ][ 'name' ] ), 'php' ) !== false || strpos( strtolower( $_FILES[ 'image_1' ][ 'name' ] ), 'js' ) !== false ) {
    $error[] = "Sorry, File is not an image. Please upload jpg, png or gif files only";
  } else
    $pic1 = $randno . "-" . ( $_FILES[ 'image_1' ][ 'name' ] );

  if ( move_uploaded_file( $_FILES[ 'image_1' ][ 'tmp_name' ], $target ) ) {

    $insertSQL = sprintf( "INSERT INTO `item`(`item_name`, `category_id`, `hsn_code`, `category_level1_id`, `category_level2_id`, `listing_status`, `size`, `mood`, `ocassion`, `local_delivery_charge`, `price`, `tax`, `discount`, `position`, `description`, `image_1`) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
      GetSQLValueString( $_POST[ 'item_name' ], "text" ),
      GetSQLValueString( $_POST[ 'category_id' ], "text" ),
      GetSQLValueString( $_POST[ 'hsn_code' ], "text" ),
      GetSQLValueString( $_POST[ 'category_level1_id' ], "text" ),
      GetSQLValueString( $_POST[ 'category_level2_id' ], "text" ),
      GetSQLValueString( $_POST[ 'listing_status' ], "text" ),
      GetSQLValueString( $_POST[ 'size' ], "text" ),
      GetSQLValueString( $_POST[ 'mood' ], "text" ),
      GetSQLValueString( $_POST[ 'ocassion' ], "text" ),
      GetSQLValueString( $_POST[ 'local_delivery_charge' ], "text" ),
      GetSQLValueString( $_POST[ 'price' ], "text" ),
      GetSQLValueString( $_POST[ 'tax' ], "text" ),
      GetSQLValueString( $_POST[ 'discount' ], "text" ),
      GetSQLValueString( $_POST[ 'position' ], "text" ),
      GetSQLValueString( $_POST[ 'description' ], "text" ),
      GetSQLValueString( $pic1, "text" ) );
    $Result = mysqli_query( $orek, $insertSQL )or die( mysqli_error( $orek ) );

    $query_Recordset6 = "SELECT * FROM item ORDER BY item_id DESC LIMIT 1";
    $Recordset6 = mysqli_query( $orek, $query_Recordset6 )or die( mysqli_error( $orek ) );
    $row_Recordset6 = mysqli_fetch_assoc( $Recordset6 );
    $totalRows_Recordset6 = mysqli_num_rows( $Recordset6 );

    $insertGoTo = "add-item.php?success=Added";
    if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
      $insertGoTo .= ( strpos( $insertGoTo, '?' ) ) ? "&" : "?";
      $insertGoTo .= $_SERVER[ 'QUERY_STRING' ];
    }
    header( sprintf( "Location: %s", $insertGoTo ) );
  }
}

if ( ( isset( $_POST[ "MM_update" ] ) ) && ( $_POST[ "MM_update" ] == "form1" ) ) {

  $updateSQL = sprintf( "UPDATE `item` SET `item_name` = %s, `category_id` = %s, `hsn_code` = %s, `category_level1_id` = %s, `category_level2_id` = %s, `listing_status` = %s, `size` = %s, `mood` = %s, `ocassion` = %s, `local_delivery_charge` = %s, `price` = %s, `tax` = %s, `discount` = %s, `position` = %s, `description` = %s WHERE item_id = %s",
    GetSQLValueString( $_POST[ 'item_name' ], "text" ),
    GetSQLValueString( $_POST[ 'category_id' ], "text" ),
    GetSQLValueString( $_POST[ 'hsn_code' ], "text" ),
    GetSQLValueString( $_POST[ 'category_level1_id' ], "text" ),
    GetSQLValueString( $_POST[ 'category_level2_id' ], "text" ),
    GetSQLValueString( $_POST[ 'listing_status' ], "text" ),
    GetSQLValueString( $_POST[ 'size' ], "text" ),
    GetSQLValueString( $_POST[ 'mood' ], "text" ),
    GetSQLValueString( $_POST[ 'ocassion' ], "text" ),
    GetSQLValueString( $_POST[ 'local_delivery_charge' ], "text" ),
    GetSQLValueString( $_POST[ 'price' ], "text" ),
    GetSQLValueString( $_POST[ 'tax' ], "text" ),
    GetSQLValueString( $_POST[ 'discount' ], "text" ),
    GetSQLValueString( $_POST[ 'position' ], "text" ),
    GetSQLValueString( $_POST[ 'description' ], "text" ),
    GetSQLValueString( $_POST[ 'item_id' ], "int" ) );
  $Result = mysqli_query( $orek, $updateSQL )or die( mysqli_error( $orek ) );

//   $deleteSQL = sprintf( "DELETE FROM `car_model_item` WHERE item_id = %s",
//     GetSQLValueString( $_POST[ 'item_id' ], "int" ) );
//   $Result1 = mysqli_query( $orek, $deleteSQL )or die( mysqli_error( $orek ) );

  $insertGoTo = "add-item.php?success=Updated";
  if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
    $insertGoTo .= ( strpos( $insertGoTo, '?' ) ) ? "&" : "?";
    $insertGoTo .= $_SERVER[ 'QUERY_STRING' ];
  }
  header( sprintf( "Location: %s", $insertGoTo ) );
}
//update image_1
if ( ( isset( $_POST[ "MM_photo" ] ) ) && ( $_POST[ "MM_photo" ] == "form2" ) ) {

  $target = "../assets/img/item/";
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
      $insertSQL = sprintf( "UPDATE `item` SET `image_1` = %s WHERE item_id = %s",
        GetSQLValueString( $pic, "text" ),
        GetSQLValueString( $_POST[ 'item_id' ], "int" ) );
      $Result = mysqli_query( $orek, $insertSQL )or die( mysqli_error( $orek ) );

      $insertGoTo = "add-item.php?success=Image Updated";
      if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
        $insertGoTo .= ( strpos( $insertGoTo, '?' ) ) ? "&" : "?";
        $insertGoTo .= $_SERVER[ 'QUERY_STRING' ];
      }
      header( sprintf( "Location: %s", $insertGoTo ) );
    }
  }
}
//image_2
if ( ( isset( $_POST[ "MM_photo" ] ) ) && ( $_POST[ "MM_photo" ] == "form3" ) ) {

  $target = "../assets/img/item/";
  $randno = rand( 100, 1000 );
  $target = $target . $randno . "-" . basename( $_FILES[ 'image_2' ][ 'name' ] );
  $imageFileType = pathinfo( $target, PATHINFO_EXTENSION );
  // Allow certain file formats
  if ( $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
    $error[] = "Sorry, File is not an image. Please upload jpg, png or gif files only";
  } else if ( strpos( strtolower( $_FILES[ 'image_2' ][ 'name' ] ), 'php' ) !== false || strpos( strtolower( $_FILES[ 'image_2' ][ 'name' ] ), 'js' ) !== false ) {
    $error[] = "Sorry, File is not an image. Please upload jpg, png or gif files only";
  } else {
    $pic = $randno . "-" . ( $_FILES[ 'image_2' ][ 'name' ] );

    if ( move_uploaded_file( $_FILES[ 'image_2' ][ 'tmp_name' ], $target ) ) {
      $insertSQL = sprintf( "UPDATE `item` SET `image_2` = %s WHERE item_id = %s",
        GetSQLValueString( $pic, "text" ),
        GetSQLValueString( $_POST[ 'item_id' ], "int" ) );
      $Result = mysqli_query( $orek, $insertSQL )or die( mysqli_error( $orek ) );

      $insertGoTo = "add-item.php?success=Image Updated";
      if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
        $insertGoTo .= ( strpos( $insertGoTo, '?' ) ) ? "&" : "?";
        $insertGoTo .= $_SERVER[ 'QUERY_STRING' ];
      }
      header( sprintf( "Location: %s", $insertGoTo ) );
    }
  }
}
//image_3
if ( ( isset( $_POST[ "MM_photo" ] ) ) && ( $_POST[ "MM_photo" ] == "form4" ) ) {

  $target = "../assets/img/item/";
  $randno = rand( 100, 1000 );
  $target = $target . $randno . "-" . basename( $_FILES[ 'image_3' ][ 'name' ] );
  $imageFileType = pathinfo( $target, PATHINFO_EXTENSION );
  // Allow certain file formats
  if ( $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
    $error[] = "Sorry, File is not an image. Please upload jpg, png or gif files only";
  } else if ( strpos( strtolower( $_FILES[ 'image_3' ][ 'name' ] ), 'php' ) !== false || strpos( strtolower( $_FILES[ 'image_3' ][ 'name' ] ), 'js' ) !== false ) {
    $error[] = "Sorry, File is not an image. Please upload jpg, png or gif files only";
  } else {
    $pic = $randno . "-" . ( $_FILES[ 'image_3' ][ 'name' ] );

    if ( move_uploaded_file( $_FILES[ 'image_3' ][ 'tmp_name' ], $target ) ) {
      $insertSQL = sprintf( "UPDATE `item` SET `image_3` = %s WHERE item_id = %s",
        GetSQLValueString( $pic, "text" ),
        GetSQLValueString( $_POST[ 'item_id' ], "int" ) );
      $Result = mysqli_query( $orek, $insertSQL )or die( mysqli_error( $orek ) );

      $insertGoTo = "add-item.php?success=Image Updated";
      if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
        $insertGoTo .= ( strpos( $insertGoTo, '?' ) ) ? "&" : "?";
        $insertGoTo .= $_SERVER[ 'QUERY_STRING' ];
      }
      header( sprintf( "Location: %s", $insertGoTo ) );
    }
  }
}

//image_4
if ( ( isset( $_POST[ "MM_photo" ] ) ) && ( $_POST[ "MM_photo" ] == "form5" ) ) {

    $target = "../assets/img/item/";
    $randno = rand( 100, 1000 );
    $target = $target . $randno . "-" . basename( $_FILES[ 'image_4' ][ 'name' ] );
    $imageFileType = pathinfo( $target, PATHINFO_EXTENSION );
    // Allow certain file formats
    if ( $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
      $error[] = "Sorry, File is not an image. Please upload jpg, png or gif files only";
    } else if ( strpos( strtolower( $_FILES[ 'image_4' ][ 'name' ] ), 'php' ) !== false || strpos( strtolower( $_FILES[ 'image_4' ][ 'name' ] ), 'js' ) !== false ) {
      $error[] = "Sorry, File is not an image. Please upload jpg, png or gif files only";
    } else {
      $pic = $randno . "-" . ( $_FILES[ 'image_4' ][ 'name' ] );
  
      if ( move_uploaded_file( $_FILES[ 'image_4' ][ 'tmp_name' ], $target ) ) {
        $insertSQL = sprintf( "UPDATE `item` SET `image_4` = %s WHERE item_id = %s",
          GetSQLValueString( $pic, "text" ),
          GetSQLValueString( $_POST[ 'item_id' ], "int" ) );
        $Result = mysqli_query( $orek, $insertSQL )or die( mysqli_error( $orek ) );
  
        $insertGoTo = "add-item.php?success=Image Updated";
        if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
          $insertGoTo .= ( strpos( $insertGoTo, '?' ) ) ? "&" : "?";
          $insertGoTo .= $_SERVER[ 'QUERY_STRING' ];
        }
      header( sprintf( "Location: %s", $insertGoTo ) );
    }
  }
}

//image_5
if ( ( isset( $_POST[ "MM_photo" ] ) ) && ( $_POST[ "MM_photo" ] == "form6" ) ) {

    $target = "../assets/img/item/";
    $randno = rand( 100, 1000 );
    $target = $target . $randno . "-" . basename( $_FILES[ 'image_5' ][ 'name' ] );
    $imageFileType = pathinfo( $target, PATHINFO_EXTENSION );
    // Allow certain file formats
    if ( $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
      $error[] = "Sorry, File is not an image. Please upload jpg, png or gif files only";
    } else if ( strpos( strtolower( $_FILES[ 'image_5' ][ 'name' ] ), 'php' ) !== false || strpos( strtolower( $_FILES[ 'image_5' ][ 'name' ] ), 'js' ) !== false ) {
      $error[] = "Sorry, File is not an image. Please upload jpg, png or gif files only";
    } else {
      $pic = $randno . "-" . ( $_FILES[ 'image_5' ][ 'name' ] );
  
      if ( move_uploaded_file( $_FILES[ 'image_5' ][ 'tmp_name' ], $target ) ) {
        $insertSQL = sprintf( "UPDATE `item` SET `image_5` = %s WHERE item_id = %s",
          GetSQLValueString( $pic, "text" ),
          GetSQLValueString( $_POST[ 'item_id' ], "int" ) );
        $Result = mysqli_query( $orek, $insertSQL )or die( mysqli_error( $orek ) );
  
        $insertGoTo = "add-item.php?success=Image Updated";
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

    <title>Add Item - Orek</title>
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
            <h1>Add Item</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active">Add Item</li>
                </ol>
            </nav>
        </div>
        <!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <?php if (isset($_GET['category_id'])) { ?>
                    <a href="item.php?category_id=<?php echo $row_Recordset2['category_id']; ?>"
                        class="btn btn-secondary">Back</a>
                    <?php } else { ?>
                    <a href="item.php" class="btn btn-secondary">Back</a>
                    <?php } ?>
                </div>
                <!-- Left side columns -->
                <?php
        if ( isset( $_GET[ 'success' ] ) ) {
            echo '<div class="col-md-12">';
            echo '<div class="alert alert-success"> Item ' . $_GET[ 'success' ] . ' Successfully</div>';
            echo '</div>';
        }
        ?>
                <div class="col-lg-12">
                    <div class="row">
                        <?php if (isset($_GET['success']) || $flag) { ?>
                        <div class="row mb-3">
                            <!-- Image 1 Upload -->
                            <div class="col-md-4">
                                <h6>Image 1</h6>
                                <img src="../assets/img/item/<?php echo $row_Recordset2['image_1']; ?>"
                                    class="img-thumbnail" width="100%">
                                <form action="<?php echo $editFormAction; ?>" method="POST"
                                    enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label for="image_1" class="form-label">Select Image 1</label>
                                        <input type="file" class="form-control" name="image_1" id="image_1">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Upload</button>
                                    <input type="hidden" name="item_id"
                                        value="<?php echo $row_Recordset2['item_id']; ?>">
                                    <input type="hidden" name="MM_photo" value="form2">
                                </form>
                            </div>
                            <!-- Image 2 Upload -->
                            <div class="col-md-4">
                                <img src="../assets/img/item/<?php echo $row_Recordset2['image_2']; ?>"
                                    class="img-thumbnail" width="100%">
                                <form action="<?php echo $editFormAction; ?>" method="POST"
                                    enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label for="image_2" class="form-label">Select Image 2</label>
                                        <input type="file" class="form-control" name="image_2" id="image_2">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Upload</button>
                                    <input type="hidden" name="item_id"
                                        value="<?php echo $row_Recordset2['item_id']; ?>">
                                    <input type="hidden" name="MM_photo" value="form3">
                                </form>
                            </div>
                            <!-- Image 3 Upload -->
                            <div class="col-md-4">
                                <img src="../assets/img/item/<?php echo $row_Recordset2['image_3']; ?>"
                                    class="img-thumbnail" width="100%">
                                <form action="<?php echo $editFormAction; ?>" method="POST"
                                    enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label for="image_3" class="form-label">Select Image 3</label>
                                        <input type="file" class="form-control" name="image_3" id="image_3">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Upload</button>
                                    <input type="hidden" name="item_id"
                                        value="<?php echo $row_Recordset2['item_id']; ?>">
                                    <input type="hidden" name="MM_photo" value="form4">
                                </form>
                            </div>
                            <!-- Image 4 Upload -->
                            <div class="col-md-4">
                                <img src="../assets/img/item/<?php echo $row_Recordset2['image_4']; ?>"
                                    class="img-thumbnail" width="100%">
                                <form action="<?php echo $editFormAction; ?>" method="POST"
                                    enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label for="image_4" class="form-label">Select Image 4</label>
                                        <input type="file" class="form-control" name="image_4" id="image_4">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Upload</button>
                                    <input type="hidden" name="item_id"
                                        value="<?php echo $row_Recordset2['item_id']; ?>">
                                    <input type="hidden" name="MM_photo" value="form5">
                                </form>
                            </div>
                            <!-- Image 5 Upload -->
                            <div class="col-md-4">
                                <img src="../assets/img/item/<?php echo $row_Recordset2['image_5']; ?>"
                                    class="img-thumbnail" width="100%">
                                <form action="<?php echo $editFormAction; ?>" method="POST"
                                    enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label for="image_5" class="form-label">Select Image 5</label>
                                        <input type="file" class="form-control" name="image_5" id="image_5">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Upload</button>
                                    <input type="hidden" name="item_id"
                                        value="<?php echo $row_Recordset2['item_id']; ?>">
                                    <input type="hidden" name="MM_photo" value="form6">
                                </form>
                            </div>
                        </div>
                        <?php } ?>

                        <!-- Product Details Form -->
                        <form method="POST" enctype="multipart/form-data" action="<?php echo $editFormAction; ?>">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="item_name" class="form-label">Item Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="item_name" class="form-control" required
                                        value="<?php if($flag) echo $row_Recordset2['item_name']; ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="hsn_code" class="form-label">HSN Code</label>
                                    <input type="text" name="hsn_code" class="form-control"
                                        value="<?php if($flag) echo $row_Recordset2['hsn_code']; ?>">
                                </div>
                                <!-- Category Dropdown -->
                                <div class="col-md-6 mt-3">
                                    <label for="category_id" class="form-label">Category Name <span
                                            class="text-danger">*</span></label>
                                    <select name="category_id" id="category_id" class="form-control" required>
                                        <option value="">Select Category</option>
                                        <?php do{ ?>
                                        <option value="<?php echo $row_Recordset5['category_id'] ?>"
                                            <?php if($flag && $row_Recordset2['category_id'] == $row_Recordset5['category_id']) echo "selected"; ?>>
                                            <?php echo $row_Recordset5['category_name']; ?></option>
                                        <?php }while($row_Recordset5=mysqli_fetch_assoc($Recordset5)) ?>
                                    </select>
                                </div>

                                <!-- Category Level1 Dropdown -->
                                <div class="col-md-6 mt-3">
                                    <label for="category_level1_id" class="form-label">Category Level1 Name <span
                                            class="text-danger">*</span></label>
                                    <select name="category_level1_id" id="category_level1_id" class="form-control"
                                        required>
                                        <option value="">Select Category Level1</option>
                                        <?php 
                                        // Get all category level 1 items for the selected category
                                        if($flag) {
                                            $query_AllLevel1 = "SELECT * FROM category_level1 WHERE category_id = '{$row_Recordset2['category_id']}'";
                                            $Recordset_AllLevel1 = mysqli_query($orek, $query_AllLevel1) or die(mysqli_error($orek));
                                            
                                            while($row_AllLevel1 = mysqli_fetch_assoc($Recordset_AllLevel1)) {
                                                $selected = ($row_AllLevel1['category_level1_id'] == $row_Recordset2['category_level1_id']) ? 'selected' : '';
                                                echo "<option value='" . $row_AllLevel1['category_level1_id'] . "' " . $selected . ">";
                                                echo $row_AllLevel1['category_level1_name'];
                                                echo "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label for="category_level2_id" class="form-label">Category Level2 Name </label>
                                    <select name="category_level2_id" id="category_level2_id" class="form-control">
                                        <option value="">Select Category Level2</option>
                                        <?php 
                                        // Get all category level 2 items for the selected category level 1
                                        if($flag) {
                                            $query_AllLevel2 = "SELECT * FROM category_level2 WHERE category_level1_id = '{$row_Recordset2['category_level1_id']}'";
                                            $Recordset_AllLevel2 = mysqli_query($orek, $query_AllLevel2) or die(mysqli_error($orek));
                                            
                                            while($row_AllLevel2 = mysqli_fetch_assoc($Recordset_AllLevel2)) {
                                                $selected = ($row_AllLevel2['category_level2_id'] == $row_Recordset2['category_level2_id']) ? 'selected' : '';
                                                echo "<option value='" . $row_AllLevel2['category_level2_id'] . "' " . $selected . ">";
                                                echo $row_AllLevel2['category_level2_name'];
                                                echo "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label for="listing_status" class="form-label">Listing Status<span
                                            class="text-danger">*</span></label>
                                    <select name="listing_status" class="form-select" required>
                                        <option value="">Select</option>
                                        <option value="Active"
                                            <?php if($flag && $row_Recordset2['listing_status'] == "Active") echo "selected"; ?>>
                                            Active</option>
                                        <option value="Inactive"
                                            <?php if($flag && $row_Recordset2['listing_status'] == "Inactive") echo "selected"; ?>>
                                            Inactive</option>
                                        <option value="Gift"
                                            <?php if($flag && $row_Recordset2['listing_status'] == "Gift") echo "selected"; ?>>
                                            Gift</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label for="size" class="form-label">Size</label>
                                    <input type="text" name="size" class="form-control"
                                        value="<?php if($flag) echo $row_Recordset2['size']; ?>">
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label for="mood" class="form-label">Mood</label>
                                    <select name="mood" class="form-select">
                                        <option value="">Select</option>
                                        <option value="Vacay"
                                            <?php if($flag && $row_Recordset2['mood'] == "Vacay") echo "selected"; ?>>
                                            Vacay</option>
                                        <option value="Office"
                                            <?php if($flag && $row_Recordset2['mood'] == "Office") echo "selected"; ?>>
                                            Office</option>
                                        <option value="Night out"
                                            <?php if($flag && $row_Recordset2['mood'] == "Night out") echo "selected"; ?>>
                                            Night out</option>
                                        <option value="Must haves"
                                            <?php if($flag && $row_Recordset2['mood'] == "Must haves") echo "selected"; ?>>
                                            Must haves</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label for="ocassion" class="form-label">Occasion <span
                                            class="text-danger">*</span></label>
                                    <select name="ocassion" class="form-select" required>
                                        <option value="">Select</option>
                                        <option value="Casual"
                                            <?php if($flag && $row_Recordset2['ocassion'] == "Casual") echo "selected"; ?>>
                                            Casual</option>
                                        <option value="Party"
                                            <?php if($flag && $row_Recordset2['ocassion'] == "Party") echo "selected"; ?>>
                                            Party</option>
                                        <option value="Wedding"
                                            <?php if($flag && $row_Recordset2['ocassion'] == "Wedding") echo "selected"; ?>>
                                            Wedding</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label for="local_delivery_charge" class="form-label">Local Delivery Charge
                                    </label>
                                    <input type="text" name="local_delivery_charge" class="form-control"
                                        value="<?php if($flag) echo $row_Recordset2['local_delivery_charge']; ?>">
                                </div>

                                <div class="col-md-6 mt-3">
                                    <label for="price" class="form-label">Price<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="price" class="form-control"
                                        value="<?php if($flag) echo $row_Recordset2['price']; ?>" required>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label class="form-label">Tax:<span class="text-danger">*</span></label>
                                    <select name="tax" class="form-control" required>
                                        <option value="">Select Tax</option>
                                        <option value="5%"
                                            <?php if($flag && $row_Recordset2['tax'] == '5%') echo "selected" ?>>
                                            5%
                                        </option>
                                        <option value="12%"
                                            <?php if($flag && $row_Recordset2['tax'] == '12%') echo "selected" ?>>
                                            12%
                                        </option>
                                        <option value="18%"
                                            <?php if($flag && $row_Recordset2['tax'] == '18%') echo "selected" ?>>
                                            18%
                                        </option>
                                        <option value="28%"
                                            <?php if($flag && $row_Recordset2['tax'] == '28%') echo "selected" ?>>
                                            28%
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label for="discount" class="form-label">Discount<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="discount" class="form-control"
                                        value="<?php if($flag) echo $row_Recordset2['discount']; ?>" required>
                                </div>
                                <?php if(!$flag) { ?>
                                <div class="col-md-6 mt-3">
                                    <label for="image_1" class="form-label">Image</label>
                                    <input type="file" name="image_1" class="form-control"
                                        value="<?php if($flag) echo $row_Recordset2['image_1']; ?>">
                                </div>
                                <?php } ?>
                                <div class="col-md-6 mt-3">
                                    <label for="position" class="form-label">Position</label>
                                    <input type="text" name="position" class="form-control"
                                        value="<?php if($flag) echo $row_Recordset2['position']; ?>">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="control-label"> Description </label>
                                <textarea id="mytextarea" name="description"><?php if($flag) echo $row_Recordset2['description']; ?>
</textarea>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Save</button>
                                <input type="hidden" name="car_model" value="<?php echo $totalRows_Recordset4 ?>">
                                <?php
                                if ( $flag ) {
                                    ?>
                                <input type="hidden" name="MM_update" value="form1">
                                <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">
                                <?php } else { ?>
                                <input type="hidden" name="MM_insert" value="form1">
                                <?php } ?>
                                <button type="reset" class="btn btn-secondary">Reset</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Vertical Form -->
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        // Fetch Category Level1 based on Category selection
        $('#category_id').change(function() {
            var category_id = $(this).val();
            if (category_id !== '') {
                $.ajax({
                    url: "fetch_category_level1.php",
                    method: "POST",
                    data: {
                        category_id: category_id
                    },
                    success: function(data) {
                        $('#category_level1_id').html(data);
                        $('#category_level2_id').html(
                            '<option value="">Select Category Level2</option>'
                        ); // Reset Level2

                        console.log("Category Level1 Data Loaded:\n" +
                            data); // 🔹 Echo output in console
                    }
                });
            } else {
                $('#category_level1_id').html('<option value="">Select Category Level1</option>');
                $('#category_level2_id').html('<option value="">Select Category Level2</option>');
            }
        });

        // Fetch Category Level2 based on Category Level1 selection
        $('#category_level1_id').change(function() {
            var category_level1_id = $(this).val();
            if (category_level1_id !== '') {
                $.ajax({
                    url: "fetch_category_level2.php",
                    method: "POST",
                    data: {
                        category_level1_id: category_level1_id
                    },
                    success: function(data) {
                        $('#category_level2_id').html(data);
                        console.log("Category Level2 Data Loaded:\n" +
                            data); // 🔹 Echo output in console
                    }
                });
            } else {
                $('#category_level2_id').html('<option value="">Select Category Level2</option>');
            }
        });
    });
    </script>

</body>

</html>