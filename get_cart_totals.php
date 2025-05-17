<?php
require_once('Connections/orek.php');
session_start();

// Set content type header for JSON response
header('Content-Type: application/json');

// Default response
$response = [
    'success' => false,
    'subtotal' => 0,
    'total' => 0
];

// Only proceed if user is logged in
if(isset($_SESSION['email'])) {
    try {
        // Clear previous results to prevent "Commands out of sync" errors
        while (mysqli_next_result($orek)) {;}
        
        // Set a query timeout to prevent hanging
        mysqli_query($orek, "SET SESSION MAX_EXECUTION_TIME=2000");
        
        // Get cart items with updated totals
        $stmt = mysqli_prepare($orek, "CALL GetCartItems(?)");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . mysqli_error($orek));
        }
        
        mysqli_stmt_bind_param($stmt, "s", $_SESSION['email']);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Execute failed: " . mysqli_stmt_error($stmt));
        }
        
        $cart_items = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
        
        // Clear the result set
        mysqli_next_result($orek);
        
        // Calculate new subtotal
        $subtotal = 0;
        if (mysqli_num_rows($cart_items) > 0) {
            while ($item = mysqli_fetch_assoc($cart_items)) {
                $subtotal += $item['amount'];
            }
        }
        
        // Calculate shipping cost
        $shipping = $subtotal >= 1500 ? 0 : ($subtotal > 0 ? 50 : 0);
        
        // Calculate total
        $total = $subtotal + $shipping;
        
        // Set response data
        $response['success'] = true;
        $response['subtotal'] = round($subtotal);
        $response['shipping'] = $shipping;
        $response['total'] = round($total);
        
    } catch (Exception $e) {
        // Log error instead of displaying to user
        error_log("Cart totals error: " . $e->getMessage());
        $response['message'] = "Failed to update cart totals";
    }
}

// Return JSON response
echo json_encode($response);
?>