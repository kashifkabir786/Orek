<?php require_once('../Connections/car_spare.php'); ?>
<?php require_once('session.php'); ?>
<?php
if(isset($_POST['heading_id'])){
	$heading_id = $_POST['heading_id'];
	
	$query_Recordset2 = "SELECT * FROM sub_heading WHERE heading_id='$heading_id'";
    $Recordset2 = mysqli_query( $car_spare, $query_Recordset2 )or die( mysqli_error( $car_spare ) );
    $row_Recordset2 = mysqli_fetch_assoc( $Recordset2 );
    $totalRows_Recordset2 = mysqli_num_rows( $Recordset2 );
	
	if($totalRows_Recordset2 > 0){
		$sub_heading = '<option value="" selected disabled>Select Sub Heading</option>';
            do{ 
				$sub_heading .= '<option value=" ' . $row_Recordset2['sub_heading_id'] .'">'. $row_Recordset2['sub_heading_name'] . '</option>';
            
             }while($row_Recordset2 = mysqli_fetch_assoc($Recordset2));
		echo $sub_heading;
	}else{
		echo "Not Found";
	}
	
}
?>