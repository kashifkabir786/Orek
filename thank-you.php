<?php require_once('Connections/orek.php'); ?>
<?php
session_start();
// Get transaction ID from URL
$txn_id = isset($_GET['txn_id']) ? $_GET['txn_id'] : '';

// Get payment details
$payment_query = "SELECT p.*, c.date as order_date 
                 FROM payment p 
                 JOIN cart c ON p.cart_id = c.cart_id 
                 WHERE p.txn_id = '$txn_id'";
$payment_result = mysqli_query($orek, $payment_query);
$payment_data = mysqli_fetch_assoc($payment_result);

// Get order items
$items_query = "SELECT ci.*, i.item_name, i.image_1, i.price, i.discount 
               FROM cart_item ci 
               JOIN item i ON ci.item_id = i.item_id 
               WHERE ci.cart_id = '{$payment_data['cart_id']}'";
$items_result = mysqli_query($orek, $items_query);
?>
<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>Orek - Thank You</title>
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
                                    <li class="breadcrumb-item"><a href="product-list.php">shop</a></li>
                                    <li class="breadcrumb-item"><a href="checkout.php">checkout</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">thank you</li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- breadcrumb area end -->

        <!-- thank you page content start -->
        <div class="thank-you-page section-padding">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <div class="thank-you-wrap text-center">
                            <div class="thank-you-icon mb-4">
                                <i class="fa fa-check-circle text-success" style="font-size: 80px;"></i>
                            </div>
                            <h3 class="mb-3">Thank You! Your Order Has Been Placed Successfully</h3>
                            <p class="mb-4">Your order has been confirmed and will be shipped according to the chosen
                                delivery method.</p>

                            <?php if ($payment_data): ?>
                            <div class="order-details mt-5">
                                <h5 class="text-left mb-4">Order Details</h5>
                                <div class="order-info-table table-responsive">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Order ID:</th>
                                            <td><?php echo $payment_data['cart_id']; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Transaction ID:</th>
                                            <td><?php echo $payment_data['txn_id']; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Order Date:</th>
                                            <td><?php echo date('d M Y, h:i A', strtotime($payment_data['order_date'])); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Payment Method:</th>
                                            <td>Online Payment (Credit/Debit Card/Net Banking)</td>
                                        </tr>
                                        <tr>
                                            <th>Payment Amount:</th>
                                            <td>₹<?php echo round($payment_data['amount']); ?></td>
                                        </tr>
                                    </table>
                                </div>

                                <h5 class="text-left mb-4 mt-5">Ordered Items</h5>
                                <div class="ordered-items-table table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Quantity</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $subtotal = 0;
                                            while($item = mysqli_fetch_assoc($items_result)) { 
                                                $item_total = $item['amount'] * $item['qnty']; 
                                                $subtotal += $item_total;
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="assets/img/item/<?php echo $item['image_1']; ?>"
                                                            alt="<?php echo $item['item_name']; ?>"
                                                            style="width: 50px; margin-right: 10px;">
                                                        <?php echo $item['item_name']; ?>
                                                    </div>
                                                </td>
                                                <td><?php echo $item['qnty']; ?></td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="thank-you-buttons mt-5">
                                <a href="product-list.php" class="btn btn-sqr mr-3">Continue Shopping</a>
                                <a href="my-account.php" class="btn btn-sqr">My Account</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- thank you page content end -->
    </main>

    <!-- Scroll to top start -->
    <div class="scroll-top not-visible">
        <i class="fa fa-angle-up"></i>
    </div>
    <!-- Scroll to Top End -->

    <?php require_once('footer.php'); ?>

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
        $('.hero-slider-active').slick({
            arrows: true, // Ensure arrows are enabled
            dots: true,
            autoplay: true,
            autoplaySpeed: 3000,
            prevArrow: '<button type="button" class="slick-prev"><i class="fa fa-chevron-left"></i></button>',
            nextArrow: '<button type="button" class="slick-next"><i class="fa fa-chevron-right"></i></button>',
        });
    });
    </script>
</body>

</html>