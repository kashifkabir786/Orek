<?php require_once('../Connections/orek.php'); ?>
<?php require_once('session.php'); ?>
<?php

$query_Recordset3 = "SELECT * FROM site_visits ORDER BY visit_time DESC LIMIT 10";
$Recordset3 = mysqli_query( $orek, $query_Recordset3 )or die( mysqli_error( $orek ) );
$row_Recordset3 = mysqli_fetch_assoc( $Recordset3 );
$totalRows_Recordset3 = mysqli_num_rows( $Recordset3 );

// Get filter parameter, default to 'today'
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'today';

// Set up date ranges based on filter
switch($filter) {
    case 'month':
        $current_period = "DATE(payment_date) >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
        $previous_period = "DATE(payment_date) >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH) AND DATE(payment_date) < DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
        break;
    case 'year':
        $current_period = "DATE(payment_date) >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
        $previous_period = "DATE(payment_date) >= DATE_SUB(CURDATE(), INTERVAL 2 YEAR) AND DATE(payment_date) < DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
        break;
    default: // today
        $current_period = "DATE(payment_date) = CURDATE()";
        $previous_period = "DATE(payment_date) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
}

$query_Sales = "SELECT (SELECT COUNT(*) FROM payment WHERE $current_period) as current_sales, (SELECT COUNT(*) FROM payment WHERE $previous_period) as previous_sales";
$Sales = mysqli_query($orek, $query_Sales) or die(mysqli_error($orek));
$row_Sales = mysqli_fetch_assoc($Sales);

$current_sales = $row_Sales['current_sales'];
$previous_sales = $row_Sales['previous_sales'];
$sales_percentage = $previous_sales > 0 ? (($current_sales - $previous_sales) / $previous_sales * 100) : 0;
$trend = $sales_percentage >= 0 ? 'increase' : 'decrease';
$trend_class = $sales_percentage >= 0 ? 'text-success' : 'text-danger';

// Get revenue data based on filter
$revenue_filter = isset($_GET['revenue_filter']) ? $_GET['revenue_filter'] : 'month';

switch($revenue_filter) {
    case 'today':
        $revenue_current = "DATE(payment_date) = CURDATE()";
        $revenue_previous = "DATE(payment_date) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
        break;
    case 'year':
        $revenue_current = "DATE(payment_date) >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
        $revenue_previous = "DATE(payment_date) >= DATE_SUB(CURDATE(), INTERVAL 2 YEAR) AND DATE(payment_date) < DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
        break;
    default: // month
        $revenue_current = "DATE(payment_date) >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
        $revenue_previous = "DATE(payment_date) >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH) AND DATE(payment_date) < DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
}

$query_Revenue = "SELECT (SELECT COALESCE(SUM(amount), 0) FROM payment WHERE $revenue_current) as current_revenue, (SELECT COALESCE(SUM(amount), 0) FROM payment WHERE $revenue_previous) as previous_revenue";
$Revenue = mysqli_query($orek, $query_Revenue) or die(mysqli_error($orek));
$row_Revenue = mysqli_fetch_assoc($Revenue);

$current_revenue = $row_Revenue['current_revenue'];
$previous_revenue = $row_Revenue['previous_revenue'];
$revenue_percentage = $previous_revenue > 0 ? (($current_revenue - $previous_revenue) / $previous_revenue * 100) : 0;
$revenue_trend = $revenue_percentage >= 0 ? 'increase' : 'decrease';
$revenue_trend_class = $revenue_percentage >= 0 ? 'text-success' : 'text-danger';

// Get customers data based on filter
$customers_filter = isset($_GET['customers_filter']) ? $_GET['customers_filter'] : 'year';

switch($customers_filter) {
    case 'today':
        $customers_current = "DATE(date_added) = CURDATE()";
        $customers_previous = "DATE(date_added) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
        break;
    case 'month':
        $customers_current = "DATE(date_added) >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
        $customers_previous = "DATE(date_added) >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH) AND DATE(date_added) < DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
        break;
    default: // year
        $customers_current = "DATE(date_added) >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
        $customers_previous = "DATE(date_added) >= DATE_SUB(CURDATE(), INTERVAL 2 YEAR) AND DATE(date_added) < DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
}

$query_Customers = "SELECT (SELECT COUNT(*) FROM user WHERE $customers_current) as current_customers, (SELECT COUNT(*) FROM user WHERE $customers_previous) as previous_customers";
$Customers = mysqli_query($orek, $query_Customers) or die(mysqli_error($orek));
$row_Customers = mysqli_fetch_assoc($Customers);

$current_customers = $row_Customers['current_customers'];
$previous_customers = $row_Customers['previous_customers'];
$customers_percentage = $previous_customers > 0 ? (($current_customers - $previous_customers) / $previous_customers * 100) : 0;
$customers_trend = $customers_percentage >= 0 ? 'increase' : 'decrease';
$customers_trend_class = $customers_percentage >= 0 ? 'text-success' : 'text-danger';

// Get report data for the last 7 days
$query_Reports = "SELECT DATE(payment_date) as date, COUNT(*) as sales, SUM(amount) as revenue, COUNT(DISTINCT user_id) as customers FROM payment WHERE payment_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) GROUP BY DATE(payment_date) ORDER BY date ASC";
$Reports = mysqli_query($orek, $query_Reports) or die(mysqli_error($orek));

$report_dates = [];
$report_sales = [];
$report_revenue = [];
$report_customers = [];

while($row_Reports = mysqli_fetch_assoc($Reports)) {
    $report_dates[] = $row_Reports['date'];
    $report_sales[] = (int)$row_Reports['sales'];
    $report_revenue[] = (int)$row_Reports['revenue'];
    $report_customers[] = (int)$row_Reports['customers'];
}

// Add this with other queries at the top
$recent_sales_filter = isset($_GET['recent_sales_filter']) ? $_GET['recent_sales_filter'] : 'today';

switch($recent_sales_filter) {
    case 'month':
        $recent_sales_period = "DATE(payment_date) >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
        break;
    case 'year':
        $recent_sales_period = "DATE(payment_date) >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
        break;
    default: // today
        $recent_sales_period = "DATE(payment_date) = CURDATE()";
}

$query_RecentSales = "SELECT A.payment_id, A.amount, A.payment_date, CONCAT(B.fname, ' ', B.lname) as customer_name, C.status, E.item_name, E.image_1 as product_image FROM payment AS A INNER JOIN user AS B ON A.user_id = B.user_id INNER JOIN cart AS C ON A.cart_id = C.cart_id INNER JOIN cart_item AS D ON C.cart_id = D.cart_id INNER JOIN item AS E ON D.item_id = E.item_id WHERE $recent_sales_period ORDER BY A.payment_date DESC LIMIT 5";
$RecentSales = mysqli_query($orek, $query_RecentSales) or die(mysqli_error($orek));

// Get Top Selling products
$top_selling_filter = isset($_GET['top_selling_filter']) ? $_GET['top_selling_filter'] : 'today';

switch($top_selling_filter) {
    case 'month':
        $top_selling_period = "DATE(p.payment_date) >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
        break;
    case 'year':
        $top_selling_period = "DATE(p.payment_date) >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
        break;
    default: // today
        $top_selling_period = "DATE(p.payment_date) = CURDATE()";
}

$query_TopSelling = "SELECT i.item_id, i.item_name, i.image_1, COUNT(*) as total_sold, AVG(p.amount) as price, SUM(p.amount) as total_revenue FROM payment p JOIN cart c ON p.cart_id = c.cart_id JOIN cart_item ci ON c.cart_id = ci.cart_id JOIN item i ON ci.item_id = i.item_id WHERE $top_selling_period GROUP BY i.item_id ORDER BY total_sold DESC LIMIT 5";
$TopSelling = mysqli_query($orek, $query_TopSelling) or die(mysqli_error($orek));

// Get cart details
$query_CartDetails = "SELECT cart.*, (SELECT COUNT(*) FROM cart_item WHERE cart_id = cart.cart_id) as total_items, (SELECT SUM(amount) FROM cart_item WHERE cart_id = cart.cart_id) as total_amount, user.fname, user.lname, user.user_id, user.email, user.phone_no FROM cart INNER JOIN user ON cart.user_id = user.user_id WHERE cart.status = 'Pending' ORDER BY cart.date DESC LIMIT 5";
$CartDetails = mysqli_query($orek, $query_CartDetails) or die(mysqli_error($orek));

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Dashboard - Orek</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="assets/img/logo.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="assets/css/style.css" rel="stylesheet">

    <!-- =======================================================
  * Template Name: NiceAdmin
  * Updated: Mar 09 2023 with Bootstrap v5.2.3
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>

    <?php require_once('menu.php'); ?>

    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Dashboard</h1>
            <!-- <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </nav> -->
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">

                <!-- Left side columns -->
                <div class="col-lg-8">
                    <div class="row">

                        <!-- Sales Card -->
                        <div class="col-xxl-4 col-md-6">
                            <div class="card info-card sales-card">
                                <div class="filter">
                                    <a class="icon" href="#" data-bs-toggle="dropdown"><i
                                            class="bi bi-three-dots"></i></a>
                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                        <li class="dropdown-header text-start">
                                            <h6>Filter</h6>
                                        </li>
                                        <li><a class="dropdown-item" href="?filter=today">Today</a></li>
                                        <li><a class="dropdown-item" href="?filter=month">This Month</a></li>
                                        <li><a class="dropdown-item" href="?filter=year">This Year</a></li>
                                    </ul>
                                </div>

                                <div class="card-body">
                                    <h5 class="card-title">Sales <span>| <?php 
                                        echo ucfirst($filter === 'today' ? 'Today' : 
                                            ($filter === 'month' ? 'This Month' : 'This Year')); 
                                    ?></span></h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-cart"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6><?php echo $row_Sales['current_sales']; ?></h6>
                                            <span class="<?php echo $trend_class; ?> small pt-1 fw-bold">
                                                <?php echo abs(round($sales_percentage, 1)); ?>%
                                            </span>
                                            <span class="text-muted small pt-2 ps-1"><?php echo $trend; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- End Sales Card -->

                        <!-- Revenue Card -->
                        <div class="col-xxl-4 col-md-6">
                            <div class="card info-card revenue-card">
                                <div class="filter">
                                    <a class="icon" href="#" data-bs-toggle="dropdown"><i
                                            class="bi bi-three-dots"></i></a>
                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                        <li class="dropdown-header text-start">
                                            <h6>Filter</h6>
                                        </li>
                                        <li><a class="dropdown-item" href="?revenue_filter=today">Today</a></li>
                                        <li><a class="dropdown-item" href="?revenue_filter=month">This Month</a></li>
                                        <li><a class="dropdown-item" href="?revenue_filter=year">This Year</a></li>
                                    </ul>
                                </div>

                                <div class="card-body">
                                    <h5 class="card-title">Revenue <span>| <?php 
                                        echo ucfirst($revenue_filter === 'today' ? 'Today' : 
                                            ($revenue_filter === 'month' ? 'This Month' : 'This Year')); 
                                    ?></span></h5>

                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            &#8377;
                                        </div>
                                        <div class="ps-3">
                                            <h6>&#8377;<?php echo number_format($current_revenue, 0); ?></h6>
                                            <span class="<?php echo $revenue_trend_class; ?> small pt-1 fw-bold">
                                                <?php echo abs(round($revenue_percentage, 1)); ?>%
                                            </span>
                                            <span
                                                class="text-muted small pt-2 ps-1"><?php echo $revenue_trend; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- End Revenue Card -->

                        <!-- Customers Card -->
                        <div class="col-xxl-4 col-xl-12">

                            <div class="card info-card customers-card">
                                <div class="filter">
                                    <a class="icon" href="#" data-bs-toggle="dropdown"><i
                                            class="bi bi-three-dots"></i></a>
                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                        <li class="dropdown-header text-start">
                                            <h6>Filter</h6>
                                        </li>
                                        <li><a class="dropdown-item" href="?customers_filter=today">Today</a></li>
                                        <li><a class="dropdown-item" href="?customers_filter=month">This Month</a></li>
                                        <li><a class="dropdown-item" href="?customers_filter=year">This Year</a></li>
                                    </ul>
                                </div>

                                <div class="card-body">
                                    <h5 class="card-title">Customers <span>| <?php 
                                        echo ucfirst($customers_filter === 'today' ? 'Today' : 
                                            ($customers_filter === 'month' ? 'This Month' : 'This Year')); 
                                    ?></span></h5>

                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-people"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6><?php echo $row_Customers['current_customers']; ?></h6>
                                            <span class="<?php echo $customers_trend_class; ?> small pt-1 fw-bold">
                                                <?php echo abs(round($customers_percentage, 1)); ?>%
                                            </span>
                                            <span
                                                class="text-muted small pt-2 ps-1"><?php echo $customers_trend; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div><!-- End Customers Card -->

                        <!-- Reports -->
                        <div class="col-12">
                            <div class="card">
                                <!-- <div class="filter">
                                    <a class="icon" href="#" data-bs-toggle="dropdown"><i
                                            class="bi bi-three-dots"></i></a>
                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                        <li class="dropdown-header text-start">
                                            <h6>Filter</h6>
                                        </li>
                                        <li><a class="dropdown-item" href="#">Today</a></li>
                                        <li><a class="dropdown-item" href="#">This Month</a></li>
                                        <li><a class="dropdown-item" href="#">This Year</a></li>
                                    </ul>
                                </div> -->

                                <div class="card-body">
                                    <h5 class="card-title">Reports <span>/Last 7 Days</span></h5>

                                    <!-- Line Chart -->
                                    <div id="reportsChart"></div>

                                    <script>
                                    document.addEventListener("DOMContentLoaded", () => {
                                        new ApexCharts(document.querySelector("#reportsChart"), {
                                            series: [{
                                                name: 'Sales',
                                                data: <?php echo json_encode($report_sales); ?>,
                                            }, {
                                                name: 'Revenue',
                                                data: <?php echo json_encode($report_revenue); ?>,
                                            }, {
                                                name: 'Customers',
                                                data: <?php echo json_encode($report_customers); ?>,
                                            }],
                                            chart: {
                                                height: 350,
                                                type: 'area',
                                                toolbar: {
                                                    show: false
                                                },
                                            },
                                            markers: {
                                                size: 4
                                            },
                                            colors: ['#4154f1', '#2eca6a', '#ff771d'],
                                            fill: {
                                                type: "gradient",
                                                gradient: {
                                                    shadeIntensity: 1,
                                                    opacityFrom: 0.3,
                                                    opacityTo: 0.4,
                                                    stops: [0, 90, 100]
                                                }
                                            },
                                            dataLabels: {
                                                enabled: false
                                            },
                                            stroke: {
                                                curve: 'smooth',
                                                width: 2
                                            },
                                            xaxis: {
                                                type: 'datetime',
                                                categories: <?php echo json_encode($report_dates); ?>
                                            },
                                            tooltip: {
                                                x: {
                                                    format: 'dd/MM/yy'
                                                },
                                            }
                                        }).render();
                                    });
                                    </script>
                                    <!-- End Line Chart -->
                                </div>
                            </div>
                        </div><!-- End Reports -->

                        <!-- Recent Sales -->
                        <div class="col-12">
                            <div class="card recent-sales overflow-auto">
                                <div class="filter">
                                    <a class="icon" href="#" data-bs-toggle="dropdown"><i
                                            class="bi bi-three-dots"></i></a>
                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                        <li class="dropdown-header text-start">
                                            <h6>Filter</h6>
                                        </li>
                                        <li><a class="dropdown-item" href="?recent_sales_filter=today">Today</a></li>
                                        <li><a class="dropdown-item" href="?recent_sales_filter=month">This Month</a>
                                        </li>
                                        <li><a class="dropdown-item" href="?recent_sales_filter=year">This Year</a></li>
                                    </ul>
                                </div>

                                <div class="card-body">
                                    <h5 class="card-title">Recent Sales <span>| <?php 
                                        echo ucfirst($recent_sales_filter === 'today' ? 'Today' : 
                                            ($recent_sales_filter === 'month' ? 'This Month' : 'This Year')); 
                                    ?></span></h5>

                                    <table class="table table-borderless datatable">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Customer</th>
                                                <th scope="col">Product</th>
                                                <th scope="col">Price</th>
                                                <th scope="col">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while($row_RecentSales = mysqli_fetch_assoc($RecentSales)) { ?>
                                            <tr>
                                                <th scope="row">#<?php echo $row_RecentSales['payment_id']; ?></th>
                                                <td><?php echo htmlspecialchars($row_RecentSales['customer_name']); ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($row_RecentSales['item_name']); ?>
                                                </td>
                                                <td>₹<?php echo number_format($row_RecentSales['amount'], 0); ?></td>
                                                <td><span class="badge bg-success">
                                                        <?php echo htmlspecialchars($row_RecentSales['status']); ?></span>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div><!-- End Recent Sales -->

                        <!-- Top Selling -->
                        <div class="col-12">
                            <div class="card top-selling overflow-auto">
                                <div class="filter">
                                    <a class="icon" href="#" data-bs-toggle="dropdown"><i
                                            class="bi bi-three-dots"></i></a>
                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                        <li class="dropdown-header text-start">
                                            <h6>Filter</h6>
                                        </li>
                                        <li><a class="dropdown-item" href="?top_selling_filter=today">Today</a></li>
                                        <li><a class="dropdown-item" href="?top_selling_filter=month">This Month</a>
                                        </li>
                                        <li><a class="dropdown-item" href="?top_selling_filter=year">This Year</a></li>
                                    </ul>
                                </div>

                                <div class="card-body pb-0">
                                    <h5 class="card-title">Top Selling <span>| <?php 
            echo ucfirst($top_selling_filter === 'today' ? 'Today' : 
                ($top_selling_filter === 'month' ? 'This Month' : 'This Year')); 
        ?></span></h5>

                                    <table class="table table-borderless">
                                        <thead>
                                            <tr>
                                                <th scope="col">Preview</th>
                                                <th scope="col">Product</th>
                                                <th scope="col">Price</th>
                                                <th scope="col">Sold</th>
                                                <th scope="col">Revenue</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while($row_TopSelling = mysqli_fetch_assoc($TopSelling)) { ?>
                                            <tr>
                                                <th scope="row"><img
                                                        src="../assets/img/item/<?php echo htmlspecialchars($row_TopSelling['image_1']); ?>"
                                                        alt=""></th>
                                                <td><?php echo htmlspecialchars($row_TopSelling['item_name']); ?>
                                                </td>
                                                <td>₹<?php echo number_format($row_TopSelling['price'], 0); ?></td>
                                                <td class="fw-bold"><?php echo $row_TopSelling['total_sold']; ?></td>
                                                <td>₹<?php echo number_format($row_TopSelling['total_revenue'], 0); ?>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div><!-- End Top Selling -->

                    </div>
                </div><!-- End Left side columns -->

                <!-- Right side columns -->
                <div class="col-lg-4">

                    <!-- Recent Activity -->
                    <div class="card">
                        <div class="filter">
                            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                <li class="dropdown-header text-start">
                                    <h6>Filter</h6>
                                </li>

                                <li><a class="dropdown-item" href="visitors.php">View All</a></li>
                            </ul>
                        </div>

                        <div class="card-body">
                            <h5 class="card-title">Recent Visitors <span
                                    class="text-muted">(<?php echo $totalRows_Recordset3; ?>)</span> <span>|
                                    Today</span></h5>

                            <div class="activity">

                                <?php  do {  ?>
                                <div class="activity-item d-flex">
                                    <div class="activite-label">
                                        <?php echo date('d F H:i a', strtotime($row_Recordset3['visit_time']. ' +5 hours 30 minutes')); ?>
                                    </div>
                                    <i class='bi bi-circle-fill activity-badge text-success align-self-start'></i>
                                    <div class="activity-content">
                                        <a href="#"
                                            class="fw-bold text-dark"><?php echo $row_Recordset3['ip_address']; ?></a>
                                    </div>
                                </div><!-- End activity item-->
                                <?php
                                }while($row_Recordset3 = mysqli_fetch_assoc($Recordset3));
                                ?>
                            </div>

                        </div>
                    </div><!-- End Recent Activity -->

                    <!-- Budget Report -->
                    <div class="card">
                        <div class="filter">
                            <!-- <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a> -->
                            <!-- <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                <li class="dropdown-header text-start">
                                    <h6>Filter</h6>
                                </li>
                                <li><a class="dropdown-item" href="#">Today</a></li>
                                <li><a class="dropdown-item" href="#">This Month</a></li>
                                <li><a class="dropdown-item" href="#">This Year</a></li>
                            </ul> -->
                        </div>

                        <div class="card-body pb-0">
                            <h5 class="card-title">Pending Carts</h5>
                            <div class="card-body pb-0">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Cart ID</th>
                                                <th>Customer</th>
                                                <th>Items</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while($row_CartDetails = mysqli_fetch_assoc($CartDetails)) { ?>
                                            <tr>
                                                <td><a href="cart-item.php?cart_id=<?php echo $row_CartDetails['cart_id']; ?>"><?php echo $row_CartDetails['cart_id']; ?></a></td>
                                                <td><?php echo htmlspecialchars($row_CartDetails['fname'] ." " . $row_CartDetails['lname']); ?>
                                                </td>
                                                <td><?php echo $row_CartDetails['total_items']; ?></td>
                                                <td>₹<?php echo number_format($row_CartDetails['total_amount'], 0); ?>
                                                </td>
                                                <td><span
                                                        class="badge bg-<?php echo $row_CartDetails['status'] == 'Completed' ? 'success' : 'warning'; ?>">
                                                        <?php echo htmlspecialchars($row_CartDetails['status']); ?>
                                                    </span></td>
                                                <td><?php echo date('d-m-Y', strtotime($row_CartDetails['date'])); ?>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div><!-- End Budget Report -->

                    <!-- Website Traffic -->
                    <div class="card">
                        <div class="filter">
                            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                <li class="dropdown-header text-start">
                                    <h6>Filter</h6>
                                </li>

                                <li><a class="dropdown-item" href="#">Today</a></li>
                                <li><a class="dropdown-item" href="#">This Month</a></li>
                                <li><a class="dropdown-item" href="#">This Year</a></li>
                            </ul>
                        </div>

                        <div class="card-body pb-0">
                            <h5 class="card-title">Website Traffic <span>| Today</span></h5>

                            <div id="trafficChart" style="min-height: 400px;" class="echart"></div>

                            <script>
                            document.addEventListener("DOMContentLoaded", () => {
                                echarts.init(document.querySelector("#trafficChart")).setOption({
                                    tooltip: {
                                        trigger: 'item'
                                    },
                                    legend: {
                                        top: '5%',
                                        left: 'center'
                                    },
                                    series: [{
                                        name: 'Access From',
                                        type: 'pie',
                                        radius: ['40%', '70%'],
                                        avoidLabelOverlap: false,
                                        label: {
                                            show: false,
                                            position: 'center'
                                        },
                                        emphasis: {
                                            label: {
                                                show: true,
                                                fontSize: '18',
                                                fontWeight: 'bold'
                                            }
                                        },
                                        labelLine: {
                                            show: false
                                        },
                                        data: [{
                                                value: 1048,
                                                name: 'Search Engine'
                                            },
                                            {
                                                value: 735,
                                                name: 'Direct'
                                            },
                                            {
                                                value: 580,
                                                name: 'Email'
                                            },
                                            {
                                                value: 484,
                                                name: 'Union Ads'
                                            },
                                            {
                                                value: 300,
                                                name: 'Video Ads'
                                            }
                                        ]
                                    }]
                                });
                            });
                            </script>

                        </div>
                    </div><!-- End Website Traffic -->
                </div><!-- End Right side columns -->

            </div>
        </section>

    </main><!-- End #main -->

    <!-- ======= Footer ======= -->
    <footer id="footer" class="footer">
        <div class="copyright">
            &copy; Copyright <strong><span>Orek</span></strong>. All Rights Reserved
        </div>
        <div class="credits">
            <!-- All the links in the footer should remain intact. -->
            <!-- You can delete the links only if you purchased the pro version. -->
            <!-- Licensing information: https://bootstrapmade.com/license/ -->
            <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
            Designed by <a href="https://xwaydesigns.com/website-application.html">X Way Design</a>
        </div>
    </footer><!-- End Footer -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/chart.js/chart.umd.js"></script>
    <script src="assets/vendor/echarts/echarts.min.js"></script>
    <script src="assets/vendor/quill/quill.min.js"></script>
    <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>

    <!-- Template Main JS File -->
    <script src="assets/js/main.js"></script>

</body>

</html>