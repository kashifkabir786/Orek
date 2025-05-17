<?php
require_once('Connections/orek.php');
require_once('session-2.php');

header('Content-Type: application/json');

try {
    // Check if user is logged in
    if (!isset($_SESSION['email'])) {
        throw new Exception('Please login to submit a review');
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Get user_id from session email
    $email = $_SESSION['email'];
    $query_user = "SELECT user_id FROM user WHERE email = ?";
    $stmt_user = mysqli_prepare($orek, $query_user);
    mysqli_stmt_bind_param($stmt_user, "s", $email);
    mysqli_stmt_execute($stmt_user);
    $result_user = mysqli_stmt_get_result($stmt_user);
    $user_data = mysqli_fetch_assoc($result_user);
    
    if (!$user_data) {
        throw new Exception('User not found');
    }

    // Validate and sanitize inputs
    $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
    $review = isset($_POST['review']) ? mysqli_real_escape_string($orek, trim($_POST['review'])) : '';
    $rating = isset($_POST['rating']) ? $_POST['rating'] : '5';

    if (!$item_id || !$review) {
        throw new Exception('All fields are required');
    }

    // Check if user already reviewed this item
    $check_query = "SELECT item_id FROM review WHERE item_id = ? AND user_id = ?";
    $stmt_check = mysqli_prepare($orek, $check_query);
    mysqli_stmt_bind_param($stmt_check, "ii", $item_id, $user_data['user_id']);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);
    
    if (mysqli_num_rows($result_check) > 0) {
        throw new Exception('You have already reviewed this item');
    }

    // Insert review
    $query = "INSERT INTO review (item_id, user_id, review, rating) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($orek, $query);
    mysqli_stmt_bind_param($stmt, "iiss", $item_id, $user_data['user_id'], $review, $rating);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Failed to submit review');
    }

    echo json_encode([
        'success' => true,
        'message' => 'Your review has been submitted successfully!'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>