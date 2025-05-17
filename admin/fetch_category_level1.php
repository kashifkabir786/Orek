<?php
require_once('../Connections/orek.php'); // Database connection

if(isset($_POST['category_id'])) {
    $category_id = $_POST['category_id'];
    
    // Fetch related Category Level1
    $query = "SELECT * FROM category_level1 WHERE category_id = '$category_id'";
    $result = mysqli_query($orek, $query);

    echo '<option value="">Select Category Level1</option>';
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<option value="'.$row['category_level1_id'].'">'.$row['category_level1_name'].'</option>';
    }
}
?>