<?php
require_once('Connections/orek.php');

// Set proper headers
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    
    if (empty($email)) {
        throw new Exception('Email address is required');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Please enter a valid email address');
    }

    // Prevent SQL injection
    $email = mysqli_real_escape_string($orek, $email);

    // Check for existing subscription
    $check_query = "SELECT id FROM newsletter WHERE email = '$email'";
    $check_result = mysqli_query($orek, $check_query);

    if (!$check_result) {
        throw new Exception('Database error: ' . mysqli_error($orek));
    }

    if (mysqli_num_rows($check_result) > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'This email is already subscribed to our newsletter'
        ]);
        exit;
    }

    // Insert new subscription
    $insert_query = "INSERT INTO newsletter (email, created_at) VALUES ('$email', NOW())";
    
    if (!mysqli_query($orek, $insert_query)) {
        throw new Exception('Database error: ' . mysqli_error($orek));
    }

    echo json_encode([
        'success' => true,
        'message' => 'Thank you for subscribing to our newsletter!'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>