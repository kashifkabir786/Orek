<?php
session_start();
$flag = false;
if ( isset( $_GET[ 'page' ] ) && isset( $_GET[ 'id' ] ) ) {
    unset( $_SESSION[ 'email' ] );
    unset( $_SESSION[ 'ERRMSG_ARR' ] );
    unset( $_SESSION[ 'login_fail' ] );

    $flag = true;
}
?>
<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>Orek - Login</title>
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
                                    <li class="breadcrumb-item active" aria-current="page">login</li>
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
                                <?php
                                if ( isset( $_SESSION[ 'ERRMSG_ARR' ] ) ) {
                                    foreach ( $_SESSION[ 'ERRMSG_ARR' ] as $alert ) {
                                        echo "<p class='text-danger'>$alert</p>\n";
                                    }
                                }
                                ?>
                                <?php
                                if ( isset( $_SESSION[ 'login_fail' ] ) ) {
                                    $alert = $_SESSION[ 'login_fail' ];
                                    echo "<p class='text-danger'>$alert</p>\n";
                                }
                                ?>
                                <?php
                                if ( isset( $_GET[ 'sucess' ] ) && $_GET[ 'sucess' ] == "yes" )
                                    echo '<p class="text-success">Your passowrd is successfully changed. Login with your new password</p>';
                                ?>
                                <h5>Sign In</h5>
                                <form action="login-exec.php" method="post" role="form" name="form1">
                                    <div class="single-input-item">
                                        <input type="email" name="email" placeholder="Email or Username" required />
                                    </div>
                                    <div class="single-input-item">
                                        <input type="password" name="password" placeholder="Enter your Password"
                                            required />
                                    </div>
                                    <div class="single-input-item">
                                        <div
                                            class="login-reg-form-meta d-flex align-items-center justify-content-between">
                                            <div class="remember-meta">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="rememberMe">
                                                    <label class="custom-control-label" for="rememberMe">Remember
                                                        Me</label>
                                                </div>
                                            </div>
                                            <a href="forgot_password.php" class="forget-pwd">Forgot Password?</a>
                                        </div>
                                    </div>
                                    <div class="single-input-item">
                                        <button type="submit" class="btn btn-sqr">Login</button>
                                    </div>
                                </form>
                                <p class="mt-3">Don't have an account? <a href="register.php">Register</a></p>
                            </div>
                        </div>
                        <!-- Login Content End -->
                    </div>
                </div>
            </div>
        </div>
        <!-- login register wrapper end -->
    </main>

    <!-- Scroll to top start -->
    <div class="scroll-top not-visible">
        <i class="fa fa-angle-up"></i>
    </div>
    <!-- Scroll to Top End -->

    <!-- footer area start -->
    <?php require_once('footer.php'); ?>
    <!-- footer area end -->

    <!-- Quick view modal start -->
    <div class="modal" id="quick_view">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-bs-dismiss="modal">
                        &times;
                    </button>
                </div>
                <div class="modal-body">
                    <!-- product details inner end -->
                    <div class="product-details-inner">
                        <div class="row">
                            <div class="col-lg-5">
                                <div class="product-large-slider">
                                    <div class="pro-large-img img-zoom">
                                        <img src="assets/img/product/product-details-img1.jpg" alt="product-details" />
                                    </div>
                                    <div class="pro-large-img img-zoom">
                                        <img src="assets/img/product/product-details-img2.jpg" alt="product-details" />
                                    </div>
                                    <div class="pro-large-img img-zoom">
                                        <img src="assets/img/product/product-details-img3.jpg" alt="product-details" />
                                    </div>
                                    <div class="pro-large-img img-zoom">
                                        <img src="assets/img/product/product-details-img4.jpg" alt="product-details" />
                                    </div>
                                    <div class="pro-large-img img-zoom">
                                        <img src="assets/img/product/product-details-img5.jpg" alt="product-details" />
                                    </div>
                                </div>
                                <div class="pro-nav slick-row-10 slick-arrow-style">
                                    <div class="pro-nav-thumb">
                                        <img src="assets/img/product/product-details-img1.jpg" alt="product-details" />
                                    </div>
                                    <div class="pro-nav-thumb">
                                        <img src="assets/img/product/product-details-img2.jpg" alt="product-details" />
                                    </div>
                                    <div class="pro-nav-thumb">
                                        <img src="assets/img/product/product-details-img3.jpg" alt="product-details" />
                                    </div>
                                    <div class="pro-nav-thumb">
                                        <img src="assets/img/product/product-details-img4.jpg" alt="product-details" />
                                    </div>
                                    <div class="pro-nav-thumb">
                                        <img src="assets/img/product/product-details-img5.jpg" alt="product-details" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="product-details-des">
                                    <div class="manufacturer-name">
                                        <a href="product-details.html">HasTech</a>
                                    </div>
                                    <h3 class="product-name">Handmade Golden Necklace</h3>
                                    <div class="ratings d-flex">
                                        <span><i class="fa fa-star-o"></i></span>
                                        <span><i class="fa fa-star-o"></i></span>
                                        <span><i class="fa fa-star-o"></i></span>
                                        <span><i class="fa fa-star-o"></i></span>
                                        <span><i class="fa fa-star-o"></i></span>
                                        <div class="pro-review">
                                            <span>1 Reviews</span>
                                        </div>
                                    </div>
                                    <div class="price-box">
                                        <span class="price-regular">$70.00</span>
                                        <span class="price-old"><del>$90.00</del></span>
                                    </div>
                                    <h5 class="offer-text">
                                        <strong>Hurry up</strong>! offer ends in:
                                    </h5>
                                    <div class="product-countdown" data-countdown="2022/12/20"></div>
                                    <div class="availability">
                                        <i class="fa fa-check-circle"></i>
                                        <span>200 in stock</span>
                                    </div>
                                    <p class="pro-desc">
                                        Lorem ipsum dolor sit amet, consetetur sadipscing elitr,
                                        sed diam nonumy eirmod tempor invidunt ut labore et dolore
                                        magna.
                                    </p>
                                    <div class="quantity-cart-box d-flex align-items-center">
                                        <h6 class="option-title">qty:</h6>
                                        <div class="quantity">
                                            <div class="pro-qty">
                                                <input type="text" value="1" />
                                            </div>
                                        </div>
                                        <div class="action_link">
                                            <a class="btn btn-cart2" href="#">Add to cart</a>
                                        </div>
                                    </div>
                                    <div class="useful-links">
                                        <a href="#" data-bs-toggle="tooltip" title="Compare"><i
                                                class="pe-7s-refresh-2"></i>compare</a>
                                        <a href="#" data-bs-toggle="tooltip" title="Wishlist"><i
                                                class="pe-7s-like"></i>wishlist</a>
                                    </div>
                                    <div class="like-icon">
                                        <a class="facebook" href="#"><i class="fa fa-facebook"></i>like</a>
                                        <a class="twitter" href="#"><i class="fa fa-twitter"></i>tweet</a>
                                        <a class="pinterest" href="#"><i class="fa fa-pinterest"></i>save</a>
                                        <a class="google" href="#"><i class="fa fa-google-plus"></i>share</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- product details inner end -->
                </div>
            </div>
        </div>
    </div>
    <!-- Quick view modal end -->

    <!-- offcanvas mini cart start -->
    <div class="offcanvas-minicart-wrapper">
        <div class="minicart-inner">
            <div class="offcanvas-overlay"></div>
            <div class="minicart-inner-content">
                <div class="minicart-close">
                    <i style="font-size:35px;" class="fa-solid fa-xmark"></i>
                </div>
                <div class="minicart-content-box">
                    <?php
                    if(isset($_SESSION['email'])) {
                        $cart_items_query = "SELECT ci.*, i.*, ci.qnty as cart_quantity 
                                           FROM cart_item ci 
                                           JOIN cart c ON ci.cart_id = c.cart_id 
                                           JOIN item i ON ci.item_id = i.item_id 
                                           WHERE c.user_id = (SELECT user_id FROM user WHERE email = '{$_SESSION['email']}') 
                                           AND c.status = 'Pending'";
                        $cart_items_result = mysqli_query($orek, $cart_items_query);
                        
                        if(mysqli_num_rows($cart_items_result) > 0) {
                            $total = 0;
                    ?>
                    <div id="cart-items-container">
                        <div class="minicart-item-wrapper">
                            <ul>
                                <?php
                                while($cart_item = mysqli_fetch_assoc($cart_items_result)) {
                                    $subtotal = $cart_item['price'] * $cart_item['cart_quantity'];
                                    $total += $subtotal;
                                ?>
                                <li class="minicart-item">
                                    <div class="minicart-thumb">
                                        <a href="product-details.php?item_id=<?php echo $cart_item['item_id']; ?>">
                                            <img src="assets/img/item/<?php echo $cart_item['image_1']; ?>"
                                                alt="product" />
                                        </a>
                                    </div>
                                    <div class="minicart-content">
                                        <h3 class="product-name">
                                            <a href="product-details.php?item_id=<?php echo $cart_item['item_id']; ?>">
                                                <?php echo $cart_item['item_name']; ?>
                                            </a>
                                        </h3>
                                        <p>
                                            <span class="cart-quantity"><?php echo $cart_item['cart_quantity']; ?>
                                                <strong>&times;</strong></span>
                                            <span class="cart-price">₹<?php echo round($cart_item['price']); ?></span>
                                        </p>
                                    </div>
                                    <button class="minicart-remove"
                                        onclick="removeCartItem(<?php echo $cart_item['cart_id']; ?>, <?php echo $cart_item['item_id']; ?>)">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </li>
                                <?php 
                                }
                                $_SESSION['cart_total'] = $total;
                                ?>
                            </ul>
                        </div>

                        <div class="minicart-pricing-box">
                            <ul>
                                <li>
                                    <span>sub-total</span>
                                    <span><strong>₹<?php echo isset($_SESSION['cart_total']) ? round($_SESSION['cart_total'], 2) : '0.00'; ?></strong></span>
                                </li>
                                <li>
                                    <span>Shipping</span>
                                    <span>
                                        <?php if(isset($_SESSION['cart_total']) && $_SESSION['cart_total'] >= 1500): ?>
                                        <strong class="text-success">Free Shipping</strong>
                                        <small class="d-block text-muted">Orders above ₹1500</small>
                                        <?php else: ?>
                                        <strong>₹<?php echo isset($_SESSION['cart_total']) && $_SESSION['cart_total'] > 0 ? '50.00' : '0.00'; ?></strong>
                                        <?php endif; ?>
                                    </span>
                                </li>
                                <li class="total">
                                    <span>total</span>
                                    <span><strong>₹<?php echo isset($_SESSION['cart_total']) ? 
                                        ($_SESSION['cart_total'] >= 1500 ? 
                                            round($_SESSION['cart_total'], 2) : 
                                            round($_SESSION['cart_total'] + 50, 2)) : 
                                        '0.00'; ?></strong></span>
                                </li>
                            </ul>
                        </div>

                        <div class="minicart-button">
                            <a href="cart.php"><i class="fa fa-shopping-cart"></i> View Cart</a>
                            <a href="checkout.php"><i class="fa fa-share"></i> Checkout</a>
                        </div>
                    </div>
                    <?php
                        } else {
                    ?>
                    <div class="empty-cart-message text-center py-5">
                        <i class="fa fa-shopping-cart fa-4x mb-4 text-muted"></i>
                        <h4 class="mb-3">Your cart is empty</h4>
                        <p class="text-muted mb-3">No products added to the cart</p>
                        <a href="product-list.php" class="btn btn-hero">Continue Shopping</a>
                    </div>
                    <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <!-- offcanvas mini cart end -->

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