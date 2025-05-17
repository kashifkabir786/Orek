<?php require_once('../Connections/car_spare.php'); ?>
<?php require_once('session.php'); ?>
<?php
if ( isset( $_POST[ 'cars' ] ) ) {
  $cars_ar = explode( ",", $_POST[ 'cars' ] );
  $query = "";
  $total_cars = count( $cars_ar );
  $i = 1;
  foreach ( $cars_ar as $car ) {
	$car = trim($car);
    if ( $i == $total_cars )
      $query .= "model_name LIKE '%$car%'";
    else
      $query .= "model_name LIKE '%$car%' OR ";
    $i++;
  }
  $query_Recordset2 = "SELECT * FROM car_model WHERE " . $query;
  $Recordset2 = mysqli_query( $car_spare, $query_Recordset2 )or die( mysqli_error( $car_spare ) );
  $row_Recordset2 = mysqli_fetch_assoc( $Recordset2 );
  $totalRows_Recordset2 = mysqli_num_rows( $Recordset2 );
}
if ( $totalRows_Recordset2 > 0 ) {
  $i = 1;
  do {
    ?>
<label>
  <input type="checkbox" name="model<?php echo $i ?>" value="<?php echo $row_Recordset2['car_model_id'] ?>">
  <?php echo $row_Recordset2['model_name'] ?></label>
<?php 
	  $i++; 
  }while($row_Recordset2 = mysqli_fetch_assoc($Recordset2));
} else {
	echo "Not Found";
}
?>
