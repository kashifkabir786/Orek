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

// Check if address belongs to the user
$query_Address = "SELECT * FROM user_shipping WHERE shipping_id = '$address_id' AND user_id = '{$row_User['user_id']}'";
$Address = mysqli_query($orek, $query_Address) or die(mysqli_error($orek));
$totalRows_Address = mysqli_num_rows($Address);

if ($totalRows_Address == 0) {
    header('Location: checkout.php?step=1');
    exit();
}

// Delete address
$deleteSQL = "DELETE FROM user_shipping WHERE shipping_id = '$address_id' AND user_id = '{$row_User['user_id']}'";
$Result1 = mysqli_query($orek, $deleteSQL) or die(mysqli_error($orek));

// Redirect back to checkout page
header('Location: checkout.php?step=1&success=address_deleted');
exit();
?> 