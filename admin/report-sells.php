<?php require_once('../Connections/car_spare.php'); ?>
<?php require_once('session.php'); ?>
<?php
$editFormAction = $_SERVER[ 'PHP_SELF' ];
if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
    $editFormAction .= "?" . htmlentities( $_SERVER[ 'QUERY_STRING' ] );
}
if ( isset( $_GET[ 'submit' ] ) && $_GET[ 'submit' ] == 'filter' ) {

    $query_Recordset2 = "SELECT A.*, B.`fname`, B.`lname` FROM payment AS A INNER JOIN user AS B ON A.`user_id` = B.`user_id` WHERE A.`payment_date` BETWEEN '{$_GET['from']}' AND '{$_GET['to']}'";
    $Recordset2 = mysqli_query( $car_spare, $query_Recordset2 )or die( mysqli_error( $car_spare ) );
    $row_Recordset2 = mysqli_fetch_assoc( $Recordset2 );
    $totalRows_Recordset2 = mysqli_num_rows( $Recordset2 );

} else if ( isset( $_GET[ 'filter_download' ] ) && $_GET[ 'filter_download' ] == 'filter_download' ) {

    header( "Location: reports/sells-report.php?from=" . $_GET[ 'from' ] . "&to=" . $_GET[ 'to' ] );

} else {

    $query_Recordset2 = "SELECT A.*, B.`fname`, B.lname FROM payment AS A INNER JOIN user AS B ON A.`user_id` = B.`user_id`";
    $Recordset2 = mysqli_query( $car_spare, $query_Recordset2 )or die( mysqli_error( $car_spare ) );
    $row_Recordset2 = mysqli_fetch_assoc( $Recordset2 );
    $totalRows_Recordset2 = mysqli_num_rows( $Recordset2 );
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Sells Report</title>
<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css">
<link href="css/style.css" rel="stylesheet" type="text/css">
<link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
<link href="css/dataTables.bootstrap.css" rel="stylesheet" type="text/css">
<link href="css/jquery.dataTables.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="left-side height-100">
  <div class="row height-100">
    <div class="col-md-2 bg-green padding-top-bottom height-100 left-menu">
      <div class="row">
        <div class="padding-10 text-center"> <a href="logo.php"><img src="images/logo.png" width="60%"></a>
          <h4 style="color: #fff;">WELCOME ADMIN</h4>
          <a href="logout.php" class="btn btn-danger">Logout</a> <a href="change-password.php" class="btn btn-primary">Change Password</a> </div>
      </div>
      <div class="row margin-70">
        <div class="item-list-menu">
          <?php include('menu.php'); ?>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-offset-2 col-md-10">
      <h3><strong>SELLS REPORT</strong></h3>
      <hr/>
      <!--      <div class="margin-50"> <a href="add-payment.php" class="btn btn-primary">Add New Payment</a>-->
      
      <form name="form1" action="<?php echo $editFormAction ?>" method="get" role="form">
        <table class="table table-nobordered" id="contact" width="70%" cellspacing="0">
          <tr>
            <td></td>
            <td>From :</td>
            <td><input type="date" name="from" class="form-control" required></td>
          </tr>
          <tr>
            <td></td>
            <td>To :</td>
            <td><input type="date" name="to" class="form-control" required></td>
          </tr>
          <tr>
            <td colspan="3"></td>
          </tr>
        </table>
        <left>
          <button class="btn btn-primary" name="submit" value="filter">Filter</button>
          <button class="btn btn-success" name="filter_download" value="filter_download">Filter and Download</button>
          <a href="<?php echo 'reports/sells-report.php?download=all'; ?>" class="btn btn-info">Download All</a> </left>
      </form>
      <div class="bg-white table-responsive margin-50">
        <table class="table table-striped" id="example">
          <thead>
            <tr>
              <th>Payment Id</th>
              <th>User Name</th>
              <th>Payment Date</th>
              <th>Amount</th>
              <th>Txn Id</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if ( $totalRows_Recordset2 > 0 ) {
                do {
                    ?>
            <tr>
              <td><?php echo $row_Recordset2['payment_id'] ?></td>
              <td><?php echo $row_Recordset2['fname'] ." " . $row_Recordset2['lname'] ?></td>
              <td><?php echo $row_Recordset2['payment_date'] ?></td>
              <td><?php echo $row_Recordset2['amount'] ?></td>
              <td><?php echo $row_Recordset2['txn_id'] ?></td>
            </tr>
            <?php }while($row_Recordset2 = mysqli_fetch_assoc($Recordset2)); 
}?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
</div>
</body>
<script src="js/jquery-3.2.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="js/dataTables.bootstrap.min.js"></script>
<script>
$(document).ready(function() {
    $('#example').DataTable( {
        initComplete: function () {
            this.api().columns().every( function () {
                var column = this;
                var select = $('<select><option value=""></option></select>')
                    .appendTo( $(column.footer()).empty() )
                    .on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
 
                        column
                            .search( val ? '^'+val+'$' : '', true, false )
                            .draw();
                    } );
 
                column.data().unique().sort().each( function ( d, j ) {
                    select.append( '<option value="'+d+'">'+d+'</option>' )
                } );
            } );
        }
    } );
} );
</script>
</html>
