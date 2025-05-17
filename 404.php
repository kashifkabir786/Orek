<?php require_once('Connections/orek.php'); ?>
<?php require_once('session-2.php'); ?>
<?php
// Log 404 errors for later analysis
$log_file = 'logs/404_errors.log';
$log_dir = dirname($log_file);

// Create logs directory if it doesn't exist
if (!file_exists($log_dir)) {
    mkdir($log_dir, 0755, true);
}

// Get base URL for absolute paths
$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$base_url = $protocol . $_SERVER['HTTP_HOST'];

$current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'Direct access';
$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Unknown';
$ip_address = $_SERVER['REMOTE_ADDR'];

$log_message = date('Y-m-d H:i:s') . " | IP: $ip_address | URL: $current_url | Referrer: $referrer | User-Agent: $user_agent\n";
@file_put_contents($log_file, $log_message, FILE_APPEND);

// If this is a WordPress URL, try to determine the equivalent resource
$is_wordpress_url = false;
$redirect_url = null;

if (
    strpos($_SERVER['REQUEST_URI'], '/wp-') !== false || 
    preg_match('/\?p=\d+/', $_SERVER['REQUEST_URI'])
) {
    $is_wordpress_url = true;
    
    // Extract post ID if available
    if (preg_match('/\?p=(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
        // Here we could look up the post ID in a mapping table if we had one
        // For now, redirect to homepage
        $redirect_url = '/';
    }
    
    // Redirect category URLs
    if (preg_match('/\/category\/([^\/]+)\/?/', $_SERVER['REQUEST_URI'], $matches)) {
        $category_slug = $matches[1];
        // Could look up category slug in a mapping table
        $redirect_url = '/product-list.php';
    }
    
    if ($redirect_url) {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: $redirect_url");
        exit;
    }
}

// Get some product suggestions
$query_suggestions = "SELECT item_id, item_name, image_1 FROM item WHERE listing_status = 'Active' ORDER BY RAND() LIMIT 6";
$suggestions = mysqli_query($orek, $query_suggestions) or die(mysqli_error($orek));

// Header variables
$amount = 0;
$total_amount = 0;
$totalRows_Cart = 0;
$totalRows_Wishlist = 0;
$flag = false;
if (isset($_SESSION['email']) && (trim($_SESSION['email']) !== '')) {
    $query_User = "SELECT * FROM user WHERE email = '{$_SESSION['email']}'";
    $User = mysqli_query($orek, $query_User) or die(mysqli_error($orek));
    $row_User = mysqli_fetch_assoc($User);
    $totalRows_User = mysqli_num_rows($User);

    $query_Cart = "SELECT A.*, B.*, C.item_name, C.image_1 FROM cart AS A INNER JOIN cart_item AS B ON A.cart_id = B.cart_id INNER JOIN item AS C ON B.item_id = C.item_id WHERE A.user_id = '{$row_User['user_id']}' AND status = 'Pending'";
    $Cart = mysqli_query($orek, $query_Cart) or die(mysqli_error($orek));
    $row_Cart = mysqli_fetch_assoc($Cart);
    $totalRows_Cart = mysqli_num_rows($Cart);

    $flag = true;
}

$query_Category = "SELECT * FROM category";
$Category = mysqli_query($orek, $query_Category) or die(mysqli_error($orek));
$row_Category = mysqli_fetch_assoc($Category);
$totalRows_Category = mysqli_num_rows($Category);

if ($flag) {
    $query_Wishlist = "SELECT COUNT(*) as wishlist_count FROM wishlist WHERE user_id = '{$row_User['user_id']}'";
    $Wishlist = mysqli_query($orek, $query_Wishlist) or die(mysqli_error($orek));
    $row_Wishlist = mysqli_fetch_assoc($Wishlist);
    $totalRows_Wishlist = $row_Wishlist['wishlist_count'];
}
?>

<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>Page Not Found - Orek</title>
    <meta name="robots" content="noindex, follow" />
    <meta name="description" content="The page you are looking for could not be found. Browse our collection of designer jewelry instead." />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo $base_url; ?>/assets/img/logo/logo.png" />

    <!-- CSS
	============================================ -->
    <!-- google fonts -->
    <link href="https://fonts.googleapis.com/css?family=Lato:300,300i,400,400i,700,900" rel="stylesheet" />
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/bootstrap.min.css" />
    <!-- Pe-icon-7-stroke CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/pe-icon-7-stroke.css" />
    <!-- Font-awesome CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/font-awesome.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <!-- main style css -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style.css" />
    <!-- Custom css -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/custom.css" />
    
    <style>
        .error-404 {
            padding: 80px 0;
            text-align: center;
        }
        .error-404 .error-code {
            font-size: 160px;
            font-weight: 700;
            color: #333;
            margin-bottom: 30px;
            line-height: 1;
        }
        .error-404 .error-message {
            font-size: 24px;
            margin-bottom: 30px;
        }
        .error-404 .search-form {
            max-width: 500px;
            margin: 0 auto 40px;
        }
        .suggestions-title {
            margin-bottom: 30px;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <!-- Start Header Area - Directly included -->
    <header class="header-area header-wide">
        <!-- main header start -->
        <div class="main-header d-none d-lg-block">
            <!-- header top start -->
            <div class="header-top bdr-bottom">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <div class="welcome-message">
                                <p>Welcome to Orek Jewelry online store</p>
                            </div>
                        </div>
                        <div class="col-lg-6 text-right">
                            <div class="header-top-settings">
                                <ul class="nav align-items-center justify-content-end">
                                    <li class="curreny-wrap">
                                        <a href="<?php echo $base_url; ?>/index.php">Home</a>
                                    </li>
                                    <li class="curreny-wrap">
                                        <a href="<?php echo $base_url; ?>/about.php">About Us</a>
                                    </li>
                                    <li class="language">
                                        <a href="<?php echo $base_url; ?>/contact.php">Contact Us</a>
                                    </li>
                                    <li class="language">
                                        <a href="<?php echo $base_url; ?>/blog.php">Blog</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- header top end -->

            <!-- header middle area start -->
            <div class="header-main-area sticky">
                <div class="container">
                    <div class="row align-items-center position-relative">
                        <!-- start logo area -->
                        <div class="col-lg-2">
                            <div class="logo">
                                <a href="<?php echo $base_url; ?>/index.php">
                                    <img src="<?php echo $base_url; ?>/assets/img/logo/logo.png" height="100%" width="100%" alt="Brand Logo" />
                                </a>
                            </div>
                        </div>
                        <!-- start logo area -->

                        <!-- main menu area start -->
                        <div class="col-lg-6 position-static">
                            <div class="main-menu-area">
                                <div class="main-menu">
                                    <!-- main menu navbar start -->
                                    <nav class="desktop-menu">
                                        <ul>
                                            <?php
                                            // Reset the category pointer
                                            mysqli_data_seek($Category, 0);
                                            while($row_Category = mysqli_fetch_assoc($Category)) {
                                                // Get subcategories for current category
                                                $query_SubCategory = "SELECT * FROM category_level1 WHERE category_id = '{$row_Category['category_id']}'";
                                                $SubCategory = mysqli_query($orek, $query_SubCategory) or die(mysqli_error($orek));
                                                $hasSubcategories = mysqli_num_rows($SubCategory) > 0;
                                            ?>
                                            <li <?php if(!$hasSubcategories) echo 'class="position-static"'; ?>>
                                                <a href="product-list.php?category_id=<?php echo $row_Category['category_id']; ?>">
                                                    <?php echo $row_Category['category_name']; ?>
                                                    <?php if($hasSubcategories) { ?>
                                                    <i class="fa fa-angle-down"></i>
                                                    <?php } ?>
                                                </a>
                                                <?php if($hasSubcategories) { ?>
                                                <ul class="dropdown">
                                                    <?php while($row_SubCategory = mysqli_fetch_assoc($SubCategory)) { ?>
                                                    <li>
                                                        <a href="product-list.php?category_id=<?php echo $row_Category['category_id']; ?>&category_level1_id=<?php echo $row_SubCategory['category_level1_id']; ?>">
                                                            <?php echo $row_SubCategory['category_level1_name']; ?>
                                                        </a>
                                                    </li>
                                                    <?php } ?>
                                                </ul>
                                                <?php } ?>
                                            </li>
                                            <?php 
                                                mysqli_free_result($SubCategory);
                                            } 
                                            ?>
                                            <li class="position-static">
                                                <a href="product-list-gift.php" style="color: #ecb0a3;">
                                                    Free Gifts <i class="fa-solid fa-gift"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </nav>
                                    <!-- main menu navbar end -->
                                </div>
                            </div>
                        </div>
                        <!-- main menu area end -->

                        <!-- mini cart area start -->
                        <div class="col-lg-4">
                            <div class="header-right d-flex align-items-center justify-content-xl-between justify-content-lg-end">
                                <div class="header-search-container">
                                    <button class="search-trigger d-xl-none d-lg-block">
                                        <i class="fa-light fa-magnifying-glass"></i>
                                    </button>
                                    <form class="header-search-box d-lg-none d-xl-block" action="product-list.php" method="GET">
                                        <input type="text" name="search" placeholder="Search entire store here" class="header-search-field" />
                                        <button type="submit" class="header-search-btn">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </button>
                                    </form>
                                </div>
                                <div class="header-configure-area">
                                    <ul class="nav justify-content-end">
                                        <li class="user-hover">
                                            <a href="#">
                                                <i class="fa-regular fa-user"></i>
                                            </a>
                                            <ul class="dropdown-list">
                                                <?php if($flag){ ?>
                                                <li><a href="my-account.php">My Account</a></li>
                                                <li><a href="logout.php">Logout</a></li>
                                                <?php } else { ?>
                                                <li><a href="login.php">Login</a></li>
                                                <li><a href="register.php">Register</a></li>
                                                <?php } ?>
                                            </ul>
                                        </li>
                                        <li class="birthday-popper">
                                            <a href="product-list-gift.php" class="gift-icon">
                                                <i class="fa-solid fa-gift" style="color:#ecb0a3"></i>
                                                <div class="confetti-container">
                                                    <span class="confetti c1"></span>
                                                    <span class="confetti c2"></span>
                                                    <span class="confetti c3"></span>
                                                    <span class="confetti c4"></span>
                                                    <span class="confetti c5"></span>
                                                    <span class="confetti c6"></span>
                                                    <span class="confetti c7"></span>
                                                    <span class="confetti c8"></span>
                                                    <span class="confetti c9"></span>
                                                    <span class="confetti c10"></span>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="wishlist.php">
                                                <i class="fa-regular fa-heart"></i>
                                                <div class="notification wishlist-count"><?php echo $totalRows_Wishlist; ?></div>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" class="minicart-btn">
                                                <i class="fa-solid fa-bag-shopping"></i>
                                                <div class="notification"><?php echo $totalRows_Cart; ?></div>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- mini cart area end -->
                    </div>
                </div>
            </div>
            <!-- header middle area end -->
        </div>
        <!-- main header start -->

        <!-- mobile header start -->
        <div class="mobile-header d-lg-none d-md-block sticky">
            <!--mobile header top start -->
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-12">
                        <div class="mobile-main-header">
                            <div class="mobile-logo">
                                <a href="index.php">
                                    <img src="<?php echo $base_url; ?>/assets/img/logo/logo.png" alt="Brand Logo" />
                                </a>
                            </div>
                            <div class="mobile-menu-toggler">
                                <div class="mini-cart-wrap">
                                    <a href="cart.php">
                                        <i class="fa-solid fa-bag-shopping"></i>
                                        <div class="notification"><?php echo $totalRows_Cart; ?></div>
                                    </a>
                                </div>
                                <div class="mini-cart-wrap birthday-popper">
                                    <a href="product-list-gift.php" class="gift-icon">
                                        <i class="fa-solid fa-gift" style="color:#ecb0a3"></i>
                                        <div class="confetti-container">
                                            <span class="confetti c1"></span>
                                            <span class="confetti c2"></span>
                                            <span class="confetti c3"></span>
                                            <span class="confetti c4"></span>
                                            <span class="confetti c5"></span>
                                        </div>
                                    </a>
                                </div>
                                <div class="my-account-wrap">
                                    <a href="<?php if($flag){ echo 'my-account.php'; } else { echo 'login.php'; } ?>">
                                        <i class="fa-solid fa-user"></i>
                                    </a>
                                </div>
                                <button class="mobile-menu-btn">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- end Header Area -->

    <main>
        <div class="error-404">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h1 class="error-code">404</h1>
                        <h2 class="error-message">Oops! Page Not Found</h2>
                        <p class="mb-4">The page you're looking for doesn't exist or has been moved.</p>
                        
                        <!-- Search form -->
                        <div class="search-form">
                            <form action="product-list.php" method="get">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Search for products...">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="submit">Search</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                        <div class="action-buttons mb-5">
                            <a href="/" class="btn btn-hero">Go to Homepage</a>
                            <a href="product-list.php" class="btn btn-outline-secondary ml-3">Browse Products</a>
                        </div>
                    </div>
                </div>
                
                <!-- Product suggestions -->
                <div class="row">
                    <div class="col-12">
                        <h3 class="suggestions-title">You might be interested in:</h3>
                    </div>
                    
                    <?php while($suggestion = mysqli_fetch_assoc($suggestions)) { ?>
                    <div class="col-6 col-md-4 col-lg-2 mb-4">
                        <div class="product-item">
                            <figure class="product-thumb">
                                <a href="product-details.php?item_id=<?php echo $suggestion['item_id']; ?>">
                                    <img class="pri-img" src="<?php echo $base_url; ?>/assets/img/item/<?php echo $suggestion['image_1']; ?>" alt="<?php echo $suggestion['item_name']; ?>">
                                </a>
                            </figure>
                            <div class="product-caption text-center">
                                <h6 class="product-name">
                                    <a href="product-details.php?item_id=<?php echo $suggestion['item_id']; ?>"><?php echo $suggestion['item_name']; ?></a>
                                </h6>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer area start - Directly included -->
    <footer class="footer-widget-area">
        <div class="footer-top section-padding">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="widget-item">
                            <div class="widget-title">
                                <div class="widget-logo">
                                    <a href="index.php">
                                        <img src="<?php echo $base_url; ?>/assets/img/logo/logo.png" width="200px" alt="brand logo" />
                                    </a>
                                </div>
                            </div>
                            <div class="widget-item">
                                <div class="widget-body social-link">
                                    <a href="https://www.facebook.com/share/1BvPk2MvX5/"><i
                                            class="fa-brands fa-facebook-f"></i></a>
                                    <a href="https://www.instagram.com/orekdotin"><i
                                            class="fa-brands fa-instagram"></i></a>
                                    <a href="https://wa.me/917992381874"><i class="fa-brands fa-whatsapp"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <div class="widget-item">
                            <h6 class="widget-title">Help</h6>
                            <div class="widget-body">
                                <address class="contact-block">
                                    <ul>
                                        <li>
                                            <a href="contact.php"> Email Us</a>
                                        </li>
                                        <li>
                                            <a href="">Help & FAQ
                                            </a>
                                        </li>
                                        <li>
                                            <a href="">Make a Return</a>
                                        </li>
                                        <li>
                                            <a href="">Shipping Policy</a>
                                        </li>
                                    </ul>
                                </address>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <div class="widget-item">
                            <h6 class="widget-title">Quick Link</h6>
                            <div class="widget-body">
                                <address class="contact-block">
                                    <ul>
                                        <li><a href="terms-condition.php">Terms & Conditions</a></li>
                                        <li><a href="privacy-policy.php">Privacy Policy</a></li>
                                        <li><a href="return-policy.php">Return Policy</a></li>
                                    </ul>
                                </address>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <div class="widget-item">
                            <h6 class="widget-title">Company</h6>
                            <div class="widget-body">
                                <address class="contact-block">
                                    <ul>
                                        <li><a href="#">We are hiring</a></li>
                                        <li><a href="#">Press Links</a></li>
                                    </ul>
                                </address>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <div class="widget-item">
                            <h6 class="widget-title">Company</h6>
                            <div class="widget-body">
                                <address class="contact-block">
                                    <ul>
                                        <li><a href="index.php">Home</a></li>
                                        <li><a href="about.php">About Us</a></li>
                                        <li><a href="contact.php">Contact Us</a></li>
                                    </ul>
                                </address>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="copyright-text text-center">
                            <p>
                                All Right Reserved By Orek 2024
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- footer area end -->

    <!-- JS
    ============================================ -->
    <!-- Modernizer JS -->
    <script src="<?php echo $base_url; ?>/assets/js/modernizr-3.6.0.min.js"></script>
    <!-- jQuery JS -->
    <script src="<?php echo $base_url; ?>/assets/js/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="<?php echo $base_url; ?>/assets/js/bootstrap.bundle.min.js"></script>
    <!-- Main JS -->
    <script src="<?php echo $base_url; ?>/assets/js/main.js"></script>
    
    <script>
    // Track 404 errors in Google Analytics if available
    if (typeof ga === 'function') {
        ga('send', {
            hitType: 'event',
            eventCategory: '404 Errors',
            eventAction: window.location.href,
            eventLabel: document.referrer
        });
    }
    </script>
</body>
</html>
<?php
// Free result set
mysqli_free_result($suggestions);
?> 