<?php
require_once('Connections/orek.php');
require_once('session.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_id'])) {
    $cart_id = mysqli_real_escape_string($orek, $_POST['cart_id']);
    $response = array();

    // Check if the order is within 1 hour
    $check_time = mysqli_query($orek, "SELECT TIMESTAMPDIFF(SECOND, created_at, NOW()) as time_diff 
        FROM cart WHERE cart_id = '$cart_id'");
    $time_result = mysqli_fetch_assoc($check_time);

    if ($time_result['time_diff'] <= 3600) {
        mysqli_begin_transaction($orek);

        try {
            // Delete from payment table
            $delete_payment = mysqli_query($orek, "DELETE FROM payment WHERE cart_id = '$cart_id'");
            
            // Delete from cart_item table
            $delete_cart_items = mysqli_query($orek, "DELETE FROM cart_item WHERE cart_id = '$cart_id'");
            
            // Delete from cart table
            $delete_cart = mysqli_query($orek, "DELETE FROM cart WHERE cart_id = '$cart_id'");

            if ($delete_payment && $delete_cart_items && $delete_cart) {
                mysqli_commit($orek);
                $response['success'] = true;
                $response['message'] = 'Order cancelled successfully';
                $response['redirect'] = 'my-account.php';  // This is already correct
            } else {
                throw new Exception("Error cancelling order");
            }
        } catch (Exception $e) {
            mysqli_rollback($orek);
            $response['success'] = false;
            $response['message'] = 'Failed to cancel order: ' . $e->getMessage();
        }
    } else {
        $response['success'] = false;
        $response['message'] = 'Cannot cancel order after 1 hour of placing it';
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>