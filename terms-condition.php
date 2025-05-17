<?php require_once('Connections/orek.php'); ?>
<?php require_once('session-2.php'); ?>

<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>Orek - Terms & Conditons</title>
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
                                    <li class="breadcrumb-item active" aria-current="page">Terms & Conditions</li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- breadcrumb area end -->

        <!-- terms and conditions area start -->
        <section class="terms-conditions section-padding">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="terms-content">
                            <h2 class="title text-center mb-4">Terms and Conditions</h2>

                            <div class="terms-section mb-4">
                                <h4 class="mb-4"> Shopping At Orek</h4>
                                <p>
                                    At Orek, we prioritize security above all else, ensuring that every transaction on
                                    our site is protected and seamless for our valued customers. Our commitment to
                                    transparency is unwavering, guiding every step of the customer journey with clear
                                    and open policies. We take pride in offering our customers peace of mind with a
                                    lifetime buyback policy on all jewelry purchases, safeguarding against tarnish for
                                    years to come. Furthermore, all Orek jewelry is crafted to be 100% safe to wear in
                                    water, so you can enjoy your pieces without worry. With an eye for detail and
                                    excellence, Orek delivers beautifully crafted jewellery including rings, earrings,
                                    pendants, necklaces, bracelets, chains, designed to make every moment unforgettable.
                                </p>
                            </div>

                            <div class="terms-section mb-4">
                                <h4 class="mb-4">1. Shipping & Handling FAQ's</h4>
                                <p>A. How long will it take for my order to ship?</p>
                                <p>Orders are processed and shipped within 24 - 48 working hours. Please note, some
                                    orders, such as pre-order items, may take longer to ship.</p>
                                <p>B. How long does delivery take within India?</p>
                                <p>For domestic orders, deliveries are made within 3 - 5 working days. We offer free
                                    shipping on all prepaid domestic orders.</p>
                                <p>C. How long will international orders take to be delivered?</p>
                                <p>International orders can take up to 18-20 working days for delivery, depending on
                                    your location.</p>
                                <p>D. What should I do if my package is damaged or tampered with?</p>
                                <p>If you receive a product in poor condition or if the packaging is damaged or tampered
                                    with upon delivery, please refuse the delivery and do not accept the package.
                                    Immediately contact our customer care team with your order ID, and we will arrange
                                    for a brand-new replacement at no additional cost.</p>
                                <p>E. What payment methods do you accept?</p>
                                <p>We accept all major VISA and CC Avenue credit/debit cards through our secure payment
                                    gateway. Payment is accepted only in INR (Indian National Rupee) at the time of
                                    purchase. All duties and taxes are included in the price.</p>
                                <p>F. When will I need to self-ship the product back to Orek?</p>
                                <p>In rare cases, a courier may not offer reverse pick-up services. If that happens, you
                                    will need to return the product to us. Please get in touch with our care team, and
                                    we will provide you with the return address and any necessary instructions.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- terms and conditions area end -->
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
    <?php require_once('minicart.php'); ?>

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

    function removeCartItem(cartId, itemId) {
        $.ajax({
            url: 'cart_operations.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'remove',
                cart_id: cartId,
                item_id: itemId
            },
            success: function(response) {
                if (response.success) {
                    // Remove the item from DOM
                    const $item = $(`button[onclick="removeCartItem(${cartId}, ${itemId})"]`).closest(
                        '.minicart-item');
                    $item.fadeOut(300, function() {
                        $(this).remove();

                        // Check if this was the last item
                        if ($('.minicart-item').length === 0) {
                            // Replace entire minicart content with empty cart message
                            $('.minicart-content-box').fadeOut(300, function() {
                                $(this).html(`
                                <div class="empty-cart-message text-center py-5">
                                    <i class="fa fa-shopping-cart fa-4x mb-4 text-muted"></i>
                                    <h4 class="mb-3">Your cart is empty</h4>
                                    <p class="text-muted mb-3">No products added to the cart</p>
                                    <a href="product-list.php" class="btn btn-hero">Continue Shopping</a>
                                </div>
                            `).fadeIn(300);
                            });

                            // Update cart count to 0 when cart is empty
                            $(".notification").each(function() {
                                if ($(this).closest("a").find(
                                        ".fa-bag-shopping, .pe-7s-shopbag").length) {
                                    $(this).text("0");
                                }
                            });
                        } else {
                            // Update totals
                            updateMiniCart();
                        }
                    });
                } else {
                    alert('Error removing item from cart');
                }
            },
            error: function() {
                alert('Error connecting to server');
            }
        });
    }
    </script>
</body>

</html>