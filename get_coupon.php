<?php
// Include database connection
require_once('Connections/orek.php');

// Get current date and time
$current_date = date('Y-m-d H:i:s');

// Query to get a random active coupon
$coupon_query = "SELECT * FROM coupon 
                WHERE start_date <= '$current_date' 
                AND end_date >= '$current_date' 
                ORDER BY RAND() 
                LIMIT 1";

$coupon_result = mysqli_query($orek, $coupon_query);

if (mysqli_num_rows($coupon_result) > 0) {
    $coupon_data = mysqli_fetch_assoc($coupon_result);
    
    // Return coupon data as JSON
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'coupon_code' => $coupon_data['coupon_code'],
        'percentage' => $coupon_data['percentage']
    ]);
} else {
    // No active coupons found
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'No active coupons available'
    ]);
}

// Close connection
mysqli_close($orek);
?> 