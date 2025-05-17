<?php require_once('../Connections/orek.php'); ?>
<?php require_once('session.php'); ?>
<?php
if ( isset( $_GET[ 'category_id' ] ) ) {

     $deleteSQL = sprintf( "DELETE FROM `category_size_chart` WHERE `size_name` = %s AND `category_id` = %s",
        GetSQLValueString( $_GET[ 'size_name' ], "text" ),
        GetSQLValueString( $_GET[ 'category_id' ], "int" ) );
    $Result1 = mysqli_query( $orek, $deleteSQL )or die( mysqli_error( $orek ) );

    $deleteGoTo = "add-category.php?category_id=" . $Result1['category_id'];
    if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
        $deleteGoTo .= ( strpos( $deleteGoTo, '?' ) ) ? "&" : "?";
        $deleteGoTo .= $_SERVER[ 'QUERY_STRING' ];
    }
    header( sprintf( "Location: %s", $deleteGoTo ) );
}
?>