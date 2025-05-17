<?php require_once('../Connections/orek.php'); ?>
<?php require_once('session.php'); ?>
<?php
if ( isset( $_GET[ 'grn_id' ] ) ) {

    $deleteSQL = sprintf( "DELETE FROM `grn` WHERE grn_id = %s",
        GetSQLValueString( $_GET[ 'grn_id' ], "int" ) );
    $Result1 = mysqli_query( $orek, $deleteSQL )or die( mysqli_error( $orek ) );

    $deleteSQL = sprintf( "DELETE FROM `grn_item` WHERE grn_id = %s",
        GetSQLValueString( $_GET[ 'grn_id' ], "int" ) );
    $Result1 = mysqli_query( $orek, $deleteSQL )or die( mysqli_error( $orek ) );

    $deleteGoTo = "grn.php";
    if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
        $deleteGoTo .= ( strpos( $deleteGoTo, '?' ) ) ? "&" : "?";
        $deleteGoTo .= $_SERVER[ 'QUERY_STRING' ];
    }
    header( sprintf( "Location: %s", $deleteGoTo ) );
}
?>