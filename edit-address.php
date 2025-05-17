<?php require_once('Connections/orek.php'); ?>
<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
    header('Location: login.php?redirect=checkout.php');
    exit();
}

// Get user details
$query_User = "SELECT * FROM user WHERE email = '{$_SESSION['email']}'";
$User = mysqli_query($orek, $query_User) or die(mysqli_error($orek));
$row_User = mysqli_fetch_assoc($User);
$totalRows_User = mysqli_num_rows($User);

// Check if address ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: checkout.php?step=1');
    exit();
}

$address_id = $_GET['id'];

// Get address details
$query_Address = "SELECT * FROM user_shipping WHERE shipping_id = '$address_id' AND user_id = '{$row_User['user_id']}'";
$Address = mysqli_query($orek, $query_Address) or die(mysqli_error($orek));
$row_Address = mysqli_fetch_assoc($Address);
$totalRows_Address = mysqli_num_rows($Address);

// Check if address belongs to the user
if ($totalRows_Address == 0) {
    header('Location: checkout.php?step=1');
    exit();
}

$message = "";
$success = false;

// Handle form submission
if (isset($_POST['update_address'])) {
    $address_name = $_POST['address_name'];
    $recipient_name = $_POST['recipient_name'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $pin_code = $_POST['pin_code'];
    $phone = $_POST['phone'];
    $is_default = isset($_POST['is_default']) ? 1 : 0;
    
    // If is_default is checked, unset all other default addresses
    if ($is_default) {
        $update_defaults = "UPDATE user_shipping SET is_default = 0 WHERE user_id = '{$row_User['user_id']}'";
        mysqli_query($orek, $update_defaults);
    }
    
    // Update address
    $updateSQL = sprintf("UPDATE user_shipping SET address_name = %s, recipient_name = %s, address = %s, city = %s, state = %s, pin_code = %s, phone = %s, is_default = %s WHERE shipping_id = %s AND user_id = %s",
        GetSQLValueString($address_name, "text"),
        GetSQLValueString($recipient_name, "text"),
        GetSQLValueString($address, "text"),
        GetSQLValueString($city, "text"),
        GetSQLValueString($state, "text"),
        GetSQLValueString($pin_code, "text"),
        GetSQLValueString($phone, "text"),
        GetSQLValueString($is_default, "int"),
        GetSQLValueString($address_id, "int"),
        GetSQLValueString($row_User['user_id'], "int"));
    
    $Result1 = mysqli_query($orek, $updateSQL) or die(mysqli_error($orek));
    
    $success = true;
    $message = "Address updated successfully!";
    
    // Refresh address details
    $Address = mysqli_query($orek, $query_Address) or die(mysqli_error($orek));
    $row_Address = mysqli_fetch_assoc($Address);
}
?>
<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Orek - Edit Address</title>
    <meta name="robots" content="noindex, follow" />
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
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
                                    <li class="breadcrumb-item"><a href="checkout.php">checkout</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">edit address</li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- breadcrumb area end -->

        <!-- edit address page content start -->
        <div class="edit-address-page-wrapper section-padding">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <div class="edit-address-wrap">
                            <h5 class="checkout-title">Edit Shipping Address</h5>

                            <?php if ($message): ?>
                            <div class="alert <?php echo $success ? 'alert-success' : 'alert-danger'; ?>">
                                <?php echo $message; ?></div>
                            <?php endif; ?>

                            <div class="billing-form-wrap">
                                <form action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . $address_id; ?>" method="post"
                                    id="address-form">
                                    <div class="single-input-item">
                                        <label for="address_name" class="required">Address Name</label>
                                        <input type="text" id="address_name" name="address_name"
                                            placeholder="Home, Office, etc."
                                            value="<?php echo $row_Address['address_name']; ?>" required />
                                    </div>

                                    <div class="single-input-item">
                                        <label for="recipient_name" class="required">Recipient Name</label>
                                        <input type="text" id="recipient_name" name="recipient_name"
                                            placeholder="Recipient Name"
                                            value="<?php echo $row_Address['recipient_name']; ?>" required />
                                    </div>

                                    <div class="single-input-item">
                                        <label for="address" class="required">Address</label>
                                        <textarea name="address" id="address" rows="4"
                                            placeholder="Enter your full address"
                                            required><?php echo $row_Address['address']; ?></textarea>
                                    </div>

                                    <div class="single-input-item">
                                        <label for="city" class="required">City</label>
                                        <input type="text" id="city" name="city" placeholder="City"
                                            value="<?php echo $row_Address['city']; ?>" required />
                                    </div>

                                    <div class="single-input-item">
                                        <label for="state" class="required">State</label>
                                        <select name="state" id="state" class="nice-select" required>
                                            <option value="">Select State</option>
                                            <option value="Andhra Pradesh"
                                                <?php echo ($row_Address['state'] == 'Andhra Pradesh') ? 'selected' : ''; ?>>
                                                Andhra Pradesh</option>
                                            <option value="Arunachal Pradesh"
                                                <?php echo ($row_Address['state'] == 'Arunachal Pradesh') ? 'selected' : ''; ?>>
                                                Arunachal Pradesh</option>
                                            <option value="Assam"
                                                <?php echo ($row_Address['state'] == 'Assam') ? 'selected' : ''; ?>>
                                                Assam</option>
                                            <option value="Bihar"
                                                <?php echo ($row_Address['state'] == 'Bihar') ? 'selected' : ''; ?>>
                                                Bihar</option>
                                            <option value="Chhattisgarh"
                                                <?php echo ($row_Address['state'] == 'Chhattisgarh') ? 'selected' : ''; ?>>
                                                Chhattisgarh</option>
                                            <option value="Goa"
                                                <?php echo ($row_Address['state'] == 'Goa') ? 'selected' : ''; ?>>Goa
                                            </option>
                                            <option value="Gujarat"
                                                <?php echo ($row_Address['state'] == 'Gujarat') ? 'selected' : ''; ?>>
                                                Gujarat</option>
                                            <option value="Haryana"
                                                <?php echo ($row_Address['state'] == 'Haryana') ? 'selected' : ''; ?>>
                                                Haryana</option>
                                            <option value="Himachal Pradesh"
                                                <?php echo ($row_Address['state'] == 'Himachal Pradesh') ? 'selected' : ''; ?>>
                                                Himachal Pradesh</option>
                                            <option value="Jharkhand"
                                                <?php echo ($row_Address['state'] == 'Jharkhand') ? 'selected' : ''; ?>>
                                                Jharkhand</option>
                                            <option value="Karnataka"
                                                <?php echo ($row_Address['state'] == 'Karnataka') ? 'selected' : ''; ?>>
                                                Karnataka</option>
                                            <option value="Kerala"
                                                <?php echo ($row_Address['state'] == 'Kerala') ? 'selected' : ''; ?>>
                                                Kerala</option>
                                            <option value="Madhya Pradesh"
                                                <?php echo ($row_Address['state'] == 'Madhya Pradesh') ? 'selected' : ''; ?>>
                                                Madhya Pradesh</option>
                                            <option value="Maharashtra"
                                                <?php echo ($row_Address['state'] == 'Maharashtra') ? 'selected' : ''; ?>>
                                                Maharashtra</option>
                                            <option value="Manipur"
                                                <?php echo ($row_Address['state'] == 'Manipur') ? 'selected' : ''; ?>>
                                                Manipur</option>
                                            <option value="Meghalaya"
                                                <?php echo ($row_Address['state'] == 'Meghalaya') ? 'selected' : ''; ?>>
                                                Meghalaya</option>
                                            <option value="Mizoram"
                                                <?php echo ($row_Address['state'] == 'Mizoram') ? 'selected' : ''; ?>>
                                                Mizoram</option>
                                            <option value="Nagaland"
                                                <?php echo ($row_Address['state'] == 'Nagaland') ? 'selected' : ''; ?>>
                                                Nagaland</option>
                                            <option value="Odisha"
                                                <?php echo ($row_Address['state'] == 'Odisha') ? 'selected' : ''; ?>>
                                                Odisha</option>
                                            <option value="Punjab"
                                                <?php echo ($row_Address['state'] == 'Punjab') ? 'selected' : ''; ?>>
                                                Punjab</option>
                                            <option value="Rajasthan"
                                                <?php echo ($row_Address['state'] == 'Rajasthan') ? 'selected' : ''; ?>>
                                                Rajasthan</option>
                                            <option value="Sikkim"
                                                <?php echo ($row_Address['state'] == 'Sikkim') ? 'selected' : ''; ?>>
                                                Sikkim</option>
                                            <option value="Tamil Nadu"
                                                <?php echo ($row_Address['state'] == 'Tamil Nadu') ? 'selected' : ''; ?>>
                                                Tamil Nadu</option>
                                            <option value="Telangana"
                                                <?php echo ($row_Address['state'] == 'Telangana') ? 'selected' : ''; ?>>
                                                Telangana</option>
                                            <option value="Tripura"
                                                <?php echo ($row_Address['state'] == 'Tripura') ? 'selected' : ''; ?>>
                                                Tripura</option>
                                            <option value="Uttar Pradesh"
                                                <?php echo ($row_Address['state'] == 'Uttar Pradesh') ? 'selected' : ''; ?>>
                                                Uttar Pradesh</option>
                                            <option value="Uttarakhand"
                                                <?php echo ($row_Address['state'] == 'Uttarakhand') ? 'selected' : ''; ?>>
                                                Uttarakhand</option>
                                            <option value="West Bengal"
                                                <?php echo ($row_Address['state'] == 'West Bengal') ? 'selected' : ''; ?>>
                                                West Bengal</option>
                                            <option value="Andaman and Nicobar Islands"
                                                <?php echo ($row_Address['state'] == 'Andaman and Nicobar Islands') ? 'selected' : ''; ?>>
                                                Andaman and Nicobar Islands</option>
                                            <option value="Chandigarh"
                                                <?php echo ($row_Address['state'] == 'Chandigarh') ? 'selected' : ''; ?>>
                                                Chandigarh</option>
                                            <option value="Dadra and Nagar Haveli and Daman and Diu"
                                                <?php echo ($row_Address['state'] == 'Dadra and Nagar Haveli and Daman and Diu') ? 'selected' : ''; ?>>
                                                Dadra and Nagar Haveli and Daman and Diu</option>
                                            <option value="Delhi"
                                                <?php echo ($row_Address['state'] == 'Delhi') ? 'selected' : ''; ?>>
                                                Delhi</option>
                                            <option value="Jammu and Kashmir"
                                                <?php echo ($row_Address['state'] == 'Jammu and Kashmir') ? 'selected' : ''; ?>>
                                                Jammu and Kashmir</option>
                                            <option value="Ladakh"
                                                <?php echo ($row_Address['state'] == 'Ladakh') ? 'selected' : ''; ?>>
                                                Ladakh</option>
                                            <option value="Lakshadweep"
                                                <?php echo ($row_Address['state'] == 'Lakshadweep') ? 'selected' : ''; ?>>
                                                Lakshadweep</option>
                                            <option value="Puducherry"
                                                <?php echo ($row_Address['state'] == 'Puducherry') ? 'selected' : ''; ?>>
                                                Puducherry</option>
                                        </select>
                                    </div>

                                    <div class="single-input-item">
                                        <label for="pin_code" class="required">PIN Code</label>
                                        <input type="text" id="pin_code" name="pin_code" placeholder="PIN Code"
                                            value="<?php echo $row_Address['pin_code']; ?>" required />
                                    </div>

                                    <div class="single-input-item">
                                        <label for="phone" class="required">Phone</label>
                                        <input type="text" id="phone" name="phone" placeholder="Phone"
                                            value="<?php echo $row_Address['phone']; ?>" required />
                                    </div>

                                    <div class="single-input-item">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="is_default"
                                                name="is_default" value="1">
                                            <label class="custom-control-label" for="is_default">Set as default shipping
                                                address</label>
                                        </div>
                                    </div>

                                    <div class="single-input-item">
                                        <button type="submit" name="update_address" class="btn btn-sqr btn-block">Update
                                            Address</button>
                                        <a href="checkout.php?step=1" class="btn btn-sqr btn-block">Cancel</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- edit address page content end -->
    </main>

    <!-- Scroll to top start -->
    <div class="scroll-top not-visible">
        <i class="fa fa-angle-up"></i>
    </div>
    <!-- Scroll to Top End -->

    <!-- footer area start -->
    <?php require_once('footer.php'); ?>
    <!-- footer area end -->

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
    <!-- Main JS -->
    <script src="assets/js/main.js"></script>
</body>

</html>