<?php require_once('../Connections/car_spare.php'); ?>
<?php require_once('session.php'); ?>
<?php
if ( isset( $_GET[ 'heading_id' ] ) ) {
	
    $photoSQL = sprintf( "SELECT `image_1` FROM `heading` WHERE `heading_id` = %s", GetSQLValueString( $_GET[ 'heading_id' ], "int" ) );
    $photo = mysqli_query( $car_spare, $photoSQL )or die( mysqli_error( $car_spare ) );
    $row_photo = mysqli_fetch_assoc( $photo );

    $deleteSQL = sprintf( "DELETE FROM `heading` WHERE heading_id = %s",
        GetSQLValueString( $_GET[ 'heading_id' ], "int" ) );
    $Result1 = mysqli_query( $car_spare, $deleteSQL )or die( mysqli_error( $car_spare ) );
	
	unlink("../assets/img/heading/" . $row_photo['image_1']);

    $deleteGoTo = "heading.php";
    if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
        $deleteGoTo .= ( strpos( $deleteGoTo, '?' ) ) ? "&" : "?";
        $deleteGoTo .= $_SERVER[ 'QUERY_STRING' ];
    }
    header( sprintf( "Location: %s", $deleteGoTo ) );
}
?>