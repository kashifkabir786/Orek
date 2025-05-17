<?php require_once('../Connections/car_spare.php'); ?>
<?php
if ( isset( $_POST[ 'MM_download' ] ) && $_POST[ 'MM_download' ] == 'form1' ) {

    $query_Recordset3 = "SELECT A.cart_id, E.payment_id, DATE_FORMAT(`date`, '%Y-%m-%d'), A.user_id, B.fname, C.item_id, D.item_name, E.address, C.qnty, E.pin_code, D.price, NULL AS sale_price, D.discount, B.phone_no, C.amount FROM cart AS A INNER JOIN user AS B ON A.user_id = B.user_id INNER JOIN cart_item AS C ON A.cart_id = C.cart_id INNER JOIN item AS D ON C. item_id = D.item_id INNER JOIN payment AS F ON A.cart_id = F.cart_id INNER JOIN shipping AS E ON F.payment_id = E.payment_id WHERE A.date BETWEEN '{$_POST['start_date']}' AND '{$_POST['end_date']}'";
    $Recordset3 = mysqli_query( $car_spare, $query_Recordset3 );
    $heading = "Order ID" . "\t" . "Payment ID" . "\t" . "Order Date" . "\t" . "Customer ID" . "\t" . "Custermer Name" . "\t" . "Product ID" . "\t" . "Product Name" . "\t" . "Address" . "\t" . "Quantity" . "\t" . "Pin Code" . "\t" . "MRP" . "\t" . "Sale Price" . "\t" . "Disc%" . "\t" . "Phone No" . "\t" . "Amount" . "\t";
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
    header( "Content-Disposition: attachment; filename=orders.xls" );
    header( "Pragma: no-cache" );
    header( "Expires: 0" );

    echo ucwords( $heading ) . "\n" . $setData . "\n";
}
?>