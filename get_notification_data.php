<?php
require_once('Connections/orek.php');

// Get random users and items for notifications
$query = "SELECT DISTINCT u.fname as user_name, 
          (SELECT item_name FROM item ORDER BY RAND() LIMIT 1) as item_name 
          FROM user u 
          GROUP BY u.fname 
          ORDER BY RAND()";

$result = mysqli_query($orek, $query);
$notifications = array();

while($row = mysqli_fetch_assoc($result)) {
    $notifications[] = array(
        'user_name' => $row['user_name'],
        'item_name' => $row['item_name']
    );
}

$response = array(
    'success' => true,
    'notifications' => $notifications
);

header('Content-Type: application/json');
echo json_encode($response);
?>