<?php
/**
 * Orek Analytics - User Interaction Events Tracking
 * 
 * Receives and stores user interaction events from client-side JavaScript
 */

// Include database connection
require_once('Connections/orek.php');

// Set response header to JSON
header('Content-Type: application/json');

// Get raw JSON data from the request
$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

// Basic validation - exit if no data
if (!$data || !isset($data['visitor_id']) || !isset($data['event_type']) || !isset($data['page_url'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

// Sanitize common fields
$visitor_id = mysqli_real_escape_string($orek, $data['visitor_id']);
$event_type = mysqli_real_escape_string($orek, $data['event_type']);
$page_url = mysqli_real_escape_string($orek, $data['page_url']);
$timestamp = isset($data['timestamp']) ? mysqli_real_escape_string($orek, $data['timestamp']) : date('Y-m-d H:i:s');

// Initialize optional fields with defaults
$element_type = isset($data['element_type']) ? mysqli_real_escape_string($orek, $data['element_type']) : NULL;
$element_id = isset($data['element_id']) ? mysqli_real_escape_string($orek, $data['element_id']) : NULL;
$element_class = isset($data['element_class']) ? mysqli_real_escape_string($orek, $data['element_class']) : NULL;
$element_text = isset($data['element_text']) ? mysqli_real_escape_string($orek, $data['element_text']) : NULL;
$link_url = isset($data['link_url']) ? mysqli_real_escape_string($orek, $data['link_url']) : NULL;
$scroll_depth = isset($data['scroll_depth']) ? intval($data['scroll_depth']) : NULL;
$time_spent = isset($data['time_spent']) ? intval($data['time_spent']) : NULL;
$status = isset($data['status']) ? mysqli_real_escape_string($orek, $data['status']) : NULL;

try {
    // Insert data into the user_events table
    $query = "INSERT INTO user_events 
              (visitor_id, event_type, page_url, timestamp, element_type, element_id, element_class, 
               element_text, link_url, scroll_depth, time_spent, status) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($orek, $query);
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . mysqli_error($orek));
    }
    
    mysqli_stmt_bind_param(
        $stmt, 
        'ssssssssssis', 
        $visitor_id, $event_type, $page_url, $timestamp, $element_type, $element_id, $element_class,
        $element_text, $link_url, $scroll_depth, $time_spent, $status
    );
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Execute failed: " . mysqli_stmt_error($stmt));
    }
    
    mysqli_stmt_close($stmt);
    echo json_encode(['success' => true]);
    
} catch (Exception $e) {
    // Log error but don't expose details in response
    error_log("Event tracking error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error']);
}
?> 