<?php require_once('../Connections/car_spare.php'); ?>
<?php require_once('session.php'); ?>
<?php
$flag = false;

$query_Recordset3 = "SELECT * FROM car";
$Recordset3 = mysqli_query( $car_spare, $query_Recordset3 )or die( mysqli_error( $car_spare ) );
$row_Recordset3 = mysqli_fetch_assoc( $Recordset3 );
$totalRows_Recordset3 = mysqli_num_rows( $Recordset3 );

if ( isset( $_GET[ 'car_model_id' ] ) ) {
    $car_model_id = $_GET[ 'car_model_id' ];

    $query_Recordset2 = "SELECT * FROM car_model WHERE car_model_id='$car_model_id'";
    $Recordset2 = mysqli_query( $car_spare, $query_Recordset2 )or die( mysqli_error( $car_spare ) );
    $row_Recordset2 = mysqli_fetch_assoc( $Recordset2 );
    $totalRows_Recordset2 = mysqli_num_rows( $Recordset2 );
    $flag = true;
}

if ( isset( $_GET[ 'success' ] ) && $_GET[ 'success' ] == "Added" ) {
    $query_Recordset2 = "SELECT * FROM car_model ORDER BY car_model_id DESC LIMIT 1";
    $Recordset2 = mysqli_query( $car_spare, $query_Recordset2 )or die( mysqli_error( $car_spare ) );
    $row_Recordset2 = mysqli_fetch_assoc( $Recordset2 );
    $totalRows_Recordset2 = mysqli_num_rows( $Recordset2 );

    $car_model_id = $row_Recordset2[ 'car_model_id' ];
    $flag = true;
}

$editFormAction = $_SERVER[ 'PHP_SELF' ];
if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
    $editFormAction .= "?" . htmlentities( $_SERVER[ 'QUERY_STRING' ] );
}
if ( ( isset( $_POST[ "MM_insert" ] ) ) && ( $_POST[ "MM_insert" ] == "form1" ) ) {

    $target = "../assets/img/car_model/";
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

        $insertSQL = sprintf( "INSERT INTO `car_model`(`car_id`, `model_name`, `image_1`) VALUES (%s, %s, %s)",
            GetSQLValueString( $_POST[ 'car_id' ], "text" ),
            GetSQLValueString( $_POST[ 'model_name' ], "text" ),
            GetSQLValueString( $pic, "text" ) );
        $Result = mysqli_query( $car_spare, $insertSQL )or die( mysqli_error( $car_spare ) );

        $insertGoTo = "add-car-model.php?success=Added";
        if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
            $insertGoTo .= ( strpos( $insertGoTo, '?' ) ) ? "&" : "?";
            $insertGoTo .= $_SERVER[ 'QUERY_STRING' ];
        }
        header( sprintf( "Location: %s", $insertGoTo ) );
    }
}

if ( ( isset( $_POST[ "MM_update" ] ) ) && ( $_POST[ "MM_update" ] == "form1" ) ) {
    $updateSQL = sprintf( "UPDATE `car_model` SET `car_id` = %s, `model_name` = %s WHERE car_model_id = %s",
        GetSQLValueString( $_POST[ 'car_id' ], "text" ),
        GetSQLValueString( $_POST[ 'model_name' ], "text" ),
        GetSQLValueString( $_POST[ 'car_model_id' ], "int" ) );
    $Result = mysqli_query( $car_spare, $updateSQL )or die( mysqli_error( $car_spare ) );

    $insertGoTo = "add-car-model.php?success=Updated";
    if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
        $insertGoTo .= ( strpos( $insertGoTo, '?' ) ) ? "&" : "?";
        $insertGoTo .= $_SERVER[ 'QUERY_STRING' ];
    }
    header( sprintf( "Location: %s", $insertGoTo ) );
}
//update image_1
if ( ( isset( $_POST[ "MM_photo" ] ) ) && ( $_POST[ "MM_photo" ] == "form2" ) ) {

    $target = "../assets/img/car_model/";
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
            $insertSQL = sprintf( "UPDATE `car_model` SET `image_1` = %s WHERE car_model_id = %s",
                GetSQLValueString( $pic, "text" ),
                GetSQLValueString( $_POST[ 'car_model_id' ], "int" ) );
            $Result = mysqli_query( $car_spare, $insertSQL )or die( mysqli_error( $car_spare ) );

            $insertGoTo = "add-car-model.php?success=Image Updated";
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
<title>Add car_model</title>
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
          <a href="logout.php" class="btn btn-danger">Logout</a> <a href="change-password.php" class="btn btn-primary">Change Password</a> </div>
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
      <h3><strong>ADD car_model</strong></h3>
      <hr/>
      <?php
      if ( isset( $_GET[ 'success' ] ) ) {
          echo '<div class="col-md-12">';
          echo '<div class="alert alert-success">Car Model ' . $_GET[ 'success' ] . ' Successfully</div>';
          echo '</div>';
      }
      ?>
      <div class="margin-50"> <a href="car-model.php" class="btn btn-primary">Back</a></div>
      <div class="row margin-70">
        <div class="col-md-10">
          <?php
          if ( isset( $_GET[ 'success' ] ) || $flag ) {
              ?>
          <h4>Image 1</h4>
          <div class="row">
            <div class="col-md-4"> <img src="../assets/img/car_model/<?php echo $row_Recordset2['image_1'] ?>" width="60%" class="img-thumbnail"> </div>
            <div class="col-md-4">
              <form action="<?php echo $editFormAction; ?>" method="POST" enctype="multipart/form-data" name="form2" role="form">
                <div class="form-group">
                  <label class="control-label" for="image_1">Select Car Model Image</label>
                  <input type="file" name="image_1" id="image_1" />
                </div>
                <div class="form-group">
                  <button type="submit" id="upload" class="btn btn-info">Upload</button>
                  <input type="hidden" name="car_model_id" value="<?php echo $row_Recordset2['car_model_id'] ?>"/>
                  <input type="hidden" name="MM_photo" value="form2">
                </div>
              </form>
            </div>
          </div>
          <?php } ?>
          <form method="POST" name="form1" id="form1" role="form" action="<?php echo $editFormAction; ?>" enctype="multipart/form-data">
            <div class="form-group">
              <label class="control-label" for="car_id">Select Car</label>
              <select name="car_id" class="form-control" required>
                <option value="" selected disabled>Select Car</option>
                <?php do{ ?>
                <option value="<?php echo $row_Recordset3['car_id'] ?>" <?php if($flag && $row_Recordset3['car_id'] == $row_Recordset2['car_id']) echo "selected" ?>><?php echo $row_Recordset3['car_name'] ?></option>
                <?php }while($row_Recordset3 = mysqli_fetch_assoc($Recordset3)) ?>
              </select>
            </div>
            <div class="form-group">
              <label class="control-label" for="model_name">Car Model Name</label>
              <input type="text" name="model_name" class="form-control" value="<?php if($flag) echo $row_Recordset2['model_name']; ?>" required>
            </div>
            <?php if(!$flag) { ?>
            <div class="form-group">
              <label class="control-label">Add Car Model Image</label>
              <input type="file" name="image_1" class="form-control" required>
            </div>
            <?php } ?>
            <div class="form-group">
              <button type="submit" id="save" class="btn btn-info">Save</button>
              <?php
              if ( $flag ) {
                  ?>
              <input type="hidden" name="MM_update" value="form1">
              <input type="hidden" name="car_model_id" value="<?php echo $car_model_id; ?>">
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
