<?php require_once('Connections/orek.php') ?>
<?php
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

// Function to log debug info
function logOrderEmails($message) {
    $logFile = 'order_emails.log';
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . $message . PHP_EOL, FILE_APPEND);
}

session_start();
$total_amount = 0;
$discount_amount = 0;
$message = "";
$delivery = "";
$current_step = isset($_GET['step']) ? intval($_GET['step']) : 1;

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
    $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

// Check if user is logged in
if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
    header('Location: login.php?redirect=checkout.php');
    exit();
}

// Get user details
$query_User = "SELECT * FROM user WHERE email = '{$_SESSION['email']}'";
$User = mysqli_query($orek, $query_User) or die(mysqli_error($orek));
$row_User = mysqli_fetch_assoc($User);
$totalRows_User = mysqli_num_rows($User);

// Get saved shipping addresses
$query_Addresses = "SELECT * FROM user_shipping WHERE user_id = '{$row_User['user_id']}' ORDER BY is_default DESC, shipping_id DESC";
$Addresses = mysqli_query($orek, $query_Addresses) or die(mysqli_error($orek));
$totalRows_Addresses = mysqli_num_rows($Addresses);

// Get cart details
$query_Cart = "SELECT * FROM cart WHERE user_id = '{$row_User['user_id']}' AND status = 'Pending'";
$Cart = mysqli_query($orek, $query_Cart) or die(mysqli_error($orek));
$row_Cart = mysqli_fetch_assoc($Cart);
$totalRows_Cart = mysqli_num_rows($Cart);

if ($totalRows_Cart == 0) {
    header('Location: cart.php');
    exit();
}

// Get cart items
$query_CartItems = "SELECT ci.*, i.item_name, i.image_1 
                   FROM cart_item ci 
                   JOIN item i ON ci.item_id = i.item_id 
                   WHERE ci.cart_id = '{$row_Cart['cart_id']}'";
$CartItems = mysqli_query($orek, $query_CartItems) or die(mysqli_error($orek));
$totalRows_CartItems = mysqli_num_rows($CartItems);

$query_Total = "SELECT SUM(amount * qnty) as total FROM cart_item WHERE cart_id = '{$row_Cart['cart_id']}'";
$Total = mysqli_query($orek, $query_Total) or die(mysqli_error($orek));
$row_Total = mysqli_fetch_assoc($Total);
$total_amount = $row_Total['total'];

// Apply shipping cost (Free if total >= 1500)
$shipping_cost = ($total_amount >= 1500) ? 0 : 50;
$final_amount = $total_amount + $shipping_cost;

// Initialize coupon discount variable
$coupon_discount = 0;

// Apply coupon discount if available
if (isset($_SESSION['applied_coupon']) && isset($_SESSION['applied_coupon']['discount_percentage'])) {
    $coupon_discount = round(($total_amount * $_SESSION['applied_coupon']['discount_percentage']) / 100);
    $final_amount = round($final_amount - $coupon_discount);
}

// Handle address form submission
if (isset($_POST['save_address'])) {
    $address_name = mysqli_real_escape_string($orek, $_POST['address_name']);
    $recipient_name = mysqli_real_escape_string($orek, $_POST['recipient_name']);
    $address = mysqli_real_escape_string($orek, $_POST['address']);
    $city = mysqli_real_escape_string($orek, $_POST['city']);
    $state = mysqli_real_escape_string($orek, $_POST['state']);
    $pin_code = mysqli_real_escape_string($orek, $_POST['pin_code']);
    $phone = mysqli_real_escape_string($orek, $_POST['phone']);
    $is_default = isset($_POST['is_default']) ? 1 : 0;
    
    // If this is the first address or marked as default, update all other addresses
    if ($is_default || $totalRows_Addresses == 0) {
        $update_query = "UPDATE user_shipping SET is_default = 0 WHERE user_id = '{$row_User['user_id']}'";
        mysqli_query($orek, $update_query);
        $is_default = 1;
    }

    // Insert new address
    $insert_query = "INSERT INTO user_shipping (user_id, address_name, recipient_name, address, city, state, pin_code, phone, is_default) 
                     VALUES ('{$row_User['user_id']}', '$address_name', '$recipient_name', '$address', '$city', '$state', '$pin_code', '$phone', '$is_default')";
    
    if (mysqli_query($orek, $insert_query)) {
        header("Location: checkout.php?success=address_added");
        exit();
    } else {
        $delivery = "Error adding address: " . mysqli_error($orek);
    }
}

// Get default address if exists
$query_DefaultAddress = "SELECT * FROM user_shipping WHERE user_id = '{$row_User['user_id']}' AND is_default = 1";
$DefaultAddress = mysqli_query($orek, $query_DefaultAddress);
$row_DefaultAddress = mysqli_fetch_assoc($DefaultAddress);
$totalRows_DefaultAddress = mysqli_num_rows($DefaultAddress);

// Helper functions for email templates are kept since they are referenced in ccavenue_response.php
// But remove the sendOrderConfirmationEmails function - it's now handled in ccavenue_response.php
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
<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Orek - Checkout</title>
    <meta name="robots" content="noindex, follow" />
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/logo/logo.png" />

    <!-- CSS
	============================================ -->
    <!-- google fonts -->
    <link href="https://fonts.googleapis.com/css?family=Lato:300,300i,400,400i,700,900" rel="stylesheet" />
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <!-- Pe-icon-7-stroke CSS -->
    <link rel="stylesheet" href="assets/css/pe-icon-7-stroke.css" />
    <!-- Font-awesome CSS -->
    <link rel="stylesheet" href="assets/css/font-awesome.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />

    <!-- Slick slider css -->
    <link rel="stylesheet" href="assets/css/slick.min.css" />
    <!-- animate css -->
    <link rel="stylesheet" href="assets/css/animate.css" />
    <!-- Nice Select css -->
    <link rel="stylesheet" href="assets/css/nice-select.css" />
    <!-- jquery UI css -->
    <link rel="stylesheet" href="assets/css/jqueryui.min.css" />
    <!-- main style css -->
    <link rel="stylesheet" href="assets/css/style.css" />
    <!-- Custom css -->
    <link rel="stylesheet" href="assets/css/custom.css" />

    <style>
    .checkout-steps {
        display: flex;
        margin-bottom: 30px;
        border-bottom: 1px solid #e5e5e5;
    }

    .checkout-step {
        flex: 1;
        text-align: center;
        padding: 15px;
        position: relative;
        cursor: pointer;
    }

    .checkout-step.active {
        font-weight: bold;
        color: #c29958;
    }

    .checkout-step.active:after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 3px;
        background-color: #c29958;
    }

    .checkout-step-content {
        display: none;
    }

    .checkout-step-content.active {
        display: block;
    }

    .address-card {
        border: 1px solid #e5e5e5;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 15px;
        position: relative;
    }

    .address-card.selected {
        border-color: #c29958;
        background-color: #fff9e9;
    }

    .address-card .address-actions {
        position: absolute;
        top: 10px;
        right: 10px;
    }

    .address-card .form-check {
        margin-bottom: 10px;
    }
    </style>
</head>

<body>
    <!-- Start Header Area -->
    <?php require_once('header.php'); ?>
    <!-- end Header Area -->
    <main>
        <!-- breadcrumb area start -->
        <div class="breadcrumb-area">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-wrap">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.php"><i class="fa fa-home"></i></a></li>
                                    <li class="breadcrumb-item"><a href="product-list.php">shop</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">checkout</li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- breadcrumb area end -->

        <!-- checkout main wrapper start -->
        <div class="checkout-page-wrapper section-padding">
            <div class="container">
                <?php if ($message): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-danger"><?php echo $message; ?></div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['payment_error'])): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-danger"><?php echo $_SESSION['payment_error']; ?></div>
                    </div>
                </div>
                <?php 
                    // Clear the payment error after displaying
                    unset($_SESSION['payment_error']);
                endif; ?>

                <?php if (isset($_GET['success']) && $_GET['success'] == 'address_added'): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-success">Shipping address added successfully!</div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (isset($_GET['success']) && $_GET['success'] == 'address_deleted'): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-success">Shipping address deleted successfully!</div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-12">
                        <div class="checkout-steps">
                            <div class="checkout-step <?php echo ($current_step == 1) ? 'active' : ''; ?>"
                                data-step="1">
                                <span>1. Shipping</span>
                            </div>
                            <div class="checkout-step <?php echo ($current_step == 2) ? 'active' : ''; ?>"
                                data-step="2">
                                <span>2. Review & Payment</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <!-- Step 1: Shipping Address -->
                        <div class="checkout-step-content <?php echo ($current_step == 1) ? 'active' : ''; ?>"
                            id="step-1">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="checkout-billing-details-wrap">
                                        <h5 class="checkout-title">Add New Shipping Address</h5>

                                        <?php if ($delivery): ?>
                                        <div class="alert alert-warning"><?php echo $delivery; ?></div>
                                        <?php endif; ?>

                                        <div class="billing-form-wrap">
                                            <form action="<?php echo $editFormAction; ?>" method="post"
                                                id="address-form">
                                                <div class="single-input-item">
                                                    <label for="address_name" class="required">Address Name</label>
                                                    <input type="text" id="address_name" name="address_name"
                                                        placeholder="Home, Office, etc." value="Home" required />
                                                </div>

                                                <div class="single-input-item">
                                                    <label for="recipient_name" class="required">Recipient Name</label>
                                                    <input type="text" id="recipient_name" name="recipient_name"
                                                        placeholder="Recipient Name"
                                                        value="<?php echo $row_User['fname'] . ' ' . $row_User['lname']; ?>"
                                                        required />
                                                </div>

                                                <div class="single-input-item">
                                                    <label for="address" class="required">Address</label>
                                                    <textarea name="address" id="address" rows="4"
                                                        placeholder="Enter your full address" required></textarea>
                                                </div>

                                                <div class="single-input-item">
                                                    <label for="city" class="required">City</label>
                                                    <input type="text" id="city" name="city" placeholder="City"
                                                        required />
                                                </div>

                                                <div class="single-input-item">
                                                    <label for="state" class="required">State</label>
                                                    <select name="state" id="state" class="nice-select" required>
                                                        <option value="">Select State</option>
                                                        <option value="Andhra Pradesh">Andhra Pradesh</option>
                                                        <option value="Arunachal Pradesh">Arunachal Pradesh</option>
                                                        <option value="Assam">Assam</option>
                                                        <option value="Bihar">Bihar</option>
                                                        <option value="Chhattisgarh">Chhattisgarh</option>
                                                        <option value="Goa">Goa</option>
                                                        <option value="Gujarat">Gujarat</option>
                                                        <option value="Haryana">Haryana</option>
                                                        <option value="Himachal Pradesh">Himachal Pradesh</option>
                                                        <option value="Jharkhand">Jharkhand</option>
                                                        <option value="Karnataka">Karnataka</option>
                                                        <option value="Kerala">Kerala</option>
                                                        <option value="Madhya Pradesh">Madhya Pradesh</option>
                                                        <option value="Maharashtra">Maharashtra</option>
                                                        <option value="Manipur">Manipur</option>
                                                        <option value="Meghalaya">Meghalaya</option>
                                                        <option value="Mizoram">Mizoram</option>
                                                        <option value="Nagaland">Nagaland</option>
                                                        <option value="Odisha">Odisha</option>
                                                        <option value="Punjab">Punjab</option>
                                                        <option value="Rajasthan">Rajasthan</option>
                                                        <option value="Sikkim">Sikkim</option>
                                                        <option value="Tamil Nadu">Tamil Nadu</option>
                                                        <option value="Telangana">Telangana</option>
                                                        <option value="Tripura">Tripura</option>
                                                        <option value="Uttar Pradesh">Uttar Pradesh</option>
                                                        <option value="Uttarakhand">Uttarakhand</option>
                                                        <option value="West Bengal">West Bengal</option>
                                                        <option value="Andaman and Nicobar Islands">Andaman and Nicobar
                                                            Islands</option>
                                                        <option value="Chandigarh">Chandigarh</option>
                                                        <option value="Dadra and Nagar Haveli and Daman and Diu">Dadra
                                                            and Nagar Haveli and Daman and Diu</option>
                                                        <option value="Delhi">Delhi</option>
                                                        <option value="Jammu and Kashmir">Jammu and Kashmir</option>
                                                        <option value="Ladakh">Ladakh</option>
                                                        <option value="Lakshadweep">Lakshadweep</option>
                                                        <option value="Puducherry">Puducherry</option>
                                                    </select>
                                                </div>

                                                <div class="single-input-item">
                                                    <label for="pin_code" class="required">PIN Code</label>
                                                    <input type="text" id="pin_code" name="pin_code"
                                                        placeholder="PIN Code" required />
                                                </div>

                                                <div class="single-input-item">
                                                    <label for="phone" class="required">Phone</label>
                                                    <input type="text" id="phone" name="phone" placeholder="Phone"
                                                        value="<?php echo $row_User['phone_no']; ?>" required />
                                                </div>

                                                <div class="single-input-item">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="is_default" name="is_default" value="1">
                                                        <label class="custom-control-label" for="is_default">Set as
                                                            default shipping address</label>
                                                    </div>
                                                </div>

                                                <div class="single-input-item">
                                                    <button type="submit" name="save_address"
                                                        class="btn btn-sqr btn-block">Save Address</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="checkout-billing-details-wrap">
                                        <h5 class="checkout-title">Your Saved Addresses</h5>

                                        <?php if ($totalRows_Addresses > 0): ?>
                                        <form action="checkout.php?step=2" method="post" id="shipping-form">
                                            <div class="saved-addresses">
                                                <?php 
                                                mysqli_data_seek($Addresses, 0);
                                                while ($row_Address = mysqli_fetch_assoc($Addresses)): 
                                                    $is_selected = ($totalRows_DefaultAddress > 0 && $row_Address['shipping_id'] == $row_DefaultAddress['shipping_id']);
                                                ?>
                                                <div class="address-card <?php echo $is_selected ? 'selected' : ''; ?>">
                                                    <div class="address-actions">
                                                        <a href="edit-address.php?id=<?php echo $row_Address['shipping_id']; ?>"
                                                            class="btn btn-sm btn-outline-secondary"><i
                                                                class="fa fa-edit"></i></a>
                                                        <a href="delete-address.php?id=<?php echo $row_Address['shipping_id']; ?>"
                                                            class="btn btn-sm btn-outline-danger"
                                                            onclick="return confirm('Are you sure you want to delete this address?');"><i
                                                                class="fa fa-trash"></i></a>
                                                    </div>
                                                    <div class="form-check">
                                                        <input type="radio" class="form-check-input"
                                                            id="address_<?php echo $row_Address['shipping_id']; ?>"
                                                            name="shipping_id"
                                                            value="<?php echo $row_Address['shipping_id']; ?>"
                                                            <?php echo $is_selected ? 'checked' : ''; ?> required>
                                                        <label class="form-check-label"
                                                            for="address_<?php echo $row_Address['shipping_id']; ?>">
                                                            <?php if ($row_Address['is_default']): ?>
                                                            <span class="badge badge-primary">Default</span>
                                                            <?php endif; ?>
                                                            <strong><?php echo $row_Address['address_name']; ?></strong>
                                                        </label>
                                                    </div>
                                                    <p class="mb-1">
                                                        <strong><?php echo $row_Address['recipient_name']; ?></strong>
                                                    </p>
                                                    <p class="mb-1"><?php echo $row_Address['address']; ?></p>
                                                    <p class="mb-1"><?php echo $row_Address['city']; ?>,
                                                        <?php echo $row_Address['state']; ?> -
                                                        <?php echo $row_Address['pin_code']; ?></p>
                                                    <p class="mb-0">Phone: <?php echo $row_Address['phone']; ?></p>
                                                </div>
                                                <?php endwhile; ?>
                                            </div>

                                            <div class="single-input-item mt-4">
                                                <button type="submit" class="btn btn-sqr btn-block">Proceed to
                                                    Payment</button>
                                            </div>
                                        </form>
                                        <?php else: ?>
                                        <div class="alert alert-info">
                                            You don't have any saved addresses. Please add a new shipping address.
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Review & Payment -->
                        <div class="checkout-step-content <?php echo ($current_step == 2) ? 'active' : ''; ?>"
                            id="step-2">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="checkout-billing-details-wrap">
                                        <h5 class="checkout-title">Shipping Details</h5>

                                        <?php if (isset($_POST['shipping_id'])): 
                                            $shipping_id = $_POST['shipping_id'];
                                            $query_SelectedAddress = "SELECT * FROM user_shipping WHERE shipping_id = '$shipping_id'";
                                            $SelectedAddress = mysqli_query($orek, $query_SelectedAddress);
                                            $row_SelectedAddress = mysqli_fetch_assoc($SelectedAddress);
                                        ?>
                                        <div class="shipping-details">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h6 class="card-title">Shipping Address</h6>
                                                    <p class="card-text">
                                                        <strong><?php echo $row_SelectedAddress['address_name']; ?></strong><br>
                                                        <strong><?php echo $row_SelectedAddress['recipient_name']; ?></strong><br>
                                                        <?php echo $row_SelectedAddress['address']; ?><br>
                                                        <?php echo $row_SelectedAddress['city']; ?>,
                                                        <?php echo $row_SelectedAddress['state']; ?> -
                                                        <?php echo $row_SelectedAddress['pin_code']; ?><br>
                                                        Phone: <?php echo $row_SelectedAddress['phone']; ?>
                                                    </p>
                                                    <a href="checkout.php?step=1"
                                                        class="btn btn-sm btn-outline-secondary">Change</a>
                                                </div>
                                            </div>
                                        </div>
                                        <?php else: ?>
                                        <div class="alert alert-warning">
                                            Please select a shipping address first. <a href="checkout.php?step=1">Go
                                                back</a>
                                        </div>
                                        <?php endif; ?>

                                        <h5 class="checkout-title mt-4">Payment Method</h5>
                                        <div class="payment-method">
                                            <div class="custom-control custom-radio mb-3">
                                                <input type="radio" id="ccavenue" name="payment_method" value="CCAVENUE"
                                                    class="custom-control-input" checked>
                                                <label class="custom-control-label" for="ccavenue">Credit Card / Debit
                                                    Card / Net Banking</label>
                                                <p>Pay securely through CCAvenue Payment Gateway.</p>
                                                <img src="assets/img/payment/ccavenue.png" alt="CCAvenue"
                                                    class="payment-logo mt-2" style="max-height: 30px;">
                                            </div>

                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="cod" name="payment_method" value="COD"
                                                    class="custom-control-input">
                                                <label class="custom-control-label" for="cod">Cash on Delivery
                                                    (COD)</label>
                                                <p>Pay with cash when your order is delivered.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="order-summary-details">
                                        <h5 class="checkout-title">Your Order Summary</h5>
                                        <div class="order-summary-content">
                                            <div class="order-items">
                                                <?php 
                                                $subtotal = 0;
                                                mysqli_data_seek($CartItems, 0);
                                                while($row_CartItem = mysqli_fetch_assoc($CartItems)) { 
                                                    $item_total = $row_CartItem['amount'] * $row_CartItem['qnty'];
                                                    $subtotal += $item_total;
                                                ?>
                                                <div class="order-item">
                                                    <div class="item-info">
                                                        <a href="product-details.php?item_id=<?php echo $row_CartItem['item_id']; ?>"
                                                            class="item-name"><?php echo $row_CartItem['item_name']; ?></a>
                                                        <div class="item-meta">
                                                            <span class="quantity">Qty:
                                                                <?php echo $row_CartItem['qnty']; ?></span>
                                                            <span class="price">
                                                                ₹<?php echo round($row_CartItem['amount'], 2); ?> ×
                                                                <?php echo $row_CartItem['qnty']; ?> =
                                                                ₹<?php echo round($item_total, 2); ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php } ?>
                                            </div>

                                            <div class="order-summary-totals">
                                                <div class="summary-row">
                                                    <span>Subtotal</span>
                                                    <span class="amount">₹<?php echo round($subtotal, 2); ?></span>
                                                </div>

                                                <?php if(isset($_SESSION['applied_coupon'])): ?>
                                                <div class="summary-row discount">
                                                    <span>Coupon Discount
                                                        (<?php echo $_SESSION['applied_coupon']['discount_percentage']; ?>%)</span>
                                                    <span class="amount text-success">-₹<?php 
                                                        $discount_amount = ($subtotal * $_SESSION['applied_coupon']['discount_percentage']) / 100;
                                                        echo round($discount_amount);
                                                        $subtotal = $subtotal - $discount_amount;
                                                    ?></span>
                                                </div>
                                                <?php endif; ?>

                                                <div class="summary-row">
                                                    <span>Shipping</span>
                                                    <?php if($subtotal >= 1500): ?>
                                                    <span class="amount text-success">Free</span>
                                                    <?php else: ?>
                                                    <span class="amount">₹50</span>
                                                    <?php endif; ?>
                                                </div>

                                                <div class="summary-row total">
                                                    <span>Total Amount</span>
                                                    <span class="amount">₹<?php 
                                                        $final_amount = $subtotal + (($subtotal > 0 && $subtotal < 1500) ? 50 : 0);
                                                        echo round($final_amount); 
                                                    ?></span>
                                                </div>
                                            </div>

                                            <?php if (isset($_POST['shipping_id'])): ?>
                                            <form action="process_ccavenue_payment.php" method="post" id="order-form">
                                                <input type="hidden" name="shipping_id"
                                                    value="<?php echo $_POST['shipping_id']; ?>">
                                                <input type="hidden" name="selected_payment" id="selected_payment"
                                                    value="CCAVENUE">
                                                <div class="summary-footer-area">
                                                    <button type="submit" id="place-order-btn" class="btn btn-sqr">Place
                                                        Order</button>
                                                </div>
                                            </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- checkout main wrapper end -->
    </main>

    <!-- Scroll to top start -->
    <div class="scroll-top not-visible">
        <i class="fa fa-angle-up"></i>
    </div>
    <!-- Scroll to Top End -->

    <!-- footer area start -->
    <?php require_once('footer.php'); ?>
    <!-- footer area end -->

    <!-- JS
    ============================================ -->
    <!-- Modernizer JS -->
    <script src="assets/js/modernizr-3.6.0.min.js"></script>
    <!-- jQuery JS -->
    <script src="assets/js/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <!-- slick Slider JS -->
    <script src="assets/js/slick.min.js"></script>
    <!-- Countdown JS -->
    <script src="assets/js/countdown.min.js"></script>
    <!-- Nice Select JS -->
    <script src="assets/js/nice-select.min.js"></script>
    <!-- jquery UI JS -->
    <script src="assets/js/jqueryui.min.js"></script>
    <!-- Image zoom JS -->
    <script src="assets/js/image-zoom.min.js"></script>
    <!-- Images loaded JS -->
    <script src="assets/js/imagesloaded.pkgd.min.js"></script>
    <!-- mail-chimp active js -->
    <script src="assets/js/ajaxchimp.js"></script>
    <!-- contact form dynamic js -->
    <script src="assets/js/ajax-mail.js"></script>
    <!-- google map api -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCfmCVTjRI007pC1Yk2o2d_EhgkjTsFVN8"></script>
    <!-- google map active js -->
    <script src="assets/js/google-map.js"></script>
    <!-- Main JS -->
    <script src="assets/js/main.js"></script>
    <script>
    $(document).ready(function() {
        // Step navigation
        $('.checkout-step').click(function() {
            var step = $(this).data('step');
            if (step == 2 && $('.saved-addresses input:checked').length == 0) {
                alert('Please select a shipping address first.');
                return;
            }
            window.location.href = 'checkout.php?step=' + step;
        });

        // Address selection
        $('.address-card').click(function() {
            $(this).find('input[type="radio"]').prop('checked', true);
            $('.address-card').removeClass('selected');
            $(this).addClass('selected');
        });

        // Form validation
        $('#shipping-form').submit(function(e) {
            if ($('.saved-addresses input:checked').length == 0) {
                e.preventDefault();
                alert('Please select a shipping address.');
            }
        });

        // Payment method toggle
        $('input[name="payment_method"]').on('change', function() {
            var selected = $(this).val();
            if (selected === 'ccavenue') {
                $('#ccavenue').collapse('show');
                $('#cod').collapse('hide');
            } else if (selected === 'cod') {
                $('#cod').collapse('show');
                $('#ccavenue').collapse('hide');
            }
        });

        // Submit order
        $('#place-order-btn').on('click', function() {
            var selectedPayment = $('input[name="payment_method"]:checked').val();

            // Check shipping address
            if (!<?php echo $shipping_id; ?>) {
                alert('Please select a shipping address before proceeding.');
                $('#collapseOne').collapse('show');
                return false;
            }

            if (selectedPayment === 'ccavenue') {
                $('#ccavenue-payment-form').submit();
            } else if (selectedPayment === 'cod') {
                $('#cod-payment-form').submit();
            }
        });
    });
    </script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        // Get references to the payment method radio buttons
        const ccavenueRadio = document.getElementById('ccavenue');
        const codRadio = document.getElementById('cod');
        const orderForm = document.getElementById('order-form');
        const selectedPaymentField = document.getElementById('selected_payment');

        // Function to update form action based on selected payment method
        function updateFormAction() {
            if (ccavenueRadio.checked) {
                orderForm.action = "process_ccavenue_payment.php";
                selectedPaymentField.value = "CCAVENUE";
                console.log("Payment method set to CCAvenue");
            } else if (codRadio.checked) {
                orderForm.action = "process_cod_order.php";
                selectedPaymentField.value = "COD";
                console.log("Payment method set to COD");
            }
        }

        // Add event listeners to radio buttons
        if (ccavenueRadio && codRadio && orderForm) {
            ccavenueRadio.addEventListener('change', updateFormAction);
            codRadio.addEventListener('change', updateFormAction);

            // Also add click event listeners for better mobile compatibility
            ccavenueRadio.addEventListener('click', updateFormAction);
            codRadio.addEventListener('click', updateFormAction);

            // Set initial form action based on default selection
            updateFormAction();

            // Debug - log when form is submitted
            orderForm.addEventListener('submit', function(e) {
                console.log("Form submitted to: " + orderForm.action);
            });
        } else {
            console.log("One or more elements not found");
        }
    });
    </script>
</body>

</html>