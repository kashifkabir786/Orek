<?php
require_once('Connections/orek.php');
session_start();

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => 'Invalid request',
    'subtotal' => 0,
    'total' => 0,
    'cart_count' => 0,
    'show_gift_banner' => false
];

// ... existing validation code ...

try {
    $cart_id = (int)$_POST['cart_id'];
    $item_id = (int)$_POST['item_id'];
    
    // ... existing user verification code ...
    
    // Delete the cart item
    $delete_query = "DELETE FROM cart_item WHERE cart_id = $cart_id AND item_id = $item_id";
    $delete_result = mysqli_query($orek, $delete_query);
    
    if (!$delete_result) {
        throw new Exception('Failed to remove item: ' . mysqli_error($orek));
    }
    
    // Calculate new totals and check gift eligibility
    $total_query = "SELECT 
        SUM(CASE 
            WHEN i.listing_status != 'Gift' 
            THEN ROUND(i.price * (1 - i.discount/100) * ci.qnty)
            ELSE 0 
        END) as subtotal,
        COUNT(*) as cart_count,
        SUM(CASE WHEN i.listing_status = 'Gift' THEN 1 ELSE 0 END) as gift_count
        FROM cart_item ci 
        JOIN item i ON ci.item_id = i.item_id 
        WHERE ci.cart_id = $cart_id";
    
    $total_result = mysqli_query($orek, $total_query);
    
    if (!$total_result) {
        throw new Exception('Failed to calculate total: ' . mysqli_error($orek));
    }
    
    $total_row = mysqli_fetch_assoc($total_result);
    $subtotal = $total_row['subtotal'] ?? 0;
    $cart_count = $total_row['cart_count'] ?? 0;
    $has_gift = ($total_row['gift_count'] ?? 0) > 0;

    // Remove gift if total falls below threshold
    if ($subtotal < 999 && $has_gift) {
        $remove_gift_query = "DELETE FROM cart_item 
            WHERE cart_id = $cart_id 
            AND item_id IN (
                SELECT item_id FROM item 
                WHERE listing_status = 'Gift'
            )";
        mysqli_query($orek, $remove_gift_query);
        
        // Update cart count after gift removal
        $cart_count = $cart_count - $total_row['gift_count'];
    }
    
    // Calculate shipping and final total
    $shipping = $subtotal >= 1500 ? 0 : ($subtotal > 0 ? 50 : 0);
    $total = $subtotal + $shipping;
    
    // Prepare successful response
    $response = [
        'success' => true,
        'message' => 'Item removed successfully',
        'subtotal' => round($subtotal),
        'shipping' => $shipping,
        'total' => round($total),
        'cart_count' => $cart_count,
        'show_gift_banner' => ($subtotal >= 999),
        'gift_removed' => ($subtotal < 999 && $has_gift)
    ];
    
} catch (Exception $e) {
    error_log("Remove item error: " . $e->getMessage());
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>