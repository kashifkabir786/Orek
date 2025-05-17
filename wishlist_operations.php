<?php
require_once('Connections/orek.php');
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['email'])) {
    echo json_encode(['status' => 'ERROR', 'message' => 'User not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'add' && isset($_POST['item_id'])) {
        $item_id = (int)$_POST['item_id'];
        
        // Get user_id from email
        $query_User = "SELECT user_id FROM user WHERE email = ?";
        $stmt = mysqli_prepare($orek, $query_User);
        mysqli_stmt_bind_param($stmt, "s", $_SESSION['email']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        
        if ($user) {
            // Check if item already exists in wishlist
            $check_query = "SELECT * FROM wishlist WHERE user_id = ? AND item_id = ?";
            $stmt = mysqli_prepare($orek, $check_query);
            mysqli_stmt_bind_param($stmt, "ii", $user['user_id'], $item_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if (mysqli_num_rows($result) > 0) {
                echo json_encode(['status' => 'EXISTS', 'message' => 'Item already in wishlist']);
            } else {
                // Add to wishlist
                $insert_query = "INSERT INTO wishlist (user_id, item_id) VALUES (?, ?)";
                $stmt = mysqli_prepare($orek, $insert_query);
                mysqli_stmt_bind_param($stmt, "ii", $user['user_id'], $item_id);
                
                if (mysqli_stmt_execute($stmt)) {
                    // Get updated count
                    $count_query = "SELECT COUNT(*) as count FROM wishlist WHERE user_id = ?";
                    $stmt = mysqli_prepare($orek, $count_query);
                    mysqli_stmt_bind_param($stmt, "i", $user['user_id']);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $count = mysqli_fetch_assoc($result)['count'];
                    
                    echo json_encode([
                        'status' => 'ADDED',
                        'message' => 'Item added to wishlist successfully',
                        'count' => $count
                    ]);
                } else {
                    echo json_encode(['status' => 'ERROR', 'message' => 'Database error']);
                }
            }
        } else {
            echo json_encode(['status' => 'ERROR', 'message' => 'User not found']);
        }
    }
}
?>