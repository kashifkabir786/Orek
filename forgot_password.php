<?php
session_start();
require_once('Connections/orek.php');
require_once('lib/smtp_config.php');
require_once('lib/PHPMailer/src/Exception.php');
require_once('lib/PHPMailer/src/PHPMailer.php');
require_once('lib/PHPMailer/src/SMTP.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($orek, $_POST['email']);
    
    // Check if email exists
    $query = "SELECT * FROM user WHERE email = ?";
    $stmt = mysqli_prepare($orek, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        // Generate unique token
        $token = bin2hex(random_bytes(32));
        
        // Store token in database
        $insert_query = "INSERT INTO password_resets (email, token, created_at) VALUES (?, ?, NOW())";
        $insert_stmt = mysqli_prepare($orek, $insert_query);
        mysqli_stmt_bind_param($insert_stmt, "ss", $email, $token);
        
        if (mysqli_stmt_execute($insert_stmt)) {
            // Send reset email
            try {
                $mail = new PHPMailer(true);
                
                // Server settings
                $mail->SMTPDebug = 0;
                $mail->isSMTP();
                $mail->Host = SMTP_HOST;
                $mail->SMTPAuth = true;
                $mail->Username = SMTP_USERNAME;
                $mail->Password = SMTP_PASSWORD;
                $mail->SMTPSecure = SMTP_SECURE;
                $mail->Port = SMTP_PORT;
                
                $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
                $mail->addAddress($email);
                
                // Content
                $reset_link = "https://orek.in/reset_password.php?token=" . $token;
                
                $mail->isHTML(true);
                $mail->Subject = 'Reset Your Password - Orek';
                $mail->Body = '
                <!DOCTYPE html>
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background-color: #c29958; color: white; padding: 20px; text-align: center; }
                        .content { padding: 20px; background-color: #f9f9f9; }
                        .button { display: inline-block; padding: 10px 20px; background-color: #c29958; color: white; text-decoration: none; border-radius: 5px; }
                        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <div class="header">
                            <img src="https://orek.in/assets/img/logo/logo.png" alt="Orek Logo" style="max-height: 60px;">
                        </div>
                        <div class="content">
                            <h2>Password Reset Request</h2>
                            <p>Hello,</p>
                            <p>We received a request to reset your password. Click the button below to create a new password:</p>
                            <p style="text-align: center;">
                                <a href="' . $reset_link . '" class="button">Reset Password</a>
                            </p>
                            <p>If you did not request this password reset, please ignore this email.</p>
                            <p>This link will expire in 24 hours.</p>
                            <p>Best regards,<br>The Orek Team</p>
                        </div>
                        <div class="footer">
                            <p>&copy; ' . date('Y') . ' Orek. All rights reserved.</p>
                        </div>
                    </div>
                </body>
                </html>';
                
                $mail->send();
                $success = true;
                $message = "Password reset instructions have been sent to your email.";
            } catch (Exception $e) {
                $message = "Error sending email. Please try again later.";
            }
        } else {
            $message = "Error processing request. Please try again.";
        }
    } else {
        $message = "No account found with this email address.";
    }
}
?>
<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>Orek - Forgot Password</title>
    <meta name="robots" content="noindex, follow" />
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
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
                                    <li class="breadcrumb-item active" aria-current="page">forgot password</li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- breadcrumb area end -->

        <!-- login register wrapper start -->
        <div class="login-register-wrapper section-padding">
            <div class="container">
                <div class="member-area-from-wrap">
                    <div class="row">
                        <!-- Login Content Start -->
                        <div class="col-lg-12">
                            <div class="login-reg-form-wrap">
                                <?php if ($message): ?>
                                    <p class="<?php echo $success ? 'text-success' : 'text-danger'; ?>"><?php echo $message; ?></p>
                                <?php endif; ?>

                                <h5>Forgot Password</h5>
                                <p class="mb-3">Enter your email address below and we'll send you instructions to reset your password.</p>
                                <form action="" method="post" role="form">
                                    <div class="single-input-item">
                                        <input type="email" name="email" placeholder="Enter your email" required />
                                    </div>
                                    <div class="single-input-item">
                                        <button type="submit" class="btn btn-sqr">Send Reset Link</button>
                                    </div>
                                </form>
                                <p class="mt-3">Remember your password? <a href="login.php">Login</a></p>
                            </div>
                        </div>
                        <!-- Login Content End -->
                    </div>
                </div>
            </div>
        </div>
        <!-- login register wrapper end -->
    </main>

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
    <!-- Main JS -->
    <script src="assets/js/main.js"></script>
</body>

</html> 