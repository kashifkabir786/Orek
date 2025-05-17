<?php
require_once('Connections/orek.php');
session_start();

header('Content-Type: application/json');

if (isset($_SESSION['email'])) {
    // Get user_id from email
    $query_User = "SELECT user_id FROM user WHERE email = ?";
    $stmt = mysqli_prepare($orek, $query_User);
    mysqli_stmt_bind_param($stmt, "s", $_SESSION['email']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    
    if ($user) {
        // Get wishlist count
        $query_Wishlist = "SELECT COUNT(*) as wishlist_count FROM wishlist WHERE user_id = ?";
        $stmt = mysqli_prepare($orek, $query_Wishlist);
        mysqli_stmt_bind_param($stmt, "i", $user['user_id']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        
        echo json_encode(['count' => $row['wishlist_count']]);
    } else {
        echo json_encode(['count' => 0]);
    }
} else {
    echo json_encode(['count' => 0]);
}
?>