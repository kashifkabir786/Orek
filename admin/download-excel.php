<?php require_once('../Connections/orek.php'); ?>
<?php require_once('session.php'); ?>
<?php
$type = "";

    if (isset($_GET['type'])) {
        $type = $_GET['type'];

        switch ($type) {
        case "item":
        $query_Recordset2 = "SELECT * FROM `item`";
        $Recordset2 = mysqli_query( $orek, $query_Recordset2 )or die( mysqli_error( $orek ) );
        //$row_Recordset2 = mysqli_fetch_assoc( $Recordset2 );
        $totalRows_Recordset2 = mysqli_num_rows( $Recordset2 );
        break;

        case "brand":
        $query_Recordset2 = "SELECT * FROM `brand`";
        $Recordset2 = mysqli_query( $orek, $query_Recordset2 )or die( mysqli_error( $orek ) );
        //$row_Recordset2 = mysqli_fetch_assoc( $Recordset2 );
        $totalRows_Recordset2 = mysqli_num_rows( $Recordset2 );
        break;

        case "heading":
        $query_Recordset2 = "SELECT * FROM `heading`";
        $Recordset2 = mysqli_query( $orek, $query_Recordset2 )or die( mysqli_error( $orek ) );
        //$row_Recordset2 = mysqli_fetch_assoc( $Recordset2 );
        $totalRows_Recordset2 = mysqli_num_rows( $Recordset2 );
        break;
        
        case "sub-heading":
        $query_Recordset2 = "SELECT * FROM `sub_heading`";
        $Recordset2 = mysqli_query( $orek, $query_Recordset2 )or die( mysqli_error( $orek ) );
        //$row_Recordset2 = mysqli_fetch_assoc( $Recordset2 );
        $totalRows_Recordset2 = mysqli_num_rows( $Recordset2 );
        break;

        case "category":
        $query_Recordset2 = "SELECT * FROM `category`";
        $Recordset2 = mysqli_query( $orek, $query_Recordset2 )or die( mysqli_error( $orek ) );
        //$row_Recordset2 = mysqli_fetch_assoc( $Recordset2 );
        $totalRows_Recordset2 = mysqli_num_rows( $Recordset2 );
        break;
        
        case "payment":
        $query_Recordset2 = "SELECT * FROM `payment`";
        $Recordset2 = mysqli_query( $orek, $query_Recordset2 )or die( mysqli_error( $orek ) );
        //$row_Recordset2 = mysqli_fetch_assoc( $Recordset2 );
        $totalRows_Recordset2 = mysqli_num_rows( $Recordset2 );
        break;

        default:
        echo "something wrong";
        exit;
    }

    } else {
        echo "something wrong";
    }

header( "Content-Type: application/xls" );
header( "Content-Disposition: attachment; filename=$type-master.xls" );
header( "Pragma: no-cache" );
header( "Expires: 0" );

$sep = "\t"; //tabbed character
//start of printing column names as names of MySQL fields
while ( $property = mysqli_fetch_field( $Recordset2 ) ) {
    echo $property->name . "\t";
}
print( "\n" );
while ( $row = mysqli_fetch_row( $Recordset2 ) ) {
    $schema_insert = "";
    for ( $j = 0; $j < mysqli_num_fields( $Recordset2 ); $j++ ) {
        if ( !isset( $row[ $j ] ) )
            $schema_insert .= "NULL" . $sep;
        elseif ( $row[ $j ] != "" )
            $schema_insert .= "$row[$j]" . $sep;
        else
            $schema_insert .= "" . $sep;
    }
    $schema_insert = str_replace( $sep . "$", "", $schema_insert );
    $schema_insert = preg_replace( "/\r\n|\n\r|\n|\r/", " ", $schema_insert );
    $schema_insert .= "\t";
    print( trim( $schema_insert ) );
    print "\n";
}
?>