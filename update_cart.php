<?php
require_once('Connections/orek.php');
session_start();

// Set content type header for JSON response
header('Content-Type: application/json');

// Default response
$response = [
    'success' => false,
    'message' => 'Invalid request',
    'subtotal' => 0,
    'item_total' => 0,
    'total' => 0
];

// Validate inputs
if (!isset($_POST['cart_id']) || !isset($_POST['item_id']) || !isset($_POST['quantity'])) {
    echo json_encode($response);
    exit;
}

// Only proceed if user is logged in
if (!isset($_SESSION['email'])) {
    $response['message'] = 'Please login first';
    echo json_encode($response);
    exit;
}

try {
    // Get and sanitize inputs
    $cart_id = (int)$_POST['cart_id'];
    $item_id = (int)$_POST['item_id'];
    $quantity = (int)$_POST['quantity'];
    
    if ($cart_id <= 0 || $item_id <= 0 || $quantity <= 0) {
        throw new Exception('Invalid input parameters');
    }
    
    // Verify the cart belongs to the logged-in user
    $email = mysqli_real_escape_string($orek, $_SESSION['email']);
    $user_check_query = "SELECT c.cart_id FROM cart c 
                         JOIN user u ON c.user_id = u.user_id 
                         WHERE u.email = '$email' AND c.cart_id = $cart_id AND c.status = 'Pending'";
    
    $user_check_result = mysqli_query($orek, $user_check_query);
    
    if (!$user_check_result || mysqli_num_rows($user_check_result) == 0) {
        throw new Exception('Unauthorized access');
    }
    
    // Update the cart item
    $update_query = "UPDATE cart_item SET qnty = $quantity WHERE cart_id = $cart_id AND item_id = $item_id";
    $update_result = mysqli_query($orek, $update_query);
    
    if (!$update_result) {
        throw new Exception('Failed to update cart: ' . mysqli_error($orek));
    }
    
    // Get the updated item total
    $item_query = "SELECT ROUND(i.price * (1 - i.discount/100) * ci.qnty) as amount
                   FROM cart_item ci 
                   JOIN item i ON ci.item_id = i.item_id 
                   WHERE ci.cart_id = $cart_id AND ci.item_id = $item_id";
    
    $item_result = mysqli_query($orek, $item_query);
    
    if (!$item_result || mysqli_num_rows($item_result) == 0) {
        throw new Exception('Item not found');
    }
    
    $item_row = mysqli_fetch_assoc($item_result);
    $item_total = $item_row['amount'];
    
    // Get updated cart total
    $total_query = "SELECT SUM(ROUND(i.price * (1 - i.discount/100) * ci.qnty)) as total
                    FROM cart_item ci 
                    JOIN item i ON ci.item_id = i.item_id 
                    WHERE ci.cart_id = $cart_id";
    
    $total_result = mysqli_query($orek, $total_query);
    
    if (!$total_result) {
        throw new Exception('Failed to calculate total: ' . mysqli_error($orek));
    }
    
    $total_row = mysqli_fetch_assoc($total_result);
    $subtotal = $total_row['total'] ?? 0;
    
    // Calculate shipping and final total
    $shipping = $subtotal >= 1500 ? 0 : ($subtotal > 0 ? 50 : 0);
    $total = $subtotal + $shipping;
    
    // Prepare successful response
    $response = [
        'success' => true,
        'message' => 'Cart updated successfully',
        'item_total' => round($item_total),
        'subtotal' => round($subtotal),
        'shipping' => $shipping,
        'total' => round($total)
    ];
    
} catch (Exception $e) {
    // Log error
    error_log("Cart update error: " . $e->getMessage());
    $response['message'] = $e->getMessage();
}

// Send response
echo json_encode($response);
?> 