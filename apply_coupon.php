<?php
require_once('Connections/orek.php');
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $coupon_code = mysqli_real_escape_string($orek, $_POST['coupon_code']);
    $subtotal = floatval($_POST['subtotal']);
    
    // Check if coupon exists and is valid based on your table structure
    $query = "SELECT * FROM coupon WHERE coupon_code = ? 
              AND start_date <= NOW() AND end_date >= NOW()";
    
    $stmt = mysqli_prepare($orek, $query);
    mysqli_stmt_bind_param($stmt, "s", $coupon_code);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        // Store coupon details in session
        $_SESSION['applied_coupon'] = [
            'coupon_id' => $row['coupon_id'],
            'coupon_code' => $row['coupon_code'],
            'discount_percentage' => floatval($row['percentage']) // Convert percentage to float
        ];
        
        echo json_encode([
            'success' => true,
            'message' => 'Coupon applied successfully!',
            'discount_percentage' => floatval($row['percentage'])
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid or expired coupon code.'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
}
?>