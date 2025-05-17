<?php require_once('../Connections/car_spare.php'); ?>
<?php require_once('session.php'); ?>
<?php
$flag = false;
$query_Recordset3 = "SELECT * FROM heading";
$Recordset3 = mysqli_query( $car_spare, $query_Recordset3 )or die( mysqli_error( $car_spare ) );
$row_Recordset3 = mysqli_fetch_assoc( $Recordset3 );
$totalRows_Recordset3 = mysqli_num_rows( $Recordset3 );

if ( isset( $_GET[ 'sub_heading_id' ] ) ) {
  $sub_heading_id = $_GET[ 'sub_heading_id' ];

  $query_Recordset2 = "SELECT * FROM sub_heading WHERE sub_heading_id='$sub_heading_id'";
  $Recordset2 = mysqli_query( $car_spare, $query_Recordset2 )or die( mysqli_error( $car_spare ) );
  $row_Recordset2 = mysqli_fetch_assoc( $Recordset2 );
  $totalRows_Recordset2 = mysqli_num_rows( $Recordset2 );
  $flag = true;
}

if ( isset( $_GET[ 'success' ] ) && $_GET[ 'success' ] == "Added" ) {
  $query_Recordset2 = "SELECT * FROM sub_heading ORDER BY sub_heading_id DESC LIMIT 1";
  $Recordset2 = mysqli_query( $car_spare, $query_Recordset2 )or die( mysqli_error( $car_spare ) );
  $row_Recordset2 = mysqli_fetch_assoc( $Recordset2 );
  $totalRows_Recordset2 = mysqli_num_rows( $Recordset2 );

  $sub_heading_id = $row_Recordset2[ 'sub_heading_id' ];
  $flag = true;
}

$editFormAction = $_SERVER[ 'PHP_SELF' ];
if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
  $editFormAction .= "?" . htmlentities( $_SERVER[ 'QUERY_STRING' ] );
}
if ( ( isset( $_POST[ "MM_insert" ] ) ) && ( $_POST[ "MM_insert" ] == "form1" ) ) {

  $target = "../assets/img/sub-heading/";
  $randno = rand( 100, 1000 );
  $target = $target . $randno . "-" . basename( $_FILES[ 'image_1' ][ 'name' ] );
  $imageFileType = pathinfo( $target, PATHINFO_EXTENSION );
  // Allow certain file formats
  if ( $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
    $error = "Sorry, File is not an image. Please upload jpg, png or gif files only";
  } else if ( strpos( strtolower( $_FILES[ 'image_1' ][ 'name' ] ), 'php' ) !== false || strpos( strtolower( $_FILES[ 'image_1' ][ 'name' ] ), 'js' ) !== false ) {
    $error = "Sorry, File is not an image. Please upload jpg, png or gif files only";
  } else
    $pic = $randno . "-" . ( $_FILES[ 'image_1' ][ 'name' ] );
  if ( move_uploaded_file( $_FILES[ 'image_1' ][ 'tmp_name' ], $target ) ) {

    $insertSQL = sprintf( "INSERT INTO `sub_heading`(`heading_id`, `sub_heading_name`, `age_restricted`, `image_1`) VALUES (%s, %s, %s, %s)",
      GetSQLValueString( $_POST[ 'heading_id' ], "text" ),
      GetSQLValueString( $_POST[ 'sub_heading_name' ], "text" ),
      GetSQLValueString( $_POST[ 'age_restricted' ], "text" ),
      GetSQLValueString( $pic, "text" ) );
    $Result = mysqli_query( $car_spare, $insertSQL )or die( mysqli_error( $car_spare ) );

    $insertGoTo = "add-sub-heading.php?success=Added";
    if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
      $insertGoTo .= ( strpos( $insertGoTo, '?' ) ) ? "&" : "?";
      $insertGoTo .= $_SERVER[ 'QUERY_STRING' ];
    }
    header( sprintf( "Location: %s", $insertGoTo ) );
  }
}

if ( ( isset( $_POST[ "MM_update" ] ) ) && ( $_POST[ "MM_update" ] == "form1" ) ) {
  $updateSQL = sprintf( "UPDATE `sub_heading` SET `heading_id` = %s, `sub_heading_name` = %s, `age_restricted` = %s WHERE sub_heading_id = %s",
    GetSQLValueString( $_POST[ 'heading_id' ], "text" ),
    GetSQLValueString( $_POST[ 'sub_heading_name' ], "text" ),
    GetSQLValueString( $_POST[ 'age_restricted' ], "text" ),
    GetSQLValueString( $_POST[ 'sub_heading_id' ], "int" ) );
  $Result = mysqli_query( $car_spare, $updateSQL )or die( mysqli_error( $car_spare ) );

  $insertGoTo = "add-sub-heading.php?success=Updated";
  if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
    $insertGoTo .= ( strpos( $insertGoTo, '?' ) ) ? "&" : "?";
    $insertGoTo .= $_SERVER[ 'QUERY_STRING' ];
  }
  header( sprintf( "Location: %s", $insertGoTo ) );
}
//update image_1
if ( ( isset( $_POST[ "MM_photo" ] ) ) && ( $_POST[ "MM_photo" ] == "form2" ) ) {

  $target = "../assets/img/sub-heading/";
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
      $insertSQL = sprintf( "UPDATE `sub_heading` SET `image_1` = %s WHERE sub_heading_id = %s",
        GetSQLValueString( $pic, "text" ),
        GetSQLValueString( $_POST[ 'sub_heading_id' ], "int" ) );
      $Result = mysqli_query( $car_spare, $insertSQL )or die( mysqli_error( $car_spare ) );

      $insertGoTo = "add-sub-heading.php?success=Image Updated";
      if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
        $insertGoTo .= ( strpos( $insertGoTo, '?' ) ) ? "&" : "?";
        $insertGoTo .= $_SERVER[ 'QUERY_STRING' ];
      }
      header( sprintf( "Location: %s", $insertGoTo ) );
    }
  }
}
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Add Sub Heading</title>
    <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
</head>

<body>
    <div class="left-side height-100">
        <div class="row height-100">
            <div class="col-md-2 bg-green padding-top-bottom height-100 left-menu">
                <div class="row">
                    <div class="padding-10 text-center"><img src="images/logo.png" width="60%">
                        <h4 style="color: #fff;">Welcome <?php echo $row_Recordset1['uname']; ?></h4>
                        <a href="logout.php" class="btn btn-danger">Logout</a> <a href="change-password.php"
                            class="btn btn-primary">Change Password</a>
                    </div>
                </div>
                <div class="row margin-70">
                    <div class="item-list-menu">
                        <?php include('menu.php'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-offset-2 col-md-10">
                <h3><strong>ADD SUB-HEADING</strong></h3>
                <hr />
                <?php
      if ( isset( $_GET[ 'success' ] ) ) {
        echo '<div class="col-md-12">';
        echo '<div class="alert alert-success">Sub-Heading ' . $_GET[ 'success' ] . ' Successfully</div>';
        echo '</div>';
      }
      ?>
                <div class="margin-50"> <a href="sub-heading.php" class="btn btn-primary">Back</a></div>
                <?php
      if ( isset( $_GET[ 'success' ] ) || $flag ) {
        ?>
                <h4>Image 1</h4>
                <div class="row">
                    <div class="col-md-4"> <img src="../assets/img/sub-heading/<?php echo $row_Recordset2['image_1'] ?>"
                            width="60%" class="img-thumbnail"> </div>
                    <div class="col-md-4">
                        <form action="<?php echo $editFormAction; ?>" method="POST" enctype="multipart/form-data"
                            name="form2" role="form">
                            <div class="form-group">
                                <label class="control-label" for="image_1">Select Sub heading Image</label>
                                <input type="file" name="image_1" id="image_1" />
                            </div>
                            <div class="form-group">
                                <button type="submit" id="upload" class="btn btn-info">Upload</button>
                                <input type="hidden" name="sub_heading_id"
                                    value="<?php echo $row_Recordset2['sub_heading_id'] ?>" />
                                <input type="hidden" name="MM_photo" value="form2">
                            </div>
                        </form>
                    </div>
                </div>
                <?php } ?>
                <form method="POST" name="form1" id="form1" enctype="multipart/form-data" role="form"
                    action="<?php echo $editFormAction; ?>">
                    <div class="form-group">
                        <label class="control-label" for="heading_id">Select Heading</label>
                        <select name="heading_id" class="form-control" required>
                            <option value="" selected disabled>Select Heading</option>
                            <?php do{ ?>
                            <option value="<?php echo $row_Recordset3['heading_id'] ?>"
                                <?php if($flag && $row_Recordset3['heading_id'] == $row_Recordset2['heading_id']) echo "selected" ?>>
                                <?php echo $row_Recordset3['heading_name'] ?></option>
                            <?php }while($row_Recordset3 = mysqli_fetch_assoc($Recordset3)) ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="sub_heading_name">Sub Heading Name</label>
                        <input type="text" name="sub_heading_name" class="form-control"
                            value="<?php if($flag) echo $row_Recordset2['sub_heading_name']; ?>" maxlength="100"
                            required>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Age Restricted</label>
                        <select name="age_restricted" class="form-control">
                            <option value="" selected disabled>Select Age Restricted</option>
                            <option value="Yes"
                                <?php if($flag && $row_Recordset2['age_restricted'] == 'Yes') echo "selected" ?>>Yes
                            </option>
                            <option value="No"
                                <?php if($flag && $row_Recordset2['age_restricted'] == 'No') echo "selected" ?>>No
                            </option>
                        </select>
                    </div>
                    <?php
          if ( !$flag ) {
            ?>
                    <div class="form-group">
                        <label for="image_1" class="control-label">Heading Image</label>
                        <input type="file" name="image_1" class="form-control" required>
                    </div>
                    <?php } ?>
                    <div class="form-group">
                        <button type="submit" id="save" class="btn btn-info">Save</button>
                        <?php
          if ( $flag ) {
            ?>
                        <input type="hidden" name="MM_update" value="form1">
                        <input type="hidden" name="sub_heading_id" value="<?php echo $sub_heading_id; ?>">
                        <?php } else { ?>
                        <input type="hidden" name="MM_insert" value="form1">
                        <?php } ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
    </div>
</body>
<script src="js/jquery-3.2.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>

</html>