<?php require_once('../Connections/car_spare.php'); ?>
<?php require_once('session.php'); ?>
<?php
$flag = false;
if ( isset( $_GET[ 'uname' ] ) ) {
  $uname = $_GET[ 'uname' ];

  $query_Recordset2 = "SELECT * FROM admin WHERE uname='$uname'";
  $Recordset2 = mysqli_query( $car_spare, $query_Recordset2 )or die( mysqli_error( $car_spare ) );
  $row_Recordset2 = mysqli_fetch_assoc( $Recordset2 );
  $totalRows_Recordset2 = mysqli_num_rows( $Recordset2 );
  $flag = true;
}

if ( isset( $_GET[ 'uname' ] ) && !empty( $_GET[ 'uname' ] ) ) {
  $uname = $_GET[ 'uname' ];

  $query_Recordset2 = "SELECT * FROM admin WHERE uname = '$uname'";
  $Recordset2 = mysqli_query( $car_spare, $query_Recordset2 )or die( mysqli_error( $car_spare ) );
  $row_Recordset2 = mysqli_fetch_assoc( $Recordset2 );
  $totalRows_Recordset2 = mysqli_num_rows( $Recordset2 );

  $flag = true;
}

$editFormAction = $_SERVER[ 'PHP_SELF' ];
if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
  $editFormAction .= "?" . htmlentities( $_SERVER[ 'QUERY_STRING' ] );
}
$erruname = $erremail = $errpassword = "";
if ( ( isset( $_POST[ "MM_insert" ] ) ) && ( $_POST[ "MM_insert" ] == "form1" ) ) {

  //validate username
  $query_Recordset2 = "SELECT uname FROM admin WHERE uname = '{$_POST['uname']}'";
  $Recordset2 = mysqli_query( $car_spare, $query_Recordset2 )or die( mysqli_error( $car_spare ) );
  $row_Recordset2 = mysqli_fetch_assoc( $Recordset2 );
  $totalRows_Recordset2 = mysqli_num_rows( $Recordset2 );

  if ( $totalRows_Recordset2 > 0 )
    $erruname = "Username already exists";

  if ( $_POST[ 'password' ] != $_POST[ 'repassword' ] )
    $errpassword = "Passwords Does not Match";

  if ( empty( $erruname ) && empty( $errpassword ) ) {
    $password = $_POST[ 'password' ];
    $hash = password_hash( $password, PASSWORD_DEFAULT );
    $insertSQL = sprintf( "INSERT INTO `admin`(`uname`, `password`, `fname`, `lname`, `role`) VALUES (%s, %s, %s, %s, %s)",
      GetSQLValueString( $_POST[ 'uname' ], "text" ),
      GetSQLValueString( $hash, "text" ),
      GetSQLValueString( $_POST[ 'fname' ], "text" ),
      GetSQLValueString( $_POST[ 'lname' ], "text" ),
      GetSQLValueString( "Staff", "text" ) );
    $Result = mysqli_query( $car_spare, $insertSQL )or die( mysqli_error( $car_spare ) );

    $insertGoTo = "add-staff.php?success=Added&uname=" . $_POST[ 'uname' ];
    if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
      $insertGoTo .= ( strpos( $insertGoTo, '?' ) ) ? "&" : "?";
      $insertGoTo .= $_SERVER[ 'QUERY_STRING' ];
    }
    header( sprintf( "Location: %s", $insertGoTo ) );
  }
}

if ( ( isset( $_POST[ "MM_update" ] ) ) && ( $_POST[ "MM_update" ] == "form1" ) ) {
  $updateSQL = sprintf( "UPDATE `admin` SET `fname` = %s, `lname` = %s WHERE uname = %s",
    GetSQLValueString( $_POST[ 'fname' ], "text" ),
    GetSQLValueString( $_POST[ 'lname' ], "text" ),
    GetSQLValueString( $_POST[ 'uname' ], "int" ) );
  $Result = mysqli_query( $car_spare, $updateSQL )or die( mysqli_error( $car_spare ) );

  $insertGoTo = "add-staff.php?success=Updated";
  if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
    $insertGoTo .= ( strpos( $insertGoTo, '?' ) ) ? "&" : "?";
    $insertGoTo .= $_SERVER[ 'QUERY_STRING' ];
  }
  header( sprintf( "Location: %s", $insertGoTo ) );
}

//update password
if ( ( isset( $_POST[ "MM_password" ] ) ) && ( $_POST[ "MM_password" ] == "form2" ) ) {
  if ( $_POST[ 'password' ] != $_POST[ 'repassword' ] )
    $errpassword = "Passwords Does not Match";

  if ( empty( $errpassword ) ) {
    $password = $_POST[ 'password' ];
    $hash = password_hash( $password, PASSWORD_DEFAULT );

    $updateSQL = sprintf( "UPDATE `admin` SET `password` = '$hash' WHERE `uname` = %s", GetSQLValueString( $_POST[ 'uname' ], "text" ) );
    $Result1 = mysqli_query( $car_spare, $updateSQL )or die( mysqli_error( $car_spare ) );

    $insertGoTo = "add-staff.php?sucess=Password Changed";
    if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
      $insertGoTo .= ( strpos( $insertGoTo, '?' ) ) ? "&" : "?";
      $insertGoTo .= $_SERVER[ 'QUERY_STRING' ];
    }
    header( sprintf( "Location: %s", $insertGoTo ) );
  }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Add Staff</title>
<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css">
<link href="css/style.css" rel="stylesheet" type="text/css">
<link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="left-side height-100">
  <div class="row height-100">
    <div class="col-md-2 bg-green padding-top-bottom height-100 left-menu">
      <div class="row">
        <div class="padding-10 text-center"><img src="images/footer-logo.png" width="60%">
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
      <h3><strong>ADD Staff</strong></h3>
      <hr/>
      <?php
      if ( isset( $_GET[ 'success' ] ) ) {
        echo '<div class="col-md-12">';
        echo '<div class="alert alert-success">Staff ' . $_GET[ 'success' ] . ' Successfully</div>';
        echo '</div>';
      }
      ?>
      <div class="margin-50"> <a href="staff.php" class="btn btn-primary">Back</a></div>
      <div class="row margin-70">
        <div class="col-md-10">
          <form method="POST" name="form1" id="form1" role="form" action="<?php echo $editFormAction; ?>">
            <div class="form-group">
              <label class="control-label" for="fname">Username<span class="text-danger">* <?php echo $erruname ?></span></label>
              <input type="text" name="uname" class="form-control" value="<?php if($flag) echo $row_Recordset2['uname']; ?>" required>
            </div>
            <div class="form-group">
              <label class="control-label" for="fname">First Name</label>
              <input type="text" name="fname" class="form-control" value="<?php if($flag) echo $row_Recordset2['fname']; ?>" required>
            </div>
            <div class="form-group">
              <label class="control-label" for="lname">Last Name</label>
              <input type="text" name="lname" class="form-control" value="<?php if($flag) echo $row_Recordset2['lname']; ?>" required>
            </div>
            <?php if(!$flag){ ?>
            <div class="form-group">
              <label for="password" class="control-label">Password <span class="text-danger">* <?php echo $errpassword ?></span></label>
              <input type="password" class="form-control" name="password" required>
            </div>
            <div class="form-group">
              <label for="repassword" class="control-label">Confirm Password <span class="text-danger">*</span></label>
              <input type="password" class="form-control" name="repassword" required>
            </div>
            <?php } ?>
            <div class="form-group">
              <button type="submit" id="save" class="btn btn-info">Save</button>
              <?php if ( $flag ) { ?>
              <input type="hidden" name="MM_update" value="form1">
              <input type="hidden" name="uname" value="<?php echo $uname; ?>">
              <?php } else { ?>
              <input type="hidden" name="MM_insert" value="form1">
              <?php } ?>
            </div>
          </form>
          <?php if ( $flag ) { ?>
          <hr/>
          <h3>Update Password</h3>
          <form method="POST" name="form2" role="form" action="<?php echo $editFormAction; ?>">
            <div class="form-group">
              <label for="password" class="control-label">Password <span class="text-danger">* <?php echo $errpassword ?></span></label>
              <input type="password" class="form-control" name="password" required>
            </div>
            <div class="form-group">
              <label for="repassword" class="control-label">Confirm Password <span class="text-danger">*</span></label>
              <input type="password" class="form-control" name="repassword" required>
            </div>
            <div class="form-group">
              <button type="submit" id="save" class="btn btn-info">Save</button>
              <input type="hidden" name="MM_password" value="form2">
              <input type="hidden" name="uname" value="<?php echo $uname ?>">
            </div>
          </form>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
<script src="js/jquery-3.2.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</html>
