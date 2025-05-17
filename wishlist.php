<?php
require_once('Connections/orek.php');
session_start();

if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

if (isset($_POST['item_id'])) {
    $item_id = $_POST['item_id'];
    
    // Get user_id
    $query_user = "SELECT user_id FROM user WHERE email = '{$_SESSION['email']}'";
    $result_user = mysqli_query($orek, $query_user);
    $user = mysqli_fetch_assoc($result_user);
    
    // Check if item already exists in wishlist
    $check_query = "SELECT * FROM wishlist WHERE user_id = {$user['user_id']} AND item_id = {$item_id}";
    $check_result = mysqli_query($orek, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        echo json_encode(['success' => false, 'message' => 'Item already in wishlist']);
        exit;
    }
    
    // Add to wishlist
    $query = "INSERT INTO wishlist (user_id, item_id) VALUES ({$user['user_id']}, {$item_id})";
    if (mysqli_query($orek, $query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}
?>
<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>Orek - Wishlist</title>
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
                                    <li class="breadcrumb-item active" aria-current="page">wishlist</li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- breadcrumb area end -->

        <!-- wishlist main wrapper start -->
        <div class="wishlist-main-wrapper section-padding">
            <div class="container">
                <!-- Wishlist Page Content Start -->
                <div class="section-bg-color">
                    <div class="row">
                        <div class="col-lg-12">
                            <!-- Wishlist Table Area -->
                            <div class="cart-table table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="pro-thumbnail">Thumbnail</th>
                                            <th class="pro-title">Product</th>
                                            <th class="pro-price">Price</th>
                                            <th class="pro-quantity">Stock Status</th>
                                            <th class="pro-subtotal">Add to Cart</th>
                                            <th class="pro-remove">Remove</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                    // Get user_id
                    $query_user = "SELECT user_id FROM user WHERE email = '{$_SESSION['email']}'";
                    $result_user = mysqli_query($orek, $query_user);
                    $user = mysqli_fetch_assoc($result_user);
                    
                    // Get wishlist items
                    $query_wishlist = "SELECT i.*, w.wishlist_id 
                                     FROM wishlist w 
                                     JOIN item i ON w.item_id = i.item_id 
                                     WHERE w.user_id = {$user['user_id']}";
                    $result_wishlist = mysqli_query($orek, $query_wishlist);
                    
                    if(mysqli_num_rows($result_wishlist) > 0) {
                        while($row = mysqli_fetch_assoc($result_wishlist)) {
                            $discounted_price = $row['price'] - ($row['price'] * ($row['discount'] / 100));
                    ?>
                                        <tr>
                                            <td class="pro-thumbnail">
                                                <a href="product-details.php?item_id=<?php echo $row['item_id']; ?>">
                                                    <img class="img-fluid"
                                                        src="assets/img/item/<?php echo $row['image_1']; ?>"
                                                        alt="Product" />
                                                </a>
                                            </td>
                                            <td class="pro-title">
                                                <a href="product-details.php?item_id=<?php echo $row['item_id']; ?>">
                                                    <?php echo $row['item_name']; ?>
                                                </a>
                                            </td>
                                            <td class="pro-price">
                                                <span>₹<?php echo round($discounted_price); ?></span>
                                                <?php if($row['discount'] > 0) { ?>
                                                <del class="text-muted">₹<?php echo round($row['price']); ?></del>
                                                <?php } ?>
                                            </td>
                                            <td class="pro-quantity">
                                                <span
                                                    class="<?php echo ($row['stock_alert'] > 0) ? 'text-success' : 'text-danger'; ?>">
                                                    <?php echo ($row['stock_alert'] > 0) ? 'In Stock' : 'Out of Stock'; ?>
                                                </span>
                                            </td>
                                            <td class="pro-subtotal">
                                                <?php if($row['stock_alert'] > 0) { ?>
                                                <?php
                                                // Check if item is already in cart
                                                $stmt = mysqli_prepare($orek, "CALL CheckItemInCart(?, ?, @is_in_cart)");
                                                mysqli_stmt_bind_param($stmt, "si", $_SESSION['email'], $row['item_id']);
                                                mysqli_stmt_execute($stmt);
                                                mysqli_stmt_close($stmt);
                                                
                                                $result = mysqli_query($orek, "SELECT @is_in_cart as in_cart");
                                                $cart_check = mysqli_fetch_assoc($result);
                                                mysqli_free_result($result);
                                                
                                                if($cart_check['in_cart']): ?>
                                                <a href="cart.php" class="btn btn-sqr">View Cart</a>
                                                <?php else: ?>
                                                <button type="button" class="btn btn-sqr add-to-cart-btn"
                                                    data-item-id="<?php echo $row['item_id']; ?>">
                                                    Add to Cart
                                                </button>
                                                <?php endif; ?>
                                                <?php } else { ?>
                                                <button type="button" class="btn btn-sqr" disabled>
                                                    Out of Stock
                                                </button>
                                                <?php } ?>
                                            </td>
                                            <td class="pro-remove">
                                                <a href="javascript:void(0)"
                                                    onclick="removeFromWishlist(<?php echo $row['wishlist_id']; ?>)">
                                                    <i class="fa fa-trash-o"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php 
                        }
                    } else {
                    ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No items in wishlist</td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Wishlist Page Content End -->
            </div>
        </div>
        <!-- wishlist main wrapper end -->
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
                                        <a href="product-details.php">HasTech</a>
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
    <?php require_once('minicart.php'); ?>

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

    function removeFromWishlist(wishlistId) {
        if (confirm('Are you sure you want to remove this item from wishlist?')) {
            $.ajax({
                url: 'delete-wishlist.php',
                type: 'POST',
                data: {
                    wishlist_id: wishlistId
                },
                success: function(response) {
                    var result = JSON.parse(response);
                    if (result.success) {
                        // Remove the item from DOM
                        const $item = $(`a[onclick="removeFromWishlist(${wishlistId})"]`).closest('tr');
                        $item.fadeOut(300, function() {
                            $(this).remove();

                            // Check if this was the last item
                            if ($('.cart-table tbody tr').length === 1) {
                                // Replace entire wishlist content with empty message
                                $('.cart-table').fadeOut(300, function() {
                                    $(this).html(`
                                    <div class="empty-wishlist-message text-center py-5">
                                        <i class="fa fa-heart fa-4x mb-4 text-muted"></i>
                                        <h4 class="mb-3">Your wishlist is empty</h4>
                                        <p class="text-muted mb-3">No items in your wishlist</p>
                                        <a href="product-list.php" class="btn btn-hero">Continue Shopping</a>
                                    </div>
                                `).fadeIn(300);
                                });

                                // Update wishlist count to 0 when wishlist is empty
                                $(".notification.wishlist-count").text("0").hide();
                            }

                            // Update wishlist count in header
                            let currentCount = parseInt($(".notification.wishlist-count").text()) ||
                                0;
                            if (currentCount > 0) {
                                currentCount--;
                                $(".notification.wishlist-count").text(currentCount);
                                if (currentCount === 0) {
                                    $(".notification.wishlist-count").hide();
                                }
                            }
                        });
                    } else {
                        alert('Error: ' + result.message);
                    }
                },
                error: function() {
                    alert('Error connecting to server');
                }
            });
        }
    }

    $(document).ready(function() {
        $(document).on('click', '.add-to-cart-btn', function(e) {
            e.preventDefault();
            const button = $(this);
            const itemId = button.data('item-id');

            button.html('<i class="fa fa-spinner fa-spin"></i> Adding...');
            button.prop('disabled', true);

            $.ajax({
                url: 'cart_operations.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'add_to_cart',
                    item_id: itemId,
                    qnty: 1
                },
                success: function(response) {
                    if (response.success) {
                        // Store success message in session storage
                        sessionStorage.setItem('cartMessage',
                            'Item added to cart successfully!');
                        // Refresh the page
                        window.location.reload();
                    } else {
                        button.html('Add to Cart');
                        button.prop('disabled', false);
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    button.html('Add to Cart');
                    button.prop('disabled', false);
                    alert('Connection error. Please try again.');
                }
            });
        });

        // Check for message on page load
        if (sessionStorage.getItem('cartMessage')) {
            // Show message above table
            $('.cart-table').before('<div class="alert alert-success text-center mb-4">' + sessionStorage
                .getItem('cartMessage') + '</div>');
            // Remove message from session storage
            sessionStorage.removeItem('cartMessage');
            // Remove message after 3 seconds
            setTimeout(function() {
                $('.alert').fadeOut(300, function() {
                    $(this).remove();
                });
            }, 3000);
        }
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