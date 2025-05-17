<?php require_once('Connections/orek.php'); ?>
<?php require_once('session-2.php'); ?>
<?php
// Get the user's IP address
$ip_address = $_SERVER['REMOTE_ADDR'];

// Get the user agent (browser/device information)
$user_agent = $_SERVER['HTTP_USER_AGENT'];

// Get the current page URL
$page_url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

// Insert visit information into the database
$query = "INSERT INTO site_visits (ip_address, user_agent, page_url) 
          VALUES (?, ?, ?)";

$stmt = mysqli_prepare($orek, $query);
mysqli_stmt_bind_param($stmt, 'sss', $ip_address, $user_agent, $page_url);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

$query_Recordset2 = "SELECT * FROM banner";
$Recordset2 = mysqli_query( $orek, $query_Recordset2 )or die( mysqli_error( $orek ) );
$row_Recordset2 = mysqli_fetch_assoc( $Recordset2 );
$totalRows_Recordset2 = mysqli_num_rows( $Recordset2 );

$query_Recordset3 = "SELECT * FROM category";
$Recordset3 = mysqli_query( $orek, $query_Recordset3 )or die( mysqli_error( $orek ) );
$row_Recordset3 = mysqli_fetch_assoc( $Recordset3 );
$totalRows_Recordset3 = mysqli_num_rows( $Recordset3 );

$query_Recordset5 = "SELECT * FROM blogs";
$Recordset5 = mysqli_query( $orek, $query_Recordset5 )or die( mysqli_error( $orek ) );
$row_Recordset5 = mysqli_fetch_assoc( $Recordset5 );
$totalRows_Recordset5 = mysqli_num_rows( $Recordset5 );

$query_Recordset6 = "SELECT * FROM category";
$Recordset6 = mysqli_query( $orek, $query_Recordset6 )or die( mysqli_error( $orek ) );
$row_Recordset6 = mysqli_fetch_assoc( $Recordset6 );
$totalRows_Recordset6 = mysqli_num_rows( $Recordset6 );

$query_Recordset7 = "SELECT A.item_id, A.item_name, A.price, A.discount, A.image_1, COUNT(B.item_id) AS total_sales FROM item A INNER JOIN cart_item B ON A.item_id = B.item_id INNER JOIN cart C ON B.cart_id = C.cart_id WHERE C.status = 'Paid' AND A.listing_status = 'Active' GROUP BY A.item_id, A.item_name, A.price, A.discount, A.image_1 ORDER BY total_sales DESC LIMIT 8";
$Recordset7 = mysqli_query( $orek, $query_Recordset7 )or die( mysqli_error( $orek ) );
$row_Recordset7 = mysqli_fetch_assoc( $Recordset7 );
$totalRows_Recordset7 = mysqli_num_rows( $Recordset7 );


?>

<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>Orek - Designer Jewelry Collection | Earrings, Necklaces & Bracelets</title>
    <meta name="description"
        content="Shop Orek's exquisite collection of designer jewelry including necklaces, bracelets, earrings and more. Find unique pieces for every occasion with free delivery on orders above ₹1500." />
    <meta name="keywords"
        content="jewelry, earrings, necklaces, bracelets, designer jewelry, fashion accessories, Orek" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Canonical URL to prevent duplicate content issues -->
    <link rel="canonical" href="https://<?php echo $_SERVER['HTTP_HOST']; ?>/" />

    <!-- Open Graph tags for social sharing -->
    <meta property="og:title" content="Orek - Designer Jewelry Collection" />
    <meta property="og:description"
        content="Shop our exquisite collection of designer jewelry including necklaces, bracelets, earrings and more." />
    <meta property="og:image" content="https://<?php echo $_SERVER['HTTP_HOST']; ?>/assets/img/logo/logo.png" />
    <meta property="og:url" content="https://<?php echo $_SERVER['HTTP_HOST']; ?>/" />
    <meta property="og:type" content="website" />

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="Orek - Designer Jewelry Collection" />
    <meta name="twitter:description"
        content="Shop our exquisite collection of designer jewelry including necklaces, bracelets, earrings and more." />
    <meta name="twitter:image" content="https://<?php echo $_SERVER['HTTP_HOST']; ?>/assets/img/logo/logo.png" />

    <!-- Mobile-specific meta -->
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-title" content="Orek" />

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/logo/logo.png" />
    <link rel="apple-touch-icon" href="assets/img/logo/logo.png" />

    <!-- CSS
	============================================ -->
    <!-- google fonts -->
    <link href="https://fonts.googleapis.com/css?family=Lato:300,300i,400,400i,700,900&display=swap" rel="stylesheet" />
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

    <!-- Structured Data / JSON-LD for better SEO -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "Orek",
        "url": "https://<?php echo $_SERVER['HTTP_HOST']; ?>/",
        "logo": "https://<?php echo $_SERVER['HTTP_HOST']; ?>/assets/img/logo/logo.png",
        "contactPoint": {
            "@type": "ContactPoint",
            "email": "orekaccessories@gmail.com",
            "contactType": "customer service"
        },
        "sameAs": [
            "https://www.facebook.com/orekaccessories",
            "https://www.instagram.com/orekaccessories"
        ]
    }
    </script>
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "url": "https://<?php echo $_SERVER['HTTP_HOST']; ?>/",
        "potentialAction": {
            "@type": "SearchAction",
            "target": "https://<?php echo $_SERVER['HTTP_HOST']; ?>/product-list.php?search={search_term_string}",
            "query-input": "required name=search_term_string"
        }
    }
    </script>
</head>

<body>
    <!-- Start Header Area -->
    <?php require_once('header.php'); ?>
    <!-- end Header Area -->

    <!-- XML Sitemap generation for SEO -->
    <?php
    // Generate sitemap.xml if it doesn't exist or needs to be updated
    $sitemap_file = 'sitemap.xml';
    $sitemap_age = file_exists($sitemap_file) ? (time() - filemtime($sitemap_file)) : 999999;
    
    // Update sitemap weekly (604800 seconds = 1 week)
    if ($sitemap_age > 604800) {
        // This will be processed in a separate script
        file_put_contents('generate_sitemap.php', '<?php include "sitemap_generator.php"; ?>');
    }
    ?>

    <!-- 301 Redirect for old WordPress URLs -->
    <?php
    // Check for old WordPress URL patterns
    $request_uri = $_SERVER['REQUEST_URI'];
    if (strpos($request_uri, '/wp-content/') !== false || 
        strpos($request_uri, '/wp-admin/') !== false || 
        strpos($request_uri, '/wp-includes/') !== false ||
        preg_match('/\?p=\d+/', $request_uri)) {
        
        // Log the redirect for analysis
        $log_file = 'redirect_log.txt';
        $log_message = date('Y-m-d H:i:s') . ' - ' . $_SERVER['REMOTE_ADDR'] . ' - ' . $request_uri . "\n";
        @file_put_contents($log_file, $log_message, FILE_APPEND);
        
        // Redirect to homepage with 301 status
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: https://" . $_SERVER['HTTP_HOST'] . "/");
        exit();
    }
    ?>

    <!-- Coupon Popup Modal Start -->
    <div class="coupon-popup-overlay" id="couponPopupOverlay">
        <div class="coupon-popup-container">
            <button class="coupon-popup-close" id="couponPopupClose">
                <i class="fa fa-times"></i>
            </button>
            <div class="coupon-popup-content">
                <div class="row">
                    <div class="col-md-6 d-none d-md-block coupon-popup-image">
                        <img src="assets/img/offer/offer.png" alt="Special Offer">
                    </div>
                    <div class="col-12 col-md-6 coupon-popup-form">
                        <h3>Exclusive Discount For You!</h3>
                        <div class="discount-announcement">
                            <span class="discount-value">Get Up To 30% OFF</span>
                            <span class="discount-text">on your first purchase</span>
                        </div>
                        <p>Enter your email to receive your exclusive coupon code</p>
                        <form id="couponEmailForm" method="post">
                            <div class="coupon-form-group">
                                <input type="email" name="email" id="couponEmail" placeholder="Your Email Address"
                                    required>
                                <input type="hidden" name="coupon_code" id="hiddenCouponCode" value="">
                            </div>
                            <button type="submit" class="coupon-submit-btn">Send Me The Coupon</button>
                        </form>
                        <div class="coupon-popup-footer">
                            <p>* Offer valid for limited time only</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Coupon Popup Modal End -->

    <main>
        <!-- hero slider area start -->
        <section class="slider-area">
            <div class="hero-slider-active slick-arrow-style slick-arrow-style_hero slick-dot-style">
                <?php do {?>
                <!-- single slider item start -->
                <div class="hero-single-slide">
                    <div class="hero-slider-item bg-img"
                        data-bg="assets/img/banner/<?php echo $row_Recordset2['image_1']; ?>">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="hero-slider-content slide-1">
                                        <h2 class="slide-title">
                                            <?php
                                            $banner_name = $row_Recordset2['banner_name'];
                                            // String ko words me convert karein
                                            $words = explode(" ", $banner_name);
                                            // Aakhri word nikal lein
                                            $last_word = array_pop($words);
                                            // Baki ka string wapas jod dein
                                            $remaining_text = implode(" ", $words);
                                            echo $remaining_text . ' <span>' . $last_word . '</span>';
                                            ?>
                                        </h2>
                                        <h4 class="slide-desc">
                                            Designer Jewelry Necklaces-Bracelets-Earings
                                        </h4>
                                        <a href="product-details.php?item_id=<?php echo $row_Recordset2['item_id']; ?>"
                                            class="btn btn-hero">Read More</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- single slider item start -->
                <?php }while($row_Recordset2=mysqli_fetch_assoc($Recordset2)); ?>
            </div>
        </section>
        <!-- hero slider area end -->

        <!-- product area start -->
        <section class="product-area section-padding mt-5">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <!-- section title start -->
                        <div class="section-title text-center">
                            <h2 class="title">OUR LATEST COLLECTION</h2>
                            <p class="sub-title">Add our products to weekly lineup</p>
                        </div>
                        <!-- section title start -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="product-container">
                            <!-- product tab menu start -->
                            <div class="product-tab-menu">
                                <ul class="nav justify-content-center">
                                    <?php 
                                    mysqli_data_seek($Recordset6, 0); // Reset pointer to beginning
                                    $first = true;
                                    $row_Recordset6 = mysqli_fetch_assoc($Recordset6);
                                    while ($row_Recordset6) { ?>
                                    <li>
                                        <a href="#tab<?php echo $row_Recordset6['category_id']; ?>"
                                            class="<?php echo $first ? 'active' : ''; ?>" data-bs-toggle="tab">
                                            <?php echo $row_Recordset6['category_name']; ?>
                                        </a>
                                    </li>
                                    <?php 
                                    $first = false;
                                    $row_Recordset6 = mysqli_fetch_assoc($Recordset6);
                                    } ?>
                                </ul>
                            </div>
                            <!-- product tab menu end -->

                            <!-- product tab content start -->
                            <div class="tab-content">
                                <?php 
                                mysqli_data_seek($Recordset6, 0); // Reset pointer to beginning
                                $first = true;
                                $row_Recordset6 = mysqli_fetch_assoc($Recordset6);
                                while ($row_Recordset6) { 
                                    // Get products for current category
                                    $category_id = $row_Recordset6['category_id'];
                                    $query_products = "SELECT A.*, B.category_id, B.category_name
                                                     FROM item AS A 
                                                     INNER JOIN category AS B ON A.category_id = B.category_id 
                                                     WHERE B.category_id = '{$category_id}' AND A.listing_status = 'Active'
                                                     ORDER BY A.item_id DESC LIMIT 5";
                                    $products_result = mysqli_query($orek, $query_products) or die(mysqli_error($orek));
                                ?>
                                <div class="tab-pane fade <?php echo $first ? 'show active' : ''; ?>"
                                    id="tab<?php echo $category_id; ?>">
                                    <div class="product-carousel-4 slick-row-10 slick-arrow-style">
                                        <?php while($product = mysqli_fetch_assoc($products_result)) { ?>
                                        <div class="product-item">
                                            <figure class="product-thumb">
                                                <a
                                                    href="product-details.php?item_id=<?php echo $product['item_id']; ?>">
                                                    <img class="pri-img"
                                                        src="assets/img/item/<?php echo $product['image_1']; ?>"
                                                        alt="product">
                                                    <img class="sec-img"
                                                        src="assets/img/item/<?php echo $product['image_1']; ?>"
                                                        alt="product">
                                                </a>
                                                <div class="product-badge">
                                                    <div class="product-label new">
                                                        <span>new</span>
                                                    </div>
                                                    <div class="product-label discount">
                                                        <span><?php echo $product['discount']; ?>%</span>
                                                    </div>
                                                </div>
                                                <div class="cart-hover">
                                                    <?php
                                                    if(isset($_SESSION['email'])) {
                                                        $stmt = mysqli_prepare($orek, "CALL CheckItemInCart(?, ?, @is_in_cart)");
                                                        $email = $_SESSION['email'];
                                                        $item_id = $product['item_id'];
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
                                                        onclick="addToCartProc(<?php echo $item_id; ?>)">
                                                        Add to Cart
                                                    </button>
                                                    <?php endif;
                                                    } else { ?>
                                                    <a href="login.php" class="btn btn-cart">Add to Cart</a>
                                                    <?php } ?>
                                                </div>
                                            </figure>
                                            <div class="product-caption text-center">
                                                <div class="product-identity">
                                                    <p class="manufacturer-name">
                                                        <a
                                                            href="product-details.php?item_id=<?php echo $product['item_id']; ?>">
                                                            <?php echo $product['category_name']; ?>
                                                        </a>
                                                    </p>
                                                </div>
                                                <h6 class="product-name">
                                                    <a
                                                        href="product-details.php?item_id=<?php echo $product['item_id']; ?>">
                                                        <?php echo $product['item_name']; ?>
                                                    </a>
                                                </h6>
                                                <div class="price-box">
                                                    <?php
                                                    $actual_price = $product['price'];
                                                    $discount_percentage = $product['discount'];
                                                    $discounted_price = $actual_price - ($actual_price * ($discount_percentage / 100));
                                                    ?>
                                                    <span
                                                        class="price-regular">₹<?php echo number_format($discounted_price, 0); ?></span>
                                                    <?php if($discount_percentage > 0): ?>
                                                    <span
                                                        class="price-old"><del>₹<?php echo number_format($actual_price, 0); ?></del></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </div>
                                    <div class="text-center mt-3">
                                        <a href="product-list.php?category_id=<?php echo $category_id; ?>"
                                            class="btn btn-gift">View All
                                            <?php echo $row_Recordset6['category_name']; ?></a>
                                    </div>
                                </div>
                                <?php 
                                $first = false;
                                mysqli_free_result($products_result);
                                $row_Recordset6 = mysqli_fetch_assoc($Recordset6);
                                } 
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- product area end -->
        <!-- Must haves Mood product area end -->

        <!-- Gift Section Start -->
        <section class="gift-section py-5">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <div class="gift-content text-center text-lg-start">
                            <h2 class="gift-title mb-4">Special Gift on Orders Above ₹999</h2>
                            <p class="gift-description mb-4">
                                Shop for ₹999 or more and receive an exclusive Orek surprise gift!
                                Choose from our stunning collection of jewelry pieces and get rewarded
                                with a special gift from us. Make your shopping experience even more
                                memorable with Orek's complimentary gifts.
                            </p>
                            <div class="special-offer-badge mb-4">
                                <div class="offer-circle">
                                    <span class="offer-amount">₹999</span>
                                    <span class="offer-text">Min. Purchase</span>
                                </div>
                            </div>
                            <div class="gift-features mb-4">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <div class="feature-item">
                                            <i class="fas fa-gift mb-2"></i>
                                            <h5>Free Gift Box</h5>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="feature-item">
                                            <i class="fas fa-birthday-cake mb-2"></i>
                                            <h5>Birthday Special</h5>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="feature-item">
                                            <i class="fas fa-hand-holding-heart mb-2"></i>
                                            <h5>Anniversary Gifts</h5>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="feature-item">
                                            <i class="fas fa-star mb-2"></i>
                                            <h5>Special Occasions</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a href="product-list-gift.php" class="btn btn-gift">Explore Gift Collection</a>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="gift-image position-relative">
                            <img src="assets/img/offer/offer.png" alt="Gift Collection" class="img-fluid rounded-3">
                            <div class="gift-overlay">
                                <div class="gift-badge">
                                    <span class="badge-text">Special</span>
                                    <span class="badge-title">Gift Collection</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Gift Section End -->

        <!-- footer start -->
        <!-- product banner statistics start -->
        <section class="shop-category-section">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="section-title text-center">
                            <h2 class="title">SHOP BY CATEGORY</h2>
                        </div>
                    </div>
                </div>

                <!-- Horizontal Scroll for all devices -->
                <div class="category-scroll-wrapper">
                    <div class="category-scroll-row">
                        <?php 
                        mysqli_data_seek($Recordset3, 0); // Reset pointer to beginning
                        $row_Recordset3 = mysqli_fetch_assoc($Recordset3);
                        do { ?>
                        <div class="category-scroll-col">
                            <div class="category-card">
                                <a href="product-list.php?category_id=<?php echo $row_Recordset3['category_id']; ?>">
                                    <img src="assets/img/category/<?php echo $row_Recordset3['image_1']; ?>"
                                        alt="<?php echo $row_Recordset3['category_name']; ?>">
                                </a>
                                <div class="category-content">
                                    <h5>
                                        <a
                                            href="product-list.php?category_id=<?php echo $row_Recordset3['category_id']; ?>">
                                            <?php echo $row_Recordset3['category_name']; ?>
                                        </a>
                                    </h5>
                                    <a href="product-list.php?category_id=<?php echo $row_Recordset3['category_id']; ?>"
                                        class="category-btn">
                                        SHOP NOW
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php } while($row_Recordset3=mysqli_fetch_assoc($Recordset3)); ?>
                    </div>
                </div>
            </div>
        </section>
        <!-- product banner statistics end -->

        <!-- Vacay Mood product area start -->
        <section class="mood-product-section">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <!-- section title start -->
                        <div class="section-title text-center">
                            <h2 class="title">Vacay Mood</h2>
                            <p class="sub-title">Shop by vacation mood</p>
                        </div>
                        <!-- section title end -->
                    </div>
                </div>

                <!-- Horizontal Scroll for all devices -->
                <div class="mood-products-wrapper">
                    <div class="mood-products-row">
                        <?php 
                        $query_Recordset4 = "SELECT * FROM item WHERE mood = 'Vacay' AND listing_status = 'Active' ORDER BY RAND()";
                        $Recordset4 = mysqli_query( $orek, $query_Recordset4 )or die( mysqli_error( $orek ) );
                        $row_Recordset4 = mysqli_fetch_assoc( $Recordset4 );
                        $totalRows_Recordset4 = mysqli_num_rows( $Recordset4 );
                        do {
                        ?>
                        <!-- product item start -->
                        <div class="mood-product-col">
                            <div class="mood-product-item">
                                <div class="mood-product-thumb">
                                    <a href="product-details.php?item_id=<?php echo $row_Recordset4['item_id']; ?>">
                                        <img class="pri-img"
                                            src="assets/img/item/<?php echo $row_Recordset4['image_1']; ?>"
                                            alt="<?php echo $row_Recordset4['item_name']; ?>">
                                        <img class="sec-img"
                                            src="assets/img/item/<?php echo $row_Recordset4['image_2'] == '' ? $row_Recordset4['image_1'] : $row_Recordset4['image_2']; ?>"
                                            alt="<?php echo $row_Recordset4['item_name']; ?>">
                                    </a>
                                    <div class="mood-product-badge">
                                        <?php if(strtotime($row_Recordset4['date_added']) > strtotime('-30 days')) { ?>
                                        <div class="product-label new">
                                            <span>New</span>
                                        </div>
                                        <?php } ?>
                                        <?php if($row_Recordset4['discount'] > 0) { ?>
                                        <div class="product-label discount">
                                            <span><?php echo $row_Recordset4['discount']; ?>% Off</span>
                                        </div>
                                        <?php } ?>
                                    </div>
                                    <div class="mood-cart-hover">
                                        <?php
                                            if(isset($_SESSION['email'])) {
                                            // Clear previous results
                                            while (mysqli_next_result($orek)) {;}
                                            
                                            // Initialize output parameters
                                            mysqli_query($orek, "SET @is_in_cart = 0");
                                            
                                            // Call stored procedure to check cart status
                                            $stmt = mysqli_prepare($orek, "CALL CheckItemInCart(?, ?, @is_in_cart)");
                                            $email = $_SESSION['email'];
                                            $item_id = $row_Recordset4['item_id'];
                                            mysqli_stmt_bind_param($stmt, "si", $email, $item_id);
                                            mysqli_stmt_execute($stmt);
                                            mysqli_stmt_close($stmt);
                                            
                                            // Get result from procedure
                                            $result = mysqli_query($orek, "SELECT @is_in_cart as in_cart");
                                            $row = mysqli_fetch_assoc($result);
                                            $in_cart = (bool)$row['in_cart'];
                                            mysqli_free_result($result);
                                            
                                            if($in_cart): ?>
                                        <a href="cart.php" class="mood-cart-btn">View Cart</a>
                                        <?php else: ?>
                                        <button class="mood-cart-btn" onclick="addToCartProc(<?php echo $item_id; ?>)">
                                            Add to Cart
                                        </button>
                                        <?php endif;
                                        } else { ?>
                                        <a href="login.php" class="mood-cart-btn">Add to Cart</a>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="mood-product-content">
                                    <h6 class="mood-product-title">
                                        <a href="product-details.php?item_id=<?php echo $row_Recordset4['item_id']; ?>">
                                            <?php echo $row_Recordset4['item_name']; ?>
                                        </a>
                                    </h6>
                                    <div class="mood-product-price">
                                        <?php
                                        $actual_price = $row_Recordset4['price'];
                                        $discount_percentage = $row_Recordset4['discount'];
                                        $discounted_price = $actual_price - ($actual_price * ($discount_percentage / 100));
                                        ?>
                                        <span
                                            class="mood-price-regular">₹<?php echo number_format($discounted_price, 0); ?></span>
                                        <?php if($discount_percentage > 0): ?>
                                        <span
                                            class="mood-price-old">₹<?php echo number_format($actual_price, 0); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- product item end -->
                        <?php } while($row_Recordset4 = mysqli_fetch_assoc($Recordset4)); ?>
                    </div>
                </div>
            </div>
        </section>
        <!-- Vacay Mood product area end -->

        <!-- Office Mood product area start -->
        <section class="mood-product-section">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <!-- section title start -->
                        <div class="section-title text-center">
                            <h2 class="title">Office Mood</h2>
                            <p class="sub-title">Shop by office mood</p>
                        </div>
                        <!-- section title end -->
                    </div>
                </div>

                <!-- Horizontal Scroll for all devices -->
                <div class="mood-products-wrapper">
                    <div class="mood-products-row">
                        <?php 
                        $query_Recordset4 = "SELECT * FROM item WHERE mood = 'Office' AND listing_status = 'Active' ORDER BY RAND()";
                        $Recordset4 = mysqli_query( $orek, $query_Recordset4 )or die( mysqli_error( $orek ) );
                        $row_Recordset4 = mysqli_fetch_assoc( $Recordset4 );
                        $totalRows_Recordset4 = mysqli_num_rows( $Recordset4 );
                        do {
                        ?>
                        <!-- product item start -->
                        <div class="mood-product-col">
                            <div class="mood-product-item">
                                <div class="mood-product-thumb">
                                    <a href="product-details.php?item_id=<?php echo $row_Recordset4['item_id']; ?>">
                                        <img class="pri-img"
                                            src="assets/img/item/<?php echo $row_Recordset4['image_1']; ?>"
                                            alt="<?php echo $row_Recordset4['item_name']; ?>">
                                        <img class="sec-img"
                                            src="assets/img/item/<?php echo $row_Recordset4['image_2'] == '' ? $row_Recordset4['image_1'] : $row_Recordset4['image_2']; ?>"
                                            alt="<?php echo $row_Recordset4['item_name']; ?>">
                                    </a>
                                    <div class="mood-product-badge">
                                        <?php if(strtotime($row_Recordset4['date_added']) > strtotime('-30 days')) { ?>
                                        <div class="product-label new">
                                            <span>New</span>
                                        </div>
                                        <?php } ?>
                                        <?php if($row_Recordset4['discount'] > 0) { ?>
                                        <div class="product-label discount">
                                            <span><?php echo $row_Recordset4['discount']; ?>% Off</span>
                                        </div>
                                        <?php } ?>
                                    </div>
                                    <div class="mood-cart-hover">
                                        <?php
                                        if(isset($_SESSION['email'])) {
                                            // Clear previous results
                                            while (mysqli_next_result($orek)) {;}
                                            
                                            // Initialize output parameters
                                            mysqli_query($orek, "SET @is_in_cart = 0");
                                            
                                        // Call stored procedure to check cart status
                                        $stmt = mysqli_prepare($orek, "CALL CheckItemInCart(?, ?, @is_in_cart)");
                                        $email = $_SESSION['email'];
                                        $item_id = $row_Recordset4['item_id'];
                                        mysqli_stmt_bind_param($stmt, "si", $email, $item_id);
                                        mysqli_stmt_execute($stmt);
                                        mysqli_stmt_close($stmt);
                                        
                                        // Get result from procedure
                                        $result = mysqli_query($orek, "SELECT @is_in_cart as in_cart");
                                        $row = mysqli_fetch_assoc($result);
                                        $in_cart = (bool)$row['in_cart'];
                                        mysqli_free_result($result);
                                        
                                        if($in_cart): ?>
                                        <a href="cart.php" class="mood-cart-btn">View Cart</a>
                                        <?php else: ?>
                                        <button class="mood-cart-btn" onclick="addToCartProc(<?php echo $item_id; ?>)">
                                            Add to Cart
                                        </button>
                                        <?php endif;
                                        } else { ?>
                                        <a href="login.php" class="mood-cart-btn">Add to Cart</a>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="mood-product-content">
                                    <h6 class="mood-product-title">
                                        <a href="product-details.php?item_id=<?php echo $row_Recordset4['item_id']; ?>">
                                            <?php echo $row_Recordset4['item_name']; ?>
                                        </a>
                                    </h6>
                                    <div class="mood-product-price">
                                        <?php
                                        $actual_price = $row_Recordset4['price'];
                                        $discount_percentage = $row_Recordset4['discount'];
                                        $discounted_price = $actual_price - ($actual_price * ($discount_percentage / 100));
                                        ?>
                                        <span
                                            class="mood-price-regular">₹<?php echo number_format($discounted_price, 0); ?></span>
                                        <?php if($discount_percentage > 0): ?>
                                        <span
                                            class="mood-price-old">₹<?php echo number_format($actual_price, 0); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- product item end -->
                        <?php } while($row_Recordset4 = mysqli_fetch_assoc($Recordset4)); ?>
                    </div>
                </div>
            </div>
        </section>
        <!-- Office Mood product area end -->

        <!-- Night out Mood product area start -->
        <section class="mood-product-section">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <!-- section title start -->
                        <div class="section-title text-center">
                            <h2 class="title">Night out Mood</h2>
                            <p class="sub-title">Shop by night out mood</p>
                        </div>
                        <!-- section title end -->
                    </div>
                </div>

                <!-- Horizontal Scroll for all devices -->
                <div class="mood-products-wrapper">
                    <div class="mood-products-row">
                        <?php 
                        $query_Recordset4 = "SELECT * FROM item WHERE mood = 'Night out' AND listing_status = 'Active' ORDER BY RAND()";
                        $Recordset4 = mysqli_query( $orek, $query_Recordset4 )or die( mysqli_error( $orek ) );
                        $row_Recordset4 = mysqli_fetch_assoc( $Recordset4 );
                        $totalRows_Recordset4 = mysqli_num_rows( $Recordset4 );
                        do {
                        ?>
                        <!-- product item start -->
                        <div class="mood-product-col">
                            <div class="mood-product-item">
                                <div class="mood-product-thumb">
                                    <a href="product-details.php?item_id=<?php echo $row_Recordset4['item_id']; ?>">
                                        <img class="pri-img"
                                            src="assets/img/item/<?php echo $row_Recordset4['image_1']; ?>"
                                            alt="<?php echo $row_Recordset4['item_name']; ?>">
                                        <img class="sec-img"
                                            src="assets/img/item/<?php echo $row_Recordset4['image_2'] == '' ? $row_Recordset4['image_1'] : $row_Recordset4['image_2']; ?>"
                                            alt="<?php echo $row_Recordset4['item_name']; ?>">
                                    </a>
                                    <div class="mood-product-badge">
                                        <?php if(strtotime($row_Recordset4['date_added']) > strtotime('-30 days')) { ?>
                                        <div class="product-label new">
                                            <span>New</span>
                                        </div>
                                        <?php } ?>
                                        <?php if($row_Recordset4['discount'] > 0) { ?>
                                        <div class="product-label discount">
                                            <span><?php echo $row_Recordset4['discount']; ?>% Off</span>
                                        </div>
                                        <?php } ?>
                                    </div>
                                    <div class="mood-cart-hover">
                                        <?php
                                        if(isset($_SESSION['email'])) {
                                            // Clear previous results
                                            while (mysqli_next_result($orek)) {;}
                                            
                                            // Initialize output parameters
                                            mysqli_query($orek, "SET @is_in_cart = 0");
                                            
                                            // Call stored procedure to check cart status
                                            $stmt = mysqli_prepare($orek, "CALL CheckItemInCart(?, ?, @is_in_cart)");
                                            $email = $_SESSION['email'];
                                            $item_id = $row_Recordset4['item_id'];
                                            mysqli_stmt_bind_param($stmt, "si", $email, $item_id);
                                            mysqli_stmt_execute($stmt);
                                            mysqli_stmt_close($stmt);
                                            
                                            // Get result from procedure
                                            $result = mysqli_query($orek, "SELECT @is_in_cart as in_cart");
                                            $row = mysqli_fetch_assoc($result);
                                            $in_cart = (bool)$row['in_cart'];
                                            mysqli_free_result($result);
                                            
                                            if($in_cart): ?>
                                        <a href="cart.php" class="mood-cart-btn">View Cart</a>
                                        <?php else: ?>
                                        <button class="mood-cart-btn" onclick="addToCartProc(<?php echo $item_id; ?>)">
                                            Add to Cart
                                        </button>
                                        <?php endif;
                                        } else { ?>
                                        <a href="login.php" class="mood-cart-btn">Add to Cart</a>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="mood-product-content">
                                    <h6 class="mood-product-title">
                                        <a href="product-details.php?item_id=<?php echo $row_Recordset4['item_id']; ?>">
                                            <?php echo $row_Recordset4['item_name']; ?>
                                        </a>
                                    </h6>
                                    <div class="mood-product-price">
                                        <?php
                                        $actual_price = $row_Recordset4['price'];
                                        $discount_percentage = $row_Recordset4['discount'];
                                        $discounted_price = $actual_price - ($actual_price * ($discount_percentage / 100));
                                        ?>
                                        <span
                                            class="mood-price-regular">₹<?php echo number_format($discounted_price, 0); ?></span>
                                        <?php if($discount_percentage > 0): ?>
                                        <span
                                            class="mood-price-old">₹<?php echo number_format($actual_price, 0); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- product item end -->
                        <?php } while($row_Recordset4 = mysqli_fetch_assoc($Recordset4)); ?>
                    </div>
                </div>
            </div>
        </section>
        <!-- Night out Mood product area end -->

        <!-- Must haves Mood product area start -->
        <section class="mood-product-section">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <!-- section title start -->
                        <div class="section-title text-center">
                            <h2 class="title">Must haves Mood</h2>
                            <p class="sub-title">Shop by must haves mood</p>
                        </div>
                        <!-- section title end -->
                    </div>
                </div>

                <!-- Horizontal Scroll for all devices -->
                <div class="mood-products-wrapper">
                    <div class="mood-products-row">
                        <?php 
                        $query_Recordset4 = "SELECT * FROM item WHERE mood = 'Must haves' AND listing_status = 'Active' ORDER BY RAND()";
                        $Recordset4 = mysqli_query( $orek, $query_Recordset4 )or die( mysqli_error( $orek ) );
                        $row_Recordset4 = mysqli_fetch_assoc( $Recordset4 );
                        $totalRows_Recordset4 = mysqli_num_rows( $Recordset4 );
                        do {
                        ?>
                        <!-- product item start -->
                        <div class="mood-product-col">
                            <div class="mood-product-item">
                                <div class="mood-product-thumb">
                                    <a href="product-details.php?item_id=<?php echo $row_Recordset4['item_id']; ?>">
                                        <img class="pri-img"
                                            src="assets/img/item/<?php echo $row_Recordset4['image_1']; ?>"
                                            alt="<?php echo $row_Recordset4['item_name']; ?>">
                                        <img class="sec-img"
                                            src="assets/img/item/<?php echo $row_Recordset4['image_2'] == '' ? $row_Recordset4['image_1'] : $row_Recordset4['image_2']; ?>"
                                            alt="<?php echo $row_Recordset4['item_name']; ?>">
                                    </a>
                                    <div class="mood-product-badge">
                                        <?php if(strtotime($row_Recordset4['date_added']) > strtotime('-30 days')) { ?>
                                        <div class="product-label new">
                                            <span>New</span>
                                        </div>
                                        <?php } ?>
                                        <?php if($row_Recordset4['discount'] > 0) { ?>
                                        <div class="product-label discount">
                                            <span><?php echo $row_Recordset4['discount']; ?>% Off</span>
                                        </div>
                                        <?php } ?>
                                    </div>
                                    <div class="mood-cart-hover">
                                        <?php
                                        if(isset($_SESSION['email'])) {
                                            // Clear previous results
                                            while (mysqli_next_result($orek)) {;}
                                            
                                            // Initialize output parameters
                                            mysqli_query($orek, "SET @is_in_cart = 0");
                                            
                                            // Call stored procedure to check cart status
                                            $stmt = mysqli_prepare($orek, "CALL CheckItemInCart(?, ?, @is_in_cart)");
                                            $email = $_SESSION['email'];
                                            $item_id = $row_Recordset4['item_id'];
                                            mysqli_stmt_bind_param($stmt, "si", $email, $item_id);
                                            mysqli_stmt_execute($stmt);
                                            mysqli_stmt_close($stmt);
                                            
                                            // Get result from procedure
                                            $result = mysqli_query($orek, "SELECT @is_in_cart as in_cart");
                                            $row = mysqli_fetch_assoc($result);
                                            $in_cart = (bool)$row['in_cart'];
                                            mysqli_free_result($result);
                                            
                                            if($in_cart): ?>
                                        <a href="cart.php" class="mood-cart-btn">View Cart</a>
                                        <?php else: ?>
                                        <button class="mood-cart-btn" onclick="addToCartProc(<?php echo $item_id; ?>)">
                                            Add to Cart
                                        </button>
                                        <?php endif;
                                        } else { ?>
                                        <a href="login.php" class="mood-cart-btn">Add to Cart</a>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="mood-product-content">
                                    <h6 class="mood-product-title">
                                        <a href="product-details.php?item_id=<?php echo $row_Recordset4['item_id']; ?>">
                                            <?php echo $row_Recordset4['item_name']; ?>
                                        </a>
                                    </h6>
                                    <div class="mood-product-price">
                                        <?php
                                        $actual_price = $row_Recordset4['price'];
                                        $discount_percentage = $row_Recordset4['discount'];
                                        $discounted_price = $actual_price - ($actual_price * ($discount_percentage / 100));
                                        ?>
                                        <span
                                            class="mood-price-regular">₹<?php echo number_format($discounted_price, 0); ?></span>
                                        <?php if($discount_percentage > 0): ?>
                                        <span
                                            class="mood-price-old">₹<?php echo number_format($actual_price, 0); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- product item end -->
                        <?php } while($row_Recordset4 = mysqli_fetch_assoc($Recordset4)); ?>
                    </div>
                </div>
            </div>
        </section>
        <!-- Must haves Mood product area end -->

        <!-- Special Offers Section Start -->
        <section class="special-offers-section">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="section-title text-center">
                        <h2 class="title">EXCLUSIVE OFFERS</h2>
                        <p class="sub-title">Limited time deals you can't miss</p>
                    </div>
                </div>

                <div class="row offer-banner-row">
                    <?php
                    // Fetch offers from database
                    $offer_query = "SELECT o.*, i.item_name, i.price, i.discount 
                                   FROM offer o 
                                   JOIN item i ON o.item_id = i.item_id 
                                   ORDER BY o.offer_id DESC 
                                   LIMIT 4";
                    $offer_result = mysqli_query($orek, $offer_query);
                    
                    if (mysqli_num_rows($offer_result) > 0) {
                        while ($offer_row = mysqli_fetch_assoc($offer_result)) {
                            // Calculate discount percentage if applicable
                            $discount_percent = 0;
                            if ($offer_row['discount'] > 0 && $offer_row['price'] > 0) {
                                $discount_percent = round(($offer_row['price'] - $offer_row['discount']) / $offer_row['price'] * 100);
                            }
                    ?>
                    <div class="col-md-6 mb-4">
                        <div class="offer-banner">
                            <div class="offer-banner-img">
                                <img src="assets/img/offer/<?php echo $offer_row['image_1']; ?>"
                                    alt="<?php echo $offer_row['item_name']; ?>">
                                <div class="offer-banner-overlay">
                                    <div class="offer-banner-content">
                                        <span class="offer-category"><?php echo $offer_row['offer_category']; ?></span>
                                        <h3 class="offer-title"><?php echo $offer_row['item_name']; ?></h3>
                                        <?php if ($discount_percent > 0) { ?>
                                        <div class="offer-price">
                                            <span class="current-price">₹<?php echo $offer_row['discount']; ?></span>
                                            <span class="old-price">₹<?php echo $offer_row['price']; ?></span>
                                        </div>
                                        <div class="discount-badge">
                                            <span><?php echo $discount_percent; ?>% OFF</span>
                                        </div>
                                        <?php } else { ?>
                                        <div class="offer-price">
                                            <span class="current-price">₹<?php echo $offer_row['price']; ?></span>
                                        </div>
                                        <?php } ?>
                                        <a href="product-details.php?item_id=<?php echo $offer_row['item_id']; ?>"
                                            class="btn-shop-now">SHOP NOW</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                        }
                    } else {
                        // If no offers found, display placeholder
                        for ($i = 0; $i < 2; $i++) {
                    ?>
                    <div class="col-md-6 mb-4">
                        <div class="offer-banner">
                            <div class="offer-banner-img">
                                <img src="assets/img/product/placeholder.jpg" alt="Special Offer">
                                <div class="offer-banner-overlay">
                                    <div class="offer-banner-content">
                                        <span class="offer-category">Limited Time</span>
                                        <h3 class="offer-title">Special Collection</h3>
                                        <div class="offer-price">
                                            <span class="current-price">₹1299</span>
                                            <span class="old-price">₹1999</span>
                                        </div>
                                        <div class="discount-badge">
                                            <span>35% OFF</span>
                                        </div>
                                        <a href="#" class="btn-shop-now">SHOP NOW</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </section>
        <!-- Special Offers Section End -->

        <section class="product-banner-statistics">
            <div class="container-fluid py-5">
                <div class="row justify-content-center">
                    <div class="section-title text-center">
                        <h2 class="title">SHOP IN BUDGET</h2>
                        <p class="sub-title">Explore the best deals tailored to your budget</p>
                    </div>
                </div>
                <div class="row text-center">
                    <!-- Single Budget Item -->
                    <div class="col-12 col-sm-6 col-md-3 mb-4">
                        <div class="budget-card p-4 rounded shadow-sm h-100" style="cursor: pointer;"
                            onclick="window.location.href='product-list.php?min_price=0&max_price=599'">
                            <div class="icon-box mb-3">
                                <i class="fas fa-tags fa-2x"></i>
                            </div>
                            <h5 class="fw-bold">Under &#8377;599</h5>
                            <p class="text-muted">Affordable everyday items</p>
                            <a href="product-list.php?min_price=0&max_price=599" class="btn btn-primary">Shop Now</a>
                        </div>
                    </div>
                    <!-- Single Budget Item -->
                    <div class="col-12 col-sm-6 col-md-3 mb-4">
                        <div class="budget-card p-4 rounded shadow-sm h-100" style="cursor: pointer;"
                            onclick="window.location.href='product-list.php?min_price=600&max_price=799'">
                            <div class="icon-box mb-3">
                                <i class="fas fa-shopping-cart fa-2x"></i>
                            </div>
                            <h5 class="fw-bold">Under &#8377;799</h5>
                            <p class="text-muted">Great value for money</p>
                            <a href="product-list.php?min_price=600&max_price=799" class="btn btn-primary">Shop Now</a>
                        </div>
                    </div>
                    <!-- Single Budget Item -->
                    <div class="col-12 col-sm-6 col-md-3 mb-4">
                        <div class="budget-card p-4 rounded shadow-sm h-100" style="cursor: pointer;"
                            onclick="window.location.href='product-list.php?min_price=800&max_price=1099'">
                            <div class="icon-box mb-3">
                                <i class="fas fa-gift fa-2x"></i>
                            </div>
                            <h5 class="fw-bold">Under &#8377;1099</h5>
                            <p class="text-muted">Premium products at great prices</p>
                            <a href="product-list.php?min_price=800&max_price=1099" class="btn btn-primary">Shop Now</a>
                        </div>
                    </div>
                    <!-- Single Budget Item -->
                    <div class="col-12 col-sm-6 col-md-3 mb-4">
                        <div class="budget-card p-4 rounded shadow-sm h-100" style="cursor: pointer;"
                            onclick="window.location.href='product-list.php?min_price=1100&max_price=2999'">
                            <div class="icon-box mb-3">
                                <i class="fas fa-star fa-2x"></i>
                            </div>
                            <h5 class="fw-bold">Under &#8377;2999</h5>
                            <p class="text-muted">Luxury within reach</p>
                            <a href="product-list.php?min_price=1100&max_price=2999" class="btn btn-primary">Shop
                                Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- latest blog area start -->
        <section class="latest-blog-area section-padding">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <!-- section title start -->
                        <div class="section-title text-center">
                            <h2 class="title">latest blogs</h2>
                            <p class="sub-title">There are latest blog posts</p>
                        </div>
                        <!-- section title start -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="blog-carousel-active slick-row-10 slick-arrow-style">
                            <!-- blog post item start -->
                            <?php do { ?>
                            <div class="blog-post-item">
                                <figure class="blog-thumb">
                                    <a href="blog-details.php?blog_id=<?php echo $row_Recordset5['blog_id']; ?>">
                                        <img src="assets/img/blog/<?php echo $row_Recordset5['image_1']; ?>"
                                            alt="blog image">
                                    </a>
                                </figure>
                                <div class="blog-content">
                                    <div class="blog-meta">
                                        <p><?php echo date('d-m-Y',strtotime($row_Recordset5['date'])); ?> </p>
                                    </div>
                                    <h5 class="blog-title">
                                        <a
                                            href="blog-details.php?blog_id=<?php echo $row_Recordset5['blog_id']; ?>"><?php echo $row_Recordset5['blog_name']; ?></a>
                                    </h5>
                                </div>
                            </div>
                            <!-- blog post item end -->
                            <?php } while($row_Recordset5=mysqli_fetch_assoc($Recordset5)); ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- latest blog area end -->

        <!-- testimonial area start -->
        <section class="testimonial-area section-padding bg-img" data-bg="assets/img/testimonial/.jpeg">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <!-- section title start -->
                        <div class="section-title text-center">
                            <h2 class="title">testimonials</h2>
                            <p class="sub-title">What our customers say</p>
                        </div>
                        <!-- section title start -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="testimonial-thumb-wrapper">
                            <div class="testimonial-thumb-carousel">
                                <div class="testimonial-thumb">
                                    <img src="assets/img/testimonial/1.webp" alt="testimonial-thumb">
                                </div>
                                <div class="testimonial-thumb">
                                    <img src="assets/img/testimonial/2.webp" alt="testimonial-thumb">
                                </div>
                                <div class="testimonial-thumb">
                                    <img src="assets/img/testimonial/3.webp" alt="testimonial-thumb">
                                </div>
                                <div class="testimonial-thumb">
                                    <img src="assets/img/testimonial/2.webp" alt="testimonial-thumb">
                                </div>
                            </div>
                        </div>
                        <div class="testimonial-content-wrapper">
                            <div class="testimonial-content-carousel">
                                <div class="testimonial-content">
                                    <p>I have been shopping at this store for years and have always had a great
                                        experience. The products are of high quality, and the customer service is
                                        excellent.</p>
                                    <div class="ratings">
                                        <span><i class="fa fa-star-o"></i></span>
                                        <span><i class="fa fa-star-o"></i></span>
                                        <span><i class="fa fa-star-o"></i></span>
                                        <span><i class="fa fa-star-o"></i></span>
                                        <span><i class="fa fa-star-o"></i></span>
                                    </div>
                                    <h5 class="testimonial-author">Priya Kapoor</h5>
                                </div>
                                <div class="testimonial-content">
                                    <p>The jewelry I purchased from this store is beautiful and unique. I highly
                                        recommend it to anyone looking for stylish accessories.</p>
                                    <div class="ratings">
                                        <span><i class="fa fa-star-o"></i></span>
                                        <span><i class="fa fa-star-o"></i></span>
                                        <span><i class="fa fa-star-o"></i></span>
                                        <span><i class="fa fa-star-o"></i></span>
                                        <span><i class="fa fa-star-o"></i></span>
                                    </div>
                                    <h5 class="testimonial-author">Sita Mehta</h5>
                                </div>
                                <div class="testimonial-content">
                                    <p>I had an amazing shopping experience here. The variety and quality of products
                                        are impressive. I can't wait to shop again!</p>
                                    <div class="ratings">
                                        <span><i class="fa fa-star-o"></i></span>
                                        <span><i class="fa fa-star-o"></i></span>
                                        <span><i class="fa fa-star-o"></i></span>
                                        <span><i class="fa fa-star-o"></i></span>
                                        <span><i class="fa fa-star-o"></i></span>
                                    </div>
                                    <h5 class="testimonial-author">Ananya Singh</h5>
                                </div>
                                <div class="testimonial-content">
                                    <p>This store has the best collection of accessories! I always find something new
                                        and exciting every time I visit.</p>
                                    <div class="ratings">
                                        <span><i class="fa fa-star-o"></i></span>
                                        <span><i class="fa fa-star-o"></i></span>
                                        <span><i class="fa fa-star-o"></i></span>
                                        <span><i class="fa fa-star-o"></i></span>
                                        <span><i class="fa fa-star-o"></i></span>
                                    </div>
                                    <h5 class="testimonial-author">Neha Sharma</h5>
                                </div>
                                <div class="testimonial-content">
                                    <p>I love the quality of the products and the friendly staff. Shopping here is
                                        always a pleasure!</p>
                                    <div class="ratings">
                                        <span><i class="fa fa-star-o"></i></span>
                                        <span><i class="fa fa-star-o"></i></span>
                                        <span><i class="fa fa-star-o"></i></span>
                                        <span><i class="fa fa-star-o"></i></span>
                                        <span><i class="fa fa-star-o"></i></span>
                                    </div>
                                    <h5 class="testimonial-author">Riya Verma</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- testimonial area end -->

        <!-- service policy area start -->
        <div class="section-padding">
            <div class="container">
                <div class="row mtn-30">
                    <div class="col-sm-6 col-lg-3">
                        <div class="policy-item">
                            <div class="policy-icon">
                                <i class="fa-solid fa-phone"></i>
                            </div>
                            <div class="policy-content">
                                <h6>Expert Support</h6>
                                <p>orekaccessories@gmail.com</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="policy-item">
                            <div class="policy-icon">
                                <i class="fa-solid fa-truck"></i>
                            </div>
                            <div class="policy-content">
                                <h6>Free Delivery</h6>
                                <p> <small class="d-block text-muted">Orders above ₹1500</small>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="policy-item">
                            <div class="policy-icon">
                                <i class="fa-regular fa-envelope"></i>
                            </div>
                            <div class="policy-content">
                                <h6>Buyer discount</h6>
                                <p>Special Offer Every Month</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="policy-item">
                            <div class="policy-icon">
                                <i class="fa-regular fa-clock"></i>
                            </div>
                            <div class="policy-content">
                                <h6>Excellent quality</h6>
                                <p>Over 4K happy clients</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- service policy area end -->

        <!-- SEO Text Section for relevant keywords -->
        <section class="seo-text-section py-4 bg-light">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="seo-content">
                            <h1 class="h2 text-center mb-4">Orek - Premium Designer Jewelry Collection</h1>
                            <p>Welcome to Orek, your destination for high-quality designer jewelry in India. Our curated
                                collection features stunning necklaces, elegant bracelets, statement earrings, and more
                                accessories tailored to enhance your style. Each piece is thoughtfully designed to
                                complement your personal aesthetic, whether for everyday wear or special occasions.</p>
                            <p>At Orek, we believe in making luxury accessible. Explore our range of affordable jewelry
                                pieces that don't compromise on quality or design. With free delivery on orders above
                                ₹1500 and special gifts on purchases over ₹999, we ensure your shopping experience is as
                                delightful as our products.</p>
                            <p>Browse our various mood collections - Office, Vacay, Night Out, and Must-haves - to find
                                the perfect accessory for every occasion and outfit. With new styles added regularly and
                                exclusive seasonal offers, there's always something new to discover at Orek.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <footer class="footer-widget-area">
            <!-- ... existing footer content ... -->
        </footer>
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
            </div>
        </div>
    </div>
    <!-- Quick view modal end -->

    <!-- offcanvas mini cart start -->
    <?php require_once('minicart.php'); ?>
    <!-- offcanvas mini cart end -->
    <!-- Toast Notifications Container -->
    <div class="toast-container">
        <!-- Notifications will be dynamically inserted here -->
    </div>
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
    <!-- Coupon Popup JS -->
    <script src="assets/js/coupon-popup.js"></script>

    <!-- SEO Site optimization -->
    <script>
    // Add lazy loading to images for performance
    document.addEventListener("DOMContentLoaded", function() {
        var lazyImages = [].slice.call(document.querySelectorAll("img:not(.pri-img):not(.sec-img)"));

        if ("IntersectionObserver" in window) {
            let lazyImageObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        let lazyImage = entry.target;
                        if (lazyImage.dataset.src) {
                            lazyImage.src = lazyImage.dataset.src;
                            lazyImage.removeAttribute("data-src");
                        }
                        lazyImageObserver.unobserve(lazyImage);
                    }
                });
            });

            lazyImages.forEach(function(lazyImage) {
                if (!lazyImage.src && lazyImage.dataset.src) {
                    lazyImageObserver.observe(lazyImage);
                }
            });
        }
    });
    </script>

    <script>
    $(document).ready(function() {
        // Add alt text to any image that lacks it
        $('img').each(function() {
            if (!$(this).attr('alt') || $(this).attr('alt') === '') {
                let altText = '';
                // Try to generate meaningful alt text from context
                if ($(this).closest('a').length) {
                    altText = $(this).closest('a').text().trim();
                } else if ($(this).closest('div').find('h1, h2, h3, h4, h5, h6').length) {
                    altText = $(this).closest('div').find('h1, h2, h3, h4, h5, h6').first().text()
                    .trim();
                } else if ($(this).closest('div').find('p').length) {
                    altText = $(this).closest('div').find('p').first().text().trim().substring(0, 60);
                }

                if (altText !== '') {
                    $(this).attr('alt', altText + ' - Orek Jewelry');
                } else {
                    $(this).attr('alt', 'Orek Designer Jewelry Collection');
                }
            }
        });

        $('.hero-slider-active').slick({
            arrows: true, // Ensure arrows are enabled
            dots: true,
            autoplay: true,
            autoplaySpeed: 3000,
            prevArrow: '<button type="button" class="slick-prev"><i class="fa-solid fa-angle-left"></i></button>',
            nextArrow: '<button type="button" class="slick-next"><i class="fa-solid fa-angle-right"></i></button>',
        });
    });
    </script>
    <script>
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
    // Store recent purchases
    let notificationData = [];

    // Fetch users and items from database
    function fetchNotificationData() {
        $.ajax({
            url: 'get_notification_data.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    notificationData = response.notifications;
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching data:', error);
            }
        });
    }

    <?php /*
    function createNotification() {
        if (notificationData.length === 0) return;

        // Get random notification data
        const notification = notificationData[Math.floor(Math.random() * notificationData.length)];

        const toast = document.createElement('div');
        toast.className = 'purchase-toast';
        toast.innerHTML = `
        <div class="notification-content">
            <p><strong>${notification.user_name}</strong></p>
            <p class="item-name">purchased ${notification.item_name}</p>
            <small>Just now</small>
        </div>
    `;

        document.querySelector('.toast-container').appendChild(toast);

        // Show with animation
        requestAnimationFrame(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateX(0)';
        });

        // Remove after 5 seconds
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => toast.remove(), 500);
        }, 5000);
    }
    */ ?>

    // Initialize notifications
    $(document).ready(function() {
        fetchNotificationData();

        // First notification after 3 seconds
        setTimeout(createNotification, 3000);

        // Show notifications every 8-15 seconds
        setInterval(() => {
            if (Math.random() > 0.3) { // 70% chance to show notification
                createNotification();
            }
        }, Math.random() * (15000 - 8000) + 8000);

        // Refresh data every 5 minutes
        setInterval(fetchNotificationData, 300000);
    });
    </script>
</body>

</html>