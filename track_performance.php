<?php
/**
 * Orek Analytics - Performance Tracking Endpoint
 * 
 * Receives and stores performance metrics from the client-side JavaScript
 */

// Include database connection
require_once('Connections/orek.php');

// Set response header to JSON
header('Content-Type: application/json');

// Get raw JSON data from the request
$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

// Basic validation - exit if no data
if (!$data || !isset($data['visitor_id']) || !isset($data['page_url'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

// Sanitize and extract data
$visitor_id = mysqli_real_escape_string($orek, $data['visitor_id']);
$page_url = mysqli_real_escape_string($orek, $data['page_url']);
$load_time = isset($data['pageLoadTime']) ? intval($data['pageLoadTime']) : 0;
$render_time = isset($data['pageRenderTime']) ? intval($data['pageRenderTime']) : 0;
$total_time = isset($data['totalTime']) ? intval($data['totalTime']) : 0;
$screen_width = isset($data['screenWidth']) ? intval($data['screenWidth']) : 0;
$screen_height = isset($data['screenHeight']) ? intval($data['screenHeight']) : 0;
$color_depth = isset($data['colorDepth']) ? intval($data['colorDepth']) : 0;
$viewport_width = isset($data['viewportWidth']) ? intval($data['viewportWidth']) : 0;
$viewport_height = isset($data['viewportHeight']) ? intval($data['viewportHeight']) : 0;
$timestamp = date('Y-m-d H:i:s');

try {
    // Insert data into the performance_metrics table
    $query = "INSERT INTO performance_metrics 
              (visitor_id, page_url, load_time, render_time, total_time, 
               screen_width, screen_height, color_depth, viewport_width, viewport_height, timestamp) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($orek, $query);
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . mysqli_error($orek));
    }
    
    mysqli_stmt_bind_param(
        $stmt, 
        'ssiiiiiiiis', 
        $visitor_id, $page_url, $load_time, $render_time, $total_time,
        $screen_width, $screen_height, $color_depth, $viewport_width, $viewport_height, $timestamp
    );
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Execute failed: " . mysqli_stmt_error($stmt));
    }
    
    mysqli_stmt_close($stmt);
    echo json_encode(['success' => true]);
    
} catch (Exception $e) {
    // Log error but don't expose details in response
    error_log("Performance tracking error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error']);
}
?> 