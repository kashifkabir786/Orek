<?php require_once('Connections/orek.php'); ?>
<?php require_once('session-2.php'); ?>

<?php
// Initialize filter variables
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 
              (isset($_GET['category']) ? (int)$_GET['category'] : null);
$category_level1_id = isset($_GET['category_level1_id']) ? (int)$_GET['category_level1_id'] : null;
$min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : null;
$max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : null;
$size = isset($_GET['size']) ? $_GET['size'] : null;
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';
$search = isset($_GET['search']) ? mysqli_real_escape_string($orek, trim($_GET['search'])) : null;

$limit = 9; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Modify count query to include filters
$count_query = "SELECT COUNT(DISTINCT i.item_id) as total 
                FROM item i 
                LEFT JOIN category_level1 cl ON i.category_level1_id = cl.category_level1_id 
                LEFT JOIN category c ON i.category_id = c.category_id 
                WHERE i.listing_status = 'Active'";
if ($search) $count_query .= " AND (i.item_name LIKE '%$search%' OR c.category_name LIKE '%$search%' OR cl.category_level1_name LIKE '%$search%')";
if ($category_id) $count_query .= " AND i.category_id = $category_id";
if ($category_level1_id) $count_query .= " AND i.category_level1_id = $category_level1_id";
if ($min_price) $count_query .= " AND (i.price - (i.price * i.discount/100)) >= $min_price";
if ($max_price) $count_query .= " AND (i.price - (i.price * i.discount/100)) <= $max_price";
if ($size) $count_query .= " AND i.size = '$size'";

$count_result = mysqli_query($orek, $count_query);
$total_records = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_records / $limit);
mysqli_free_result($count_result);

// Replace stored procedure with direct query
$query_Recordset2 = "SELECT DISTINCT i.*, (i.price - (i.price * i.discount/100)) as discounted_price 
                     FROM item i 
                     LEFT JOIN category_level1 cl ON i.category_level1_id = cl.category_level1_id 
                     LEFT JOIN category c ON i.category_id = c.category_id 
                     WHERE i.listing_status = 'Active'";
if ($search) $query_Recordset2 .= " AND (i.item_name LIKE '%$search%' OR c.category_name LIKE '%$search%' OR cl.category_level1_name LIKE '%$search%')";
if ($category_id) $query_Recordset2 .= " AND i.category_id = $category_id";
if ($category_level1_id) $query_Recordset2 .= " AND i.category_level1_id = $category_level1_id";
if ($min_price) $query_Recordset2 .= " AND (i.price - (i.price * i.discount/100)) >= $min_price";
if ($max_price) $query_Recordset2 .= " AND (i.price - (i.price * i.discount/100)) <= $max_price";
if ($size) $query_Recordset2 .= " AND i.size = '$size'";

// Modified ORDER BY clause to use discounted price
if ($sort == 'price_asc') {
    $query_Recordset2 .= " ORDER BY discounted_price ASC";
} else if ($sort == 'price_desc') {
    $query_Recordset2 .= " ORDER BY discounted_price DESC";
} else {
    $query_Recordset2 .= " ORDER BY item_id DESC";
}

$query_Recordset2 .= " LIMIT $start, $limit";
$Recordset2 = mysqli_query($orek, $query_Recordset2) or die(mysqli_error($orek));
$row_Recordset2 = mysqli_fetch_assoc($Recordset2);
$totalRows_Recordset2 = mysqli_num_rows($Recordset2);

// Get categories for filter
$query_Recordset3 = "SELECT * FROM category";
$Recordset3 = mysqli_query($orek, $query_Recordset3) or die(mysqli_error($orek));
$row_Recordset3 = mysqli_fetch_assoc($Recordset3);
$totalRows_Recordset3 = mysqli_num_rows($Recordset3);

// Get available sizes for filter
$query_sizes = "SELECT DISTINCT size FROM item WHERE size IS NOT NULL ORDER BY size";
$sizes_result = mysqli_query($orek, $query_sizes);

// Get price range
$price_query = "SELECT MIN(price) as min_original_price, MAX(price) as max_original_price, MIN(ROUND(price - (price * discount/100))) as min_discounted_price, MAX(ROUND(price - (price * discount/100))) as max_discounted_price FROM item WHERE price > 0";
$price_result = mysqli_query($orek, $price_query);
$price_range = mysqli_fetch_assoc($price_result);

// Use original prices instead of discounted prices
$min_price_db = max(0, floor($price_range['min_original_price']));
$max_price_db = max($min_price_db, ceil($price_range['max_original_price']));

// Initialize current min and max from GET parameters or use database values
$current_min = isset($_GET['min_price']) ? (int)$_GET['min_price'] : $min_price_db;
$current_max = isset($_GET['max_price']) ? (int)$_GET['max_price'] : $max_price_db;

mysqli_free_result($price_result);
?>

<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <?php
    // Get current URL for canonical tag
    $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $current_url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    
    // Generate page title based on filters applied
    $page_title = "Products";
    $category_name = "";
    
    if ($category_id) {
        $cat_query = "SELECT category_name FROM category WHERE category_id = $category_id";
        $cat_result = mysqli_query($orek, $cat_query);
        if ($cat_result && mysqli_num_rows($cat_result) > 0) {
            $category_name = mysqli_fetch_assoc($cat_result)['category_name'];
            $page_title = $category_name . " Products";
            mysqli_free_result($cat_result);
        }
    }
    
    if ($search) {
        $page_title = "Search Results for \"" . htmlspecialchars($search) . "\"";
    }
    
    $page_title .= " - Orek";
    
    // Generate meta description
    $meta_description = "Explore our collection of ";
    if ($category_name) {
        $meta_description .= $category_name . " products. ";
    } else {
        $meta_description .= "quality products. ";
    }
    
    if ($min_price && $max_price) {
        $meta_description .= "Price range ₹$min_price-₹$max_price. ";
    }
    
    $meta_description .= "Find the perfect items at Orek with easy shopping, secure checkout, and fast delivery.";
    ?>
    <title><?php echo $page_title; ?></title>
    <!-- Meta tags for SEO -->
    <meta name="description" content="<?php echo $meta_description; ?>" />
    <meta name="robots" content="index, follow" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="canonical" href="<?php echo $current_url; ?>" />
    
    <!-- Additional meta tags for SEO -->
    <meta name="keywords" content="<?php echo $category_name ? $category_name . ',' : ''; ?> online shopping, orek products, buy online, india shopping" />
    <meta name="author" content="Orek" />
    
    <!-- Open Graph tags for social media sharing -->
    <meta property="og:title" content="<?php echo $page_title; ?>" />
    <meta property="og:description" content="<?php echo $meta_description; ?>" />
    <meta property="og:url" content="<?php echo $current_url; ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="Orek" />
    
    <!-- Twitter Card data -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="<?php echo $page_title; ?>" />
    <meta name="twitter:description" content="<?php echo $meta_description; ?>" />
    
    <!-- JSON-LD structured data for Product List Page -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "ItemList",
        "name": "<?php echo $page_title; ?>",
        "description": "<?php echo $meta_description; ?>",
        "numberOfItems": <?php echo $total_records; ?>,
        "itemListOrder": "<?php echo ($sort == 'price_asc') ? 'https://schema.org/ItemListOrderAscending' : (($sort == 'price_desc') ? 'https://schema.org/ItemListOrderDescending' : 'https://schema.org/ItemListUnordered'); ?>",
        "url": "<?php echo $current_url; ?>"
    }
    </script>

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
                                <ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">
                                    <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                                        <a href="index.php" itemprop="item"><i class="fa fa-home"></i><span itemprop="name" class="sr-only">Home</span></a>
                                        <meta itemprop="position" content="1" />
                                    </li>
                                    <?php if($category_name): ?>
                                    <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                                        <a href="product-list.php" itemprop="item"><span itemprop="name">All Products</span></a>
                                        <meta itemprop="position" content="2" />
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                                        <span itemprop="name"><?php echo $category_name; ?></span>
                                        <meta itemprop="position" content="3" />
                                    </li>
                                    <?php else: ?>
                                    <li class="breadcrumb-item active" aria-current="page" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                                        <span itemprop="name">Product List</span>
                                        <meta itemprop="position" content="2" />
                                    </li>
                                    <?php endif; ?>
                                </ol>
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
                            <!-- Categories filter -->
                            <div class="sidebar-single">
                                <h3 class="sidebar-title h5">Categories</h3>
                                <div class="sidebar-body">
                                    <ul class="shop-categories" role="navigation" aria-label="Product Categories">
                                        <li><a href="product-list.php">All Categories</a></li>
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
                                            <a href="product-list.php?category_id=<?php echo $row_Recordset3['category_id']; ?>"
                                                class="<?php echo ($category_id == $row_Recordset3['category_id']) ? 'active' : ''; ?>">
                                                <?php echo $row_Recordset3['category_name']; ?>
                                            </a>
                                            <?php if($hasSubcategories && $category_id == $row_Recordset3['category_id']) { ?>
                                            <ul class="sub-categories" style="margin-left: 20px; margin-top: 10px;">
                                                <?php while($row_SubCategory = mysqli_fetch_assoc($SubCategory)) { ?>
                                                <li>
                                                    <a href="product-list.php?category_id=<?php echo $row_Recordset3['category_id']; ?>&category_level1_id=<?php echo $row_SubCategory['category_level1_id']; ?>"
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

                            <!-- single sidebar start -->
                            <div class="sidebar-single">
                                <h3 class="sidebar-title h5">Price</h3>
                                <div class="sidebar-body">
                                    <div class="price-range-wrap" role="search" aria-label="Filter by price">
                                        <div class="price-range" data-min="<?php echo $min_price_db; ?>"
                                            data-max="<?php echo $max_price_db; ?>"
                                            data-current-min="<?php echo $current_min; ?>"
                                            data-current-max="<?php echo $current_max; ?>">
                                        </div>
                                        <div class="range-slider">
                                            <form id="priceFilterForm" action="product-list.php" method="GET">
                                                <?php if($category_id): ?>
                                                <input type="hidden" name="category_id"
                                                    value="<?php echo $category_id; ?>">
                                                <?php endif; ?>
                                                <?php if($category_level1_id): ?>
                                                <input type="hidden" name="category_level1_id"
                                                    value="<?php echo $category_level1_id; ?>">
                                                <?php endif; ?>
                                                <?php if($size): ?>
                                                <input type="hidden" name="size" value="<?php echo $size; ?>">
                                                <?php endif; ?>
                                                <?php if($sort): ?>
                                                <input type="hidden" name="sort" value="<?php echo $sort; ?>">
                                                <?php endif; ?>
                                                <div class="price-input">
                                                    <label for="amount">Price: </label>
                                                    <input type="text" id="amount" readonly>
                                                    <input type="hidden" name="min_price" id="min_price"
                                                        value="<?php echo $current_min; ?>">
                                                    <input type="hidden" name="max_price" id="max_price"
                                                        value="<?php echo $current_max; ?>">
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- single sidebar end -->
                        </aside>
                    </div>
                    <!-- single sidebar end -->

                    <!-- shop main wrapper start -->
                    <div class="col-lg-9 order-1">
                        <div class="shop-product-wrapper">
                            <!-- shop product top wrap start -->
                            <div class="shop-top-bar">
                                <div class="row align-items-center">
                                    <div class="col-lg-7 col-md-6 order-2 order-md-1">
                                        <div class="top-bar-left">
                                            <div class="product-view-mode">
                                                <a class="active" href="#" data-target="grid-view"
                                                    data-bs-toggle="tooltip" title="Grid View"><i
                                                        class="fa fa-th"></i><span class="sr-only">Grid view</span></a>
                                                <a href="#" data-target="list-view" data-bs-toggle="tooltip"
                                                    title="List View"><i class="fa fa-list"></i><span class="sr-only">List view</span></a>
                                            </div>
                                            <div class="product-amount">
                                                <p>Showing
                                                    <?php echo min($start + 1, $total_records); ?>–<?php echo min($start + $limit, $total_records); ?>
                                                    of <?php echo $total_records; ?> results</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-5 col-md-6 order-1 order-md-2">
                                        <div class="top-bar-right">
                                            <div class="product-short">
                                                <p>Sort By : </p>
                                                <select class="nice-select" name="sort" id="sortSelect" aria-label="Sort products by">
                                                    <option value="">Relevance</option>
                                                    <option value="price_asc"
                                                        <?php echo ($sort == 'price_asc') ? 'selected' : ''; ?>>Price
                                                        (Low > High)</option>
                                                    <option value="price_desc"
                                                        <?php echo ($sort == 'price_desc') ? 'selected' : ''; ?>>Price
                                                        (High > Low)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- product item list wrapper start -->
                            <div class="shop-product-wrap grid-view row mbn-30">
                                <?php do { ?>
                                <!-- product single item start -->
                                <div class="col-md-4 col-sm-6">
                                    <!-- product grid start -->
                                    <div class="product-item" itemscope itemtype="https://schema.org/Product">
                                        <figure class="product-thumb">
                                            <a href="product-details.php?item_id=<?php echo $row_Recordset2['item_id']; ?>">
                                                <img class="pri-img" src="assets/img/item/<?php echo $row_Recordset2['image_1']; ?>"
                                                    alt="<?php echo $row_Recordset2['item_name']; ?>" itemprop="image" loading="lazy">
                                                <img class="sec-img" src="assets/img/item/<?php echo $row_Recordset2['image_2'] == '' ? $row_Recordset2['image_1'] : $row_Recordset2['image_2']; ?>"
                                                    alt="<?php echo $row_Recordset2['item_name']; ?> - additional view" loading="lazy">
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
                                                    // Call stored procedure to check cart status
                                                    $stmt = mysqli_prepare($orek, "CALL CheckItemInCart(?, ?, @is_in_cart)");
                                                    $email = $_SESSION['email'];
                                                    $item_id = $row_Recordset2['item_id'];
                                                    mysqli_stmt_bind_param($stmt, "si", $email, $item_id);
                                                    mysqli_stmt_execute($stmt);
                                                    mysqli_stmt_close($stmt);
                                                    
                                                    // Get result from procedure
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
                                                <p class="manufacturer-name"><a
                                                        href="product-details.php?item_id=<?php echo $row_Recordset2['item_id']; ?>">Silver</a>
                                                </p>
                                            </div>

                                            <h2 class="product-name h6" itemprop="name">
                                                <a href="product-details.php?item_id=<?php echo $row_Recordset2['item_id']; ?>"><?php echo $row_Recordset2['item_name']; ?></a>
                                            </h2>
                                            <div class="price-box" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                                                <?php
                                                // Actual price
                                                $actual_price = $row_Recordset2['price'];
                                                // Discount percentage
                                                $discount_percentage = $row_Recordset2['discount'];
                                                // Discounted price calculation with rounding
                                                $discounted_price = round($actual_price - ($actual_price * ($discount_percentage / 100)));
                                                ?>
                                                <span class="price-regular">&#8377;<span itemprop="price" content="<?php echo $discounted_price; ?>"><?php echo $discounted_price; ?></span></span>
                                                <meta itemprop="priceCurrency" content="INR">
                                                <span class="price-old"><del>&#8377;<?php echo round($actual_price); ?></del></span>
                                                <link itemprop="availability" href="https://schema.org/InStock">
                                                <link itemprop="url" href="product-details.php?item_id=<?php echo $row_Recordset2['item_id']; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- product grid end -->

                                    <!-- product list item end -->
                                    <div class="product-list-item" itemscope itemtype="https://schema.org/Product">
                                        <figure class="product-thumb">
                                            <a href="product-details.php?item_id=<?php echo $row_Recordset2['item_id']; ?>">
                                                <img class="pri-img" src="assets/img/item/<?php echo $row_Recordset2['image_1']; ?>"
                                                    alt="<?php echo $row_Recordset2['item_name']; ?>" itemprop="image" loading="lazy">
                                                <img class="sec-img" src="assets/img/item/<?php echo $row_Recordset2['image_2'] == '' ? $row_Recordset2['image_1'] : $row_Recordset2['image_2']; ?>"
                                                    alt="<?php echo $row_Recordset2['item_name']; ?> - alternate view" loading="lazy">
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
                                                <?php if(isset($_SESSION['email'])): ?>
                                                <button class="btn btn-cart"
                                                    onclick="addToCartProc(<?php echo $row_Recordset2['item_id']; ?>)">Add
                                                    to Cart</button>
                                                <?php else: ?>
                                                <a href="login.php" class="btn btn-cart">Add to Cart</a>
                                                <?php endif; ?>
                                            </div>
                                        </figure>
                                        <div class="product-content-list">
                                            <div class="manufacturer-name">
                                                <a href="product-details.php?item_id=<?php echo $row_Recordset2['item_id']; ?>">Silver</a>
                                            </div>

                                            <h3 class="product-name" itemprop="name"><a
                                                    href="product-details.php?item_id=<?php echo $row_Recordset2['item_id']; ?>"><?php echo $row_Recordset2['item_name']; ?></a>
                                            </h3>
                                            <div class="price-box" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                                                <?php
                                                // Actual price
                                                $actual_price = $row_Recordset2['price'];
                                                // Discount percentage
                                                $discount_percentage = $row_Recordset2['discount'];
                                                // Discounted price calculation with rounding
                                                $discounted_price = round($actual_price - ($actual_price * ($discount_percentage / 100)));
                                                ?>
                                                <span class="price-regular">&#8377;<span itemprop="price" content="<?php echo $discounted_price; ?>"><?php echo $discounted_price; ?></span></span>
                                                <meta itemprop="priceCurrency" content="INR">
                                                <span class="price-old"><del>&#8377;<?php echo round($actual_price); ?></del></span>
                                                <link itemprop="availability" href="https://schema.org/InStock">
                                                <link itemprop="url" href="product-details.php?item_id=<?php echo $row_Recordset2['item_id']; ?>">
                                            </div>
                                            <hr class="single-divider">
                                            <p itemprop="description">
                                                <?php echo $row_Recordset2['item_name']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <!-- product list item end -->
                                </div>
                                <!-- product single item start -->
                                <?php }while($row_Recordset2=mysqli_fetch_assoc($Recordset2)); ?>
                            </div>
                            <!-- product item list wrapper end -->

                            <!-- start pagination area -->
                            <div class="paginatoin-area text-center">
                                <nav aria-label="Product pagination">
                                    <ul class="pagination-box">
                                        <?php 
                                        // Build base query parameters without page
                                        $query_params = $_GET;
                                        if(isset($query_params['page'])) {
                                            unset($query_params['page']);
                                        }
                                        $base_query = http_build_query($query_params);
                                        $base_url = "product-list.php?" . ($base_query ? $base_query . '&' : '');
                                        
                                        // Previous page link
                                        if ($page > 1): 
                                        ?>
                                        <li><a class="previous" href="<?php echo $base_url; ?>page=<?php echo ($page-1); ?>" aria-label="Previous page"><i
                                                    style="font-size: 15px;" class="fa-solid fa-angle-left"></i><span class="sr-only">Previous</span></a></li>
                                        <?php endif; ?>

                                        <?php
                                        // Calculate range of page numbers to show
                                        $range = 2;
                                        $start_page = max(1, $page - $range);
                                        $end_page = min($total_pages, $page + $range);

                                        // Show first page if we're not starting at 1
                                        if ($start_page > 1) {
                                            echo '<li><a href="' . $base_url . 'page=1" aria-label="Page 1">1</a></li>';
                                            if ($start_page > 2) {
                                                echo '<li><span aria-hidden="true">...</span></li>';
                                            }
                                        }

                                        // Show page numbers
                                        for ($i = $start_page; $i <= $end_page; $i++) {
                                            echo '<li' . ($page == $i ? ' class="active" aria-current="page"' : '') . '><a href="' . $base_url . 'page=' . $i . '" aria-label="Page ' . $i . '">' . $i . '</a></li>';
                                        }

                                        // Show last page if we're not ending at last page
                                        if ($end_page < $total_pages) {
                                            if ($end_page < $total_pages - 1) {
                                                echo '<li><span aria-hidden="true">...</span></li>';
                                            }
                                            echo '<li><a href="' . $base_url . 'page=' . $total_pages . '" aria-label="Page ' . $total_pages . '">' . $total_pages . '</a></li>';
                                        }
                                        ?>

                                        <?php if ($page < $total_pages): ?>
                                        <li><a class="next" href="<?php echo $base_url; ?>page=<?php echo ($page+1); ?>" aria-label="Next page"><i
                                                    style="font-size: 15px;" class="fa-solid fa-angle-right"></i><span class="sr-only">Next</span></a></li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
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
    </script>
</body>

</html>