<?php
// Database connection
require_once('Connections/orek.php');

// Function to get client IP address
function getClientIP() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

// Track user visit
function trackUserVisit() {
    global $orek;
    
    // Get user information
    $user_id = isset($_SESSION['email']) ? getUserIdFromEmail($_SESSION['email']) : NULL;
    $ip_address = getClientIP();
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $page_url = $_SERVER['REQUEST_URI'];
    $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL;
    
    // Create visits table if it doesn't exist
    $create_table_query = "CREATE TABLE IF NOT EXISTS `user_visits` (
        `visit_id` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) DEFAULT NULL,
        `ip_address` varchar(50) NOT NULL,
        `user_agent` text NOT NULL,
        `page_url` varchar(255) NOT NULL,
        `referrer` varchar(255) DEFAULT NULL,
        `visit_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`visit_id`),
        KEY `user_id` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    mysqli_query($orek, $create_table_query);
    
    // Insert visit data
    $insertSQL = sprintf("INSERT INTO user_visits (user_id, ip_address, user_agent, page_url, referrer) VALUES (%s, %s, %s, %s, %s)",
        $user_id ? GetSQLValueString($user_id, "int") : "NULL",
        GetSQLValueString($ip_address, "text"),
        GetSQLValueString($user_agent, "text"),
        GetSQLValueString($page_url, "text"),
        $referrer ? GetSQLValueString($referrer, "text") : "NULL");
    
    mysqli_query($orek, $insertSQL);
}

// Helper function to get user_id from email
function getUserIdFromEmail($email) {
    global $orek;
    $query = "SELECT user_id FROM user WHERE email = '" . mysqli_real_escape_string($orek, $email) . "'";
    $result = mysqli_query($orek, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['user_id'];
    }
    return NULL;
}

// Track the current visit
trackUserVisit();
?> 