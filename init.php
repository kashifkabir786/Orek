<?php
/**
 * Orek Initialization File
 * 
 * This file contains all common initialization code needed across all pages.
 * Include this file after session-2.php in every page.
 * 
 * Includes:
 * - Database connection
 * - Analytics tracking
 * - Common functions
 */

// Include database connection if not already included
if (!isset($orek)) {
    require_once('Connections/orek.php');
}

// Include analytics tracking - will be initialized on every page
require_once('analytics.php');

// Set timezone to India
date_default_timezone_set('Asia/Kolkata');

// Configure error reporting (commented out for production)
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// Date utility functions that are not in session-2.php
function dateconvertdMY($dateymd, $timezone) {
    $serverTime = strtotime($dateymd);
    if ($timezone == "UTC") {
        $istTime = $serverTime + (5 * 3600) + (30 * 60);
    } else {
        $istTime = $serverTime;
    }
    $converted = date("d M Y", $istTime);
    return ($converted);
}

function dateconvertHI($dateymd, $timezone) {
    $serverTime = strtotime($dateymd);
    if ($timezone == "UTC") {
        $istTime = $serverTime + (5 * 3600) + (30 * 60);
    } else {
        $istTime = $serverTime;
    }
    $converted = date("h:i", $istTime);
    return ($converted);
}

// Other utility functions
function get_tiny_url($url) {
    $ch = curl_init();
    $timeout = 5;
    curl_setopt($ch, CURLOPT_URL, 'http://tinyurl.com/api-create.php?url=' . $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['email']) && !empty($_SESSION['email']);
}

// Function to get current user ID if logged in
function getCurrentUserId($connection) {
    if (isLoggedIn()) {
        $email = $_SESSION['email'];
        $query = "SELECT user_id FROM user WHERE email = '$email' LIMIT 1";
        $result = mysqli_query($connection, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            return $row['user_id'];
        }
    }
    return null;
}
?> 