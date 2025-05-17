<?php require_once('Connections/orek.php'); ?>
<?php require_once('session-2.php'); ?>

<?php
// Initialize filter variables
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 
              (isset($_GET['category']) ? (int)$_GET['category'] : null);
$category_level1_id = isset($_GET['category_level1_id']) ? (int)$_GET['category_level1_id'] : null;
$search = isset($_GET['search']) ? mysqli_real_escape_string($orek, trim($_GET['search'])) : null;

$limit = 9; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Modify count query to include listing_status = 'Gift'
$count_query = "SELECT COUNT(DISTINCT i.item_id) as total 
                FROM item i 
                LEFT JOIN category_level1 cl ON i.category_level1_id = cl.category_level1_id 
                LEFT JOIN category c ON i.category_id = c.category_id 
                WHERE i.listing_status = 'Gift'";
if ($search) $count_query .= " AND (i.item_name LIKE '%$search%' OR c.category_name LIKE '%$search%' OR cl.category_level1_name LIKE '%$search%')";
if ($category_id) $count_query .= " AND i.category_id = $category_id";
if ($category_level1_id) $count_query .= " AND i.category_level1_id = $category_level1_id";

$count_result = mysqli_query($orek, $count_query);
$total_records = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_records / $limit);

// Ensure page number is within valid range
if ($page > $total_pages && $total_pages > 0) {
    $page = $total_pages;
}
$start = ($page - 1) * $limit;
mysqli_free_result($count_result);

// Modified main query with listing_status = 'Gift'
$query_Recordset2 = "SELECT DISTINCT i.*, (i.price - (i.price * i.discount/100)) as discounted_price 
                     FROM item i 
                     LEFT JOIN category_level1 cl ON i.category_level1_id = cl.category_level1_id 
                     LEFT JOIN category c ON i.category_id = c.category_id 
                     WHERE i.listing_status = 'Gift'";
if ($search) $query_Recordset2 .= " AND (i.item_name LIKE '%$search%' OR c.category_name LIKE '%$search%' OR cl.category_level1_name LIKE '%$search%')";
if ($category_id) $query_Recordset2 .= " AND i.category_id = $category_id";
if ($category_level1_id) $query_Recordset2 .= " AND i.category_level1_id = $category_level1_id";

// Add ORDER BY clause for consistent ordering
$query_Recordset2 .= " ORDER BY i.item_id DESC LIMIT $start, $limit";
$Recordset2 = mysqli_query($orek, $query_Recordset2) or die(mysqli_error($orek));
$row_Recordset2 = mysqli_fetch_assoc($Recordset2);
$totalRows_Recordset2 = mysqli_num_rows($Recordset2);

// Get categories for filter
$query_Recordset3 = "SELECT A.*, B.* FROM category AS A INNER JOIN item AS B ON A.category_id = B.category_id WHERE listing_status = 'Gift'";
$Recordset3 = mysqli_query($orek, $query_Recordset3) or die(mysqli_error($orek));
$row_Recordset3 = mysqli_fetch_assoc($Recordset3);
$totalRows_Recordset3 = mysqli_num_rows($Recordset3);

// Get available sizes for filter
$query_sizes = "SELECT DISTINCT size FROM item WHERE size IS NOT NULL ORDER BY size";
$sizes_result = mysqli_query($orek, $query_sizes);

?>

<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>Orek - Product List</title>
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
                                    <li class="breadcrumb-item active" aria-current="page">Product List</li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- breadcrumb area end -->

        <!-- page main wrapper start -->
        <div class="shop-main-wrapper section-padding">
            <div class="container">
                <div class="row">
                    <!-- sidebar area start -->
                    <div class="col-lg-3 order-2">
                        <aside class="sidebar-wrapper">
                            <!-- single sidebar start -->
                            <div class="sidebar-single">
                                <h5 class="sidebar-title">categories</h5>
                                <div class="sidebar-body">
                                    <ul class="shop-categories">
                                        <li><a href="product-list-gift.php">All Categories</a></li>
                                        <?php 
                                        // Reset the pointer
                                        mysqli_data_seek($Recordset3, 0);
                                        
                                        // Create an array to track displayed categories
                                        $displayed_categories = array();
                                        
                                        do { 
                                            // Check if we've already displayed this category
                                            if (!in_array($row_Recordset3['category_id'], $displayed_categories)) {
                                                // Add this category to our tracking array
                                                $displayed_categories[] = $row_Recordset3['category_id'];
                                                
                                                // Get subcategories for current category
                                                $query_SubCategory = "SELECT * FROM category_level1 
                                                                WHERE category_id = '{$row_Recordset3['category_id']}'";
                                                $SubCategory = mysqli_query($orek, $query_SubCategory) or die(mysqli_error($orek));
                                                $hasSubcategories = mysqli_num_rows($SubCategory) > 0;
                                        ?>
                                        <li>
                                            <a href="product-list-gift.php?category_id=<?php echo $row_Recordset3['category_id']; ?>"
                                                class="<?php echo ($category_id == $row_Recordset3['category_id']) ? 'active' : ''; ?>">
                                                <?php echo $row_Recordset3['category_name']; ?>
                                            </a>
                                            <?php if($hasSubcategories && $category_id == $row_Recordset3['category_id']) { ?>
                                            <ul class="sub-categories" style="margin-left: 20px; margin-top: 10px;">
                                                <?php while($row_SubCategory = mysqli_fetch_assoc($SubCategory)) { ?>
                                                <li>
                                                    <a href="product-list-gift.php?category_id=<?php echo $row_Recordset3['category_id']; ?>&category_level1_id=<?php echo $row_SubCategory['category_level1_id']; ?>"
                                                        class="<?php echo (isset($_GET['category_level1_id']) && $_GET['category_level1_id'] == $row_SubCategory['category_level1_id']) ? 'active' : ''; ?>">
                                                        - <?php echo $row_SubCategory['category_level1_name']; ?>
                                                    </a>
                                                </li>
                                                <?php } ?>
                                            </ul>
                                            <?php } ?>
                                        </li>
                                        <?php 
                                                mysqli_free_result($SubCategory);
                                            }
                                        } while($row_Recordset3 = mysqli_fetch_assoc($Recordset3)); 
                                        ?>
                                    </ul>
                                </div>
                            </div>
                            <!-- single sidebar end -->
                        </aside>

                        <!-- single sidebar start -->
                        <div class="sidebar-banner">
                            <div class="img-container">
                                <a href="#">
                                    <img src="assets/img/banner/sidebar-banner.jpg" alt="">
                                </a>
                            </div>
                        </div>
                        <!-- single sidebar end -->
                    </div>
                    <!-- sidebar area end -->

                    <!-- shop main wrapper start -->
                    <div class="col-lg-9 order-1">
                        <div class="shop-product-wrapper">
                            <div class="mb-4"
                                style="background: linear-gradient(90deg, #ff7e5f, #feb47b); padding: 12px 20px; text-align: center; color: #fff; font-size: 18px; font-weight: 600; border-radius: 10px; font-family: 'Poppins', sans-serif; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);">
                                Shop for ₹999+ and Unlock a FREE Gift of Your Choice! 🛍️🎁
                            </div>
                            <!-- shop product top wrap start -->
                            <div class="shop-top-bar">
                                <div class="row align-items-center">
                                    <div class="col-lg-7 col-md-6 order-2 order-md-1">
                                        <div class="top-bar-left">
                                            <div class="product-view-mode">
                                                <a class="active" href="#" data-target="grid-view"
                                                    data-bs-toggle="tooltip" title="Grid View"><i
                                                        class="fa fa-th"></i></a>
                                                <a href="#" data-target="list-view" data-bs-toggle="tooltip"
                                                    title="List View"><i class="fa fa-list"></i></a>
                                            </div>
                                            <div class="product-amount">
                                                <p>Showing
                                                    <?php echo min($start + 1, $total_records); ?>–<?php echo min($start + $limit, $total_records); ?>
                                                    of <?php echo $total_records; ?> results</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- shop product top wrap end -->

                            <!-- product item list wrapper start -->
                            <div class="shop-product-wrap grid-view row mbn-30">
                                <?php do { ?>
                                <!-- product single item start -->
                                <div class="col-md-4 col-sm-6">
                                    <!-- product grid start -->
                                    <div class="product-item">
                                        <figure class="product-thumb">
                                            <a
                                                href="product-details.php?item_id=<?php echo $row_Recordset2['item_id']; ?>">
                                                <img class="pri-img"
                                                    src="assets/img/item/<?php echo $row_Recordset2['image_1']; ?>"
                                                    alt="product">
                                                <img class="sec-img"
                                                    src="assets/img/item/<?php echo $row_Recordset2['image_2'] == '' ? $row_Recordset2['image_1'] : $row_Recordset2['image_2']; ?>"
                                                    alt="product">
                                            </a>
                                            <div class="product-badge">
                                                <div class="product-label new">
                                                    <span>new</span>
                                                </div>
                                                <div class="product-label discount">
                                                    <span><?php echo $row_Recordset2['discount']; ?>%</span>
                                                </div>
                                            </div>
                                            <div class="button-group">
                                                <?php if(isset($_SESSION['email'])): 
                                                    // Check if item is in wishlist
                                                    $email = $_SESSION['email'];
                                                    $item_id = $row_Recordset2['item_id'];
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
                                                <a href="javascript:void(0)"
                                                    onclick="addToWishlist(<?php echo $item_id; ?>)"
                                                    data-bs-toggle="tooltip" data-bs-placement="left"
                                                    title="Add to wishlist">
                                                    <i class="fa-regular fa-heart"></i>
                                                </a>
                                                <?php endif; ?>
                                                <?php else: ?>
                                                <a href="login.php" data-bs-toggle="tooltip" data-bs-placement="left"
                                                    title="Add to wishlist">
                                                    <i class="fa-regular fa-heart"></i>
                                                </a>
                                                <?php endif; ?>
                                            </div>
                                            <div class="cart-hover">
                                                <?php
                                        if(isset($_SESSION['email'])) {
                                            // Check cart total with proper calculation
                                            $cart_total_query = mysqli_query($orek, "SELECT 
                                                    SUM(ROUND(i.price * (1 - i.discount/100)) * ci.qnty) as cart_total 
                                                FROM cart_item ci 
                                                JOIN cart c ON ci.cart_id = c.cart_id 
                                                JOIN user u ON c.user_id = u.user_id 
                                                JOIN item i ON ci.item_id = i.item_id 
                                                WHERE u.email = '{$_SESSION['email']}' 
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
                                            
                                            // Check if user already has any gift item in cart
                                             $check_gift = mysqli_query($orek, "SELECT COUNT(item_id) as gift_count FROM cart_item A INNER JOIN `cart` B ON A.cart_id = B.cart_id WHERE B.user_id = '{$row_Recordset1['user_id']}' AND `status` = 'Pending' AND A.item_id IN (SELECT item_id FROM item WHERE listing_status = 'Gift')");
                                                        $gift_count = mysqli_fetch_assoc($check_gift)['gift_count'];
                                            
                                            if($in_cart): ?>
                                                <a href="cart.php" class="btn btn-cart">View Cart</a>
                                                <?php elseif($gift_count > 0): ?>
                                                <button class="btn btn-cart" disabled>Gift Limit Reached</button>
                                                <?php elseif($cart_total < 999): ?>
                                                <button class="btn btn-cart" disabled>Shop for ₹999+ to Get Free
                                                    Gift</button>
                                                <?php else: ?>
                                                <button class="btn btn-cart gift-btn"
                                                    data-item-id="<?php echo $item_id; ?>"
                                                    onclick="addGiftToCart(<?php echo $item_id; ?>)">
                                                    Add Free Gift
                                                </button>
                                                <?php endif;
                                        } else { ?>
                                                <a href="login.php" class="btn btn-cart">Add Free Gift</a>
                                                <?php } ?>
                                            </div>
                                        </figure>
                                        <div class="product-caption text-center">
                                            <div class="product-identity">
                                                <p class="manufacturer-name"><a
                                                        href="product-details.php?item_id=<?php echo $row_Recordset2['item_id']; ?>">Silver</a>
                                                </p>
                                            </div>

                                            <h6 class="product-name">
                                                <a
                                                    href="product-details.php?item_id=<?php echo $row_Recordset2['item_id']; ?>"><?php echo $row_Recordset2['item_name']; ?></a>
                                            </h6>
                                        </div>
                                    </div>
                                    <!-- product grid end -->

                                    <!-- product list item end -->
                                    <div class="product-list-item">
                                        <figure class="product-thumb">
                                            <a
                                                href="product-details.php?item_id=<?php echo $row_Recordset2['item_id']; ?>">
                                                <img class="pri-img"
                                                    src="assets/img/item/<?php echo $row_Recordset2['image_1']; ?>"
                                                    alt="product">
                                                <img class="sec-img"
                                                    src="assets/img/item/<?php echo $row_Recordset2['image_2'] == '' ? $row_Recordset2['image_1'] : $row_Recordset2['image_2']; ?>"
                                                    alt="product">
                                            </a>
                                            <div class="product-badge">
                                                <div class="product-label new">
                                                    <span>new</span>
                                                </div>
                                                <div class="product-label discount">
                                                    <span><?php echo $row_Recordset2['discount']; ?>%</span>
                                                </div>
                                            </div>
                                            <div class="button-group">
                                                <?php if(isset($_SESSION['email'])): 
                                                // Check if item is in wishlist
                                                $email = $_SESSION['email'];
                                                $item_id = $row_Recordset2['item_id'];
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
                                                <a href="javascript:void(0)"
                                                    onclick="addToWishlist(<?php echo $item_id; ?>)"
                                                    data-bs-toggle="tooltip" data-bs-placement="left"
                                                    title="Add to wishlist">
                                                    <i class="fa-regular fa-heart"></i>
                                                </a>
                                                <?php endif; ?>
                                                <?php else: ?>
                                                <a href="login.php" data-bs-toggle="tooltip" data-bs-placement="left"
                                                    title="Add to wishlist">
                                                    <i class="fa-regular fa-heart"></i>
                                                </a>
                                                <?php endif; ?>
                                            </div>
                                            <div class="cart-hover">
                                                <?php
                                        if(isset($_SESSION['email'])) {
                                            // Check cart total with proper calculation
                                            $cart_total_query = mysqli_query($orek, "SELECT 
                                                    SUM(ROUND(i.price * (1 - i.discount/100)) * ci.qnty) as cart_total 
                                                FROM cart_item ci 
                                                JOIN cart c ON ci.cart_id = c.cart_id 
                                                JOIN user u ON c.user_id = u.user_id 
                                                JOIN item i ON ci.item_id = i.item_id 
                                                WHERE u.email = '{$_SESSION['email']}' 
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
                                            
                                            // Check if user already has any gift item in cart
                                             $check_gift = mysqli_query($orek, "SELECT COUNT(item_id) as gift_count FROM cart_item A INNER JOIN `cart` B ON A.cart_id = B.cart_id WHERE B.user_id = '{$row_Recordset1['user_id']}' AND `status` = 'Pending' AND A.item_id IN (SELECT item_id FROM item WHERE listing_status = 'Gift')");
                                                        $gift_count = mysqli_fetch_assoc($check_gift)['gift_count'];
                                            
                                            if($in_cart): ?>
                                                <a href="cart.php" class="btn btn-cart">View Cart</a>
                                                <?php elseif($gift_count > 0): ?>
                                                <button class="btn btn-cart" disabled>Gift Limit Reached</button>
                                                <?php elseif($cart_total < 999): ?>
                                                <button class="btn btn-cart" disabled>Shop for ₹999+ to Get Free
                                                    Gift</button>
                                                <?php else: ?>
                                                <button class="btn btn-cart gift-btn"
                                                    data-item-id="<?php echo $item_id; ?>"
                                                    onclick="addGiftToCart(<?php echo $item_id; ?>)">
                                                    Add Free Gift
                                                </button>
                                                <?php endif;
                                        } else { ?>
                                                <a href="login.php" class="btn btn-cart">Add Free Gift</a>
                                                <?php } ?>
                                            </div>
                                        </figure>
                                        <div class="product-content-list">
                                            <div class="manufacturer-name">
                                                <a
                                                    href="product-details.php?item_id=<?php echo $row_Recordset2['item_id']; ?>&from=gift">Silver</a>
                                            </div>

                                            <h5 class="product-name"><a
                                                    href="product-details.php?item_id=<?php echo $row_Recordset2['item_id']; ?>"><?php echo $row_Recordset2['item_name']; ?></a>
                                            </h5>
                                            <div class="price-box">
                                                <?php
                                                // Actual price
                                                $actual_price = $row_Recordset2['price'];
                                                // Discount percentage
                                                $discount_percentage = $row_Recordset2['discount'];
                                                // Discounted price calculation with rounding
                                                $discounted_price = round($actual_price - ($actual_price * ($discount_percentage / 100)));
                                                ?>
                                                <span
                                                    class="price-regular">&#8377;<?php echo $discounted_price; ?></span>
                                                <span
                                                    class="price-old"><del>&#8377;<?php echo round($actual_price); ?></del></span>
                                            </div>
                                            <hr class="single-divider">
                                            <p>
                                                <?php echo $row_Recordset2['item_name']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <!-- product list item end -->
                                </div>
                                <!-- product single item end -->
                                <?php }while($row_Recordset2=mysqli_fetch_assoc($Recordset2)); ?>
                            </div>
                            <!-- product item list wrapper end -->

                            <!-- start pagination area -->
                            <div class="paginatoin-area text-center">
                                <ul class="pagination-box">
                                    <?php 
                                    // Build base query parameters without page
                                    $query_params = $_GET;
                                    if(isset($query_params['page'])) {
                                        unset($query_params['page']);
                                    }
                                    $base_query = http_build_query($query_params);
                                    $base_url = "product-list-gift.php?" . ($base_query ? $base_query . '&' : '');
                                    
                                    // Previous page link
                                    if ($page > 1): 
                                    ?>
                                    <li><a class="previous"
                                            href="<?php echo $base_url; ?>page=<?php echo ($page-1); ?>"><i
                                                style="font-size: 15px;" class="fa-solid fa-angle-left"></i></a></li>
                                    <?php endif; ?>

                                    <?php
                                    // Calculate range of page numbers to show
                                    $range = 2;
                                    $start_page = max(1, $page - $range);
                                    $end_page = min($total_pages, $page + $range);

                                    // Show first page if we're not starting at 1
                                    if ($start_page > 1) {
                                        echo '<li><a href="' . $base_url . 'page=1">1</a></li>';
                                        if ($start_page > 2) {
                                            echo '<li><span>...</span></li>';
                                        }
                                    }

                                    // Show page numbers
                                    for ($i = $start_page; $i <= $end_page; $i++) {
                                        echo '<li' . ($page == $i ? ' class="active"' : '') . '><a href="' . $base_url . 'page=' . $i . '">' . $i . '</a></li>';
                                    }

                                    // Show last page if we're not ending at last page
                                    if ($end_page < $total_pages) {
                                        if ($end_page < $total_pages - 1) {
                                            echo '<li><span>...</span></li>';
                                        }
                                        echo '<li><a href="' . $base_url . 'page=' . $total_pages . '">' . $total_pages . '</a></li>';
                                    }
                                    ?>

                                    <?php if ($page < $total_pages): ?>
                                    <li><a class="next" href="<?php echo $base_url; ?>page=<?php echo ($page+1); ?>"><i
                                                style="font-size: 15px;" class="fa-solid fa-angle-right"></i></a></li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                            <!-- end pagination area -->
                        </div>
                    </div>
                    <!-- shop main wrapper end -->
                </div>
            </div>
        </div>
        <!-- page main wrapper end -->
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
    <script>
    $(document).ready(function() {
        var $priceRange = $(".price-range");
        var minPrice = $priceRange.data('min');
        var maxPrice = $priceRange.data('max');
        var currentMin = $priceRange.data('current-min');
        var currentMax = $priceRange.data('current-max');

        $priceRange.slider({
            range: true,
            min: minPrice,
            max: maxPrice,
            values: [currentMin, currentMax],
            slide: function(event, ui) {
                $("#amount").val("₹" + ui.values[0] + " - ₹" + ui.values[1]);
                $("#min_price").val(ui.values[0]);
                $("#max_price").val(ui.values[1]);
            },
            change: function(event, ui) {
                $("#priceFilterForm").submit();
            }
        });

        // Initialize display values
        $("#amount").val("₹" + currentMin + " - ₹" + currentMax);
        $("#min_price").val(currentMin);
        $("#max_price").val(currentMax);
    });

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
                    if (response.status === 'ADDED') {
                        // Update all wishlist buttons for this item across the page
                        $(`a[onclick="addToWishlist(${itemId})"]`).each(function() {
                            $(this).replaceWith(`
                            <a href="wishlist.php" class="in-wishlist" data-bs-toggle="tooltip" title="View Wishlist">
                                <i class="fa-solid fa-heart"></i>
                            </a>
                        `);
                        });

                        // Update count
                        $('.wishlist-count').text(response.count);

                        // Show success message
                        const messageDiv = $('<div>')
                            .addClass('wishlist-message')
                            .html('<i class="fa fa-check-circle"></i> Item added to wishlist successfully!')
                            .appendTo('body');

                        setTimeout(function() {
                            messageDiv.fadeOut(function() {
                                $(this).remove();
                            });
                        }, 3000);

                    } else if (response.status === 'EXISTS') {
                        button.html(originalHtml);
                        button.removeClass('disabled');

                        const messageDiv = $('<div>')
                            .addClass('wishlist-message')
                            .css('background-color', '#ecb0a3')
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
        $('#sortSelect').on('change', function() {
            let currentUrl = new URL(window.location.href);
            let params = new URLSearchParams(currentUrl.search);

            // Update or remove sort parameter
            if (this.value) {
                params.set('sort', this.value);
            } else {
                params.delete('sort');
            }

            // Reset to page 1 when sorting changes
            params.set('page', '1');

            // Preserve all other parameters and redirect
            window.location.href = `${currentUrl.pathname}?${params.toString()}`;
        });
    });

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
                        $('.gift-btn').prop('disabled', true)
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