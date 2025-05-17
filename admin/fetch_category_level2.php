<?php
require_once('../Connections/orek.php');

if(isset($_POST["category_level1_id"])) {
    $category_level1_id = $_POST["category_level1_id"];

    $query = "SELECT * FROM category_level2 WHERE category_level1_id = '$category_level1_id'";
    $result = mysqli_query($orek, $query);

    echo '<option value="">Select Category Level2</option>';
    while($row = mysqli_fetch_assoc($result)) {
        echo '<option value="'.$row['category_level2_id'].'">'.$row['category_level2_name'].'</option>';
    }
}
?>