<?php
require_once('Connections/orek.php');
require_once('session-2.php');

// Initialize variables
$cart_items = null;
$total = 0;
$cart_data = [];
$regular_total = 0;

// Only fetch cart items if user is logged in
if(isset($_SESSION['email'])) {
    try {
        $email = mysqli_real_escape_string($orek, $_SESSION['email']);
        
        // Get User ID first
        $user_query = "SELECT user_id FROM user WHERE email = '$email' LIMIT 1";
        $user_result = mysqli_query($orek, $user_query);
        
        if (!$user_result) {
            throw new Exception("Error getting user: " . mysqli_error($orek));
        }
        
        if (mysqli_num_rows($user_result) > 0) {
            $user_row = mysqli_fetch_assoc($user_result);
            $user_id = $user_row['user_id'];
            
            // Get active cart ID
            $cart_query = "SELECT cart_id FROM cart WHERE user_id = '$user_id' AND status = 'Pending' LIMIT 1";
            $cart_result = mysqli_query($orek, $cart_query);
            
            if (!$cart_result) {
                throw new Exception("Error getting cart: " . mysqli_error($orek));
            }
            
            if (mysqli_num_rows($cart_result) > 0) {
                $cart_row = mysqli_fetch_assoc($cart_result);
                $cart_id = $cart_row['cart_id'];
                
                // Get cart items with optimized query
                $items_query = "SELECT ci.cart_id, ci.item_id, ci.qnty, 
    i.item_name, i.price, i.discount, i.image_1, i.listing_status,
    CASE 
        WHEN i.listing_status = 'Gift' THEN 0
        ELSE (i.price * (1 - i.discount/100) * ci.qnty)
    END as amount
    FROM cart_item ci 
    JOIN item i ON ci.item_id = i.item_id 
    JOIN cart c ON ci.cart_id = c.cart_id
    WHERE ci.cart_id = '$cart_id' AND c.status = 'Pending'";
                
                $cart_items = mysqli_query($orek, $items_query);
                
                if (!$cart_items) {
                    throw new Exception("Error getting cart items: " . mysqli_error($orek));
                }

                // Calculate totals
                if (mysqli_num_rows($cart_items) > 0) {
                    while ($item = mysqli_fetch_assoc($cart_items)) {
                        if ($item['listing_status'] != 'Gift') {
                            $total += $item['amount'];
                            $regular_total += $item['amount'];
                        }
                        $cart_data[] = $item;
                    }
                    // Reset pointer for later use
                    mysqli_data_seek($cart_items, 0);
                }
            }
        }
    } catch (Exception $e) {
        error_log("Cart error: " . $e->getMessage());
        echo '<div class="alert alert-danger">Error loading cart. Please try again.</div>';
    }
}
?>

<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>Orek - Shopping Cart</title>
    <meta name="robots" content="index, follow" />
    <meta name="description"
        content="View and modify your shopping cart at Orek. Adjust quantities, apply coupon codes, and proceed to checkout." />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Performance: DNS Prefetch for external domains -->
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/logo/logo.png" />

    <!-- Critical CSS inlined for faster initial render -->
    <style>
    /* Critical CSS for above-the-fold content */
    body {
        font-family: 'Lato', sans-serif;
        margin: 0;
        padding: 0;
    }

    .header-area {
        position: relative;
        z-index: 100;
    }

    .container {
        width: 100%;
        padding-right: 15px;
        padding-left: 15px;
        margin-right: auto;
        margin-left: auto;
    }

    @media (min-width: 576px) {
        .container {
            max-width: 540px;
        }
    }

    @media (min-width: 768px) {
        .container {
            max-width: 720px;
        }
    }

    @media (min-width: 992px) {
        .container {
            max-width: 960px;
        }
    }

    @media (min-width: 1200px) {
        .container {
            max-width: 1140px;
        }
    }

    .row {
        display: flex;
        flex-wrap: wrap;
        margin-right: -15px;
        margin-left: -15px;
    }

    .col-12,
    .col-lg-12,
    .col-lg-5 {
        position: relative;
        width: 100%;
        padding-right: 15px;
        padding-left: 15px;
    }

    .col-12 {
        flex: 0 0 100%;
        max-width: 100%;
    }

    @media (min-width: 992px) {
        .col-lg-5 {
            flex: 0 0 41.666667%;
            max-width: 41.666667%;
        }

        .col-lg-12 {
            flex: 0 0 100%;
            max-width: 100%;
        }

        .ml-auto {
            margin-left: auto !important;
        }
    }

    .breadcrumb-area {
        padding: 15px 0;
        background-color: #f5f5f5;
    }

    .breadcrumb {
        display: flex;
        flex-wrap: wrap;
        padding: 0;
        margin-bottom: 0;
        list-style: none;
    }

    .breadcrumb-item {
        display: inline-block;
        margin-right: 5px;
    }

    .breadcrumb-item.active {
        color: #333;
    }

    .section-padding {
        padding: 30px 0;
    }

    .cart-table {
        margin-bottom: 20px;
    }

    .cart-headers {
        display: none;
    }

    @media (min-width: 768px) {
        .cart-headers {
            display: block;
            background-color: #f5f5f5;
            padding: 10px 15px;
            margin-bottom: 15px;
        }
    }

    .cart-item {
        display: flex;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid #ddd;
    }

    .empty-cart-message {
        text-align: center;
        padding: 30px 0;
    }

    .btn-sqr {
        background-color: #333;
        color: #fff;
        padding: 10px 20px;
        display: inline-block;
        text-decoration: none;
    }

    /* Fix for cart item display on mobile */
    @media (max-width: 767px) {
        .cart-item {
            flex-wrap: wrap;
        }

        .product-details {
            flex: 0 0 100%;
            margin-bottom: 15px;
        }

        .item-quantity,
        .item-price,
        .item-total {
            flex: 1;
            margin: 0 5px;
        }

        .item-remove {
            flex: 0 0 auto;
        }
    }
    </style>

    <!-- Preload critical fonts -->
    <link rel="preload" href="https://fonts.googleapis.com/css?family=Lato:300,300i,400,400i,700,900&display=swap"
        as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link href="https://fonts.googleapis.com/css?family=Lato:300,300i,400,400i,700,900&display=swap"
            rel="stylesheet">
    </noscript>

    <!-- Non-critical CSS loaded asynchronously -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="assets/css/pe-icon-7-stroke.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
        media="print" onload="this.media='all'">
    <link rel="stylesheet" href="assets/css/slick.min.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="assets/css/animate.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="assets/css/nice-select.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="assets/css/jqueryui.min.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="assets/css/style.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="assets/css/custom.css" media="print" onload="this.media='all'">

    <!-- Fallback for browsers without JavaScript -->
    <noscript>
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="assets/css/pe-icon-7-stroke.css">
        <link rel="stylesheet" href="assets/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link rel="stylesheet" href="assets/css/slick.min.css">
        <link rel="stylesheet" href="assets/css/animate.css">
        <link rel="stylesheet" href="assets/css/nice-select.css">
        <link rel="stylesheet" href="assets/css/jqueryui.min.css">
        <link rel="stylesheet" href="assets/css/style.css">
        <link rel="stylesheet" href="assets/css/custom.css">
    </noscript>
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
                                    <li class="breadcrumb-item active" aria-current="page">cart</li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- breadcrumb area end -->

        <!-- cart main wrapper start -->
        <div class="cart-main-wrapper section-padding">
            <div class="container">
                <div class="section-bg-color">
                    <div class="row">
                        <div class="col-lg-12">
                            <div id="cart-message" class="alert" style="display: none; margin-bottom: 20px;">
                            </div>
                            <!-- Cart Table Area -->
                            <div class="cart-table table-responsive">
                                <!-- Cart Headers -->
                                <div class="cart-headers">
                                    <div class="header-grid">
                                        <div class="header-item">Product Details</div>
                                        <div class="header-item">Price</div>
                                        <div class="header-item">Quantity</div>
                                        <div class="header-item">Total</div>
                                        <div class="header-item"></div>
                                    </div>
                                </div>
                                <!-- Cart Items -->
                                <div class="cart-items-wrapper">
                                    <?php 
                                    if (isset($cart_items) && mysqli_num_rows($cart_items) > 0):
                                        $regular_total = 0;
                                        $has_gift = false;
                                        $gift_item = null;
                                        $cart_data = [];
                                        
                                        // First pass: calculate regular total and check for gifts
                                        mysqli_data_seek($cart_items, 0);
                                        while ($item = mysqli_fetch_assoc($cart_items)):
                                            if ($item['listing_status'] == 'Gift') {
                                                $has_gift = true;
                                                $gift_item = $item;
                                            } else {
                                                $regular_total += $item['amount'];
                                            }
                                            $cart_data[] = $item;
                                        endwhile;

                                        // Check if gift should be removed
                                        if ($has_gift && $regular_total < 999) {
                                            mysqli_query($orek, "DELETE FROM cart_item WHERE cart_id = '{$gift_item['cart_id']}' AND item_id = '{$gift_item['item_id']}'");
                                            echo '<div class="alert alert-warning">Free gift has been removed as cart total is below ₹999.</div>';
                                            // Remove gift from cart data
                                            $cart_data = array_filter($cart_data, function($item) {
                                                return $item['listing_status'] != 'Gift';
                                            });
                                        }

                                        // Display items
                                        foreach ($cart_data as $item):
                                    ?>
                                    <div class="cart-item" data-cart-id="<?php echo $item['cart_id']; ?>"
                                        data-item-id="<?php echo $item['item_id']; ?>">
                                        <div class="product-details">
                                            <div class="item-image">
                                                <a href="product-details.php?item_id=<?php echo $item['item_id']; ?>">
                                                    <img class="img-fluid" width="100" height="100"
                                                        src="assets/img/item/<?php echo $item['image_1']; ?>"
                                                        alt="<?php echo $item['item_name']; ?>" loading="lazy" />
                                                </a>
                                            </div>
                                            <div class="item-info">
                                                <div class="item-name">
                                                    <a
                                                        href="product-details.php?item_id=<?php echo $item['item_id']; ?>">
                                                        <?php echo $item['item_name']; ?>
                                                        <?php if($item['listing_status'] == 'Gift'): ?>
                                                        <span class="badge badge-success">Free Gift</span>
                                                        <?php endif; ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="item-price">
                                            <?php if($item['listing_status'] == 'Gift'): ?>
                                            <span class="current-price text-success">Free</span>
                                            <?php else: ?>
                                            <span class="current-price">₹<?php echo $item['price']; ?></span>
                                            <?php if($item['discount'] > 0): ?>
                                            <span class="discount">-<?php echo $item['discount']; ?>%</span>
                                            <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                        <div class="item-quantity">
                                            <div class="quantity-controls">
                                                <?php if($item['listing_status'] == 'Gift'): ?>
                                                <input type="text" value="1" class="quantity-input" readonly>
                                                <?php else: ?>
                                                <button type="button" class="qty-btn minus">-</button>
                                                <input type="text" value="<?php echo $item['qnty']; ?>"
                                                    class="quantity-input" readonly>
                                                <button type="button" class="qty-btn plus">+</button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="item-total">
                                            <?php if($item['listing_status'] == 'Gift'): ?>
                                            <span class="text-success">Free</span>
                                            <?php else: ?>
                                            <span>₹<?php echo round($item['amount']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="item-remove">
                                            <button class="remove-item-btn">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <?php 
                                        endforeach;
                                    else:
                                    ?>
                                    <div class="empty-cart-message">
                                        <div class="text-center py-4">
                                            <i class="fa fa-shopping-cart fa-4x mb-4 text-muted"></i>
                                            <h4 class="mb-3">Your cart is empty</h4>
                                            <p class="text-muted mb-4">No products added to your shopping cart</p>
                                            <a href="product-list.php" class="btn btn-sqr">Continue Shopping</a>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <!-- Cart Update Option -->

                                <div class="cart-update-option d-block d-md-flex justify-content-between">
                                    <div class="apply-coupon-wrapper">
                                        <?php 
                                    // We should not remove coupon from session when cart page loads
                                    // as it prevents coupon from being applied
                                    /*
                                    unset($_SESSION['coupon_code']);
                                    unset($_SESSION['coupon_discount']);
                                    unset($_SESSION['applied_coupon']);
                                    */
                                    ?>
                                        <form id="coupon-form" class="d-block d-md-flex">
                                            <input type="text" name="coupon_code" id="coupon-code"
                                                placeholder="Enter Your Coupon Code" required />
                                            <button type="submit" class="btn btn-sqr">Apply Coupon</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 gift-banner">
                                <?php 
                                // Calculate total without gifts
                                $cart_total = 0;
                                if (isset($cart_items) && mysqli_num_rows($cart_items) > 0) {
                                    mysqli_data_seek($cart_items, 0);
                                    while ($item = mysqli_fetch_assoc($cart_items)) {
                                        if ($item['listing_status'] != 'Gift') {
                                            $cart_total += round($item['price'] * (1 - $item['discount']/100) * $item['qnty']);
                                        }
                                    }
                                }
                                
                                if($cart_total >= 999): 
                                ?>
                                <div class="gift-offer-section">
                                    <div class="gift-offer-content">
                                        <div class="gift-icon1">
                                            <i class="fas fa-gift"></i>
                                        </div>
                                        <h3 class="gift-title1">Congratulations! 🎉</h3>
                                        <p class="gift-description1">You've unlocked a special gift from Orek! Shop with
                                            us and receive an exclusive free gift with your purchase above ₹999.</p>
                                        <a href="product-list-gift.php" class="view-gifts-btn">
                                            View Your Free Gifts <i class="fas fa-arrow-right ml-2"></i>
                                        </a>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-lg-6 ml-auto">
                                <!-- Cart Calculation Area -->
                                <div class="cart-calculate-items">
                                    <h6>Cart Totals</h6>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <tr class="subtotal-row">
                                                <td>Sub Total</td>
                                                <td class="subtotal-value">₹<?php echo round($total); ?></td>
                                            </tr>
                                            <tr class="shipping-row">
                                                <td>Shipping</td>
                                                <td class="shipping-value">
                                                    <?php if($total >= 1500): ?>
                                                    <span class="text-success">Free Shipping</span>
                                                    <small class="d-block text-muted">Orders above ₹1500</small>
                                                    <?php else: ?>
                                                    ₹<?php echo ($total > 0) ? '50' : '0'; ?>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php if(isset($_SESSION['applied_coupon'])): ?>
                                            <tr class="discount-row">
                                                <td>Coupon Discount
                                                    (<?php echo $_SESSION['applied_coupon']['discount_percentage']; ?>%)
                                                </td>
                                                <td class="discount-value text-danger">-₹<?php 
                                                    // Recalculate total from cart items to ensure accuracy
                                                    $cart_total = 0;
                                                    mysqli_data_seek($cart_items, 0);
                                                    while ($item = mysqli_fetch_assoc($cart_items)) {
                                                        if ($item['listing_status'] != 'Gift') {
                                                            $cart_total += ($item['price'] * (1 - $item['discount']/100) * $item['qnty']);
                                                        }
                                                    }
                                                    $discount_amount = ($cart_total * $_SESSION['applied_coupon']['discount_percentage']) / 100;
                                                    echo round($discount_amount);
                                                    $total = $cart_total - $discount_amount;
                                                ?></td>
                                            </tr>
                                            <?php endif; ?>
                                            <tr class="total">
                                                <td>Total</td>
                                                <td class="total-amount">
                                                    ₹<?php echo round($total + (($total > 0 && $total < 1500) ? 50 : 0)); ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <?php if($total > 0): ?>
                                <a href="checkout.php" class="btn btn-sqr d-block">Proceed Checkout</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- cart main wrapper end -->
    </main>


    <!-- Scroll to top start -->
    <div class="scroll-top not-visible">
        <i class="fa fa-angle-up"></i>
    </div>
    <!-- Scroll to Top End -->

    <!-- footer area start -->
    <?php require_once('footer.php'); ?>
    <!-- footer area end -->

    <!-- jQuery JS (loaded first without defer for critical functionality) -->
    <script src="assets/js/jquery-3.6.0.min.js"></script>

    <!-- Non-critical JS loaded with defer attribute -->
    <script src="assets/js/bootstrap.bundle.min.js" defer></script>
    <script src="assets/js/main.js" defer></script>

    <!-- Cart specific JavaScript -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Wait for jQuery to be available
        if (window.jQuery) {
            initializeCart();
        } else {
            document.addEventListener('jquery-loaded', initializeCart);
        }
    });

    function initializeCart() {
        // Cache DOM elements
        const $cartMessage = $('#cart-message');
        const $couponForm = $('#coupon-form');
        const $couponCode = $('#coupon-code');
        const $cartItems = $('.cart-item');

        // Initialize cart functionality
        setupQuantityButtons();
        setupRemoveButtons();
        setupCouponForm();

        // Handle quantity buttons with debounce to prevent multiple rapid clicks
        function setupQuantityButtons() {
            let debounceTimer;

            $('.qty-btn').on('click', function() {
                // Clear any pending request
                clearTimeout(debounceTimer);

                const $button = $(this);

                // Disable button to prevent multiple clicks
                if ($button.prop('disabled')) return;
                $button.prop('disabled', true);

                const $item = $button.closest('.cart-item');
                const $input = $button.siblings('.quantity-input');
                const oldValue = parseInt($input.val());
                const cartId = $item.data('cart-id');
                const itemId = $item.data('item-id');

                // Calculate new value
                let newValue;
                if ($button.hasClass('plus')) {
                    newValue = oldValue + 1;
                } else {
                    newValue = oldValue > 1 ? oldValue - 1 : 1;
                }

                if (newValue === oldValue) {
                    $button.prop('disabled', false);
                    return;
                } // No change, exit early

                // Update UI first for responsive feel
                $input.val(newValue);

                // Show loading indicator
                showMessage('Updating cart...', true);

                // Use direct AJAX request with simplified payload
                $.ajax({
                    url: 'update_cart.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        cart_id: cartId,
                        item_id: itemId,
                        quantity: newValue
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update item total without page reload
                            $item.find('.item-total span').text('₹' + response.item_total);

                            // Update cart totals
                            $('.subtotal-value').text('₹' + response.subtotal);

                            // Dynamic gift section update with correct targeting
                            const isNowAboveThreshold = parseFloat(response.subtotal) >= 999;
                            const $giftSection = $('.gift-offer-section');
                            const $giftContainer = $('.gift-banner').not('.ml-auto')
                                .first();

                            if (isNowAboveThreshold && $giftSection.length === 0) {
                                const giftHTML = `
                                    <div class="gift-offer-section">
                                        <div class="gift-offer-content">
                                            <div class="gift-icon">
                                                <i class="fas fa-gift"></i>
                                            </div>
                                            <h3 class="gift-title">Congratulations! 🎉</h3>
                                            <p class="gift-description">You've unlocked a special gift from Orek! Shop with us and receive an exclusive free gift with your purchase above ₹999.</p>
                                            <a href="product-list-gift.php" class="view-gifts-btn">
                                                View Your Free Gifts <i class="fas fa-arrow-right ml-2"></i>
                                            </a>
                                        </div>
                                    </div>`;
                                $giftContainer.html(giftHTML);
                            } else if (!isNowAboveThreshold && $giftSection.length > 0) {
                                $giftSection.fadeOut(300, function() {
                                    $(this).remove();
                                });

                                // If there's a gift item in cart, refresh the page
                                if ($('.cart-item .badge-success').length > 0) {
                                    window.location.reload();
                                    return;
                                }
                            }

                            // Update shipping
                            if (parseFloat(response.subtotal) >= 1500) {
                                $('.shipping-value').html(
                                    '<span class="text-success">Free Shipping</span><small class="d-block text-muted">Orders above ₹1500</small>'
                                );
                            } else {
                                $('.shipping-value').text('₹' + (parseFloat(response.subtotal) > 0 ?
                                    '50' : '0'));
                            }

                            // Update total
                            $('.total-amount').text('₹' + response.total);

                            // Show success message
                            showMessage('Cart updated successfully', true);
                            $button.prop('disabled', false);
                        } else {
                            // Revert input value on error
                            $input.val(oldValue);
                            showMessage(response.message || 'Error updating cart', false);
                            $button.prop('disabled', false);
                        }
                    },
                    error: function() {
                        // Revert input value on error
                        $input.val(oldValue);
                        showMessage('Connection error. Please try again.', false);
                        $button.prop('disabled', false);
                    },
                    timeout: 8000 // Reduced timeout for better user experience
                });
            });
        }

        // Handle remove buttons
        function setupRemoveButtons() {
            $('.remove-item-btn').on('click', function() {
                if (confirm('Are you sure you want to remove this item?')) {
                    const $button = $(this);

                    // Disable button to prevent multiple clicks
                    if ($button.prop('disabled')) return;
                    $button.prop('disabled', true);

                    const $item = $button.closest('.cart-item');
                    const cartId = $item.data('cart-id');
                    const itemId = $item.data('item-id');

                    // Show loading indicator
                    showMessage('Removing item...', true);

                    // Use direct AJAX with simplified endpoint
                    $.ajax({
                        url: 'remove_item.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            cart_id: cartId,
                            item_id: itemId
                        },
                        success: function(response) {
                            if (response.success) {
                                showMessage('Item removed successfully', true);

                                // Force page refresh after a short delay
                                setTimeout(function() {
                                    window.location.reload();
                                }, 800);

                            } else {
                                showMessage(response.message || 'Error removing item', false);
                                $button.prop('disabled', false);
                            }
                        },
                        error: function() {
                            showMessage('Connection error. Please try again.', false);
                            $button.prop('disabled', false);
                        },
                        timeout: 8000 // Reduced timeout
                    });
                }
            });
        }

        // Handle coupon form
        function setupCouponForm() {
            $couponForm.on('submit', function(e) {
                e.preventDefault();

                const $submitButton = $(this).find('button[type="submit"]');

                // Disable button to prevent multiple submissions
                if ($submitButton.prop('disabled')) return;
                $submitButton.prop('disabled', true);

                const couponCode = $couponCode.val().trim();
                const subtotal = parseFloat($('.subtotal-value').text().replace('₹', ''));

                if (!couponCode) {
                    showMessage('Please enter a coupon code', false);
                    $submitButton.prop('disabled', false);
                    return;
                }

                // Show loading indicator
                showMessage('Applying coupon...', true);

                $.ajax({
                    url: 'apply_coupon.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        coupon_code: couponCode,
                        subtotal: subtotal
                    },
                    success: function(response) {
                        if (response.success) {
                            showMessage(response.message, true);

                            // Use a timeout to allow message to be shown
                            setTimeout(function() {
                                window.location.reload();
                            }, 800);
                        } else {
                            showMessage(response.message, false);
                            $couponCode.val('').focus();
                            $submitButton.prop('disabled', false);
                        }
                    },
                    error: function() {
                        showMessage('Error applying coupon. Please try again.', false);
                        $submitButton.prop('disabled', false);
                    },
                    timeout: 8000 // Reduced timeout
                });
            });
        }

        // Show message utility
        function showMessage(message, isSuccess) {
            $cartMessage.removeClass('alert-success alert-danger')
                .addClass(isSuccess ? 'alert-success' : 'alert-danger')
                .html(message)
                .fadeIn();

            // Hide message after 3 seconds
            setTimeout(function() {
                $cartMessage.fadeOut();
            }, 3000);
        }
    }
    </script>
</body>

</html>