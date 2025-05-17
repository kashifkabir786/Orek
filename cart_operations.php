<?php
require_once('Connections/orek.php');
session_start();

header('Content-Type: application/json');

// Debug incoming request
error_log("POST data: " . print_r($_POST, true));

if (!isset($_POST['action'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request: No action specified']);
    exit;
}

try {
    if (!isset($_SESSION['email'])) {
        throw new Exception('Please login first');
    }

    $action = trim($_POST['action']); // Clean the action value

    switch ($action) {
        case 'add_to_cart':
            $item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;
            $qnty = isset($_POST['qnty']) ? intval($_POST['qnty']) : 1;

            if ($item_id <= 0 || $qnty <= 0) {
                throw new Exception('Invalid item or quantity');
            }

            // Clear any previous results
            while (mysqli_next_result($orek)) {;}

            $stmt = mysqli_prepare($orek, "CALL AddToCart(?, ?, ?, @p_success, @p_message)");
            mysqli_stmt_bind_param($stmt, "sii", $_SESSION['email'], $item_id, $qnty);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            break;

        case 'add_gift_to_cart':
            error_log("Processing add_gift_to_cart");
            $item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;
            
            if ($item_id <= 0) {
                throw new Exception('Invalid gift item ID: ' . $item_id);
            }

            // Get current cart ID first
            $cart_query = mysqli_query($orek, "SELECT c.cart_id 
                FROM cart c 
                JOIN user u ON c.user_id = u.user_id 
                WHERE u.email = '{$_SESSION['email']}' 
                AND c.status = 'Pending'
                LIMIT 1");

            if (!$cart_query) {
                throw new Exception('Error finding active cart: ' . mysqli_error($orek));
            }

            $cart_row = mysqli_fetch_assoc($cart_query);
            if (!$cart_row) {
                throw new Exception('No active cart found');
            }

            $cart_id = $cart_row['cart_id'];

            // Check if item is a gift
            $gift_check = mysqli_query($orek, "SELECT * FROM item WHERE item_id = $item_id AND listing_status = 'Gift'");
            if (!$gift_check) {
                throw new Exception('Error checking gift item: ' . mysqli_error($orek));
            }
            if (mysqli_num_rows($gift_check) == 0) {
                throw new Exception('Selected item is not a gift item');
            }

            // Check cart total
            $cart_total_query = mysqli_query($orek, "SELECT 
                    SUM(ROUND(i.price * (1 - i.discount/100)) * ci.qnty) as cart_total 
                FROM cart_item ci 
                JOIN cart c ON ci.cart_id = c.cart_id 
                JOIN user u ON c.user_id = u.user_id 
                JOIN item i ON ci.item_id = i.item_id 
                WHERE c.cart_id = $cart_id 
                AND i.listing_status != 'Gift'");

            $cart_total = mysqli_fetch_assoc($cart_total_query)['cart_total'] ?? 0;
            error_log("Cart total: " . $cart_total);

            if ($cart_total < 999) {
                throw new Exception('Cart total must be ₹999 or more to add a gift');
            }

            // Check existing gift
            $gift_count_query = mysqli_query($orek, "SELECT COUNT(*) as gift_count 
                FROM cart_item ci 
                JOIN item i ON ci.item_id = i.item_id 
                WHERE ci.cart_id = $cart_id 
                AND i.listing_status = 'Gift'");

            $gift_count = mysqli_fetch_assoc($gift_count_query)['gift_count'];
            error_log("Gift count: " . $gift_count);

            if ($gift_count > 0) {
                throw new Exception('You already have a gift in your cart');
            }

            // Add the gift with amount = 0
            $insert_query = "INSERT INTO cart_item (cart_id, item_id, qnty, amount) VALUES ($cart_id, $item_id, 1, 0)";
            if (!mysqli_query($orek, $insert_query)) {
                throw new Exception('Error adding gift to cart: ' . mysqli_error($orek));
            }

            echo json_encode(['success' => true, 'message' => 'Gift added to cart successfully']);
            exit;
            break;

        case 'update':
            $cart_id = isset($_POST['cart_id']) ? intval($_POST['cart_id']) : 0;
            $item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;
            $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;

            if ($cart_id <= 0 || $item_id <= 0 || $quantity <= 0) {
                throw new Exception('Invalid input parameters');
            }

            // Clear any previous results
            while (mysqli_next_result($orek)) {;}

            $stmt = mysqli_prepare($orek, "CALL UpdateCartItem(?, ?, ?, @p_success, @p_message)");
            mysqli_stmt_bind_param($stmt, "iii", $cart_id, $item_id, $quantity);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            break;

        case 'remove':
            $cart_id = isset($_POST['cart_id']) ? intval($_POST['cart_id']) : 0;
            $item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;

            if ($cart_id <= 0 || $item_id <= 0) {
                throw new Exception('Invalid input parameters');
            }

            // Clear any previous results
            while (mysqli_next_result($orek)) {;}

            $stmt = mysqli_prepare($orek, "CALL RemoveCartItem(?, ?, @p_success, @p_message)");
            mysqli_stmt_bind_param($stmt, "ii", $cart_id, $item_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            break;

        default:
            throw new Exception('Invalid action');
    }

    // Get the output parameters for all actions
    $result = mysqli_query($orek, "SELECT @p_success as success, @p_message as message");
    if (!$result) {
        throw new Exception(mysqli_error($orek));
    }
    $row = mysqli_fetch_assoc($result);
    mysqli_free_result($result);

    echo json_encode([
        'success' => (bool)$row['success'],
        'message' => $row['message']
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>