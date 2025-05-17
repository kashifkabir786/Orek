<?php
require_once('Connections/orek.php');
require_once('session.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Common data
    $message = mysqli_real_escape_string($orek, $_POST['message']);
    $user_id = $row_Recordset1['user_id'];

    // Check if it's a reply (ticket_id is set)
    if (!empty($_POST['ticket_id'])) {
        $ticket_id = intval($_POST['ticket_id']);

        // Insert as reply into ticket_replies
        $insertReplySQL = "INSERT INTO ticket_replies (ticket_id, user_id, message, is_admin, created_at)
                           VALUES ('$ticket_id', '$user_id', '$message', 0, NOW())"; // is_admin = 0 means user
        mysqli_query($orek, $insertReplySQL) or die(mysqli_error($orek));

    } else {
        // It's a new ticket
        $subject = mysqli_real_escape_string($orek, $_POST['subject']);
        $insertSQL = "INSERT INTO tickets (user_id, subject, message)
                      VALUES ('$user_id', '$subject', '$message')";
        mysqli_query($orek, $insertSQL) or die(mysqli_error($orek));
    }

    // Redirect back
    header("Location: my-account.php?tab=tickets#tickets&success=1");
    exit();
}

?>