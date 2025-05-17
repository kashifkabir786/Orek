<?php
require_once('Connections/orek.php');
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

try {
    $query = "SELECT COUNT(*) as count 
              FROM cart_item ci 
              JOIN cart c ON ci.cart_id = c.cart_id 
              WHERE c.user_id = (SELECT user_id FROM user WHERE email = ?) 
              AND c.status = 'Pending'";
    
    $stmt = mysqli_prepare($orek, $query);
    mysqli_stmt_bind_param($stmt, "s", $_SESSION['email']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    
    echo json_encode([
        'success' => true,
        'count' => $row['count']
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>