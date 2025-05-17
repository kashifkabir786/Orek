<?php
// Prevent PHP from outputting HTML errors
error_reporting(0);
ini_set('display_errors', 0);

// Set JSON content type
header('Content-Type: application/json');

require_once('Connections/orek.php');
require_once('session.php');

if (!isset($_SESSION['email'])) {
    die(json_encode(['success' => false, 'message' => 'Please login first']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_SESSION['email'];
    
    try {
        // Get user_id from email first
        $user_query = mysqli_prepare($orek, "SELECT user_id FROM user WHERE email = ?");
        if (!$user_query) {
            throw new Exception("Failed to prepare user query: " . mysqli_error($orek));
        }
        
        mysqli_stmt_bind_param($user_query, "s", $email);
        if (!mysqli_stmt_execute($user_query)) {
            throw new Exception("Failed to execute user query: " . mysqli_stmt_error($user_query));
        }
        
        $user_result = mysqli_stmt_get_result($user_query);
        $user_data = mysqli_fetch_assoc($user_result);
        
        if (!$user_data) {
            throw new Exception("User not found");
        }
        
        $user_id = $user_data['user_id'];

        // Validate required fields
        $required_fields = ['item_id', 'cart_id', 'request_type', 'reason', 'description', 'product_condition'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Missing required field: $field");
            }
        }

        $item_id = mysqli_real_escape_string($orek, $_POST['item_id']);
        $cart_id = mysqli_real_escape_string($orek, $_POST['cart_id']);
        $request_type = mysqli_real_escape_string($orek, $_POST['request_type']);
        $reason = mysqli_real_escape_string($orek, $_POST['reason']);
        $description = mysqli_real_escape_string($orek, $_POST['description']);
        $product_condition = mysqli_real_escape_string($orek, $_POST['product_condition']);
        
        // Enhanced preferred_size validation and debugging
        $preferred_size = '';  // Default to empty string instead of null
        if (isset($_POST['preferred_size']) && !empty($_POST['preferred_size'])) {
            $preferred_size = trim($_POST['preferred_size']);  // Remove whitespace
            if (strlen($preferred_size) > 10) {
                throw new Exception("Preferred size must not exceed 10 characters");
            }
            $preferred_size = mysqli_real_escape_string($orek, $preferred_size);
        }

        // Debug log
        error_log("Preferred Size Value: " . var_export($preferred_size, true));

        // Start transaction
        mysqli_begin_transaction($orek);

        $stmt = mysqli_prepare($orek, "CALL sp_create_return_exchange(?, ?, ?, ?, ?, ?, ?, ?, @return_id)");
        
        if (!$stmt) {
            throw new Exception("Database error: " . mysqli_error($orek));
        }
        
        mysqli_stmt_bind_param($stmt, "iiisssss", 
            $user_id, $item_id, $cart_id, $request_type, $reason, $description, $product_condition, $preferred_size);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Execute failed: " . mysqli_stmt_error($stmt));
        }

        // Get the return_id
       $result = mysqli_query($orek, "SELECT @return_id as return_id");

        if (!$result) {
            throw new Exception("Failed to get return_id: " . mysqli_error($orek));
        }
        
        $row = mysqli_fetch_assoc($result);
        $return_id = $row['return_id'];

        if (!$return_id) {
            throw new Exception("Failed to create return/exchange record");
        }

        // Handle image uploads
        if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
            $upload_dir = 'assets/img/returns/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Limit to 3 images
            $count = 0;
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                if ($count >= 3) break;
                
                if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                    $file_name = $_FILES['images']['name'][$key];
                    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
                    $new_name = uniqid('return_') . '.' . $file_ext;
                    $upload_path = $upload_dir . $new_name;

                    if (move_uploaded_file($tmp_name, $upload_path)) {
                        $sql = "INSERT INTO return_images (return_id, image_path) VALUES (?, ?)";
                        $stmt = mysqli_prepare($orek, $sql);
                        mysqli_stmt_bind_param($stmt, "is", $return_id, $upload_path);
                        if (!mysqli_stmt_execute($stmt)) {
                            // Log error but don't stop the process
                            error_log("Failed to insert image: " . mysqli_stmt_error($stmt));
                        }
                        $count++;
                    }
                }
            }
        }

        // Commit transaction
        mysqli_commit($orek);
        echo json_encode([
            'success' => true, 
            'message' => 'Return/Exchange request submitted successfully'
        ]);
    } catch (Exception $e) {
        mysqli_rollback($orek);
        error_log("Return/Exchange Error: " . $e->getMessage());
        
        // Log detailed error information
        $debug_info = [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'post_data' => $_POST,
            'files_data' => isset($_FILES) ? array_keys($_FILES) : []
        ];
        error_log("Debug Info: " . print_r($debug_info, true));
        
        // Provide more specific error messages based on the exception
        $errorMessage = match(true) {
            str_contains($e->getMessage(), "Missing required field") => 
                "Please fill in all required fields: " . $e->getMessage(),
            str_contains($e->getMessage(), "User not found") => 
                "User authentication error. Please try logging in again.",
            str_contains($e->getMessage(), "Failed to get return_id") => 
                "System error while processing your request. Please try again later.",
            str_contains($e->getMessage(), "Database error") => 
                "Database connection error. Please try again later.",
            str_contains($e->getMessage(), "Execute failed") => 
                "Failed to process your request. Please check your input and try again.",
            default => "An unexpected error occurred. Please try again later. Error: " . $e->getMessage()
        };

        echo json_encode([
            'success' => false,
            'message' => $errorMessage,
            'error_code' => $e->getCode(),
            'debug' => $debug_info
        ]);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>