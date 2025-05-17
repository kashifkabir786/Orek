<?php
require_once('Connections/orek.php');
header('Content-Type: application/json'); // Set JSON header

if(isset($_POST['shipping_id'])) {
    $updateSQL = sprintf("UPDATE user_shipping SET 
        recipient_name=%s, 
        address_name=%s, 
        address=%s, 
        city=%s, 
        state=%s, 
        pin_code=%s, 
        phone=%s 
        WHERE shipping_id=%s",
        GetSQLValueString($_POST['recipient_name'], "text"),
        GetSQLValueString($_POST['address_name'], "text"),
        GetSQLValueString($_POST['address'], "text"),
        GetSQLValueString($_POST['city'], "text"),
        GetSQLValueString($_POST['state'], "text"),
        GetSQLValueString($_POST['pin_code'], "text"),
        GetSQLValueString($_POST['phone'], "text"),
        GetSQLValueString($_POST['shipping_id'], "int"));

    $Result = mysqli_query($orek, $updateSQL);
    
     if($Result) {
        header('Location: my-account.php?success=Address updated successfully');
        exit();
    } else {
        header('Location: my-account.php?error=' . urlencode(mysqli_error($orek)));
        exit();
    }
} else {
    header('Location: my-account.php?error=No shipping ID provided');
    exit();
}
?>