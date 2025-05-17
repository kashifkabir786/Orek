<?php require_once('../Connections/orek.php'); ?>
<?php require_once('session.php'); ?>
<?php
if ( isset( $_GET[ 'car_model_id' ] ) ) {
	
    $photoSQL = sprintf( "SELECT `image_1` FROM `car_model` WHERE `car_model_id` = %s", GetSQLValueString( $_GET[ 'car_model_id' ], "int" ) );
    $photo = mysqli_query( $orek, $photoSQL )or die( mysqli_error( $orek ) );
    $row_photo = mysqli_fetch_assoc( $photo );

    $deleteSQL = sprintf( "DELETE FROM `car_model` WHERE car_model_id = %s",
        GetSQLValueString( $_GET[ 'car_model_id' ], "int" ) );
    $Result1 = mysqli_query( $orek, $deleteSQL )or die( mysqli_error( $orek ) );
	
	unlink("../assets/img/car_model/" . $row_photo['image_1']);

    $deleteGoTo = "car_model.php";
    if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
        $deleteGoTo .= ( strpos( $deleteGoTo, '?' ) ) ? "&" : "?";
        $deleteGoTo .= $_SERVER[ 'QUERY_STRING' ];
    }
    header( sprintf( "Location: %s", $deleteGoTo ) );
}
?>