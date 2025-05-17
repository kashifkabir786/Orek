<?php require_once('../Connections/orek.php'); ?>
<?php require_once('session.php'); ?>
<?php
if ( isset( $_GET[ 'item_id' ] ) ) {
	
    $photoSQL = sprintf( "SELECT * FROM `item` WHERE `item_id` = %s", GetSQLValueString( $_GET[ 'item_id' ], "int" ) );
    $photo = mysqli_query( $orek, $photoSQL )or die( mysqli_error( $orek ) );
    $row_photo = mysqli_fetch_assoc( $photo );

    $deleteSQL = sprintf( "DELETE FROM `item` WHERE item_id = %s",
        GetSQLValueString( $_GET[ 'item_id' ], "int" ) );
    $Result1 = mysqli_query( $orek, $deleteSQL )or die( mysqli_error( $orek ) );
	
	unlink("../assets/img/item/" . $row_photo['image_1']);
	unlink("../assets/img/item/" . $row_photo['image_2']);
	unlink("../assets/img/item/" . $row_photo['image_3']);
	unlink("../assets/img/item/" . $row_photo['video']);

    $deleteGoTo = "item.php";
    if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
        $deleteGoTo .= ( strpos( $deleteGoTo, '?' ) ) ? "&" : "?";
        $deleteGoTo .= $_SERVER[ 'QUERY_STRING' ];
    }
    header( sprintf( "Location: %s", $deleteGoTo ) );
}
?>