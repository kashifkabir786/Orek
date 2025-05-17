<?php require_once('Connections/orek.php'); ?>
<?php require_once('session.php'); ?>
<?php

if ( isset( $_GET[ 'cart_id' ] ) ) {
  $cart_id = $_GET[ 'cart_id' ];
  
    $query_Recordset2 = "SELECT 
    A.*, 
    B.amount AS item_amount,  
    B.qnty, B.item_id, B.cart_id,  
    C.created_at, 
    C.status, 
    P.payment_date, 
    P.payment_id, 
    P.amount AS paid_amount,
    P.coupon_discount,  
    P.txn_id, 
    P.payment_mode,
    CASE WHEN B.amount = 0 THEN 'Free Gift' ELSE '' END as item_type
FROM 
    item AS A 
INNER JOIN cart_item AS B ON A.item_id = B.item_id 
INNER JOIN cart AS C ON B.cart_id = C.cart_id 
LEFT JOIN payment P ON C.cart_id = P.cart_id 
WHERE 
    C.cart_id = '$cart_id'
";
    $Recordset2 = mysqli_query($orek, $query_Recordset2) or die(mysqli_error($orek));
    $row_Recordset2 = mysqli_fetch_assoc($Recordset2);
    $totalRows_Recordset2 = mysqli_num_rows($Recordset2);
}

    $Recordset3 = mysqli_query($orek, $query_Recordset2) or die(mysqli_error($orek));
    $row_Recordset3 = mysqli_fetch_assoc($Recordset3);
    $totalRows_Recordset3 = mysqli_num_rows($Recordset3);
    
    // Calculate time difference from payment date
    $order_time = strtotime($row_Recordset3['payment_date']);
    $current_time = time();
    $time_diff = $current_time - $order_time;
    $can_cancel = $time_diff <= 3600; // 3600 seconds = 1 hour

?>

<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>Orek - Order Details</title>
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
                                    <li class="breadcrumb-item active" aria-current="page">Order Details</li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- breadcrumb area end -->

        <!-- my account wrapper start -->
        <div class="my-account-wrapper section-padding">
            <div class="container">
                <div class="section-bg-color">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-lg-12 col-md-8">
                                    <div class="tab-pane fade show active" id="orders" role="tabpanel">
                                        <div class="myaccount-content">
                                            <h5>Order Details</h5>
                                            <div class="orders-wrapper">
                                                <div class="order-card">
                                                    <div class="order-header">
                                                        <div class="order-id">Order ID:
                                                            #<?php echo $row_Recordset2['cart_id']; ?></div>
                                                        <div class="order-date">Ordered on:
                                                            <?php echo date('d M Y', strtotime($row_Recordset3['payment_date'])); ?>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <!-- Order Items Column -->
                                                        <div class="col-md-8 border-end">
                                                            <?php 
                                                            // Calculate totals first
                                                             $total_mrp = 0;
                                                            $total_discount = 0;
                                                            $order_items = array();
                                                            
                                                            mysqli_data_seek($Recordset2, 0);
                                                            while($row = mysqli_fetch_assoc($Recordset2)) {
                                                                if($row['item_amount'] > 0) { // Only calculate for non-free items
                                                                    $total_mrp += ($row['price'] * $row['qnty']);
                                                                    $discount_amount = ($row['price'] * $row['discount']) / 100;
                                                                    $total_discount += ($discount_amount * $row['qnty']);
                                                                }
                                                                $order_items[] = $row;
                                                            }
                                                            ?>
                                                            <?php foreach($order_items as $item): ?>
                                                            <div class="order-body d-flex align-items-center p-3 border-bottom"
                                                                style="gap: 15px;">
                                                                <div class="order-image">
                                                                    <a
                                                                        href="product-details.php?item_id=<?php echo $item['item_id']; ?>">
                                                                        <img src="assets/img/item/<?php echo $item['image_1']; ?>"
                                                                            alt="<?php echo $item['item_name']; ?>"
                                                                            style="width: 60px; height: 60px; object-fit: cover; border-radius: 6px; border: 1px solid #ddd;">
                                                                    </a>
                                                                </div>
                                                                <div class="order-details" style="flex: 1;">
                                                                    <h6 class="product-name mb-1"
                                                                        style="font-size: 15px;">
                                                                        <a href="product-details.php?item_id=<?php echo $item['item_id']; ?>"
                                                                            style="color: #333;">
                                                                            <?php echo $item['item_name']; ?>
                                                                        </a>
                                                                    </h6>
                                                                    <div
                                                                        class="product-pricing d-flex justify-content-between align-items-center">
                                                                        <div>
                                                                            <?php if ($item['item_amount'] == 0): ?>
                                                                            <span class="badge bg-success">Free
                                                                                Gift</span>
                                                                            <?php else: ?>
                                                                            <span
                                                                                class="text-muted text-decoration-line-through me-2">
                                                                                ₹<?php echo round($item['price']); ?>
                                                                            </span>
                                                                            <span class="discount-badge">
                                                                                <?php echo $item['discount']; ?>% off
                                                                            </span>
                                                                            <span class="order-total fw-bold ms-2">
                                                                                ₹<?php 
                                                                                $discount_amount = ($item['price'] * $item['discount']) / 100;
                                                                                $item_amount = $item['price'] - $discount_amount;
                                                                                echo round($item_amount); 
                                                                                ?>
                                                                            </span>
                                                                            <?php endif; ?>
                                                                        </div>

                                                                        <div class="quantity">
                                                                            Qty: <?php echo $item['qnty']; ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <?php endforeach; ?>
                                                        </div>

                                                        <!-- Order Summary Column -->
                                                        <div class="col-md-4">
                                                            <div class="order-summary p-3">
                                                                <h5 class="mb-4">Order Summary</h5>
                                                                <div
                                                                    class="summary-item d-flex justify-content-between mb-2">
                                                                    <span>Items
                                                                        (<?php echo $totalRows_Recordset2; ?>):</span>
                                                                    <span>₹<?php echo round($total_mrp); ?></span>
                                                                </div>
                                                                <div
                                                                    class="summary-item d-flex justify-content-between mb-2">
                                                                    <span>Product Discount:</span>
                                                                    <span
                                                                        class="order-total">-₹<?php echo round($total_discount); ?></span>
                                                                </div>
                                                                <?php if(!empty($row_Recordset3['coupon_discount'])): ?>
                                                                <div
                                                                    class="summary-item d-flex justify-content-between mb-2">
                                                                    <span>Coupon Discount:</span>
                                                                    <span
                                                                        class="order-total">-₹<?php echo round($row_Recordset3['coupon_discount']); ?></span>
                                                                </div>
                                                                <?php endif; ?>
                                                                <?php
                                                            $subtotal = $total_mrp - $total_discount - ($row_Recordset3['coupon_discount'] ?? 0);
                                                            $delivery_charges = ($subtotal < 1500) ? 50 : 0;
                                                            ?>
                                                                <div
                                                                    class="summary-item d-flex justify-content-between mb-2">
                                                                    <span>Delivery Charges:</span>
                                                                    <?php if($subtotal >= 1500): ?>
                                                                    <span class="order-total">FREE</span>
                                                                    <?php else: ?>
                                                                    <span>₹<?php echo $delivery_charges; ?></span>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <div
                                                                    class="summary-total d-flex justify-content-between mt-3 pt-3 border-top">
                                                                    <strong>Grand Total:</strong>
                                                                    <strong>₹<?php echo round($row_Recordset3['paid_amount']); ?></strong>
                                                                </div>
                                                                <div class="payment-info mt-4">
                                                                    <h6 class="mb-3">Payment Information</h6>
                                                                    <div class="d-flex justify-content-between mb-2">
                                                                        <span>Payment Method:</span>
                                                                        <span><?php echo $row_Recordset3['payment_mode']; ?></span>
                                                                    </div>
                                                                    <div class="d-flex justify-content-between">
                                                                        <span>Transaction ID:</span>
                                                                        <span
                                                                            class="text-muted"><?php echo $row_Recordset3['txn_id']; ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- New Order Actions Section -->
                                                <div class="order-actions mt-4">
                                                    <div class="row">
                                                        <!-- Product Review -->
                                                        <div class="col-md-4 mb-3">
                                                            <div class="action-card text-center p-3 h-100">
                                                                <i class="fas fa-star mb-3 fa-2x text-warning"></i>
                                                                <h6>Write Product Review</h6>
                                                                <p class="small mb-2">Share your experience with the
                                                                    product</p>
                                                                <button class="btn btn-sm btn-outline-warning"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#reviewModal">Write
                                                                    Review</button>
                                                            </div>
                                                        </div>

                                                        <!-- Return/Exchange -->
                                                        <div class="col-md-4 mb-3">
                                                            <div class="action-card text-center p-3 h-100">
                                                                <i
                                                                    class="fas fa-exchange-alt mb-3 fa-2x text-danger"></i>
                                                                <h6>Return or Exchange</h6>
                                                                <?php
                                                                $payment_date = strtotime($row_Recordset3['payment_date']);
                                                                $return_end_date = strtotime('+7 days', $payment_date);
                                                                $can_return = time() <= $return_end_date;
                                                                ?>
                                                                <p class="small mb-2">
                                                                    <?php if($can_return): ?>
                                                                    Valid till
                                                                    <?php echo date('d M Y', $return_end_date); ?>
                                                                    <?php else: ?>
                                                                    Return/Exchange period has ended
                                                                    <?php endif; ?>
                                                                </p>
                                                                <button class="btn btn-sm btn-outline-danger"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#returnExchangeModal"
                                                                    <?php echo !$can_return ? 'disabled' : ''; ?>>
                                                                    Initiate Return
                                                                </button>
                                                            </div>
                                                        </div>

                                                        <!-- Cancel Order -->
                                                        <div class="col-md-4 mb-3">
                                                            <div class="action-card text-center p-3 h-100">
                                                                <i
                                                                    class="fas fa-times-circle mb-3 fa-2x text-secondary"></i>
                                                                <h6>Cancel Order</h6>
                                                                <p class="small mb-2">Cancel within 1 hour of ordering
                                                                </p>
                                                                <?php if ($can_cancel): ?>
                                                                <button class="btn btn-sm btn-outline-secondary"
                                                                    onclick="cancelOrder(<?php echo $cart_id; ?>)">
                                                                    Cancel Order
                                                                </button>
                                                                <?php else: ?>
                                                                <button class="btn btn-sm btn-outline-secondary"
                                                                    disabled>
                                                                    Cannot Cancel Order
                                                                </button>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- my account wrapper end -->
    </main>
    <!-- Review Modal -->
    <div class="modal fade" id="reviewModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Write Your Review</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="reviewForm">
                        <div class="col-12 mb-4">
                            <h6 class="mb-3">Select Product to Review</h6>
                            <select class="form-select w-100" name="item_id" required>
                                <option value="">Choose product to review</option>
                                <?php 
                                mysqli_data_seek($Recordset2, 0);
                                while($row = mysqli_fetch_assoc($Recordset2)) { 
                                ?>
                                <option value="<?php echo $row['item_id']; ?>">
                                    <?php echo $row['item_name']; ?>
                                </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3 mt-3">
                            <label class="form-label">Rating</label>
                            <div class="product-rating mb-2">
                                <i class="far fa-star" data-rating="1"></i>
                                <i class="far fa-star" data-rating="2"></i>
                                <i class="far fa-star" data-rating="3"></i>
                                <i class="far fa-star" data-rating="4"></i>
                                <i class="far fa-star" data-rating="5"></i>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Your Review</label>
                            <textarea class="form-control" name="review" rows="4" required></textarea>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="cart_id" value="<?php echo $cart_id; ?>">
                            <input type="hidden" name="rating" id="selected-rating" value="">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="submitReview">Submit Review</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Return/Exchange Modal -->
    <div class="modal fade" id="returnExchangeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Return or Exchange Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="returnExchangeForm" enctype="multipart/form-data">
                        <div class="col-12 mb-4">
                            <h6 class="mb-3">Select Product</h6>
                            <select class="form-select w-100" name="item_id" required>
                                <option value="">Choose product to return/exchange</option>
                                <?php 
                                mysqli_data_seek($Recordset2, 0);
                                while($row = mysqli_fetch_assoc($Recordset2)) { 
                                ?>
                                <option value="<?php echo $row['item_id']; ?>">
                                    <?php echo $row['item_name']; ?>
                                </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label w-100">Request Type</label>
                            <select class="form-select w-100" name="request_type" id="request_type" required>
                                <option value="">Select Request Type</option>
                                <option value="Return">Return</option>
                                <option value="Exchange">Exchange</option>
                            </select>
                        </div>

                        <div class="mb-3 mt-5">
                            <label class="form-label w-100">Reason for Return/Exchange</label>
                            <select class="form-select w-100" name="reason" required>
                                <option value="">Select Reason</option>
                                <option value="Wrong Size">Wrong Size</option>
                                <option value="Product Damaged/Defective">Product Damaged/Defective</option>
                                <option value="Product Not As Described">Product Not As Described</option>
                                <option value="Received Wrong Item">Received Wrong Item</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="mb-3 mt-5">
                            <label class="form-label w-100">Detailed Description</label>
                            <textarea class="form-control w-100" name="description" rows="4" required
                                placeholder="Please provide detailed information about your return/exchange request"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label w-100">Product Condition</label>
                            <select class="form-select w-100" name="product_condition" required>
                                <option value="">Select Condition</option>
                                <option value="Unopened">Unopened</option>
                                <option value="opened but unused">Opened but unused</option>
                                <option value="used">Used</option>
                                <option value="Damaged">Damaged</option>
                            </select>
                        </div>

                        <div class="mb-3 mt-5">
                            <label class="form-label w-100">Upload Images (Optional)</label>
                            <input type="file" class="form-control w-100" name="images" accept="image/*" multiple>
                            <small class="text-muted">Upload images showing the issue (max 3 images)</small>
                        </div>

                        <div id="exchangeDetails" style="display: none;">
                            <h6 class="mt-4 mb-3">Exchange Details</h6>
                            <div class="mb-3">
                                <label class="form-label w-100">Preferred Size</label>
                                <select class="form-select w-100" name="preferred_size">
                                    <option value="">Select Size</option>
                                    <option value="Small">Small</option>
                                    <option value="Medium">Medium</option>
                                    <option value="Large">Large</option>
                                    <option value="Extra Large">Extra Large</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="cart_id" value="<?php echo $cart_id; ?>">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit Request</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll to top start -->
    <div class="scroll-top not-visible">
        <i class="fa fa-angle-up"></i>
    </div>
    <!-- Scroll to Top End -->

    <!-- footer area start -->
    <?php require_once('footer.php'); ?>
    <!-- footer area end -->

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

    function editAddress(shipping_id) {
        $.ajax({
            url: 'get_address.php',
            type: 'POST',
            data: {
                shipping_id: shipping_id
            },
            success: function(response) {
                const address = JSON.parse(response);
                $('input[name="recipient_name"]').val(address.recipient_name);
                $('input[name="address_name"]').val(address.address_name);
                $('textarea[name="address"]').val(address.address);
                $('input[name="city"]').val(address.city);
                $('select[name="state"]').val(address.state);
                $('.nice-select').niceSelect('update');
                $('input[name="pin_code"]').val(address.pin_code);
                $('input[name="phone"]').val(address.phone);
                $('input[name="shipping_id"]').val(address.shipping_id);
                $('#editAddressModal').modal('show');
            }
        });
    }

    $(document).on('submit', 'form[name="form3"]', function(e) {
        e.preventDefault();
        $.ajax({
            url: 'update_address.php',
            type: 'POST',
            dataType: 'json', // Specify that we expect JSON response
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.message || 'Error updating address');
                }
            },
            error: function(xhr, status, error) {
                console.log(error);
                if (xhr.status === 200) {
                    // If update was successful but response wasn't JSON
                    location.reload();
                } else {
                    alert('Error connecting to server');
                }
            }
        });
    });
    // document.getElementById('addAddressModal').addEventListener('show.bs.modal', function() {
    //     document.getElementById('addAddressForm').reset();
    //     const stateSelect = document.querySelector('#addAddressForm select[name="state"]');
    //     if (stateSelect) {
    //         stateSelect.value = '';
    //         // Refresh nice-select if it's initialized
    //         if (jQuery().niceSelect) {
    //             $(stateSelect).niceSelect('update');
    //         }
    //     }
    // });
    // Check URL parameters for active tab
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        if (tab) {
            const tabElement = document.querySelector(
                `.myaccount-tab-menu a[href="#${tab === 'orders' ? 'orders' : tab}"]`);
            if (tabElement) {
                document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('show',
                    'active'));
                document.querySelectorAll('.myaccount-tab-menu a').forEach(a => a.classList.remove(
                    'active'));
                tabElement.classList.add('active');
                const targetPane = document.querySelector(`#${tab === 'orders' ? 'orders' : tab}`);
                if (targetPane) {
                    targetPane.classList.add('show', 'active');
                }
            }
        }
    });

    function editAddress(shipping_id) {
        $.ajax({
            url: 'get_address.php',
            type: 'POST',
            data: {
                shipping_id: shipping_id
            },
            success: function(response) {
                const address = JSON.parse(response);
                $('input[name="recipient_name"]').val(address.recipient_name);
                $('input[name="address_name"]').val(address.address_name);
                $('textarea[name="address"]').val(address.address);
                $('input[name="city"]').val(address.city);
                $('select[name="state"]').val(address.state);
                $('.nice-select').niceSelect('update');
                $('input[name="pin_code"]').val(address.pin_code);
                $('input[name="phone"]').val(address.phone);
                $('input[name="shipping_id"]').val(address.shipping_id);
                $('#editAddressModal').modal('show');
            }
        });
    }

    $(document).on('submit', 'form[name="form3"]', function(e) {
        e.preventDefault();
        $.ajax({
            url: 'update_address.php',
            type: 'POST',
            dataType: 'json',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    $('#editAddressModal').modal('hide');
                    window.location.href = 'my-account.php?tab=address-edit#address-edit';
                } else {
                    alert(response.message || 'Error updating address');
                }
            },
            error: function(xhr, status, error) {
                console.log(error);
                if (xhr.status === 200) {
                    $('#editAddressModal').modal('hide');
                    window.location.href = 'my-account.php?tab=address-edit#address-edit';
                } else {
                    alert('Error connecting to server');
                }
            }
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
        // Safe element selection with null checks
        const getElement = (id) => {
            const el = document.getElementById(id);
            if (!el) console.error(`Element #${id} not found`);
            return el;
        };

        // Initialize review form functionality
        const initReviewForm = () => {
            const reviewForm = getElement('reviewForm');
            const submitBtn = getElement('submitReview');
            const ratingStars = document.querySelectorAll('.product-rating i');

            if (!reviewForm || !submitBtn || ratingStars.length === 0) {
                console.log('Required elements not found - review form may not be on this page');
                return;
            }

            // Star rating functionality
            ratingStars.forEach(star => {
                star.addEventListener('click', function() {
                    const rating = this.getAttribute('data-rating');
                    document.getElementById('selected-rating').value = rating;

                    // Update star display
                    ratingStars.forEach((s, index) => {
                        if (index < rating) {
                            s.classList.remove('far');
                            s.classList.add('fas');
                        } else {
                            s.classList.remove('fas');
                            s.classList.add('far');
                        }
                    });
                });
            });

            // Form submission
            reviewForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const rating = formData.get('rating');
                const review = formData.get('review');

                if (!rating) {
                    alert('Please select a rating');
                    return;
                }

                if (!review.trim()) {
                    alert('Please write your review');
                    return;
                }

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';

                fetch('submit_review.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            alert('Review submitted successfully!');
                            // Close modal if using Bootstrap
                            const modal = bootstrap.Modal.getInstance(getElement(
                                'reviewModal'));
                            if (modal) modal.hide();
                            location.reload();
                        } else {
                            alert(data.message || 'Error submitting review');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to submit review. Please try again.');
                    })
                    .finally(() => {
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Submit Review';
                    });
            });
        };

        // Initialize the review form
        initReviewForm();
    });
    // Handle Return/Exchange form
    $(document).ready(function() {
        $('#returnExchangeForm').on('submit', function(e) {
            e.preventDefault();

            var formData = new FormData(this);

            $.ajax({
                url: 'submit_return_exchange.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        $('#returnExchangeModal').modal('hide');
                        location.reload();
                    } else {
                        alert(response.message || 'An error occurred');
                    }
                },
                error: function() {
                    alert('Server error occurred. Please try again.');
                }
            });
        });

        // Show/hide exchange details based on request type
        $('#request_type').on('change', function() {
            if ($(this).val() === 'Exchange') {
                $('#exchangeDetails').show();
            } else {
                $('#exchangeDetails').hide();
            }
        });
    });

    function cancelOrder(cartId) {
        if (confirm('Are you sure you want to cancel this order?')) {
            $.ajax({
                url: 'cancel_order.php',
                type: 'POST',
                data: {
                    cart_id: cartId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert('Order cancelled successfully');
                        window.location.href = response.redirect; // Use the redirect URL from response
                    } else {
                        alert(response.message || 'Unable to cancel order');
                    }
                },
                error: function() {
                    alert('Error occurred while cancelling order');
                }
            });
        }
    }
    </script>
</body>

</html>