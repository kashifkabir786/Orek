<?php require_once('Connections/orek.php'); ?>
<?php
//Start session
session_start();

$login_fail = 'Email or Password not found';

//Array to store validation errors
$errmsg_arr = array();

//Validation error flag
$errflag = false;

//Function to sanitize values received from the form. Prevents SQL injection
function clean( $str ) {
  $str = @trim( $str );
  global $orek;
  //if(get_magic_quotes_gpc()) {
  //			$str = stripslashes($str);
  //		}
  return mysqli_real_escape_string( $orek, $str );
}

//Sanitize the POST values
$login = clean( $_POST[ 'email' ] );
$password = clean( $_POST[ 'password' ] );

//Input Validations
if ( $login == '' ) {
  $errmsg_arr[] = 'Wrong Input';
  $errflag = true;
}
if ( $password == '' ) {
  $errmsg_arr[] = 'Password missing';
  $errflag = true;
}

//If there are input validations, redirect back to the login form
if ( $errflag ) {
  $_SESSION[ 'ERRMSG_ARR' ] = $errmsg_arr;
  session_write_close();
  header( "location: login.php" );
  exit();
}

//Create query
$qry = "SELECT email, password, phone_no FROM `user` WHERE (email='$login' OR phone_no='$login') AND status = 'Verified'";
$result = mysqli_query( $orek, $qry );
$row = mysqli_fetch_assoc( $result );
//Check whether the query was successful or not
if ( $result ) {
  if ( mysqli_num_rows( $result ) == 1 ) {
    $hash = $row[ 'password' ];
    if ( password_verify( $password, $hash ) ) {
      //Login Successful
      session_regenerate_id();
      $_SESSION[ 'email' ] = $row[ 'email' ];
      session_write_close();
      header( "location: index.php" );
      exit();
    } else {
      //Login failed
      $_SESSION[ 'login_fail' ] = $login_fail;
      session_write_close();
      header( "location: login.php" );
      exit();
    }
  } else {
    //Login failed
    $_SESSION[ 'login_fail' ] = $login_fail;
    session_write_close();
    header( "location: login.php" );
    exit();
  }
} else {
  die( "Query failed" );
}
?>