<?php
require_once('../Connections/orek.php');

// Update stock for all items
$query = "UPDATE item SET stock = FLOOR(3 + RAND() * 8)";  // Random number between 3 and 10
mysqli_query($orek, $query);
?>