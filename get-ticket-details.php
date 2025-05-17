<?php
require_once('Connections/orek.php');
require_once('session.php');

if (isset($_GET['id'])) {
    $ticket_id = mysqli_real_escape_string($orek, $_GET['id']);
    $user_id = $row_Recordset1['user_id'];

    // Get ticket details
    $query_Ticket = "SELECT t.*, u.fname, u.lname 
                     FROM tickets t 
                     JOIN user u ON t.user_id = u.user_id 
                     WHERE t.ticket_id = '$ticket_id' AND t.user_id = '$user_id'";
    $Ticket = mysqli_query($orek, $query_Ticket) or die(mysqli_error($orek));
    $row_Ticket = mysqli_fetch_assoc($Ticket);

    // Get replies
    $query_Replies = "SELECT r.*, u.fname, u.lname 
                      FROM ticket_replies r
                      JOIN user u ON r.user_id = u.user_id
                      WHERE r.ticket_id = '$ticket_id'
                      ORDER BY r.created_at ASC";
    $Replies = mysqli_query($orek, $query_Replies) or die(mysqli_error($orek));

    include('ticket-details-template.php');
}