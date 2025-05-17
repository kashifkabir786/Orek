<?php
require_once('Connections/orek.php');
require_once('NON_SEAMLESS_KIT/Crypto.php');

session_start();

// Check if user is logged in
if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
    header('Location: login.php?redirect=checkout.php');
    exit();
}

// Validate form data
if (!isset($_POST['shipping_id'])) {
    header('Location: checkout.php');
    exit();
}

// Get user details
$query_User = "SELECT * FROM user WHERE email = '{$_SESSION['email']}'";
$User = mysqli_query($orek, $query_User) or die(mysqli_error($orek));
$row_User = mysqli_fetch_assoc($User);

// Get shipping address
$shipping_id = $_POST['shipping_id'];
$query_Address = "SELECT * FROM user_shipping WHERE shipping_id = '$shipping_id'";
$Address = mysqli_query($orek, $query_Address) or die(mysqli_error($orek));
$row_Address = mysqli_fetch_assoc($Address);

// Get cart details
$query_Cart = "SELECT * FROM cart WHERE user_id = '{$row_User['user_id']}' AND status = 'Pending'";
$Cart = mysqli_query($orek, $query_Cart) or die(mysqli_error($orek));
$row_Cart = mysqli_fetch_assoc($Cart);

// Calculate total amount
$query_Total = "SELECT SUM(amount * qnty) as total FROM cart_item WHERE cart_id = '{$row_Cart['cart_id']}'";
$Total = mysqli_query($orek, $query_Total) or die(mysqli_error($orek));
$row_Total = mysqli_fetch_assoc($Total);
$total_amount = $row_Total['total'];

// Apply shipping cost (Free if total >= 1500)
$shipping_cost = ($total_amount >= 1500) ? 0 : 50;
$final_amount = $total_amount + $shipping_cost;

// Apply coupon discount if available
$coupon_discount = 0;
if (isset($_SESSION['applied_coupon']) && isset($_SESSION['applied_coupon']['discount_percentage'])) {
    $coupon_discount = round(($total_amount * $_SESSION['applied_coupon']['discount_percentage']) / 100);
    $final_amount = round($final_amount - $coupon_discount);
}

// Generate order ID only (CCAvenue will provide transaction ID)
$order_id = "ORD" . time() . rand(100, 999);

// Create a pending payment record with empty transaction ID 
// (will be updated after CCAvenue response)
$insert_payment = "INSERT INTO payment (user_id, cart_id, amount, txn_id, payment_mode, user_shipping_id, coupon_discount, status) 
                  VALUES ('{$row_User['user_id']}', '{$row_Cart['cart_id']}', '$final_amount', '', 'CCAVENUE', '$shipping_id', '$coupon_discount', 'Pending')";
mysqli_query($orek, $insert_payment) or die(mysqli_error($orek));
$payment_id = mysqli_insert_id($orek);

// Store payment_id in session to retrieve later
$_SESSION['ccavenue_payment_id'] = $payment_id;
$_SESSION['ccavenue_order_id'] = $order_id;

// Prepare CCAvenue parameters
$merchant_data = '';
$working_key = '7345AA75227ACD6013D9EE9E814839B0'; // Replace with your actual working key
$access_code = 'AVVB49LA10AZ92BVZA';              // Replace with your actual access code

// Prepare CCAvenue parameters
$ccavenue_params = array(
    'merchant_id' => '3131978',                    // Replace with your actual merchant ID
    'order_id' => $order_id,
    'currency' => 'INR',
    'amount' => $final_amount,
    'redirect_url' => 'https://orek.in/ccavenue_response.php',
    'cancel_url' => 'https://orek.in/ccavenue_response.php',
    'language' => 'EN',
    
    // Billing information
    'billing_name' => $row_User['fname'] . ' ' . $row_User['lname'],
    'billing_address' => $row_Address['address'],
    'billing_city' => $row_Address['city'],
    'billing_state' => $row_Address['state'],
    'billing_zip' => $row_Address['pin_code'],
    'billing_country' => 'India',
    'billing_tel' => $row_Address['phone'],
    'billing_email' => $row_User['email'],
    
    // Shipping information (same as billing in this case)
    'delivery_name' => $row_Address['recipient_name'],
    'delivery_address' => $row_Address['address'],
    'delivery_city' => $row_Address['city'],
    'delivery_state' => $row_Address['state'],
    'delivery_zip' => $row_Address['pin_code'],
    'delivery_country' => 'India',
    'delivery_tel' => $row_Address['phone'],
    
    // Additional parameters
    'merchant_param1' => $payment_id,
    'merchant_param2' => $row_Cart['cart_id'],
    'merchant_param3' => $row_User['user_id'],
    'merchant_param4' => $shipping_id
);

// Build merchant data string
foreach ($ccavenue_params as $key => $value) {
    $merchant_data .= $key . '=' . $value . '&';
}

$encrypted_data = encrypt($merchant_data, $working_key);
$production_url = 'https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction';
$test_url = 'https://test.ccavenue.com/transaction/transaction.do?command=initiateTransaction';

// Use the test URL during development and production URL in production
$action_url = $production_url; // Change this to $production_url when ready for production
?>

<!DOCTYPE html>
<html>
<head>
    <title>Processing Payment - Orek</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
        }
        .loader {
            border: 16px solid #f3f3f3;
            border-radius: 50%;
            border-top: 16px solid #c29958;
            width: 120px;
            height: 120px;
            animation: spin 2s linear infinite;
            margin: 0 auto;
            margin-bottom: 20px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="loader"></div>
    <h2>Please wait while we redirect you to the payment gateway...</h2>
    <p>Please do not refresh this page. You will be redirected automatically.</p>
    
    <form method="post" name="redirect" action="<?php echo $action_url; ?>">
        <input type="hidden" name="encRequest" value="<?php echo $encrypted_data; ?>">
        <input type="hidden" name="access_code" value="<?php echo $access_code; ?>">
    </form>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                document.redirect.submit();
            }, 2000);
        });
    </script>
</body>
</html> 