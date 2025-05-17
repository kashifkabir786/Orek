<?php
// Ensure no output before headers
ob_start();

// Prevent any errors from being displayed
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// Set proper content type header
header('Content-Type: application/json');

// Include database connection
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

// Function to log debug info
function logDebug($message) {
    $logFile = 'smtp_debug.log';
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . $message . PHP_EOL, FILE_APPEND);
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $coupon_code = filter_var($_POST['coupon_code'], FILTER_SANITIZE_STRING);
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address']);
        exit;
    }
    
    // Get coupon details
    $coupon_query = "SELECT * FROM coupon WHERE coupon_code = '$coupon_code'";
    $coupon_result = mysqli_query($orek, $coupon_query);
    
    // If coupon not found in database, use default values
    if (mysqli_num_rows($coupon_result) > 0) {
        $coupon_data = mysqli_fetch_assoc($coupon_result);
        $percentage = $coupon_data['percentage'];
        $end_date = $coupon_data['end_date'];
    } else {
        // Default values if coupon not found
        $percentage = '10';
        $end_date = date('Y-m-d H:i:s', strtotime('+30 days'));
    }
    
    // Email content
    $subject = "Your Exclusive Coupon Code from Orek";
    
    $message = "
    <html>
    <head>
        <title>Your Exclusive Coupon Code</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(to right, #ff6b6b, #ff9e7d); color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background-color: #f9f9f9; }
            .coupon-code { background-color: #fff; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; margin: 20px 0; border: 2px dashed #ff6b6b; }
            .discount-info { font-size: 18px; color: #ff6b6b; font-weight: bold; text-align: center; margin: 15px 0; }
            .footer { text-align: center; font-size: 12px; color: #999; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Your Exclusive Coupon Code</h1>
            </div>
            <div class='content'>
                <p>Dear Customer,</p>
                <p>Thank you for subscribing to our newsletter! As promised, here's your exclusive coupon code for a special discount on your next purchase:</p>
                
                <div class='coupon-code'>
                    $coupon_code
                </div>
                
                <div class='discount-info'>
                    Get $percentage% OFF your next purchase!
                </div>
                
                <p>This coupon is valid until " . date('F j, Y', strtotime($end_date)) . ".</p>
                <p>Simply enter this code at checkout to receive your discount.</p>
                <p>Happy Shopping!</p>
            </div>
            <div class='footer'>
                <p>© " . date('Y') . " Orek. All rights reserved.</p>
                <p>This email was sent to you because you requested a coupon code from our website.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);
    
    try {
        // Log SMTP configuration for debugging
        logDebug("SMTP Host: " . SMTP_HOST);
        logDebug("SMTP Port: " . SMTP_PORT);
        logDebug("SMTP Username: " . SMTP_USERNAME);
        logDebug("SMTP From Email: " . SMTP_FROM_EMAIL);
        logDebug("SMTP Secure: " . SMTP_SECURE);
        
        // Capture SMTP debug output
        $mail->Debugoutput = function($str, $level) {
            logDebug("Debug level $level: $str");
        };
        
        // Server settings
        $mail->SMTPDebug = 0;                      // Disable debug output for production
        $mail->isSMTP();                           // Send using SMTP
        $mail->Host       = SMTP_HOST;             // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                  // Enable SMTP authentication
        $mail->Username   = SMTP_USERNAME;         // SMTP username
        $mail->Password   = SMTP_PASSWORD;         // SMTP password
        $mail->SMTPSecure = SMTP_SECURE;           // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
        $mail->Port       = SMTP_PORT;             // TCP port to connect to
        
        // Additional settings for Hostinger
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->Timeout = 60; // Set a longer timeout (in seconds)
        
        // Recipients
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($email);                 // Add a recipient
        
        // Content
        $mail->isHTML(true);                       // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $message)); // Plain text version
        
        // Send email
        $mail->send();
        logDebug("Email sent successfully to: " . $email);
        
        // Save to subscribers table if exists
        try {
            $insert_query = "INSERT INTO coupon_request (email, coupon) VALUES ('$email', '$coupon_code')";
            if (!mysqli_query($orek, $insert_query)) {
                logDebug("Database error: " . mysqli_error($orek));
                // Continue even if database insert fails
            }
            
            // Clear any output buffer before sending JSON response
            if (ob_get_length()) ob_end_clean();
            
            // Send success response
            echo json_encode(['success' => true, 'message' => 'Coupon code sent to your email']);
        } catch (Exception $e) {
            logDebug("Database Error: " . $e->getMessage());
            
            // Clear any output buffer before sending JSON response
            if (ob_get_length()) ob_end_clean();
            
            // Still return success since email was sent
            echo json_encode(['success' => true, 'message' => 'Coupon code sent to your email']);
        }
    } catch (Exception $e) {
        logDebug("PHPMailer Error: " . $mail->ErrorInfo);
        
        // Clear any output buffer before sending JSON response
        if (ob_get_length()) ob_end_clean();
        
        echo json_encode(['success' => false, 'message' => 'Failed to send email. Please try again later.']);
    }
    
    // Close connection
    mysqli_close($orek);
} else {
    // Not a POST request
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
// End script execution
exit;
?> 