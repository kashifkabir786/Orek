<?php
/**
 * Cash on Delivery Order Processing Script
 * 
 * This script handles COD order processing including:
 * - User authentication
 * - Order validation
 * - Database record creation
 * - Email notifications
 * - Cart status updates
 */

// Include required files similar to checkout.php
require_once('Connections/orek.php');
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

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Function to log debug info
function logOrderEmails($message) {
    $logFile = 'order_emails.log';
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . $message . PHP_EOL, FILE_APPEND);
}

// Function to log COD orders
function logCOD($message) {
    $logFile = 'cod_orders.log';
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . $message . PHP_EOL, FILE_APPEND);
}

// Check if user is logged in
if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
    header("Location: login.php?redirect=checkout.php");
    exit;
}

// Validate required inputs
if (!isset($_POST['shipping_id']) || empty($_POST['shipping_id'])) {
    header("Location: checkout.php?msg=Invalid shipping information");
    exit;
}

// Get shipping ID
$shipping_id = $_POST['shipping_id'];

// Get user details
$query_User = "SELECT * FROM user WHERE email = '{$_SESSION['email']}'";
$User = mysqli_query($orek, $query_User) or die(mysqli_error($orek));
$row_User = mysqli_fetch_assoc($User);
$user_id = $row_User['user_id'];

// Get shipping details
$query_Address = "SELECT * FROM user_shipping WHERE shipping_id = '$shipping_id' AND user_id = '$user_id'";
$Address = mysqli_query($orek, $query_Address) or die(mysqli_error($orek));
$row_Address = mysqli_fetch_assoc($Address);

// Verify shipping details belong to current user
if (mysqli_num_rows($Address) == 0) {
    header("Location: checkout.php?msg=Invalid shipping information");
    exit;
}

// Get cart details
$query_Cart = "SELECT * FROM cart WHERE user_id = '{$user_id}' AND status = 'Pending'";
$Cart = mysqli_query($orek, $query_Cart) or die(mysqli_error($orek));
$row_Cart = mysqli_fetch_assoc($Cart);
$totalRows_Cart = mysqli_num_rows($Cart);

if ($totalRows_Cart == 0) {
    header('Location: cart.php');
    exit();
}

$cart_id = $row_Cart['cart_id'];

// Get cart items for email and total calculation
$query_CartItems = "SELECT ci.*, i.item_name, i.image_1 
                   FROM cart_item ci 
                   JOIN item i ON ci.item_id = i.item_id 
                   WHERE ci.cart_id = '{$cart_id}'";
$CartItems = mysqli_query($orek, $query_CartItems) or die(mysqli_error($orek));

// Calculate subtotal
$query_Total = "SELECT SUM(amount * qnty) as total FROM cart_item WHERE cart_id = '{$cart_id}'";
$Total = mysqli_query($orek, $query_Total) or die(mysqli_error($orek));
$row_Total = mysqli_fetch_assoc($Total);
$total_amount = $row_Total['total'];

// Apply shipping cost (Free if total >= 1500)
$shipping_cost = ($total_amount >= 1500) ? 0 : 50;
$final_amount = $total_amount + $shipping_cost;

// Apply coupon discount if available
$discount_amount = 0;
$coupon_code = '';
if (isset($_SESSION['applied_coupon']) && isset($_SESSION['applied_coupon']['discount_percentage'])) {
    $discount_amount = round(($total_amount * $_SESSION['applied_coupon']['discount_percentage']) / 100);
    $coupon_code = $_SESSION['applied_coupon']['coupon_code'] ?? '';
    $final_amount = round($final_amount - $discount_amount);
}

// Generate order ID (timestamp + user_id)
$order_id = time() . $user_id;
$txn_id = 'COD' . $order_id; // Add COD prefix for easy identification

// Current timestamp
$timestamp = date('Y-m-d H:i:s');

// Insert payment record in database
$payment_status = "Pending";
$payment_method = "Cash on Delivery";
$payment_details = json_encode([
    'order_id' => $order_id,
    'payment_method' => $payment_method,
    'status' => $payment_status,
    'timestamp' => $timestamp,
    'shipping_cost' => $shipping_cost,
    'discount_amount' => $discount_amount,
    'total_amount' => $total_amount,
    'final_amount' => $final_amount
]);

$query_InsertPayment = "INSERT INTO payment 
                        (cart_id, user_id, user_shipping_id, txn_id, amount, 
                        payment_mode, status, payment_details, coupon_discount) 
                        VALUES 
                        ('$cart_id', '$user_id', '$shipping_id', '$txn_id', '$final_amount', 
                        '$payment_method', '$payment_status', 
                        '" . mysqli_real_escape_string($orek, $payment_details) . "', '$discount_amount')";

if (!mysqli_query($orek, $query_InsertPayment)) {
    logCOD("Error inserting payment: " . mysqli_error($orek));
    header("Location: checkout.php?msg=Order processing error");
    exit;
}

$payment_id = mysqli_insert_id($orek);
logCOD("Payment record created: $payment_id for user: $user_id, amount: $final_amount");

// Update cart status to 'Ordered'
$query_UpdateCart = "UPDATE cart SET status = 'Ordered' WHERE cart_id = '$cart_id'";
if (!mysqli_query($orek, $query_UpdateCart)) {
    logCOD("Error updating cart: " . mysqli_error($orek));
}

// Prepare order items HTML for emails
$items_html = '';
mysqli_data_seek($CartItems, 0);
while ($item = mysqli_fetch_assoc($CartItems)) {
    $items_html .= '
    <tr>
        <td style="padding: 10px; text-align: left;">' . $item['item_name'] . '</td>
        <td style="padding: 10px; text-align: center;">' . $item['qnty'] . '</td>
        <td style="padding: 10px; text-align: right;">₹' . number_format($item['amount'], 2) . '</td>
    </tr>';
}

// Format date for email
$order_date = date('d M Y, h:i A');

// Helper function to configure PHPMailer instance (same as in ccavenue_response.php)
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

// Create email templates
// Override payment method in templates for COD
$user_subject = "Order Confirmation - Orek #" . $txn_id;
$admin_subject = "New COD Order Received - #" . $txn_id;

// Create custom payment method templates for COD
function createUserCODEmailTemplate($user, $txn_id, $order_date, $items_html, $total, $shipping, $amount, $address) {
    // Set payment method to COD
    $payment_method = 'Cash on Delivery (COD)';
    
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
                    
                    <div style="background-color: #fff4e5; border-radius: 5px; padding: 15px; margin: 20px 0; border-left: 4px solid #c29958;">
                        <h3 style="margin-top: 0; color: #c29958;">Cash on Delivery Information</h3>
                        <p>You have selected to pay by Cash on Delivery. Please keep the exact amount ready at the time of delivery.</p>
                        <p>Our delivery partner will contact you before delivery.</p>
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

function createAdminCODEmailTemplate($user, $txn_id, $order_date, $items_html, $total, $shipping, $amount, $address) {
    // Set payment method to COD
    $payment_method = 'Cash on Delivery (COD)';
    
    return '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>New COD Order Received</title>
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
                    <h2 style="margin-top: 0; color: #c29958;">New Cash on Delivery Order Received</h2>
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
                    
                    <div style="background-color: #fff4e5; border-radius: 5px; padding: 15px; margin: 20px 0; border-left: 4px solid #c29958;">
                        <h3 style="margin-top: 0; color: #c29958;">Cash on Delivery Order</h3>
                        <p>This is a Cash on Delivery order. Customer will pay ₹' . number_format($amount, 2) . ' at the time of delivery.</p>
                        <p>Please process this order and arrange for shipping.</p>
                    </div>
                    
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

$user_message = createUserCODEmailTemplate($row_User, $txn_id, $order_date, $items_html, $total_amount, $shipping_cost, $final_amount, $row_Address);
$admin_message = createAdminCODEmailTemplate($row_User, $txn_id, $order_date, $items_html, $total_amount, $shipping_cost, $final_amount, $row_Address);

// Get admin email from SMTP config or use a default
$admin_email = defined('ADMIN_EMAIL') ? ADMIN_EMAIL : "orekorder@gmail.com";

try {
    // Send email to customer
    $mail = new PHPMailer(true);
    configureMailer($mail);
    
    $mail->addAddress($row_User['email'], $row_User['fname'] . ' ' . $row_User['lname']);
    $mail->Subject = $user_subject;
    $mail->Body = $user_message;
    $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $user_message));
    
    $mail->send();
    logOrderEmails("COD Order: Customer email sent successfully to: " . $row_User['email']);
    
    // Send email to admin
    $adminMail = new PHPMailer(true);
    configureMailer($adminMail);
    
    $adminMail->addAddress($admin_email, 'Orek Admin');
    $adminMail->Subject = $admin_subject;
    $adminMail->Body = $admin_message;
    $adminMail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $admin_message));
    
    $adminMail->send();
    logOrderEmails("COD Order: Admin email sent successfully to: " . $admin_email);
    
} catch (Exception $e) {
    logOrderEmails("COD Order: Email Error: " . $e->getMessage());
    // Don't throw the exception - just log it and continue
}

// Clear applied coupon from session
if (isset($_SESSION['applied_coupon'])) {
    unset($_SESSION['applied_coupon']);
}

// Redirect to thank you page with transaction ID
header("Location: thank-you.php?txn_id=" . $txn_id);
exit;
?>