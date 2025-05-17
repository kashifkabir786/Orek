<?php
require_once('../Connections/orek.php');
require_once('session.php');

$ticket_id = $_POST['ticket_id'];
$message = mysqli_real_escape_string($orek, $_POST['message']);
$is_admin = 0; // user sending

$query = "INSERT INTO ticket_replies (ticket_id, message, is_admin, created_at)
          VALUES ('$ticket_id', '$message', '$is_admin', NOW())";

mysqli_query($orek, $query) or die(mysqli_error($orek));

// Redirect back to the page
header("Location: tickets_page.php"); // Change this to your ticket page
exit;