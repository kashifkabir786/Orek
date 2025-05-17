<?php
//Start session
session_start();
$loginflag = 0;
//Check whether the session variable SESS_MEMBER_ID is present or not
if (!isset($_SESSION['email']) || (trim($_SESSION['email']) == '')) {
    $loginflag = 1;
    $current_page = $_SERVER['PHP_SELF'];
    if (!empty($_SERVER['QUERY_STRING'])) {
        $current_page .= '?' . $_SERVER['QUERY_STRING'];
    }
    $_SESSION['current_page'] = $current_page;
} else {
    // Check if there's a saved page to redirect to
    if (isset($_SESSION['current_page']) && $_SESSION['current_page'] != '/orek/login.php') {
        $redirect_to = $_SESSION['current_page'];
        unset($_SESSION['current_page']); // Clear it after use
        header("Location: " . $redirect_to);
        exit();
    }
}

if ($loginflag != 1) {
    $today = date('Y-m-d');
    $query_Recordset1 = "SELECT * FROM user WHERE email = '{$_SESSION['email']}'";
    $Recordset1 = mysqli_query($orek, $query_Recordset1) or die(mysqli_error($orek));
    $row_Recordset1 = mysqli_fetch_assoc($Recordset1);
    $totalRows_Recordset1 = mysqli_num_rows($Recordset1);
    $flag = true;
}


function dateformat( $datevar ) {
    $date = str_replace( '/', '-', $datevar );
    $changed_format = date( 'Y-m-d', strtotime( $date ) );
    return ( $changed_format );
}

function dateconvert( $dateymd ) {
    $converted = date( "d F Y", strtotime( $dateymd ) );
    return ( $converted );
}
// echo $_SESSION['current_page'] 
?>