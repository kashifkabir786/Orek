<?php require_once('Connections/orek.php'); ?>

<?php
$amount = 0;
$total_amount = 0;
$totalRows_Cart = 0;
$totalRows_Wishlist = 0;
$flag = false;
if ( isset( $_SESSION[ 'email' ] ) && ( trim( $_SESSION[ 'email' ] ) !== '' ) ) {

  $query_User = "SELECT * FROM user WHERE email = '{$_SESSION['email']}'";
  $User = mysqli_query( $orek, $query_User )or die( mysqli_error( $orek ) );
  $row_User = mysqli_fetch_assoc( $User );
  $totalRows_User = mysqli_num_rows( $User );

  $query_Cart = "SELECT A.*, B.*, C.item_name, C.image_1 FROM cart AS A INNER JOIN cart_item AS B ON A.cart_id = B.cart_id INNER JOIN item AS C ON B.item_id = C.item_id WHERE A.user_id = '{$row_User['user_id']}' AND status = 'Pending'";
  $Cart = mysqli_query( $orek, $query_Cart )or die( mysqli_error( $orek ) );
  $row_Cart = mysqli_fetch_assoc( $Cart );
  $totalRows_Cart = mysqli_num_rows( $Cart );

  $flag = true;
}
$query_Category = "SELECT * FROM category";
$Category = mysqli_query( $orek, $query_Category )or die( mysqli_error( $orek ) );
$row_Category = mysqli_fetch_assoc( $Category );
$totalRows_Category = mysqli_num_rows( $Category );

if ($flag) {
  $query_Wishlist = "SELECT COUNT(*) as wishlist_count FROM wishlist WHERE user_id = '{$row_User['user_id']}'";
  $Wishlist = mysqli_query($orek, $query_Wishlist) or die(mysqli_error($orek));
  $row_Wishlist = mysqli_fetch_assoc($Wishlist);
  $totalRows_Wishlist = $row_Wishlist['wishlist_count'];
}

$current_page = basename($_SERVER['PHP_SELF']);

// Get base URL for canonical and hreflang tags
$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$base_url = $protocol . $_SERVER['HTTP_HOST'];
$current_url = $base_url . $_SERVER['REQUEST_URI'];

// Define localized versions if they exist
$languages = array('en' => $current_url); // Add more languages if they exist
?>

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
                                    <a href="index.php">Home</a>
                                </li>
                                <li class="curreny-wrap">
                                    <a href="about.php">About Us</a>
                                </li>
                                <li class="language">
                                    <a href="contact.php">Contact Us</a>
                                </li>
                                <li class="language">
                                    <a href="blog.php">Blog</a>
                                </li>
                                <!-- <li class="position-static">
                                    <a href="product-list-gift.php" style="color: #ecb0a3;">
                                        Free Gifts <i class="fa-solid fa-gift"></i>
                                    </a>
                                </li> -->
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
                            <a href="index.php">
                                <img src="assets/img/logo/logo.png" height="100%" width="100%" alt="Brand Logo" />
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
                                            <a
                                                href="product-list.php?category_id=<?php echo $row_Category['category_id']; ?>">
                                                <?php echo $row_Category['category_name']; ?>
                                                <?php if($hasSubcategories) { ?>
                                                <i class="fa fa-angle-down"></i>
                                                <?php } ?>
                                            </a>
                                            <?php if($hasSubcategories) { ?>
                                            <ul class="dropdown">
                                                <?php while($row_SubCategory = mysqli_fetch_assoc($SubCategory)) { ?>
                                                <li>
                                                    <a
                                                        href="product-list.php?category_id=<?php echo $row_Category['category_id']; ?>&category_level1_id=<?php echo $row_SubCategory['category_level1_id']; ?>">
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
                        <div
                            class="header-right d-flex align-items-center justify-content-xl-between justify-content-lg-end">
                            <div class="header-search-container">
                                <button class="search-trigger d-xl-none d-lg-block">
                                    <i class="fa-light fa-magnifying-glass"></i>
                                </button>
                                <form class="header-search-box d-lg-none d-xl-block" action="product-list.php"
                                    method="GET">
                                    <input type="text" name="search" placeholder="Search entire store here"
                                        class="header-search-field" />
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
                                            <div class="notification wishlist-count"><?php echo $totalRows_Wishlist; ?>
                                            </div>
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
                                <img src="assets/img/logo/logo.png" alt="Brand Logo" />
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

    <aside class="off-canvas-wrapper">
        <div class="off-canvas-overlay"></div>
        <div class="off-canvas-inner-content">
            <div class="btn-close-off-canvas">
                <i class="fa-solid fa-xmark"></i>
            </div>

            <div class="off-canvas-inner">
                <!-- search box start -->
                <div class="search-box-offcanvas">
                    <form action="product-list.php" method="GET">
                        <input type="text" name="search" placeholder="Search entire store here..." />
                        <button type="submit" class="search-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </form>
                </div>

                <!-- mobile menu start -->
                <div class="mobile-navigation">
                    <nav>
                        <ul class="mobile-menu">
                            <?php
                             // Reset the category pointer
                            mysqli_data_seek($Category, 0);
                            while($row_Category = mysqli_fetch_assoc($Category)) {
                                // Get subcategories for current category
                                $query_SubCategory = "SELECT * FROM category_level1 WHERE category_id = '{$row_Category['category_id']}'";
                                $SubCategory = mysqli_query($orek, $query_SubCategory) or die(mysqli_error($orek));
                                $hasSubcategories = mysqli_num_rows($SubCategory) > 0;
                            ?>
                            <li <?php if($hasSubcategories) echo 'class="menu-item-has-children"'; ?>>
                                <a href="product-list.php?category_id=<?php echo $row_Category['category_id']; ?>"><?php echo $row_Category['category_name']; ?></a>
                                <?php if($hasSubcategories) { ?>
                                <span class="menu-expand"><i class="fa fa-angle-down"></i></span>
                                <ul class="dropdown">
                                    <?php while($row_SubCategory = mysqli_fetch_assoc($SubCategory)) { ?>
                                    <li><a
                                            href="product-list.php?category_id=<?php echo $row_Category['category_id']; ?>&category_level1_id=<?php echo $row_SubCategory['category_level1_id']; ?>"><?php echo $row_SubCategory['category_level1_name']; ?></a>
                                    </li>
                                    <?php } ?>
                                </ul>
                                <?php } ?>
                            </li>
                            <?php
                                mysqli_free_result($SubCategory);
                            } 
                            ?>
                        </ul>
                    </nav>
                </div>

                <div class="mobile-settings">
                    <ul class="nav">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="contact.php">Contact Us</a></li>
                        <li><a href="blog.php">Blog</a></li>
                        <li class="position-static">
                            <a href="product-list-gift.php" style="color: #ecb0a3;">
                                Gifts <i class="fa-solid fa-gift"></i>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown mobile-top-dropdown">

                                <a href="#" class="dropdown-toggle" id="myaccount" data-bs-toggle="dropdown">
                                    My Account
                                    <i class="fa fa-angle-down"></i>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="myaccount">
                                    <?php if($flag){ ?>
                                    <a class="dropdown-item" href="my-account.php">My Account</a>
                                    <a class="dropdown-item" href="logout.php">Logout</a>
                                    <?php } else { ?>
                                    <a class="dropdown-item" href="login.php">Login</a>
                                    <a class="dropdown-item" href="register.php">Register</a>
                                    <?php } ?>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- offcanvas widget area start -->
                <div class="offcanvas-widget-area">
                    <div class="off-canvas-contact-widget">
                        <ul>
                            <li>
                                <i class="fa fa-envelope"></i>
                                <a href="mailto:info@orek.com">care@orek.in</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- offcanvas widget area end -->
            </div>
        </div>
    </aside>
    <!-- off-canvas menu end -->
    <!-- offcanvas mobile menu end -->
</header>