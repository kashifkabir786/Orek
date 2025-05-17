<?php
//Start session
$flag = false;
session_start();
$loginflag = 0;
//Check whether the session variable SESS_MEMBER_ID is present or not
if ( !isset( $_SESSION[ 'email' ] ) || ( trim( $_SESSION[ 'email' ] ) == '' ) ) {
  $loginflag = 1;
}
if ( $loginflag == 1 ) {
  header( "location: index.php" );
}
$today = date( 'Y-m-d' );

  $query_Recordset1 = "SELECT * FROM user WHERE email = '{$_SESSION['email']}'";
  $Recordset1 = mysqli_query( $orek, $query_Recordset1 )or die( mysqli_error( $orek ) );
  $row_Recordset1 = mysqli_fetch_assoc( $Recordset1 );
  $totalRows_Recordset1 = mysqli_num_rows( $Recordset1 );

$flag = true;


function dateformat( $datevar ) {
  $date = str_replace( '/', '-', $datevar );
  $changed_format = date( 'Y-m-d', strtotime( $date ) );
  return ( $changed_format );
}

function dateconvertDFY( $dateymd, $timezone ) {
  $serverTime = strtotime( $dateymd );
  if ( $timezone == "UTC" )
    $istTime = $serverTime + ( 5 * 3600 ) + ( 30 * 60 );
  else
    $istTime = $serverTime;
  $converted = date( "d F Y", $istTime );
  return ( $converted );
}

function dateconvertYMD( $dateymd, $timezone ) {
  $serverTime = strtotime( $dateymd );
  if ( $timezone == "UTC" )
    $istTime = $serverTime + ( 5 * 3600 ) + ( 30 * 60 );
  else
    $istTime = $serverTime;
  $converted = date( "Y-m-d", $istTime );
  return ( $converted );
}

function dateconvertYMDHIS( $dateymd, $timezone ) {
  $serverTime = strtotime( $dateymd );
  if ( $timezone == "UTC" )
    $istTime = $serverTime + ( 5 * 3600 ) + ( 30 * 60 );
  else
    $istTime = $serverTime;
  $converted = date( "Y-m-d H:i:s", $istTime );
  return ( $converted );
}

function dateconvertdMHia( $dateymd, $timezone ) {
  $serverTime = strtotime( $dateymd );
  if ( $timezone == "UTC" )
    $istTime = $serverTime + ( 5 * 3600 ) + ( 30 * 60 );
  else
    $istTime = $serverTime;
  $converted = date( "d-M h:i a", $istTime );
  return ( $converted );
}

function dateconvertdMY( $dateymd, $timezone ) {
  $serverTime = strtotime( $dateymd );
  if ( $timezone == "UTC" )
    $istTime = $serverTime + ( 5 * 3600 ) + ( 30 * 60 );
  else
    $istTime = $serverTime;
  $converted = date( "d M Y", $istTime );
  return ( $converted );
}

function dateconvertHI( $dateymd, $timezone ) {
  $serverTime = strtotime( $dateymd );
  if ( $timezone == "UTC" )
    $istTime = $serverTime + ( 5 * 3600 ) + ( 30 * 60 );
  else
    $istTime = $serverTime;
  $converted = date( "h:i", $istTime );
  return ( $converted );
}

function get_tiny_url( $url ) {
  $ch = curl_init();
  $timeout = 5;
  curl_setopt( $ch, CURLOPT_URL, 'http://tinyurl.com/api-create.php?url=' . $url );
  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
  curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
  $data = curl_exec( $ch );
  curl_close( $ch );
  return $data;
}
?>