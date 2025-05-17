<?php require_once('Connections/orek.php'); ?>
<?php require_once('session.php'); ?>
<?php

  $query_Recordset2 = "SELECT * FROM user WHERE email = '{$_SESSION['email']}'";
  $Recordset2 = mysqli_query( $orek, $query_Recordset2 )or die( mysqli_error( $orek ) );
  $row_Recordset2 = mysqli_fetch_assoc( $Recordset2 );
  $totalRows_Recordset2 = mysqli_num_rows( $Recordset2 );

  $query_Recordset4 = "SELECT * FROM user_shipping WHERE user_id = '{$row_Recordset2['user_id']}'";
  $Recordset4 = mysqli_query( $orek, $query_Recordset4 )or die( mysqli_error( $orek ) );
  $row_Recordset4 = mysqli_fetch_assoc( $Recordset4 );
  $totalRows_Recordset4 = mysqli_num_rows( $Recordset4 );

  $query_Recordset5 = "SELECT * FROM user_shipping WHERE shipping_id = '{$row_Recordset4['shipping_id']}'";
  $Recordset5 = mysqli_query( $orek, $query_Recordset5 )or die( mysqli_error( $orek ) );
  $row_Recordset5 = mysqli_fetch_assoc( $Recordset5 );
  $totalRows_Recordset5 = mysqli_num_rows( $Recordset5 );

 
$errpass1 = $errpass2 = $errpass = "";

$editFormAction = $_SERVER[ 'PHP_SELF' ];
if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
  $editFormAction .= "?" . htmlentities( $_SERVER[ 'QUERY_STRING' ] );
}

if ( ( isset( $_POST[ "MM_update" ] ) ) && ( $_POST[ "MM_update" ] == "form1" ) ) {

  $updateSQL = sprintf( "UPDATE `user` SET `fname` = %s, `lname` = %s, `email` = %s WHERE user_id = %s",
    GetSQLValueString( $_POST[ 'fname' ], "text" ),
    GetSQLValueString( $_POST[ 'lname' ], "text" ),
    GetSQLValueString( $_POST[ 'email' ], "text" ),
    GetSQLValueString( $_POST[ 'user_id' ], "int" ) );
  $Result = mysqli_query( $orek, $updateSQL )or die( mysqli_error( $orek ) );

  $insertGoTo = "my-account.php?success=Updated";
  if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
    $insertGoTo .= ( strpos( $insertGoTo, '?' ) ) ? "&" : "?";
    $insertGoTo .= $_SERVER[ 'QUERY_STRING' ];
  }
  header( sprintf( "Location: %s", $insertGoTo ) );
}

$errpass1 = $errpass2 = $errpass = $errpass3 = "";

if ( ( isset( $_POST[ "MM_password" ] ) ) && ( $_POST[ "MM_password" ] == "form2" ) ) {

  $query_Recordset3 = "SELECT password FROM user WHERE email = '{$_POST[ 'email' ]}'";
  $Recordset3 = mysqli_query( $orek, $query_Recordset3 )or die( mysqli_error( $orek ) );
  $row_Recordset3 = mysqli_fetch_assoc( $Recordset3 );

  $hash = $row_Recordset3[ 'password' ];
  if ( !password_verify( $_POST[ 'old-password' ], $hash ) )
    $errpass3 = "Wrong Password Entered";
  if ( empty( $_POST[ 'password' ] ) )
    $errpass1 = "Please Enter Password";
  if ( empty( $_POST[ 'rpassword' ] ) )
    $errpass2 = "Please Retype Password";
  if ( $_POST[ 'password' ] != $_POST[ 'rpassword' ] )
    $errpass = "Passwords Don't Match";

  if ( empty( $errpass3 ) && empty( $errpass1 ) && empty( $errpass2 ) && empty( $errpass ) ) {

    $password = $_POST[ 'password' ];
    $hash = password_hash( $password, PASSWORD_DEFAULT );

    $updateSQL = sprintf( "UPDATE `user` SET `password` = '$hash' WHERE `email` = %s", GetSQLValueString( $_POST[ 'email' ], "text" ) );

    $Result1 = mysqli_query( $orek, $updateSQL )or die( mysqli_error( $orek ) );

    unset( $_SESSION[ 'email' ] );

    $insertGoTo = "login.php?success=Password Changed";
    if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
      $insertGoTo .= ( strpos( $insertGoTo, '?' ) ) ? "&" : "?";
      $insertGoTo .= $_SERVER[ 'QUERY_STRING' ];
    }
    header( sprintf( "Location: %s", $insertGoTo ) );
  }

}
$query_Order = "SELECT p.cart_id, p.payment_date, ct.status, ci.item_id, ci.qnty, ci.amount AS item_amount, i.image_1, i.item_name 
FROM payment p 
INNER JOIN cart ct ON ct.cart_id = p.cart_id 
INNER JOIN cart_item ci ON ci.cart_id = p.cart_id 
INNER JOIN item i ON i.item_id = ci.item_id 
WHERE p.user_id = '{$row_Recordset2['user_id']}' 
ORDER BY p.payment_date DESC";
$Order = mysqli_query($orek, $query_Order) or die(mysqli_error($orek));

$groupedOrders = [];

while ($row = mysqli_fetch_assoc($Order)) {
    $cartId = $row['cart_id'];
    
    // Initialize the order data only once per cart_id
    if (!isset($groupedOrders[$cartId])) {
        $groupedOrders[$cartId]['payment_date'] = $row['payment_date'];
        $groupedOrders[$cartId]['status'] = $row['status'];
        $groupedOrders[$cartId]['items'] = [];
    }
    
    // Add this item to the items array with item_id as key to prevent duplicates
    $itemId = $row['item_id'];
    if (!isset($groupedOrders[$cartId]['items'][$itemId])) {
        $groupedOrders[$cartId]['items'][$itemId] = $row;
    }
}

// Convert associative items arrays to indexed arrays
foreach ($groupedOrders as $cartId => $order) {
    $groupedOrders[$cartId]['items'] = array_values($groupedOrders[$cartId]['items']);
}

$totalRows_Order = count($groupedOrders); 

   // Insert Form
 if ( ( isset( $_POST[ "MM_insert" ] ) ) && ( $_POST[ "MM_insert" ] == "form" ) ) {

    $user_id = $row_Recordset2['user_id'];
    
      $insertSQL = sprintf( "INSERT INTO `review`(`item_id`, `user_id`, `star`, `review`) VALUES (%s, %s, %s, %s)",
            GetSQLValueString( $_POST[ 'item_id' ], "text" ),
            GetSQLValueString( $user_id, "text" ),
            GetSQLValueString( $_POST[ 'star' ], "text" ),
            GetSQLValueString( $_POST[ 'review' ], "text" ) );
           
        $Result = mysqli_query( $orek, $insertSQL )or die( mysqli_error( $orek ) );

        $insertGoTo = "my-account.php?success=Added";
        if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
            $insertGoTo .= ( strpos( $insertGoTo, '?' ) ) ? "&" : "?";
            $insertGoTo .= $_SERVER[ 'QUERY_STRING' ];
        }
        header( sprintf( "Location: %s", $insertGoTo ) );
    }

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form4")) {
    $insertSQL = sprintf("INSERT INTO user_shipping (user_id, recipient_name, address_name, address, city, state, pin_code, phone) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString($row_Recordset2['user_id'], "text"),
        GetSQLValueString($_POST['recipient_name'], "text"),
        GetSQLValueString($_POST['address_name'], "text"),
        GetSQLValueString($_POST['address'], "text"),
        GetSQLValueString($_POST['city'], "text"),
        GetSQLValueString($_POST['state'], "text"),
        GetSQLValueString($_POST['pin_code'], "text"),
        GetSQLValueString($_POST['phone'], "text"));

   $Result = mysqli_query( $orek, $insertSQL )or die( mysqli_error( $orek ) );

        $insertGoTo = "my-account.php?success=Address Added";
        if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
            $insertGoTo .= ( strpos( $insertGoTo, '?' ) ) ? "&" : "?";
            $insertGoTo .= $_SERVER[ 'QUERY_STRING' ];
        }
        header( sprintf( "Location: %s", $insertGoTo ) );
    }

?>

<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>Orek - My Account</title>
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
                                    <li class="breadcrumb-item active" aria-current="page">my-account</li>
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
                            <!-- My Account Page Start -->
                            <div class="myaccount-page-wrapper">
                                <!-- My Account Tab Menu Start -->
                                <div class="row">
                                    <div class="col-lg-3 col-md-4">
                                        <div class="myaccount-tab-menu nav" role="tablist">
                                            <a href="#dashboad"
                                                class="<?php echo (!isset($_GET['tab']) || $_GET['tab'] == 'dashboard') ? 'active' : ''; ?>"
                                                data-bs-toggle="tab"><i class="fa fa-dashboard"></i>
                                                Dashboard</a>
                                            <a href="#orders"
                                                class="<?php echo (isset($_GET['tab']) && $_GET['tab'] == 'orders') ? 'active' : ''; ?>"
                                                data-bs-toggle="tab"><i class="fa fa-cart-arrow-down"></i>
                                                Orders</a>
                                            <a href="#tickets"
                                                class="<?php echo (isset($_GET['tab']) && $_GET['tab'] == 'tickets') ? 'active' : ''; ?>"
                                                data-bs-toggle="tab"><i class="fa fa-ticket"></i>
                                                Support Tickets</a>
                                            <a href="#address-edit"
                                                class="<?php echo (isset($_GET['tab']) && $_GET['tab'] == 'address') ? 'active' : ''; ?>"
                                                data-bs-toggle="tab"><i class="fa fa-map-marker"></i>
                                                address</a>
                                            <a href="#account-info"
                                                class="<?php echo (isset($_GET['tab']) && $_GET['tab'] == 'account') ? 'active' : ''; ?>"
                                                data-bs-toggle="tab"><i class="fa fa-user"></i>
                                                Account
                                                Details</a>
                                            <a href="logout.php"><i class="fa fa-sign-out"></i> Logout</a>
                                        </div>
                                    </div>
                                    <!-- My Account Tab Menu End -->

                                    <!-- My Account Tab Content Start -->
                                    <div class="col-lg-9 col-md-8">
                                        <div class="tab-content" id="myaccountContent">
                                            <!-- Single Tab Content Start -->
                                            <div class="tab-pane fade show active" id="dashboad" role="tabpanel">
                                                <div class="myaccount-content">
                                                    <h5>Dashboard</h5>
                                                    <div class="welcome">
                                                        <p>Hello,
                                                            <strong><?php echo $row_Recordset2['fname']; ?></strong> (If
                                                            Not
                                                            <strong><?php echo $row_Recordset2['fname']; ?>
                                                                !</strong><a href="logout.php" class="logout">
                                                                Logout</a>)
                                                        </p>
                                                    </div>
                                                    <p class="mb-0">From your account dashboard. you can easily check &
                                                        view your recent orders, manage your shipping and billing
                                                        addresses
                                                        and edit your password and account details.</p>
                                                </div>
                                            </div>
                                            <!-- Single Tab Content End -->

                                            <!-- Single Tab Content Start -->
                                            <div class="tab-pane fade" id="orders" role="tabpanel">
                                                <div class="myaccount-content">
                                                    <h5>Orders</h5>
                                                    <div class="orders-wrapper">
                                                        <?php 
                                                        if($totalRows_Order > 0) {
                                                            // Pagination settings
                                                            $items_per_page = 10; // Show 10 orders per page
                                                            $total_pages = ceil(count($groupedOrders) / $items_per_page);
                                                            $current_page = isset($_GET['page']) ? $_GET['page'] : 1;
                                                            
                                                            // Get the slice of orders for current page
                                                            $start = ($current_page - 1) * $items_per_page;
                                                            $paged_orders = array_slice($groupedOrders, $start, $items_per_page, true);
                                                            
                                                            foreach ($paged_orders as $cart_id => $order) {
                                                        ?>
                                                        <div class="order-card">
                                                            <div class="order-header">
                                                                <div class="order-id"><a
                                                                        href="order-details.php?cart_id=<?php echo $cart_id; ?>">Order
                                                                        #<?php echo $cart_id; ?></a>
                                                                </div>
                                                                <div class="order-date">Ordered on:
                                                                    <?php echo date('d M Y', strtotime($order['payment_date'])); ?>
                                                                </div>
                                                                <div
                                                                    class="order-status <?php echo strtolower($order['status']); ?>">
                                                                    <?php echo $order['status']; ?>
                                                                </div>
                                                            </div>

                                                            <?php 
$items = $order['items'];
$last_index = count($items) - 1;
foreach ($items as $index => $item): 
?>
                                                            <div class="order-body">
                                                                <div class="order-image">
                                                                    <img src="assets/img/item/<?php echo $item['image_1']; ?>"
                                                                        alt="<?php echo $item['item_name']; ?>">
                                                                </div>
                                                                <div class="order-details">
                                                                    <h6 class="product-name">
                                                                        <?php echo $item['item_name']; ?>
                                                                    </h6>
                                                                    <div class="order-info">
                                                                        <span class="quantity">Quantity:
                                                                            <?php echo $item['qnty']; ?></span>
                                                                        <div class="order-total">
                                                                            ₹<?php echo $item['item_amount']; ?></div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <?php if ($index === $last_index): ?>
                                                            <div class="order-footer" style="margin: 10px; 10px;">
                                                                <a href="order-details.php?cart_id=<?php echo $cart_id; ?>"
                                                                    class="btn btn-sm">
                                                                    View Order
                                                                </a>
                                                            </div>
                                                            <?php endif; ?>
                                                            <?php endforeach; ?>

                                                        </div>

                                                        <?php 
                                                            }
                                                            // Pagination links
                                                             if($total_pages > 1) { 
                                                        ?>
                                                        <div class="pagination-wrapper">
                                                            <ul class="pagination">
                                                                <?php for($i = 1; $i <= $total_pages; $i++) { ?>
                                                                <li
                                                                    class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                                                                    <a class="page-link"
                                                                        href="?page=<?php echo $i; ?>&tab=orders#orders"><?php echo $i; ?></a>
                                                                </li>
                                                                <?php } ?>
                                                            </ul>
                                                        </div>
                                                        <?php 
                                                            }
                                                        } else { 
                                                        ?>
                                                        <div class="no-orders">
                                                            <i class="fa fa-shopping-bag"></i>
                                                            <p>No orders found</p>
                                                        </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Single Tab Content End -->
                                            <!-- Tickets Tab Content Start -->
                                            <div class="tab-pane fade" id="tickets" role="tabpanel">
                                                <div class="myaccount-content">
                                                    <h5>My Support Tickets</h5>
                                                    <button class="btn btn-sqr mb-3" data-bs-toggle="modal"
                                                        data-bs-target="#newTicketModal">
                                                        <i class="fa fa-plus"></i> New Ticket
                                                    </button>

                                                    <div class="tickets-list">
                                                        <?php
                                                        $query_Tickets = "SELECT * FROM tickets 
                                                                        WHERE user_id = '{$row_Recordset2['user_id']}' 
                                                                        ORDER BY created_at DESC";
                                                        $Tickets = mysqli_query($orek, $query_Tickets) or die(mysqli_error($orek));

                                                        while($row_Ticket = mysqli_fetch_assoc($Tickets)) {
                                                            $ticket_id = $row_Ticket['ticket_id'];
                                                        ?>
                                                        <div class="ticket-card"
                                                            style="margin-bottom: 30px; border: 1px solid #ddd; border-radius: 8px; padding: 15px;">
                                                            <div class="ticket-header">
                                                                <h6>
                                                                    Ticket
                                                                    #<?php echo str_pad($ticket_id, 4, '0', STR_PAD_LEFT); ?>
                                                                    -
                                                                    <?php echo htmlspecialchars($row_Ticket['subject']); ?>
                                                                </h6>
                                                                <span
                                                                    class="badge <?php echo $row_Ticket['status']; ?>">
                                                                    <?php echo ucfirst($row_Ticket['status']); ?>
                                                                </span>
                                                            </div>

                                                            <!-- Conversation Container -->
                                                            <div class="chat-box"
                                                                style="max-height: 300px; overflow-y: auto; margin-top: 15px; background: #f9f9f9; padding: 10px; border-radius: 6px;">

                                                                <!-- Original Ticket Message (User) -->
                                                                <div class="chat-bubble user"
                                                                    style="display: flex; justify-content: flex-end; margin-bottom: 10px;">
                                                                    <div
                                                                        style="background: #dcf8c6; padding: 10px 15px; border-radius: 15px 15px 0 15px; max-width: 75%;">
                                                                        <?php echo nl2br(htmlspecialchars($row_Ticket['message'])); ?>
                                                                        <div style="text-align: right;">
                                                                            <small><?php echo date('d M Y H:i', strtotime($row_Ticket['created_at'])); ?></small>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Replies -->
                                                                <?php
                                                                $query_Replies = "SELECT * FROM ticket_replies WHERE ticket_id = '$ticket_id' ORDER BY created_at ASC";
                                                                $Replies = mysqli_query($orek, $query_Replies) or die(mysqli_error($orek));
                                                                while($row_Reply = mysqli_fetch_assoc($Replies)) {
                                                                    $is_admin = $row_Reply['is_admin'] ?? 0;
                                                                    $is_user = !$is_admin;

                                                                    // Set styles
                                                                    $justify = $is_admin ? 'flex-start' : 'flex-end';
                                                                    $bg_color = $is_admin ? '#e2e2e2' : '#dcf8c6';
                                                                    $border_radius = $is_admin ? '15px 15px 15px 0' : '15px 15px 0 15px';
                                                                    $text_align = $is_admin ? 'left' : 'right';
                                                                ?>
                                                                <div class="chat-bubble <?php echo $is_admin ? 'admin' : 'user'; ?>"
                                                                    style="display: flex; justify-content: <?php echo $justify; ?>; margin-bottom: 10px;">
                                                                    <div
                                                                        style="background: <?php echo $bg_color; ?>; padding: 10px 15px; border-radius: <?php echo $border_radius; ?>; max-width: 75%;">
                                                                        <?php echo nl2br(htmlspecialchars($row_Reply['message'])); ?>
                                                                        <div
                                                                            style="text-align: <?php echo $text_align; ?>;">
                                                                            <small><?php echo date('d M Y h:i A', strtotime($row_Reply['created_at'])); ?></small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <?php } ?>
                                                            </div>
                                                            <!-- Message Send Form -->
                                                            <form action="submit-ticket.php" method="post"
                                                                style="margin-top: 10px; display: flex; gap: 10px;">
                                                                <input type="hidden" name="ticket_id"
                                                                    value="<?php echo $ticket_id; ?>">
                                                                <input type="text" name="message"
                                                                    placeholder="Type your message..." required
                                                                    style="flex: 1; padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
                                                                <button type="submit"
                                                                    style="padding: 10px 20px; background: #4CAF50; color: white; border: none; border-radius: 5px;">Send</button>
                                                            </form>
                                                        </div>
                                                        <?php } ?>
                                                    </div>


                                                </div>
                                            </div>
                                            <!-- Tickets Tab Content End -->
                                            <!-- New Ticket Modal -->
                                            <div class="modal fade" id="newTicketModal" tabindex="-1"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Create New Support Ticket</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form action="submit-ticket.php" method="post">
                                                                <div class="single-input-item">
                                                                    <label class="required">Subject</label>
                                                                    <input type="text" name="subject" required>
                                                                </div>
                                                                <div class="single-input-item">
                                                                    <label class="required">Message</label>
                                                                    <textarea name="message" rows="5"
                                                                        required></textarea>
                                                                </div>
                                                                <div class="single-input-item">
                                                                    <button type="submit" class="btn btn-sqr">Submit
                                                                        Ticket</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Single Tab Content Start -->
                                            <div class="tab-pane fade" id="address-edit" role="tabpanel">
                                                <div class="myaccount-content">
                                                    <h5>Shipping Address</h5>
                                                    <button class="btn btn-sqr mb-3" data-bs-toggle="modal"
                                                        data-bs-target="#addAddressModal" style="float:right;">
                                                        <i class="fa fa-add"></i> Add Address
                                                    </button>
                                                    <?php 
                                                    if($totalRows_Recordset4 > 0 ) {
                                                        $i = 1;
                                                        do {
                                                        ?>
                                                    <h6
                                                        style="color:rgb(29, 26, 19); font-size: 18px; margin-bottom:5px; border-bottom: 2px solid #333333; display: inline-block;">
                                                        Address <?php echo $i; ?></h6>
                                                    <address>
                                                        <p><strong><?php echo $row_Recordset4['recipient_name']; ?></strong>
                                                            <button class="btn p-0 ms-2"
                                                                onclick="editAddress(<?php echo $row_Recordset4['shipping_id']; ?>)"
                                                                style="transition: all 0.3s ease;">
                                                                <i class="fa fa-edit"
                                                                    style="color: #ecb0a3; font-size: 18px;"></i>
                                                            </button>
                                                        </p>
                                                        <p>Address Type:
                                                            <?php echo $row_Recordset4['address_name']; ?>
                                                        </p>
                                                        <p><?php echo $row_Recordset4['address']; ?> <br>
                                                            <?php echo $row_Recordset4['city']; ?>,
                                                            <?php echo $row_Recordset4['state']; ?>
                                                            <?php echo $row_Recordset4['pin_code']; ?></p>
                                                        <p>Mobile: (+91) <?php echo $row_Recordset4['phone']; ?></p>
                                                    </address>
                                                    <?php 
                                                    $i ++;
                                                    }while($row_Recordset4=mysqli_fetch_assoc($Recordset4)); } ?>
                                                </div>
                                            </div>

                                            <!-- Edit Address Modal -->
                                            <div class="modal fade" id="editAddressModal" tabindex="-1"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Address</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form
                                                                action="<?php echo $editFormAction; ?>?tab=address-edit#address-edit"
                                                                method="post" name="form3">
                                                                <div class="single-input-item">
                                                                    <label class="required">Recipient Name</label>
                                                                    <input type="text" name="recipient_name"
                                                                        value="<?php echo $row_Recordset5['recipient_name']; ?>"
                                                                        required>
                                                                </div>
                                                                <div class="single-input-item">
                                                                    <label>Address Type</label>
                                                                    <input type="text" name="address_name"
                                                                        value="<?php echo $row_Recordset5['address_name']; ?>">
                                                                </div>
                                                                <div class="single-input-item">
                                                                    <label class="required">Address</label>
                                                                    <textarea name="address"
                                                                        required><?php echo $row_Recordset5['address']; ?></textarea>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-lg-6">
                                                                        <div class="single-input-item">
                                                                            <label class="required">City</label>
                                                                            <input type="text" name="city"
                                                                                value="<?php echo $row_Recordset5['city']; ?>"
                                                                                required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-6">
                                                                        <div class="single-input-item">
                                                                            <label for="state"
                                                                                class="required">State</label>
                                                                            <select name="state" id="state"
                                                                                class="nice-select" required>
                                                                                <option value="">Select State</option>
                                                                                <option value="Andhra Pradesh"
                                                                                    <?php echo ($row_Recordset5['state'] == 'Andhra Pradesh') ? 'selected' : ''; ?>>
                                                                                    Andhra Pradesh</option>
                                                                                <option value="Arunachal Pradesh"
                                                                                    <?php echo ($row_Recordset5['state'] == 'Arunachal Pradesh') ? 'selected' : ''; ?>>
                                                                                    Arunachal Pradesh</option>
                                                                                <option value="Assam"
                                                                                    <?php echo ($row_Recordset5['state'] == 'Assam') ? 'selected' : ''; ?>>
                                                                                    Assam</option>
                                                                                <option value="Bihar"
                                                                                    <?php echo ($row_Recordset5['state'] == 'Bihar') ? 'selected' : ''; ?>>
                                                                                    Bihar</option>
                                                                                <option value="Chhattisgarh"
                                                                                    <?php echo ($row_Recordset5['state'] == 'Chhattisgarh') ? 'selected' : ''; ?>>
                                                                                    Chhattisgarh</option>
                                                                                <option value="Goa"
                                                                                    <?php echo ($row_Recordset5['state'] == 'Goa') ? 'selected' : ''; ?>>
                                                                                    Goa</option>
                                                                                <option value="Gujarat"
                                                                                    <?php echo ($row_Recordset5['state'] == 'Gujarat') ? 'selected' : ''; ?>>
                                                                                    Gujarat</option>
                                                                                <option value="Haryana"
                                                                                    <?php echo ($row_Recordset5['state'] == 'Haryana') ? 'selected' : ''; ?>>
                                                                                    Haryana</option>
                                                                                <option value="Himachal Pradesh"
                                                                                    <?php echo ($row_Recordset5['state'] == 'Himachal Pradesh') ? 'selected' : ''; ?>>
                                                                                    Himachal Pradesh</option>
                                                                                <option value="Jharkhand"
                                                                                    <?php echo ($row_Recordset5['state'] == 'Jharkhand') ? 'selected' : ''; ?>>
                                                                                    Jharkhand</option>
                                                                                <option value="Karnataka"
                                                                                    <?php echo ($row_Recordset5['state'] == 'Karnataka') ? 'selected' : ''; ?>>
                                                                                    Karnataka</option>
                                                                                <option value="Kerala"
                                                                                    <?php echo ($row_Recordset5['state'] == 'Kerala') ? 'selected' : ''; ?>>
                                                                                    Kerala</option>
                                                                                <option value="Madhya Pradesh"
                                                                                    <?php echo ($row_Recordset5['state'] == 'Madhya Pradesh') ? 'selected' : ''; ?>>
                                                                                    Madhya Pradesh</option>
                                                                                <option value="Maharashtra"
                                                                                    <?php echo ($row_Recordset5['state'] == 'Maharashtra') ? 'selected' : ''; ?>>
                                                                                    Maharashtra</option>
                                                                                <option value="Manipur"
                                                                                    <?php echo ($row_Recordset5['state'] == 'Manipur') ? 'selected' : ''; ?>>
                                                                                    Manipur</option>
                                                                                <option value="Meghalaya"
                                                                                    <?php echo ($row_Recordset5['state'] == 'Meghalaya') ? 'selected' : ''; ?>>
                                                                                    Meghalaya</option>
                                                                                <option value="Mizoram"
                                                                                    <?php echo ($row_Recordset5['state'] == 'Mizoram') ? 'selected' : ''; ?>>
                                                                                    Mizoram</option>
                                                                                <option value="Nagaland"
                                                                                    <?php echo ($row_Recordset5['state'] == 'Nagaland') ? 'selected' : ''; ?>>
                                                                                    Nagaland</option>
                                                                                <option value="Odisha"
                                                                                    <?php echo ($row_Recordset5['state'] == 'Odisha') ? 'selected' : ''; ?>>
                                                                                    Odisha</option>
                                                                                <option value="Punjab"
                                                                                    <?php echo ($row_Recordset5['state'] == 'Punjab') ? 'selected' : ''; ?>>
                                                                                    Punjab</option>
                                                                                <option value="Rajasthan"
                                                                                    <?php echo ($row_Recordset5['state'] == 'Rajasthan') ? 'selected' : ''; ?>>
                                                                                    Rajasthan</option>
                                                                                <option value="Sikkim"
                                                                                    <?php echo ($row_Recordset5['state'] == 'Sikkim') ? 'selected' : ''; ?>>
                                                                                    Sikkim</option>
                                                                                <option value="Tamil Nadu"
                                                                                    <?php echo ($row_Recordset5['state'] == 'Tamil Nadu') ? 'selected' : ''; ?>>
                                                                                    Tamil Nadu</option>
                                                                                <option value="Telangana"
                                                                                    <?php echo ($row_Recordset5['state'] == 'Telangana') ? 'selected' : ''; ?>>
                                                                                    Telangana</option>
                                                                                <option value="Tripura"
                                                                                    <?php echo ($row_Recordset5['state'] == 'Tripura') ? 'selected' : ''; ?>>
                                                                                    Tripura</option>
                                                                                <option value="Uttar Pradesh"
                                                                                    <?php echo ($row_Recordset5['state'] == 'Uttar Pradesh') ? 'selected' : ''; ?>>
                                                                                    Uttar Pradesh</option>
                                                                                <option value="Uttarakhand"
                                                                                    <?php echo ($row_Recordset5['state'] == 'Uttarakhand') ? 'selected' : ''; ?>>
                                                                                    Uttarakhand</option>
                                                                                <option value="West Bengal"
                                                                                    <?php echo ($row_Recordset5['state'] == 'West Bengal') ? 'selected' : ''; ?>>
                                                                                    West Bengal</option>
                                                                                <option
                                                                                    value="Andaman and Nicobar Islands"
                                                                                    <?php echo ($row_Recordset5['state'] == 'Andaman and Nicobar Islands') ? 'selected' : ''; ?>>
                                                                                    Andaman and Nicobar Islands</option>
                                                                                <option value="Chandigarh"
                                                                                    <?php echo ($row_Recordset5['state'] == 'Chandigarh') ? 'selected' : ''; ?>>
                                                                                    Chandigarh</option>
                                                                                <option
                                                                                    value="Dadra and Nagar Haveli and Daman and Diu"
                                                                                    <?php echo ($row_Recordset5['state'] == 'Dadra and Nagar Haveli and Daman and Diu') ? 'selected' : ''; ?>>
                                                                                    Dadra and Nagar Haveli and Daman and
                                                                                    Diu</option>
                                                                                <option value="Delhi"
                                                                                    <?php echo ($row_Recordset5['state'] == 'Delhi') ? 'selected' : ''; ?>>
                                                                                    Delhi</option>
                                                                                <option value="Jammu and Kashmir"
                                                                                    <?php echo ($row_Recordset5['state'] == 'Jammu and Kashmir') ? 'selected' : ''; ?>>
                                                                                    Jammu and Kashmir</option>
                                                                                <option value="Ladakh"
                                                                                    <?php echo ($row_Recordset5['state'] == 'Ladakh') ? 'selected' : ''; ?>>
                                                                                    Ladakh</option>
                                                                                <option value="Lakshadweep"
                                                                                    <?php echo ($row_Recordset5['state'] == 'Lakshadweep') ? 'selected' : ''; ?>>
                                                                                    Lakshadweep</option>
                                                                                <option value="Puducherry"
                                                                                    <?php echo ($row_Recordset5['state'] == 'Puducherry') ? 'selected' : ''; ?>>
                                                                                    Puducherry</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-lg-6">
                                                                        <div class="single-input-item">
                                                                            <label class="required">PIN Code</label>
                                                                            <input type="text" name="pin_code"
                                                                                value="<?php echo $row_Recordset5['pin_code']; ?>"
                                                                                required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-6">
                                                                        <div class="single-input-item">
                                                                            <label>Phone</label>
                                                                            <input type="text" name="phone"
                                                                                value="<?php echo $row_Recordset5['phone']; ?>">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <input type="hidden" name="MM_update" value="form3">
                                                                <input type="hidden" name="shipping_id"
                                                                    value="<?php echo $row_Recordset5['shipping_id']; ?>">
                                                                <button type="submit" class="btn btn-sqr mt-3">Update
                                                                    Address</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Add Address Modal -->
                                            <div class="modal fade" id="addAddressModal" tabindex="-1"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Add New Address</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form action="<?php echo $editFormAction; ?>" method="post"
                                                                name="form4" id="addAddressForm">
                                                                <div class="single-input-item">
                                                                    <label class="required">Recipient Name</label>
                                                                    <input type="text" name="recipient_name" required>
                                                                </div>
                                                                <div class="single-input-item">
                                                                    <label>Address Type</label>
                                                                    <input type="text" name="address_name">
                                                                </div>
                                                                <div class="single-input-item">
                                                                    <label class="required">Address</label>
                                                                    <textarea name="address" required></textarea>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-lg-6">
                                                                        <div class="single-input-item">
                                                                            <label class="required">City</label>
                                                                            <input type="text" name="city" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-6">
                                                                        <div class="single-input-item">
                                                                            <label for="state"
                                                                                class="required">State</label>
                                                                            <select name="state" id="state"
                                                                                class="nice-select" required>
                                                                                <option value="">Select State</option>
                                                                                <option value="Andhra Pradesh">Andhra
                                                                                    Pradesh</option>
                                                                                <option value="Arunachal Pradesh">
                                                                                    Arunachal Pradesh</option>
                                                                                <option value="Assam">Assam</option>
                                                                                <option value="Bihar">Bihar</option>
                                                                                <option value="Chhattisgarh">
                                                                                    Chhattisgarh</option>
                                                                                <option value="Goa">Goa</option>
                                                                                <option value="Gujarat">Gujarat</option>
                                                                                <option value="Haryana">Haryana</option>
                                                                                <option value="Himachal Pradesh">
                                                                                    Himachal Pradesh</option>
                                                                                <option value="Jharkhand">Jharkhand
                                                                                </option>
                                                                                <option value="Karnataka">Karnataka
                                                                                </option>
                                                                                <option value="Kerala">Kerala</option>
                                                                                <option value="Madhya Pradesh">Madhya
                                                                                    Pradesh</option>
                                                                                <option value="Maharashtra">Maharashtra
                                                                                </option>
                                                                                <option value="Manipur">Manipur</option>
                                                                                <option value="Meghalaya">Meghalaya
                                                                                </option>
                                                                                <option value="Mizoram">Mizoram</option>
                                                                                <option value="Nagaland">Nagaland
                                                                                </option>
                                                                                <option value="Odisha">Odisha</option>
                                                                                <option value="Punjab">Punjab</option>
                                                                                <option value="Rajasthan">Rajasthan
                                                                                </option>
                                                                                <option value="Sikkim">Sikkim</option>
                                                                                <option value="Tamil Nadu">Tamil Nadu
                                                                                </option>
                                                                                <option value="Telangana">Telangana
                                                                                </option>
                                                                                <option value="Tripura">Tripura</option>
                                                                                <option value="Uttar Pradesh">Uttar
                                                                                    Pradesh</option>
                                                                                <option value="Uttarakhand">Uttarakhand
                                                                                </option>
                                                                                <option value="West Bengal">West Bengal
                                                                                </option>
                                                                                <option
                                                                                    value="Andaman and Nicobar Islands">
                                                                                    Andaman and Nicobar
                                                                                    Islands</option>
                                                                                <option value="Chandigarh">Chandigarh
                                                                                </option>
                                                                                <option
                                                                                    value="Dadra and Nagar Haveli and Daman and Diu">
                                                                                    Dadra
                                                                                    and Nagar Haveli and Daman and Diu
                                                                                </option>
                                                                                <option value="Delhi">Delhi</option>
                                                                                <option value="Jammu and Kashmir">Jammu
                                                                                    and Kashmir</option>
                                                                                <option value="Ladakh">Ladakh</option>
                                                                                <option value="Lakshadweep">Lakshadweep
                                                                                </option>
                                                                                <option value="Puducherry">Puducherry
                                                                                </option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-lg-6">
                                                                        <div class="single-input-item">
                                                                            <label class="required">PIN Code</label>
                                                                            <input type="text" name="pin_code" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-6">
                                                                        <div class="single-input-item">
                                                                            <label>Phone</label>
                                                                            <input type="text" name="phone">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <input type="hidden" name="MM_insert" value="form4">
                                                                <input type="hidden" name="user_id"
                                                                    value="<?php echo $row_Recordset2['user_id']; ?>">
                                                                <button type="submit" class="btn btn-sqr mt-3">Add
                                                                    Address</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Single Tab Content End -->

                                            <!-- Single Tab Content Start -->
                                            <div class="tab-pane fade" id="account-info" role="tabpanel">
                                                <div class="myaccount-content">
                                                    <h5>Account Details</h5>
                                                    <div class="account-details-form">
                                                        <form
                                                            action="<?php echo $editFormAction; ?>?tab=account-info#account-info"
                                                            role="form" name="form1" method="post">
                                                            <div class="row">
                                                                <div class="col-lg-6">
                                                                    <div class="single-input-item">
                                                                        <label for="first-name" class="required">First
                                                                            Name</label>
                                                                        <input type="text" id="first-name" name="fname"
                                                                            placeholder="First Name"
                                                                            value="<?php if($flag) echo $row_Recordset2['fname'] ?>" />
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <div class="single-input-item">
                                                                        <label for="last-name" class="required">Last
                                                                            Name</label>
                                                                        <input type="text" id="last-name" name="lname"
                                                                            placeholder="Last Name"
                                                                            value="<?php if($flag) echo $row_Recordset2['lname'] ?>" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="single-input-item">
                                                                <label for="email" class="required">Email Addres</label>
                                                                <input type="email" id="email" name="email"
                                                                    placeholder="Email Address"
                                                                    value="<?php if($flag) echo $row_Recordset2['email'] ?>" />
                                                            </div>
                                                            <div class="single-input-item">
                                                                <button class="btn btn-sqr">Save Changes</button>
                                                                <input type="hidden" name="MM_update" value="form1">
                                                                <input type="hidden" name="user_id"
                                                                    value="<?php echo $row_Recordset2['user_id'] ?>">
                                                            </div>
                                                        </form>
                                                        <form name="form2" method="post"
                                                            action="<?php echo $editFormAction; ?>" role="form">
                                                            <fieldset>
                                                                <legend>Password change</legend>
                                                                <div class="single-input-item">
                                                                    <label for="current-pwd" class="required">Current
                                                                        Password <?php echo $errpass3; ?></label>
                                                                    <input type="password" id="current-pwd"
                                                                        name="old-password"
                                                                        placeholder="Current Password" />
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-lg-6">
                                                                        <div class="single-input-item">
                                                                            <label for="new-pwd" class="required">New
                                                                                Password
                                                                                <?php echo $errpass1; ?></label>
                                                                            <input type="password" id="new-pwd"
                                                                                name="password"
                                                                                placeholder="New Password" />
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-6">
                                                                        <div class="single-input-item">
                                                                            <label for="confirm-pwd"
                                                                                class="required">Confirm
                                                                                Password
                                                                                <?php echo $errpass2 . $errpass; ?></label>
                                                                            <input type="password" id="confirm-pwd"
                                                                                name="rpassword"
                                                                                placeholder="Confirm Password" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </fieldset>
                                                            <div class="single-input-item">
                                                                <button class="btn btn-sqr">Save Changes</button>
                                                                <input type="hidden" name="MM_password" value="form2">
                                                                <input type="hidden"
                                                                    value="<?php echo $row_User['user_id'] ?>"
                                                                    name="user_id">
                                                                <input type="hidden"
                                                                    value="<?php echo $_SESSION['email'] ?>"
                                                                    name="email">
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div> <!-- Single Tab Content End -->
                                        </div>
                                    </div> <!-- My Account Tab Content End -->
                                </div>
                            </div> <!-- My Account Page End -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- my account wrapper end -->
    </main>

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
    document.getElementById('addAddressModal').addEventListener('show.bs.modal', function() {
        document.getElementById('addAddressForm').reset();
        const stateSelect = document.querySelector('#addAddressForm select[name="state"]');
        if (stateSelect) {
            stateSelect.value = '';
            // Refresh nice-select if it's initialized
            if (jQuery().niceSelect) {
                $(stateSelect).niceSelect('update');
            }
        }
    });
    // Check URL parameters for active tab
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        if (tab) {
            const tabElement = document.querySelector(
                `.myaccount-tab-menu a[href="#${tab === 'orders' ? 'orders' : tab}"]`);
            if (tabElement) {
                document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('show', 'active'));
                document.querySelectorAll('.myaccount-tab-menu a').forEach(a => a.classList.remove('active'));
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
    </script>
</body>

</html>