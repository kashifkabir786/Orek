<?php require_once('../Connections/car_spare.php'); ?>
<?php
if ( isset( $_POST[ 'MM_download' ] ) && $_POST[ 'MM_download' ] == 'form1' ) {
	
    $query_Recordset3 = "SELECT fname,lname,phone_no,email FROM user";
    $Recordset3 = mysqli_query( $car_spare, $query_Recordset3 );
    $heading = "First name" . "\t" . "Last name" . "\t" . "Phone no" . "\t" . "Email" . "\t";
    $setData = '';
    while ( $totalRows_Recordset3 = mysqli_fetch_row( $Recordset3 ) ) {
        $rowData = '';
        foreach ( $totalRows_Recordset3 as $value ) {
            $value = '"' . $value . '"' . "\t";
            $rowData .= $value;
        }
        $setData .= trim( $rowData ) . "\n";
    }

    header( "Content-type: application/octet-stream" );
    header( "Content-Disposition: attachment; filename=users.xls" );
    header( "Pragma: no-cache" );
    header( "Expires: 0" );

    echo ucwords( $heading ) . "\n" . $setData . "\n";
}
?>