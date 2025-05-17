<?php require_once('Connections/orek.php'); ?>
<?php require_once('session-2.php'); ?>
<?php
if ( isset( $_GET[ 'item_id' ] ) ) {
  $item_id = $_GET[ 'item_id' ];
  
    $query_Recordset2 = "SELECT * FROM item WHERE item_id = '$item_id'";
    $Recordset2 = mysqli_query( $orek, $query_Recordset2 )or die( mysqli_error( $orek ) );
    $row_Recordset2 = mysqli_fetch_assoc( $Recordset2 );
    $totalRows_Recordset2 = mysqli_num_rows( $Recordset2 );
}

    $query_Recordset3 = "SELECT * FROM review WHERE item_id = '$item_id'";
    $Recordset3 = mysqli_query($orek, $query_Recordset3) or die(mysqli_error($orek));
    $row_Recordset3 = mysqli_fetch_assoc($Recordset3);
    $totalRows_Recordset3 = mysqli_num_rows($Recordset3);

    $category_id = $row_Recordset2['category_id'];
    $current_size = $row_Recordset2['size'];
    $current_ocassion = $row_Recordset2['ocassion'];
    $current_price = $row_Recordset2['price'];
 $current_listing_status = $row_Recordset2['listing_status'];
    $query_Recordset4 = "SELECT * FROM item 
        WHERE category_id = '$category_id' 
        AND (size = '$current_size' OR size IS NULL OR '$current_size' IS NULL) 
        AND listing_status = '$current_listing_status'
        AND item_id != '$item_id' ";
    $Recordset4 = mysqli_query($orek, $query_Recordset4) or die(mysqli_error($orek));
    $row_Recordset4 = mysqli_fetch_assoc($Recordset4);
    $totalRows_Recordset4 = mysqli_num_rows($Recordset4);
?>

<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <?php 
    // Generate dynamic title based on product name
    $page_title = isset($row_Recordset2['item_name']) ? $row_Recordset2['item_name'] . ' - Orek' : 'Product Details - Orek';
    // Generate dynamic description based on product description
    $meta_description = '';
    if(isset($row_Recordset2['description'])) {
        $meta_description = strip_tags($row_Recordset2['description']);
        $meta_description = substr($meta_description, 0, 155); // Limit to 155 characters
        if(strlen($row_Recordset2['description']) > 155) {
            $meta_description .= '...';
        }
    } else {
        $meta_description = "Shop quality products at Orek. Discover our collection with detailed product information, images, and customer reviews.";
    }
    // Get current URL for canonical tag
    $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $current_url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    ?>
    <title><?php echo $page_title; ?></title>
    <!-- Meta tags for SEO -->
    <meta name="description" content="<?php echo $meta_description; ?>" />
    <meta name="robots" content="index, follow" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="canonical" href="<?php echo $current_url; ?>" />
    
    <!-- Additional meta tags for SEO -->
    <meta name="keywords" content="<?php echo $row_Recordset2['item_name']; ?>, orek, product, <?php echo $row_Recordset2['category_id']; ?>, online shopping, india" />
    <meta name="author" content="Orek" />
    
    <!-- Open Graph tags for social media sharing -->
    <meta property="og:title" content="<?php echo $page_title; ?>" />
    <meta property="og:description" content="<?php echo $meta_description; ?>" />
    <meta property="og:url" content="<?php echo $current_url; ?>" />
    <meta property="og:type" content="product" />
    <?php if(isset($row_Recordset2['image_1']) && !empty($row_Recordset2['image_1'])): ?>
    <meta property="og:image" content="<?php echo $protocol . $_SERVER['HTTP_HOST']; ?>/assets/img/item/<?php echo $row_Recordset2['image_1']; ?>" />
    <?php endif; ?>
    
    <!-- Twitter Card data -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="<?php echo $page_title; ?>" />
    <meta name="twitter:description" content="<?php echo $meta_description; ?>" />
    <?php if(isset($row_Recordset2['image_1']) && !empty($row_Recordset2['image_1'])): ?>
    <meta name="twitter:image" content="<?php echo $protocol . $_SERVER['HTTP_HOST']; ?>/assets/img/item/<?php echo $row_Recordset2['image_1']; ?>" />
    <?php endif; ?>
    
    <!-- JSON-LD structured data for product -->
    <?php if(isset($row_Recordset2['item_id'])): ?>
    <script type="application/ld+json">
    {
        "@context": "https://schema.org/",
        "@type": "Product",
        "name": "<?php echo $row_Recordset2['item_name']; ?>",
        "image": [
            <?php 
            $images = array();
            for($i = 1; $i <= 5; $i++) {
                if(isset($row_Recordset2["image_{$i}"]) && !empty($row_Recordset2["image_{$i}"])) {
                    $images[] = "\"" . $protocol . $_SERVER['HTTP_HOST'] . "/assets/img/item/" . $row_Recordset2["image_{$i}"] . "\"";
                }
            }
            echo implode(',', $images);
            ?>
        ],
        "description": "<?php echo addslashes($meta_description); ?>",
        "sku": "<?php echo $row_Recordset2['item_id']; ?>",
        "offers": {
            "@type": "Offer",
            "url": "<?php echo $current_url; ?>",
            "priceCurrency": "INR",
            "price": "<?php echo round($discounted_price); ?>",
            "priceValidUntil": "2025-03-15",
            "availability": "<?php echo ($row_Recordset2['stock_alert'] > 0) ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock'; ?>",
            "itemCondition": "https://schema.org/NewCondition"
        },
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "<?php echo isset($avg_rating) ? $avg_rating : 0; ?>",
            "reviewCount": "<?php echo $totalRows_Recordset3; ?>"
        }
    }
    </script>
    <?php endif; ?>
    
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
    <!-- LightGallery CSS for product gallery -->
    <link rel="stylesheet" href="assets/css/lightgallery.min.css" />
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
                                <ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">
                                    <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                                        <a href="index.php" itemprop="item"><i class="fa fa-home"></i><span itemprop="name" class="sr-only">Home</span></a>
                                        <meta itemprop="position" content="1" />
                                    </li>
                                    <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                                        <a href="product-list.php" itemprop="item"><span itemprop="name">Product List</span></a>
                                        <meta itemprop="position" content="2" />
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                                        <span itemprop="name"><?php echo $row_Recordset2['item_name']; ?></span>
                                        <meta itemprop="position" content="3" />
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- breadcrumb area end -->

        <!-- page main wrapper start -->
        <div class="shop-main-wrapper section-padding pb-0">
            <div class="container">
                <div class="row">
                    <div class="mb-4"
                        style="background: linear-gradient(90deg, #ff7e5f, #feb47b); padding: 12px 20px; text-align: center; color: #fff; font-size: 18px; font-weight: 600; border-radius: 10px; font-family: 'Poppins', sans-serif; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);">
                        Shop for ₹999+ and Unlock a FREE Gift of Your Choice! 🛍️🎁
                    </div>
                    <!-- product details wrapper start -->
                    <?php do{?>
                    <div class="col-lg-12 order-1 order-lg-2" itemscope itemtype="https://schema.org/Product">
                        <!-- product details inner end -->
                        <div class="product-details-inner">
                            <div class="row">
                                <div class="col-lg-5">
                                    <div class="product-large-slider">
                                        <?php 
                                        $image_count = 1;
                                        $gallery_images = array();
                                        while($image_count <= 5) {
                                            $image_path = "assets/img/item/{$row_Recordset2["image_{$image_count}"]}";
                                            if($row_Recordset2["image_{$image_count}"] != '') {
                                                // Add to gallery images array
                                                $gallery_images[] = array(
                                                    'path' => $image_path,
                                                    'alt' => $row_Recordset2['item_name'] . ' - Image ' . $image_count
                                                );
                                                
                                                // Display main slider image 
                                                echo "<div class='pro-large-img img-zoom' data-src='{$image_path}'>
                                                        <img src='{$image_path}' alt='{$row_Recordset2['item_name']} - Image {$image_count}' loading='lazy' itemprop='image' />
                                                      </div>";
                                            }
                                            $image_count++;
                                        }
                                        ?>
                                    </div>
                                    <div class="pro-nav slick-row-10 slick-arrow-style">
                                        <?php 
                                        foreach($gallery_images as $index => $image) {
                                            echo "<div class='pro-nav-thumb'>
                                                    <img src='{$image['path']}' alt='{$image['alt']}' loading='lazy' />
                                                </div>";
                                        }
                                        ?>
                                    </div>
                                    
                                    <!-- Hidden gallery container for lightbox -->
                                    <div id="product-gallery" class="d-none">
                                        <?php 
                                        foreach($gallery_images as $image) {
                                            echo "<a href='{$image['path']}' data-src='{$image['path']}'>
                                                    <img src='{$image['path']}' alt='{$image['alt']}' />
                                                </a>";
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="col-lg-7">
                                    <div class="product-details-des">
                                        <div class="manufacturer-name">
                                            <!-- <a href="product-details.html">HasTech</a> -->
                                        </div>
                                        <h1 class="product-name"><?php echo $row_Recordset2['item_name']; ?></h1>
                                        <div class="ratings d-flex" itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">
                                            <?php 
                                            $avg_rating = isset($row_Recordset3['avg_rating']) ? round($row_Recordset3['avg_rating']) : 0;
                                            for ($i = 1; $i <= 5; $i++) {
                                                if ($i <= $avg_rating) {
                                                    echo '<span><i class="fa fa-star"></i></span>';
                                                } else {
                                                    echo '<span><i class="fa fa-star-o"></i></span>';
                                                }
                                            }
                                            ?>
                                            <div class="pro-review">
                                                <span><meta itemprop="ratingValue" content="<?php echo $avg_rating; ?>"><?php echo $totalRows_Recordset3; ?> <meta itemprop="reviewCount" content="<?php echo $totalRows_Recordset3; ?>">Reviews</span>
                                            </div>
                                        </div>
                                        <div class="price-box" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                                            <?php
                                            // Actual price
                                            $actual_price = $row_Recordset2['price'];
                                            // Discount percentage
                                            $discount_percentage = $row_Recordset2['discount'];
                                            // Discounted price calculation
                                            $discounted_price = $actual_price - ($actual_price * ($discount_percentage /
                                            100));
                                            ?>
                                            <span class="price-regular">
                                                &#8377;<span itemprop="price" content="<?php echo round($discounted_price); ?>"><?php echo round($discounted_price); ?></span>
                                                <meta itemprop="priceCurrency" content="INR">
                                            </span>
                                            <span class="price-old">
                                                <del>&#8377;<?php echo round($actual_price); ?></del>
                                            </span>
                                            <span class="discount-badge"><?php echo $discount_percentage; ?>%
                                                OFF</span>
                                            <link itemprop="availability" href="<?php echo ($row_Recordset2['stock_alert'] > 0) ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock'; ?>">
                                        </div>
                                        <h5 class="offer-text"><strong>Hurry up</strong>! offer ends in:</h5>
                                        <div class="product-countdown" data-countdown="2025/03/15"></div>
                                        <div class="availability">
                                            <?php 
                                            $stock = $row_Recordset2['stock_alert'];  
                                            if($stock <= 5) {
                                                echo '<span class="text-danger" style="font-weight: 600;"><i class="fa fa-exclamation-circle"></i> Hurry Up! Only ' . $stock . ' left!</span>';
                                            } else {
                                                echo '<span class="text-success"><i class="fa fa-check-circle"></i> In stock</span>';
                                            }
                                            ?>
                                        </div>
                                        <div class="product-description" itemprop="description">
                                            <h2 class="description-title h5">Product Description</h2>
                                            <p class="pro-desc">
                                                <?php 
                                                    // Get the full description
                                                    $description = $row_Recordset2['description'];
                                                    // Limit to 300 characters and add ellipsis if longer
                                                    echo (strlen($description) > 300) ? substr($description, 0, 300) . '...' : $description; 
                                                ?>
                                            </p>
                                        </div>
                                        <?php 
                                        $is_gift = $row_Recordset2['listing_status'] === 'Gift';
                                        ?>
                                        <div class="quantity-cart-box d-flex align-items-center">
                                            <?php if(!$is_gift): ?>
                                            <h6 class="option-title">qty:</h6>
                                            <div class="quantity">
                                                <div class="pro-qty"><input type="text" id="item-quantity" value="1">
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                            <div class="action_link d-flex gap-2">
                                                <?php
                                                if(isset($_SESSION['email'])) {
                                                    $email = $_SESSION['email'];
                                                    $item_id = $row_Recordset2['item_id'];

                                                    if($is_gift) {
                                                        // Check if user already has any gift item in cart
                                                       $check_gift = mysqli_query($orek, "SELECT COUNT(item_id) as gift_count FROM cart_item A INNER JOIN `cart` B ON A.cart_id = B.cart_id WHERE B.user_id = '{$row_Recordset1['user_id']}' AND `status` = 'Pending' AND A.item_id IN (SELECT item_id FROM item WHERE listing_status = 'Gift')");
                                                        $gift_count = mysqli_fetch_assoc($check_gift)['gift_count'];
                                                        // Get cart total
                                                        $cart_total_query = mysqli_query($orek, "SELECT 
                                                            SUM(ROUND(i.price * (1 - i.discount/100)) * ci.qnty) as cart_total 
                                                            FROM cart_item ci 
                                                            JOIN cart c ON ci.cart_id = c.cart_id 
                                                            JOIN user u ON c.user_id = u.user_id 
                                                            JOIN item i ON ci.item_id = i.item_id 
                                                            WHERE u.email = '$email' 
                                                            AND c.status = 'Pending'
                                                            AND i.listing_status != 'Gift'");
                                                        $cart_total = mysqli_fetch_assoc($cart_total_query)['cart_total'] ?? 0;
                                                        // Check if this specific item is in cart
                                                        $stmt = mysqli_prepare($orek, "CALL CheckItemInCart(?, ?, @is_in_cart)");
                                                        mysqli_stmt_bind_param($stmt, "si", $email, $item_id);
                                                        mysqli_stmt_execute($stmt);
                                                        mysqli_stmt_close($stmt);
                                                        
                                                        $result = mysqli_query($orek, "SELECT @is_in_cart as in_cart");
                                                        $row = mysqli_fetch_assoc($result);
                                                        $in_cart = (bool)$row['in_cart'];
                                                        mysqli_free_result($result);
                                                        
                                                        if($in_cart): ?>
                                                <a href="cart.php" class="btn btn-cart">View Cart</a>
                                                <?php elseif($gift_count > 0): ?>
                                                <button class="btn btn-cart" disabled> Get 1 FREE Gift with ₹999+
                                                    Shopping
                                                </button>
                                                <?php elseif($cart_total < 999): ?>
                                                <button class="btn btn-cart" disabled> Shop for ₹999+ to Claim Free
                                                    Gift</button>
                                                <?php else: ?>
                                                <button class="btn btn-cart gift-btn"
                                                    onclick="addGiftToCart(<?php echo $item_id; ?>)">
                                                    Add Free Gift
                                                </button>
                                                <?php endif;
                                                    } else {
                                                        // Regular product logic
                                                        $stmt = mysqli_prepare($orek, "CALL CheckItemInCart(?, ?, @is_in_cart)");
                                                        mysqli_stmt_bind_param($stmt, "si", $email, $item_id);
                                                        mysqli_stmt_execute($stmt);
                                                        mysqli_stmt_close($stmt);
                                                        
                                                        $result = mysqli_query($orek, "SELECT @is_in_cart as in_cart");
                                                        $row = mysqli_fetch_assoc($result);
                                                        $in_cart = (bool)$row['in_cart'];
                                                        mysqli_free_result($result);
                                                        
                                                        if($in_cart): ?>
                                                <a href="cart.php" class="btn btn-cart">View Cart</a>
                                                <?php else: ?>
                                                <button class="btn btn-cart"
                                                    onclick="addToCartWithQty(<?php echo $item_id; ?>)">
                                                    Add to Cart
                                                </button>
                                                <?php endif; ?>
                                                <button class="btn btn-buy-now"
                                                    onclick="buyNow(<?php echo $item_id; ?>)">
                                                    Buy Now
                                                </button>
                                                <?php }
                                                } else { ?>
                                                <a href="login.php"
                                                    class="btn btn-cart"><?php echo $is_gift ? 'Add Free Gift' : 'Add to Cart'; ?></a>
                                                <?php if(!$is_gift): ?>
                                                <a href="login.php" class="btn btn-buy-now">Buy Now</a>
                                                <?php endif; ?>
                                                <?php } ?>
                                            </div>
                                        </div>

                                        <div class="useful-links">
                                            <?php if(isset($_SESSION['email'])) { 
                                            // Check if item is already in wishlist
                                            $stmt = mysqli_prepare($orek, "CALL CheckItemInWishlist(?, ?, @is_in_wishlist)");
                                            $email = $_SESSION['email'];
                                            $item_id = $row_Recordset2['item_id'];
                                            mysqli_stmt_bind_param($stmt, "si", $email, $item_id);
                                            mysqli_stmt_execute($stmt);
                                            mysqli_stmt_close($stmt);
                                            
                                            $result = mysqli_query($orek, "SELECT @is_in_wishlist as in_wishlist");
                                            $row = mysqli_fetch_assoc($result);
                                            $in_wishlist = (bool)$row['in_wishlist'];
                                            mysqli_free_result($result);
                                            
                                            if($in_wishlist): ?>
                                            <a href="wishlist.php" class="in-wishlist" data-bs-toggle="tooltip"
                                                title="View Wishlist">
                                                <i class="fa-solid fa-heart"></i>
                                            </a>
                                            <?php else: ?>
                                            <a href="#" onclick="addToWishlist(<?php echo $item_id; ?>); return false;"
                                                class="add-to-wishlist" data-bs-toggle="tooltip"
                                                title="Add to Wishlist">
                                                <i class="fa-regular fa-heart"></i>
                                            </a>
                                            <?php endif;
                                            } else { ?>
                                            <a href="login.php" data-bs-toggle="tooltip" title="Login to use Wishlist">
                                                <i class="fa-regular fa-heart"></i>Wishlist
                                            </a>
                                            <?php } ?>
                                        </div>
                                        <div class="like-icon">
                                            <a class="facebook" href="https://www.facebook.com/share/1BvPk2MvX5/"><i
                                                    class="fa-brands fa-facebook"></i>Facebook</a>
                                            <a class="twitter" href="https://www.instagram.com/orekdotin"><i
                                                    class="fa-brands fa-instagram"></i>Instagram</a>
                                            <a class="pinterest" href="https://wa.me/7992381874"><i
                                                    class="fa-brands fa-whatsapp"></i>Whatsapp</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- product details inner end -->

                        <!-- product details reviews start -->

                        <div class="product-details-reviews section-padding pb-0">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="product-review-info">
                                        <ul class="nav review-tab">
                                            <li>
                                                <a class="active" data-bs-toggle="tab" href="#tab_one">description</a>
                                            </li>
                                            <li>
                                                <a data-bs-toggle="tab" href="#tab_two">information</a>
                                            </li>
                                            <li>
                                                <a data-bs-toggle="tab" href="#tab_three">reviews
                                                    (<?php echo $totalRows_Recordset3; ?>)</a>
                                            </li>
                                        </ul>
                                        <div class="tab-content reviews-tab">
                                            <div class="tab-pane fade show active" id="tab_one">
                                                <div class="tab-one">
                                                    <p><?php echo $row_Recordset2['description']; ?></p>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="tab_two">
                                                <table class="table table-bordered">
                                                    <tbody>
                                                        <tr>
                                                            <td>color</td>
                                                            <td>black, blue, red</td>
                                                        </tr>
                                                        <tr>
                                                            <td>size</td>
                                                            <td>L, M, S</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="tab-pane fade" id="tab_three">
                                                <div id="review-response"></div>
                                                <form id="review-form" class="review-form">
                                                    <h5><?php echo $totalRows_Recordset3; ?> review for
                                                        <span><?php echo $row_Recordset2['item_name']; ?></span>
                                                    </h5>
                                                    <?php if($totalRows_Recordset3 > 0) { 
                                                        mysqli_data_seek($Recordset3, 0); // Reset pointer to beginning
                                                        do { ?>
                                                    <div class="total-reviews" itemprop="review" itemscope itemtype="https://schema.org/Review">
                                                        <div class="review-box">
                                                            <div class="review-author">
                                                                <span itemprop="author" itemscope itemtype="https://schema.org/Person">
                                                                    <span itemprop="name"><?php echo $row_Recordset3['name']; ?></span>
                                                                </span>
                                                            </div>
                                                            <div class="rating-box" itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating">
                                                                <span>Rating: <meta itemprop="ratingValue" content="<?php echo $row_Recordset3['rating']; ?>"><?php echo $row_Recordset3['rating']; ?> <meta itemprop="bestRating" content="5">/ 5</span>
                                                            </div>
                                                            <div class="review-text">
                                                                <p itemprop="reviewBody"><?php echo $row_Recordset3['review']; ?></p>
                                                                <meta itemprop="datePublished" content="<?php echo date('Y-m-d', strtotime($row_Recordset3['date'])); ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php } while($row_Recordset3=mysqli_fetch_assoc($Recordset3)); } ?>

                                                    <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">
                                                    <div class="form-group row">
                                                        <div class="col">
                                                            <label class="col-form-label"><span
                                                                    class="text-danger">*</span> Your Name</label>
                                                            <input type="text" name="name" class="form-control"
                                                                required>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col">
                                                            <label class="col-form-label"><span
                                                                    class="text-danger">*</span> Your Email</label>
                                                            <input type="email" name="email" class="form-control"
                                                                required>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col">
                                                            <label class="col-form-label"><span
                                                                    class="text-danger">*</span> Your Review</label>
                                                            <textarea name="review" class="form-control"
                                                                required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col">
                                                            <label class="col-form-label"><span
                                                                    class="text-danger">*</span> Rating</label>
                                                            &nbsp;&nbsp;&nbsp; Bad&nbsp;
                                                            <input type="radio" value="1" name="rating">
                                                            &nbsp;
                                                            <input type="radio" value="2" name="rating">
                                                            &nbsp;
                                                            <input type="radio" value="3" name="rating">
                                                            &nbsp;
                                                            <input type="radio" value="4" name="rating">
                                                            &nbsp;
                                                            <input type="radio" value="5" name="rating" checked>
                                                            &nbsp;Good
                                                        </div>
                                                    </div>
                                                    <div class="buttons">
                                                        <button type="submit" class="btn btn-sqr">Submit Review</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- product details reviews end -->
                    </div>
                    <?php }while($row_Recordset2=mysqli_fetch_assoc($Recordset2)); ?>
                    <!-- product details wrapper end -->
                </div>
            </div>
        </div>
        <!-- page main wrapper end -->

        <!-- related products area start -->
        <section class="related-products section-padding">
            <?php if($totalRows_Recordset4 > 0) { ?>
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <!-- section title start -->
                        <div class="section-title text-center">
                            <h2 class="title">Related Products</h2>
                            <p class="sub-title">Add related products to weekly lineup</p>
                        </div>
                        <!-- section title end -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="product-carousel-4 slick-row-10 slick-arrow-style">
                            <?php do { 
                        $is_gift = $row_Recordset4['listing_status'] === 'Gift';
                        $email = $_SESSION['email'] ?? null;
                        $item_id = $row_Recordset4['item_id'];
                    ?>
                            <!-- product item start -->
                            <div class="product-item">
                                <figure class="product-thumb">
                                    <a href="product-details.php?item_id=<?php echo $item_id; ?>">
                                        <img class="pri-img"
                                            src="assets/img/item/<?php echo $row_Recordset4['image_1']; ?>"
                                            alt="<?php echo $row_Recordset4['item_name']; ?>" loading="lazy">
                                        <img class="sec-img"
                                            src="assets/img/item/<?php echo $row_Recordset4['image_1']; ?>"
                                            alt="<?php echo $row_Recordset4['item_name']; ?>" loading="lazy">
                                    </a>
                                    <div class="product-badge">
                                        <div class="product-label new">
                                            <span>new</span>
                                        </div>
                                        <?php if(!$is_gift): ?>
                                        <div class="product-label discount">
                                            <span><?php echo $row_Recordset4['discount']; ?>%</span>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="button-group">
                                        <?php if(isset($_SESSION['email'])): 
                                    // Check if item is in wishlist
                                    $check_wishlist = mysqli_query($orek, "SELECT * FROM wishlist w 
                                        JOIN user u ON w.user_id = u.user_id 
                                        WHERE u.email = '$email' AND w.item_id = $item_id");
                                    $in_wishlist = mysqli_num_rows($check_wishlist) > 0;
                                ?>
                                        <?php if($in_wishlist): ?>
                                        <a href="wishlist.php" data-bs-toggle="tooltip" data-bs-placement="left"
                                            title="View Wishlist">
                                            <i class="fa-solid fa-heart"></i>
                                        </a>
                                        <?php else: ?>
                                        <a href="javascript:void(0)" onclick="addToWishlist(<?php echo $item_id; ?>)"
                                            data-bs-toggle="tooltip" data-bs-placement="left" title="Add to Wishlist">
                                            <i class="fa-regular fa-heart"></i>
                                        </a>
                                        <?php endif; ?>
                                        <?php else: ?>
                                        <a href="login.php" data-bs-toggle="tooltip" data-bs-placement="left"
                                            title="Add to Wishlist">
                                            <i class="fa-regular fa-heart"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                    <div class="cart-hover">
                                        <?php if(isset($_SESSION['email'])) {
                                    // Check if item is already in cart
                                    $stmt = mysqli_prepare($orek, "CALL CheckItemInCart(?, ?, @is_in_cart)");
                                    mysqli_stmt_bind_param($stmt, "si", $email, $item_id);
                                    mysqli_stmt_execute($stmt);
                                    mysqli_stmt_close($stmt);

                                    $result = mysqli_query($orek, "SELECT @is_in_cart as in_cart");
                                    $row_incart = mysqli_fetch_assoc($result);
                                    $in_cart = (bool)$row_incart['in_cart'];
                                    mysqli_free_result($result);

                                    if($in_cart): ?>
                                        <a href="cart.php" class="btn btn-cart">View Cart</a>
                                        <?php else: 
                                        if($is_gift) {
                                            // Check cart total (excluding gifts)
                                            $cart_total_query = mysqli_query($orek, "SELECT 
                                                SUM(ROUND(i.price * (1 - i.discount/100)) * ci.qnty) as cart_total 
                                                FROM cart_item ci 
                                                JOIN cart c ON ci.cart_id = c.cart_id 
                                                JOIN user u ON c.user_id = u.user_id 
                                                JOIN item i ON ci.item_id = i.item_id 
                                                WHERE u.email = '$email' 
                                                AND c.status = 'Pending'
                                                AND i.listing_status != 'Gift'");
                                            $cart_total = mysqli_fetch_assoc($cart_total_query)['cart_total'] ?? 0;

                                            // Check if gift already added
                                            $check_gift = mysqli_query($orek, "SELECT COUNT(*) as gift_count 
                                                FROM cart_item ci 
                                                JOIN cart c ON ci.cart_id = c.cart_id 
                                                JOIN user u ON c.user_id = u.user_id 
                                                JOIN item i ON ci.item_id = i.item_id 
                                                WHERE u.email = '$email' 
                                                AND c.status = 'Pending'
                                                AND i.listing_status = 'Gift'");
                                            $gift_count = mysqli_fetch_assoc($check_gift)['gift_count'];

                                            if($gift_count > 0): ?>
                                        <button class="btn btn-cart" disabled>Gift Limit Reached</button>
                                        <?php elseif($cart_total < 999): ?>
                                        <button class="btn btn-cart" disabled>Shop for ₹999+ to Get Free Gift</button>
                                        <?php else: ?>
                                        <button class="btn btn-cart gift-btn"
                                            onclick="addGiftToCart(<?php echo $item_id; ?>)">
                                            Add Free Gift
                                        </button>
                                        <?php endif;
                                        } else { ?>
                                        <button class="btn btn-cart" onclick="addToCartProc(<?php echo $item_id; ?>)">
                                            Add to Cart
                                        </button>
                                        <?php }
                                    endif;
                                } else { ?>
                                        <a href="login.php"
                                            class="btn btn-cart"><?php echo $is_gift ? 'Add Free Gift' : 'Add to Cart'; ?></a>
                                        <?php } ?>
                                    </div>
                                </figure>

                                <!-- Product Caption Section -->
                                <div class="product-caption text-center">
                                    <div class="product-identity">
                                        <p class="manufacturer-name">
                                            <a href="product-details.php?item_id=<?php echo $item_id; ?>">
                                                <?php echo $is_gift ? 'Free Gift' : 'Gold'; ?>
                                            </a>
                                        </p>
                                    </div>
                                    <h6 class="product-name">
                                        <a href="product-details.php?item_id=<?php echo $item_id; ?>">
                                            <?php echo $row_Recordset4['item_name']; ?>
                                        </a>
                                    </h6>
                                    <div class="price-box">
                                        <?php if(!$is_gift): 
                                    $actual_price = $row_Recordset4['price'];
                                    $discount_percentage = $row_Recordset4['discount'];
                                    $discounted_price = round($actual_price - ($actual_price * ($discount_percentage / 100)));
                                ?>
                                        <span class="price-regular">&#8377;<?php echo $discounted_price; ?></span>
                                        <span
                                            class="price-old"><del>&#8377;<?php echo round($actual_price); ?></del></span>
                                        <?php else: ?>
                                        <span class="price-regular">FREE with ₹999+ Shopping</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <!-- Product Caption End -->
                            </div>
                            <!-- product item end -->
                            <?php } while($row_Recordset4 = mysqli_fetch_assoc($Recordset4)); ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </section>

        <!-- related products area end -->
    </main>

    <!-- Scroll to top start -->
    <div class="scroll-top not-visible">
        <i class="fa fa-angle-up"></i>
    </div>
    <!-- Scroll to Top End -->

    <!-- footer area start -->
    <?php require_once('footer.php'); ?>
    <!-- footer area end -->

    <!-- Schema.org Organization data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "Orek",
        "url": "<?php echo $protocol . $_SERVER['HTTP_HOST']; ?>",
        "logo": "<?php echo $protocol . $_SERVER['HTTP_HOST']; ?>/assets/img/logo/logo.png",
        "sameAs": [
            "https://www.facebook.com/share/1BvPk2MvX5/",
            "https://www.instagram.com/orekdotin",
            "https://wa.me/7992381874"
        ]
    }
    </script>

    <!-- Quick view modal start -->
    <div class="modal" id="quick_view">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-bs-dismiss="modal">
                        &times;
                    </button>
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
    <!-- LightGallery JS -->
    <script src="assets/js/lightgallery.min.js"></script>
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
    <!-- Custom mobile fixes -->
    <script src="assets/js/custom-mobile-fixes.js"></script>
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

        // Initialize product image gallery
        $('.product-large-slider').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            fade: true,
            arrows: false,
            asNavFor: '.pro-nav'
        });

        // Initialize product thumbnail navigation
        $('.pro-nav').slick({
            slidesToShow: 4,
            slidesToScroll: 1,
            asNavFor: '.product-large-slider',
            arrows: true,
            focusOnSelect: true,
            centerMode: false,
            responsive: [
                {
                    breakpoint: 576,
                    settings: {
                        slidesToShow: 3,
                    }
                }
            ]
        });

        // Initialize LightGallery for product images
        $('.img-zoom').on('click', function(e) {
            e.preventDefault();
            const gallerySelector = document.getElementById('product-gallery');
            const galleryInstance = lightGallery(gallerySelector, {
                dynamic: true,
                dynamicEl: getGalleryItems(),
                download: false,
                zoom: true,
                actualSize: false,
                share: false,
                autoplayControls: false,
                fullScreen: true,
                counter: true,
                thumbnail: true
            });
            
            // Open gallery at the current index
            const currentIndex = $(this).parent().index();
            galleryInstance.openGallery(currentIndex);
            
            // Close gallery on background click for mobile
            $('.lg-backdrop').on('click', function() {
                galleryInstance.closeGallery();
            });
            
            // Prevent zoom issues on mobile scroll
            if (window.innerWidth < 768) {
                $('.lg-outer').on('touchmove', function(e) {
                    e.stopPropagation();
                });
            }
        });
        
        // Helper function to get gallery items
        function getGalleryItems() {
            const items = [];
            $('#product-gallery a').each(function() {
                items.push({
                    src: $(this).attr('href'),
                    thumb: $(this).attr('href')
                });
            });
            return items;
        }
    });

    function addToCartWithQty(itemId) {
        const quantity = parseInt($("#item-quantity").val());
        if (isNaN(quantity) || quantity < 1) {
            alert("Please enter a valid quantity");
            return;
        }

        const button = $(`.action_link button[onclick="addToCartWithQty(${itemId})"]`);
        button.html('<i class="fa fa-spinner fa-spin"></i> Adding...');
        button.prop('disabled', true);

        $.ajax({
            url: 'cart_operations.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'add_to_cart',
                item_id: itemId,
                qnty: quantity
            },
            success: function(response) {
                if (response.success) {
                    updateMiniCart();
                    button.html('<i class="fa fa-check"></i> Added to Cart');
                    button.addClass('btn-success');

                    setTimeout(function() {
                        button.parent().html(
                            '<a href="cart.php" class="btn btn-cart">View Cart</a>');
                    }, 1000);

                    const messageDiv = $('<div>')
                        .addClass('cart-message')
                        .html('<i class="fa fa-check-circle"></i> Items added to cart successfully!')
                        .appendTo('body');

                    setTimeout(function() {
                        messageDiv.fadeOut(function() {
                            $(this).remove();
                        });
                    }, 3000);
                } else {
                    button.html('Add to Cart');
                    button.prop('disabled', false);
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                button.html('Add to Cart');
                button.prop('disabled', false);
                console.error('Error:', error);
                alert('Connection error. Please try again.');
            }
        });
    }

    function updateMiniCart() {
        $.ajax({
            url: 'get_mini_cart.php',
            type: 'GET',
            success: function(response) {
                // Update the entire minicart content
                $(".minicart-content-box").html(response);

                // Update cart count in header
                $.ajax({
                    url: 'get_cart_count.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(countResponse) {
                        if (countResponse.success) {
                            $(".notification").each(function() {
                                if ($(this).closest("a").find(
                                        ".fa-bag-shopping, .pe-7s-shopbag")
                                    .length) {
                                    $(this).text(countResponse.count);
                                }
                            });
                        }
                    }
                });
            }
        });
    }

    function addToCartProc(itemId) {
        <?php if(!isset($_SESSION['email'])): ?>
        window.location.href = 'login.php';
        return;
        <?php endif; ?>

        const button = $(`.cart-hover button[onclick="addToCartProc(${itemId})"]`);
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
                    // Update cart count immediately
                    updateMiniCart();
                    // Show the mini cart
                    // $("body").addClass("fix");
                    // $(".minicart-inner").addClass("show");
                    button.html('<i class="fa fa-check"></i> Added to Cart');
                    button.addClass('btn-success');

                    setTimeout(function() {
                        $(`.cart-hover button[onclick="addToCartProc(${itemId})"]`).each(
                            function() {
                                $(this).parent().html(
                                    '<a href="cart.php" class="btn btn-cart">View Cart</a>'
                                );
                            });
                    }, 1000);

                    const messageDiv = $('<div>')
                        .addClass('cart-message')
                        .html('<i class="fa fa-check-circle"></i> Item added to cart successfully!')
                        .appendTo('body');

                    setTimeout(function() {
                        messageDiv.fadeOut(function() {
                            $(this).remove();
                        });
                    }, 3000);
                } else {
                    button.html('Add to Cart');
                    button.prop('disabled', false);
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                button.html('Add to Cart');
                button.prop('disabled', false);
                console.error('Error:', error);
                alert('Connection error. Please try again.');
            }
        });
    }

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

    function updateWishlistCount() {
        $.ajax({
            url: 'get_wishlist_count.php',
            type: 'GET',
            success: function(response) {
                try {
                    var result = JSON.parse(response);
                    $('.wishlist-count').text(result.count);
                } catch (e) {
                    console.error('Error updating wishlist count:', e);
                }
            }
        });
    }

    // Update the existing addToWishlist function
    function addToWishlist(itemId) {
        const button = $(`a[onclick="addToWishlist(${itemId})"]`);
        const originalHtml = button.html();
        button.html('<i class="fa fa-spinner fa-spin"></i>');
        button.addClass('disabled');

        $.ajax({
            url: 'wishlist_operations.php',
            type: 'POST',
            data: {
                action: 'add',
                item_id: itemId
            },
            success: function(response) {
                try {
                    response = typeof response === 'string' ? JSON.parse(response) : response;

                    if (response.status === 'ADDED') {
                        // Show success message and refresh page
                        const messageDiv = $('<div>')
                            .addClass('wishlist-message')
                            .html('<i class="fa fa-check-circle"></i> Item added to wishlist successfully!')
                            .appendTo('body');

                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    } else if (response.status === 'EXISTS') {
                        button.html(originalHtml);
                        button.removeClass('disabled');

                        const messageDiv = $('<div>')
                            .addClass('wishlist-message')
                            .css('background-color', '#17a2b8')
                            .html('<i class="fa fa-info-circle"></i> Item already in wishlist')
                            .appendTo('body');

                        setTimeout(function() {
                            messageDiv.fadeOut(function() {
                                $(this).remove();
                            });
                        }, 3000);
                    }
                } catch (e) {
                    console.error('Error:', e);
                    button.html(originalHtml);
                    button.removeClass('disabled');

                    const messageDiv = $('<div>')
                        .addClass('wishlist-message')
                        .css('background-color', '#dc3545')
                        .html(
                            '<i class="fa fa-exclamation-circle"></i> An error occurred. Please try again.')
                        .appendTo('body');

                    setTimeout(function() {
                        messageDiv.fadeOut(function() {
                            $(this).remove();
                        });
                    }, 3000);
                }
            },
            error: function() {
                button.html(originalHtml);
                button.removeClass('disabled');

                const messageDiv = $('<div>')
                    .addClass('wishlist-message')
                    .css('background-color', '#dc3545')
                    .html('<i class="fa fa-exclamation-circle"></i> Connection error. Please try again.')
                    .appendTo('body');

                setTimeout(function() {
                    messageDiv.fadeOut(function() {
                        $(this).remove();
                    });
                }, 3000);
            },
            dataType: 'json'
        });
    }
    $(document).ready(function() {
        $('#review-form').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);
            const submitButton = form.find('button[type="submit"]');
            const originalButtonText = submitButton.text();

            submitButton.prop('disabled', true).text('Submitting...');

            $.ajax({
                url: 'submit_review.php',
                type: 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Replace form with success message
                        $('#review-form').fadeOut(function() {
                            $('#review-response').html(`
                            <div class="alert alert-success">
                                <i class="fa fa-check-circle"></i> ${response.message}
                            </div>
                        `).fadeIn();

                            // Reload page after 2 seconds to show new review
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        });
                    } else {
                        $('#review-response').html(`
                        <div class="alert alert-danger">
                            <i class="fa fa-exclamation-circle"></i> ${response.message}
                        </div>
                    `);
                        submitButton.prop('disabled', false).text(originalButtonText);
                    }
                },
                error: function() {
                    $('#review-response').html(`
                    <div class="alert alert-danger">
                        <i class="fa fa-exclamation-circle"></i> Something went wrong. Please try again.
                    </div>
                `);
                    submitButton.prop('disabled', false).text(originalButtonText);
                }
            });
        });
    });

    function buyNow(itemId) {
        const quantity = document.getElementById('item-quantity').value;
        // First add to cart
        $.ajax({
            url: 'cart_operations.php',
            type: 'POST',
            data: {
                action: 'add_to_cart',
                item_id: itemId,
                qnty: 1
            },
            success: function(response) {
                if (response.success) {
                    // Redirect directly to checkout page
                    window.location.href = 'cart.php';
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Error occurred while processing your request');
            }
        });
    }

    function addGiftToCart(itemId) {
        const clickedButton = $(`.gift-btn[data-item-id="${itemId}"]`);
        clickedButton.html('<i class="fa fa-spinner fa-spin"></i> Adding...');
        clickedButton.prop('disabled', true);

        $.ajax({
            url: 'cart_operations.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'add_gift_to_cart',
                item_id: itemId
            },
            success: function(response) {
                if (response.success) {
                    updateMiniCart();

                    // Convert clicked button to View Cart link
                    $(`.gift-btn[data-item-id="${itemId}"]`).each(function() {
                        $(this)
                            .removeClass('gift-btn')
                            .addClass('btn-cart')
                            .html('View Cart')
                            .prop('disabled', false)
                            .attr('onclick', '')
                            .wrap(`<a href="cart.php"></a>`);
                    });

                    // Update all other gift buttons to show Gift Limit Reached
                    $(`.gift-btn:not([data-item-id="${itemId}"])`).each(function() {
                        $(this)
                            .removeClass('gift-btn')
                            .addClass('btn-cart')
                            .html('Gift Limit Reached')
                            .prop('disabled', true)
                            .removeAttr('onclick');
                    });

                    const messageDiv = $('<div>')
                        .addClass('cart-message')
                        .html('<i class="fa fa-check-circle"></i> Gift added to cart successfully!')
                        .appendTo('body');

                    setTimeout(function() {
                        messageDiv.fadeOut(function() {
                            $(this).remove();
                        });
                    }, 3000);
                } else {
                    if (response.message.includes('999')) {
                        clickedButton.prop('disabled', true)
                            .html('Shop for ₹999+ to Get Free Gift');
                    } else {
                        clickedButton.html('Add Free Gift');
                        clickedButton.prop('disabled', false);
                        alert('Error: ' + response.message);
                    }
                }
            },
            error: function(xhr, status, error) {
                clickedButton.html('Add Free Gift');
                clickedButton.prop('disabled', false);
                console.error('Error:', error);
                alert('Connection error. Please try again.');
            }
        });
    }
    </script>
</body>

</html>