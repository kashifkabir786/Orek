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
                                    // Calculate discounted price
                                    $discounted_price = $cart_item['price'] - ($cart_item['price'] * $cart_item['discount'] / 100);
                                    // Calculate subtotal using discounted price
                                    $subtotal = $discounted_price * $cart_item['cart_quantity'];
                                    $total += $subtotal;
                                ?>
                            <li class="minicart-item">
                                <div class="minicart-thumb">
                                    <a href="product-details.php?item_id=<?php echo $cart_item['item_id']; ?>">
                                        <img src="assets/img/item/<?php echo $cart_item['image_1']; ?>" alt="product" />
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
                                        <span class="cart-price">
                                            ₹<?php 
                                                    $discounted_price = $cart_item['price'] - ($cart_item['price'] * $cart_item['discount'] / 100);
                                                    echo round($discounted_price); 
                                                ?>
                                            <del
                                                class="text-muted ms-2">₹<?php echo round($cart_item['price']); ?></del>
                                            <span class="discount-badge"><?php echo $cart_item['discount']; ?>%
                                                OFF</span>
                                        </span>
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
                                <span><strong>₹<?php echo isset($_SESSION['cart_total']) ? round($_SESSION['cart_total']) : '0'; ?></strong></span>
                            </li>
                            <li>
                                <span>Shipping</span>
                                <span>
                                    <?php if(isset($_SESSION['cart_total']) && $_SESSION['cart_total'] >= 1500): ?>
                                    <strong class="text-success">Free Shipping</strong>
                                    <small class="d-block text-muted">Orders above ₹1500</small>
                                    <?php else: ?>
                                    <strong>₹<?php echo isset($_SESSION['cart_total']) && $_SESSION['cart_total'] > 0 ? '50' : '0'; ?></strong>
                                    <?php endif; ?>
                                </span>
                            </li>
                            <li class="total">
                                <span>total</span>
                                <span><strong>₹<?php echo isset($_SESSION['cart_total']) ? 
                                        ($_SESSION['cart_total'] >= 1500 ? 
                                            round($_SESSION['cart_total']) : 
                                            round($_SESSION['cart_total'] + 50)) : 
                                        '0'; ?></strong></span>
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