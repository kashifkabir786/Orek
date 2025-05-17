<?php
require_once('../Connections/orek.php');
require_once('session.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ticket_id = mysqli_real_escape_string($orek, $_POST['ticket_id']);
    $message = mysqli_real_escape_string($orek, $_POST['message']);
    
    // Get admin user_id from session
    $query_Admin = "SELECT * FROM user WHERE email = '{$_SESSION['email']}'";
    $Admin = mysqli_query($orek, $query_Admin) or die(mysqli_error($orek));
    $row_Admin = mysqli_fetch_assoc($Admin);
    
    // Insert reply
    $insertSQL = "INSERT INTO ticket_replies (ticket_id, user_id, is_admin, message) 
                  VALUES ('$ticket_id', '{$row_Admin['user_id']}', 1, '$message')";
    
    if(mysqli_query($orek, $insertSQL)) {
        // Update ticket status to in_progress
        $updateSQL = "UPDATE tickets SET status = 'in_progress' 
                     WHERE ticket_id = '$ticket_id' AND status = 'open'";
        mysqli_query($orek, $updateSQL);
        
        $_SESSION['success_message'] = "Reply sent successfully.";
    } else {
        $_SESSION['error_message'] = "Failed to send reply.";
    }
    
    header("Location: view-ticket.php?id=" . $ticket_id);
    exit();
}
?>