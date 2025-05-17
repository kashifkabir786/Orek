<?php
require_once('../Connections/orek.php');
require_once('session.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ticket_id = mysqli_real_escape_string($orek, $_POST['ticket_id']);
    $status = mysqli_real_escape_string($orek, $_POST['status']);

    $updateSQL = "UPDATE tickets SET status = '$status' WHERE ticket_id = '$ticket_id'";
    mysqli_query($orek, $updateSQL) or die(mysqli_error($orek));

    header("Location: view-ticket.php?id=" . $ticket_id);
    exit();
}
?>