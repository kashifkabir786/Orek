<?php
// Set connection parameters
$hostname_orek = "localhost";
$database_orek = "orek";
$username_orek = "orek";
$password_orek = '123456'; //rQ6*=423+Gw

// Enable persistent connections for better performance
$orek = mysqli_init();

// Set connection timeout to prevent hanging on slow connections
mysqli_options($orek, MYSQLI_OPT_CONNECT_TIMEOUT, 5); 

// Set read timeout to prevent hanging on slow queries
mysqli_options($orek, MYSQLI_OPT_READ_TIMEOUT, 10);

// Establish connection
if (!mysqli_real_connect($orek, $hostname_orek, $username_orek, $password_orek, $database_orek, 3306, null, MYSQLI_CLIENT_FOUND_ROWS)) {
    trigger_error('Connection error: ' . mysqli_connect_error(), E_USER_ERROR);
}

// Set character set to UTF-8
mysqli_set_charset($orek, 'utf8mb4');

global $orek;
if (!function_exists("GetSQLValueString")) {
    function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
    {
        if (PHP_VERSION < 6) {
            $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
        }
        global $orek;
        $theValue = function_exists("mysqli_real_escape_string") ? mysqli_real_escape_string($orek, $theValue) : mysqli_escape_string($theValue);
        switch ($theType) {
            case "text":
                $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
                break;    
            case "long":
            case "int":
                $theValue = ($theValue != "") ? intval($theValue) : "NULL";
                break;
            case "double":
                $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
                break;
            case "date":
                $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
                break;
            case "defined":
                $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
                break;
        }
        return $theValue;
    }
}
?>