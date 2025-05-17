<?php
require_once('Connections/orek.php');

if(isset($_POST['shipping_id'])) {
    $shipping_id = $_POST['shipping_id'];
    
    $query = "SELECT * FROM user_shipping WHERE shipping_id = ?";
    $stmt = mysqli_prepare($orek, $query);
    mysqli_stmt_bind_param($stmt, "i", $shipping_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $address = mysqli_fetch_assoc($result);
    
    echo json_encode($address);
}
?>