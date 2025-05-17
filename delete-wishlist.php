<?php
require_once('Connections/orek.php');
session_start();

if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

if (isset($_POST['wishlist_id'])) {
    $wishlist_id = $_POST['wishlist_id'];
    
    // Get user_id for security check
    $query_user = "SELECT user_id FROM user WHERE email = '{$_SESSION['email']}'";
    $result_user = mysqli_query($orek, $query_user);
    $user = mysqli_fetch_assoc($result_user);
    
    // Delete wishlist item (with user check for security)
    $query = "DELETE FROM wishlist WHERE wishlist_id = {$wishlist_id} AND user_id = {$user['user_id']}";
    if (mysqli_query($orek, $query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>