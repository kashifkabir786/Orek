<?php
session_start();
require_once('Connections/orek.php');

// Check if token is provided
if (!isset($_GET['token'])) {
    header('Location: login.php');
    exit();
}

$token = $_GET['token'];
$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($new_password !== $confirm_password) {
        $message = "Passwords do not match!";
    } else {
        // Verify token and update password
        $query = "SELECT * FROM password_resets WHERE token = ? AND used = 0 AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)";
        $stmt = mysqli_prepare($orek, $query);
        mysqli_stmt_bind_param($stmt, "s", $token);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            // Hash the new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Update user's password
            $update_query = "UPDATE user SET password = ? WHERE email = ?";
            $update_stmt = mysqli_prepare($orek, $update_query);
            mysqli_stmt_bind_param($update_stmt, "ss", $hashed_password, $row['email']);
            
            if (mysqli_stmt_execute($update_stmt)) {
                // Mark token as used
                $mark_used_query = "UPDATE password_resets SET used = 1 WHERE token = ?";
                $mark_used_stmt = mysqli_prepare($orek, $mark_used_query);
                mysqli_stmt_bind_param($mark_used_stmt, "s", $token);
                mysqli_stmt_execute($mark_used_stmt);
                
                header('Location: login.php?sucess=yes');
                exit();
            } else {
                $message = "Error updating password. Please try again.";
            }
        } else {
            $message = "Invalid or expired reset link. Please request a new one.";
        }
    }
}
?>
<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>Orek - Reset Password</title>
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
                                    <li class="breadcrumb-item active" aria-current="page">reset password</li>
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
                                    <p class="text-danger"><?php echo $message; ?></p>
                                <?php endif; ?>

                                <h5>Reset Password</h5>
                                <form action="" method="post" role="form">
                                    <div class="single-input-item">
                                        <input type="password" name="new_password" placeholder="Enter new password" required minlength="6" />
                                    </div>
                                    <div class="single-input-item">
                                        <input type="password" name="confirm_password" placeholder="Confirm new password" required minlength="6" />
                                    </div>
                                    <div class="single-input-item">
                                        <button type="submit" class="btn btn-sqr">Reset Password</button>
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