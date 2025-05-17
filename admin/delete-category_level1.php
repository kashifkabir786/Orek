<?php require_once('../Connections/orek.php'); ?>
<?php require_once('session.php'); ?>
<?php
if ( isset( $_GET[ 'category_level1_id' ] ) ) {
	
    $photoSQL = sprintf( "SELECT `image` FROM `category_level1` WHERE `category_level1_id` = %s", GetSQLValueString( $_GET[ 'category_level1_id' ], "int" ) );
    $photo = mysqli_query( $orek, $photoSQL )or die( mysqli_error( $orek ) );
    $row_photo = mysqli_fetch_assoc( $photo );

    $deleteSQL = sprintf( "DELETE FROM `category_level1` WHERE category_level1_id = %s",
        GetSQLValueString( $_GET[ 'category_level1_id' ], "int" ) );
    $Result1 = mysqli_query( $orek, $deleteSQL )or die( mysqli_error( $orek ) );
	
	unlink("../assets/img/category/" . $row_photo['image']);

    $deleteGoTo = "category_level1.php";
    if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
        $deleteGoTo .= ( strpos( $deleteGoTo, '?' ) ) ? "&" : "?";
        $deleteGoTo .= $_SERVER[ 'QUERY_STRING' ];
    }
    header( sprintf( "Location: %s", $deleteGoTo ) );
}
?>