<?php require_once('../Connections/orek.php'); ?>
<?php require_once('session.php'); ?>
<?php
if ( isset( $_GET[ 'offer_id' ] ) ) {

    $photoSQL = sprintf( "SELECT `image_1` FROM `offer` WHERE `offer_id` = %s", GetSQLValueString( $_GET[ 'offer_id' ], "int" ) );
    $photo = mysqli_query( $orek, $photoSQL )or die( mysqli_error( $orek ) );
    $row_photo = mysqli_fetch_assoc( $photo );

    $deleteSQL = sprintf( "DELETE FROM `offer` WHERE offer_id = %s",
        GetSQLValueString( $_GET[ 'offer_id' ], "int" ) );
    $Result1 = mysqli_query( $orek, $deleteSQL )or die( mysqli_error( $orek ) );

    unlink( "../assets/img/offer/" . $row_photo[ 'image_1' ] );

    $deleteGoTo = "offer.php";
    if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
        $deleteGoTo .= ( strpos( $deleteGoTo, '?' ) ) ? "&" : "?";
        $deleteGoTo .= $_SERVER[ 'QUERY_STRING' ];
    }
    header( sprintf( "Location: %s", $deleteGoTo ) );
}
?>