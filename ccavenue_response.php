<?php
require_once('Connections/orek.php');
require_once('NON_SEAMLESS_KIT/Crypto.php');

// Include PHPMailer classes
require_once('lib/PHPMailer/src/Exception.php');
require_once('lib/PHPMailer/src/PHPMailer.php');
require_once('lib/PHPMailer/src/SMTP.php');
// Include SMTP configuration
require_once('lib/smtp_config.php');

// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

session_start();

// Logging function
function logCCAvenue($message) {
    $logFile = 'ccavenue.log';
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . $message . PHP_EOL, FILE_APPEND);
}

// CCAvenue credentials
$working_key = '7345AA75227ACD6013D9EE9E814839B0'; // Replace with your working key
$access_code = 'AVVB49LA10AZ92BVZA';              // Replace with your access code

// Decode the encrypted response from CCAvenue
$encResponse = isset($_POST["encResp"]) ? $_POST["encResp"] : '';

if (empty($encResponse)) {
    // No response received or user navigated directly to this page
    $_SESSION['payment_error'] = "No payment data received. Please try again.";
    header("Location: checkout.php");
    exit();
}

// Decrypt the response
$decryptedResponse = decrypt($encResponse, $working_key);
$responseData = array();

// Parse the decrypted response into an array
$responseParams = explode('&', $decryptedResponse);
foreach ($responseParams as $param) {
    $keyValue = explode('=', $param);
    if (count($keyValue) == 2) {
        $responseData[$keyValue[0]] = $keyValue[1];
    }
}

// Log the response for debugging
logCCAvenue("CCAvenue Response: " . print_r($responseData, true));

// Extract important information
$orderId = isset($responseData['order_id']) ? $responseData['order_id'] : '';
$trackingId = isset($responseData['tracking_id']) ? $responseData['tracking_id'] : '';
$bankRefNo = isset($responseData['bank_ref_no']) ? $responseData['bank_ref_no'] : '';
$orderStatus = isset($responseData['order_status']) ? $responseData['order_status'] : '';
$paymentMode = isset($responseData['payment_mode']) ? $responseData['payment_mode'] : '';
$cardName = isset($responseData['card_name']) ? $responseData['card_name'] : '';
$statusMessage = isset($responseData['status_message']) ? $responseData['status_message'] : '';

// Get the payment ID from session
$paymentId = isset($_SESSION['ccavenue_payment_id']) ? $_SESSION['ccavenue_payment_id'] : 0;
$sessionOrderId = isset($_SESSION['ccavenue_order_id']) ? $_SESSION['ccavenue_order_id'] : '';

// Validate session order ID matches response order ID
if ($sessionOrderId != $orderId) {
    logCCAvenue("Order ID mismatch: Session: $sessionOrderId vs Response: $orderId");
}

// Get payment details
$query_Payment = "SELECT * FROM payment WHERE payment_id = '$paymentId'";
$Payment = mysqli_query($orek, $query_Payment);

if (mysqli_num_rows($Payment) == 0) {
    logCCAvenue("Payment record not found for payment_id: $paymentId");
    $_SESSION['payment_error'] = "Payment record not found. Please contact support.";
    header("Location: checkout.php");
    exit();
}

$row_Payment = mysqli_fetch_assoc($Payment);
$cartId = $row_Payment['cart_id'];
$userId = $row_Payment['user_id'];
$shippingId = $row_Payment['user_shipping_id'];

// Get user details
$query_User = "SELECT * FROM user WHERE user_id = '$userId'";
$User = mysqli_query($orek, $query_User);
$row_User = mysqli_fetch_assoc($User);

// Get shipping address
$query_Address = "SELECT * FROM user_shipping WHERE shipping_id = '$shippingId'";
$Address = mysqli_query($orek, $query_Address);
$row_Address = mysqli_fetch_assoc($Address);

// Get cart items for email
$query_CartItems = "SELECT ci.*, i.item_name, i.image_1 
                   FROM cart_item ci 
                   JOIN item i ON ci.item_id = i.item_id 
                   WHERE ci.cart_id = '$cartId'";
$CartItems = mysqli_query($orek, $query_CartItems);

// Transaction is successful
if ($orderStatus == 'Success') {
    // Update payment record with transaction details
    $update_payment = "UPDATE payment SET 
                      txn_id = '$trackingId', 
                      status = 'Completed',
                      payment_details = '" . mysqli_real_escape_string($orek, json_encode($responseData)) . "',
                      bank_ref_no = '$bankRefNo',
                      payment_mode = 'CCAVENUE - $paymentMode'
                      WHERE payment_id = '$paymentId'";
    
    if (!mysqli_query($orek, $update_payment)) {
        logCCAvenue("Error updating payment: " . mysqli_error($orek));
    }
    
    // Update cart status
    $update_cart = "UPDATE cart SET status = 'Paid' WHERE cart_id = '$cartId'";
    if (!mysqli_query($orek, $update_cart)) {
        logCCAvenue("Error updating cart: " . mysqli_error($orek));
    }
    
    // Send order confirmation emails
    sendOrderConfirmationEmails($row_User, $trackingId, $row_Payment['amount'], $row_Address, $CartItems, $orek);
    
    // Clear the session variables
    unset($_SESSION['ccavenue_payment_id']);
    unset($_SESSION['ccavenue_order_id']);
    
    // Redirect to thank you page
    header("Location: thank-you.php?txn_id=" . $trackingId);
    exit();
} 
// Transaction failed
else {
    // Update payment record with failure details
    $update_payment = "UPDATE payment SET 
                      txn_id = '$trackingId', 
                      status = 'Failed',
                      payment_details = '" . mysqli_real_escape_string($orek, json_encode($responseData)) . "',
                      bank_ref_no = '$bankRefNo',
                      payment_mode = 'CCAVENUE - $paymentMode'
                      WHERE payment_id = '$paymentId'";
    
    if (!mysqli_query($orek, $update_payment)) {
        logCCAvenue("Error updating payment: " . mysqli_error($orek));
    }
    
    // Set error message
    $_SESSION['payment_error'] = "Payment failed: $statusMessage. Please try again.";
    
    // Clear the session variables
    unset($_SESSION['ccavenue_payment_id']);
    unset($_SESSION['ccavenue_order_id']);
    
    // Redirect back to checkout
    header("Location: checkout.php");
    exit();
}

// Function to send order confirmation emails
function sendOrderConfirmationEmails($user, $txn_id, $amount, $address, $cartItems, $db) {
    // Get admin email from database or use a default
    $admin_email = "orekorder@gmail.com";
    
    // Reset cart items pointer
    mysqli_data_seek($cartItems, 0);
    
    // Build the items HTML for email
    $items_html = '';
    $total = 0;
    
    while($item = mysqli_fetch_assoc($cartItems)) {
        $items_html .= '
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #eeeeee;">' . $item['item_name'] . '</td>
            <td style="padding: 10px; border-bottom: 1px solid #eeeeee; text-align: center;">' . $item['qnty'] . '</td>
            <td style="padding: 10px; border-bottom: 1px solid #eeeeee; text-align: right;">₹' . number_format($item['amount'], 2) . '</td>
        </tr>';
        $total += $item['amount'];
    }
    
    // Calculate shipping
    $shipping = ($total >= 1500) ? 0 : 50;
    
    // Current date and time
    $order_date = date('d M Y, h:i A');
    
    // Email templates (user and admin)
    $user_subject = "Order Confirmation - Orek #" . $txn_id;
    $admin_subject = "New Order Received - #" . $txn_id;
    
    // Create email templates
    $user_message = createUserEmailTemplate($user, $txn_id, $order_date, $items_html, $total, $shipping, $amount, $address);
    $admin_message = createAdminEmailTemplate($user, $txn_id, $order_date, $items_html, $total, $shipping, $amount, $address);
    
    try {
        // Send email to customer
        $mail = new PHPMailer(true);
        configureMailer($mail);
        
        $mail->addAddress($user['email'], $user['fname'] . ' ' . $user['lname']);
        $mail->Subject = $user_subject;
        $mail->Body = $user_message;
        $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $user_message));
        
        $mail->send();
        logOrderEmails("Customer email sent successfully to: " . $user['email']);
        
        // Send email to admin
        $adminMail = new PHPMailer(true);
        configureMailer($adminMail);
        
        $adminMail->addAddress($admin_email, 'Orek Admin');
        $adminMail->Subject = $admin_subject;
        $adminMail->Body = $admin_message;
        $adminMail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $admin_message));
        
        $adminMail->send();
        logOrderEmails("Admin email sent successfully to: " . $admin_email);
        
    } catch (Exception $e) {
        logOrderEmails("Email Error: " . $e->getMessage());
        // Don't throw the exception - just log it and continue
    }
}

// Helper function to log email status
function logOrderEmails($message) {
    $logFile = 'order_emails.log';
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . $message . PHP_EOL, FILE_APPEND);
}

// Helper function to configure PHPMailer instance
function configureMailer($mail) {
    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USERNAME;
    $mail->Password = SMTP_PASSWORD;
    $mail->SMTPSecure = SMTP_SECURE;
    $mail->Port = SMTP_PORT;
    
    $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
    $mail->isHTML(true);
    
    // Additional settings for better compatibility
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    $mail->Timeout = 60;
}

// Helper function to create user email template
function createUserEmailTemplate($user, $txn_id, $order_date, $items_html, $total, $shipping, $amount, $address) {
    // Payment method is always CCAvenue
    $payment_method = 'Online Payment';
    
    // Your existing user email HTML template
    return '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Order Confirmation</title>
    </head>
    <body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333333;">
        <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; background-color: #ffffff;">
            <tr>
                <td style="padding: 20px; text-align: center; background-color: #c29958;">
                    <img src="https://orek.in/assets/img/logo/logo.png" alt="Orek Logo" style="max-height: 60px;">
                </td>
            </tr>
            <tr>
                <td style="padding: 30px 20px;">
                    <h2 style="margin-top: 0; color: #c29958;">Order Confirmation</h2>
                    <p>Dear ' . $user['fname'] . ' ' . $user['lname'] . ',</p>
                    <p>Thank you for your order! We\'re pleased to confirm that we\'ve received your order and it\'s being processed.</p>
                    
                    <div style="background-color: #f9f9f9; border-radius: 5px; padding: 15px; margin: 20px 0;">
                        <h3 style="margin-top: 0; color: #c29958;">Order Summary</h3>
                        <p><strong>Order Number:</strong> ' . $txn_id . '</p>
                        <p><strong>Order Date:</strong> ' . $order_date . '</p>
                        <p><strong>Payment Method:</strong> ' . $payment_method . '</p>
                    </div>
                    
                    <h3 style="color: #c29958;">Order Details</h3>
                    <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                        <thead>
                            <tr style="background-color: #f0f0f0;">
                                <th style="padding: 10px; text-align: left;">Product</th>
                                <th style="padding: 10px; text-align: center;">Quantity</th>
                                <th style="padding: 10px; text-align: right;">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            ' . $items_html . '
                            <tr>
                                <td colspan="2" style="padding: 10px; text-align: right; font-weight: bold;">Subtotal:</td>
                                <td style="padding: 10px; text-align: right;">₹' . number_format($total, 2) . '</td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding: 10px; text-align: right; font-weight: bold;">Shipping:</td>
                                <td style="padding: 10px; text-align: right;">₹' . number_format($shipping, 2) . '</td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding: 10px; text-align: right; font-weight: bold;">Total:</td>
                                <td style="padding: 10px; text-align: right; font-weight: bold; color: #c29958;">₹' . number_format($amount, 2) . '</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div style="background-color: #f9f9f9; border-radius: 5px; padding: 15px; margin: 20px 0;">
                        <h3 style="margin-top: 0; color: #c29958;">Shipping Address</h3>
                        <p><strong>' . $address['recipient_name'] . '</strong><br>
                        ' . $address['address'] . '<br>
                        ' . $address['city'] . ', ' . $address['state'] . ' - ' . $address['pin_code'] . '<br>
                        Phone: ' . $address['phone'] . '</p>
                    </div>
                    
                    <p>If you have any questions about your order, please contact our customer service team at <a href="mailto:care@orek.in" style="color: #c29958;">care@orek.in</a></p>
                    
                    <p>Thank you for shopping with us!</p>
                    
                    <p>Warm regards,<br>
                    The Orek Team</p>
                </td>
            </tr>
            <tr>
                <td style="padding: 20px; text-align: center; background-color: #f0f0f0; font-size: 12px; color: #777777;">
                    <p>&copy; ' . date('Y') . ' Orek. All rights reserved.</p>
                    <p>
                        <a href="https://orek.in/privacy-policy.php" style="color: #c29958; text-decoration: none;">Privacy Policy</a> | 
                        <a href="https://orek.in/terms-conditions.php" style="color: #c29958; text-decoration: none;">Terms & Conditions</a>
                    </p>
                </td>
            </tr>
        </table>
    </body>
    </html>';
}

// Helper function to create admin email template
function createAdminEmailTemplate($user, $txn_id, $order_date, $items_html, $total, $shipping, $amount, $address) {
    // Payment method is always CCAvenue
    $payment_method = 'Online Payment';
    
    // Your existing admin email HTML template
    return '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>New Order Received</title>
    </head>
    <body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333333;">
        <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; background-color: #ffffff;">
            <tr>
                <td style="padding: 20px; text-align: center; background-color: #c29958;">
                    <img src="https://orek.in/assets/img/logo/logo.png" alt="Orek Logo" style="max-height: 60px;">
                </td>
            </tr>
            <tr>
                <td style="padding: 30px 20px;">
                    <h2 style="margin-top: 0; color: #c29958;">New Order Received</h2>
                    <p>A new order has been placed on your website. Here are the details:</p>
                    
                    <div style="background-color: #f9f9f9; border-radius: 5px; padding: 15px; margin: 20px 0;">
                        <h3 style="margin-top: 0; color: #c29958;">Order Information</h3>
                        <p><strong>Order Number:</strong> ' . $txn_id . '</p>
                        <p><strong>Order Date:</strong> ' . $order_date . '</p>
                        <p><strong>Customer:</strong> ' . $user['fname'] . ' ' . $user['lname'] . ' (' . $user['email'] . ')</p>
                        <p><strong>Payment Method:</strong> ' . $payment_method . '</p>
                        <p><strong>Order Total:</strong> ₹' . number_format($amount, 2) . '</p>
                    </div>
                    
                    <h3 style="color: #c29958;">Order Details</h3>
                    <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                        <thead>
                            <tr style="background-color: #f0f0f0;">
                                <th style="padding: 10px; text-align: left;">Product</th>
                                <th style="padding: 10px; text-align: center;">Quantity</th>
                                <th style="padding: 10px; text-align: right;">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            ' . $items_html . '
                            <tr>
                                <td colspan="2" style="padding: 10px; text-align: right; font-weight: bold;">Subtotal:</td>
                                <td style="padding: 10px; text-align: right;">₹' . number_format($total, 2) . '</td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding: 10px; text-align: right; font-weight: bold;">Shipping:</td>
                                <td style="padding: 10px; text-align: right;">₹' . number_format($shipping, 2) . '</td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding: 10px; text-align: right; font-weight: bold;">Total:</td>
                                <td style="padding: 10px; text-align: right; font-weight: bold; color: #c29958;">₹' . number_format($amount, 2) . '</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div style="background-color: #f9f9f9; border-radius: 5px; padding: 15px; margin: 20px 0;">
                        <h3 style="margin-top: 0; color: #c29958;">Shipping Address</h3>
                        <p><strong>' . $address['recipient_name'] . '</strong><br>
                        ' . $address['address'] . '<br>
                        ' . $address['city'] . ', ' . $address['state'] . ' - ' . $address['pin_code'] . '<br>
                        Phone: ' . $address['phone'] . '</p>
                    </div>
                    
                    <p>Please process this order as soon as possible.</p>
                    
                    <p>Regards,<br>
                    Orek System</p>
                </td>
            </tr>
            <tr>
                <td style="padding: 20px; text-align: center; background-color: #f0f0f0; font-size: 12px; color: #777777;">
                    <p>&copy; ' . date('Y') . ' Orek. All rights reserved.</p>
                </td>
            </tr>
        </table>
    </body>
    </html>';
}
?>