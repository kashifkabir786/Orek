<?php require_once('../Connections/car_spare.php'); ?>
<?php require_once('session.php'); ?>
<?php

$editFormAction = $_SERVER[ 'PHP_SELF' ];
if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
    $editFormAction .= "?" . htmlentities( $_SERVER[ 'QUERY_STRING' ] );
}
$query_Recordset2 = "SELECT * FROM stock";
$Recordset2 = mysqli_query( $car_spare, $query_Recordset2 )or die( mysqli_error( $car_spare ) );
$row_Recordset2 = mysqli_fetch_assoc( $Recordset2 );
$totalRows_Recordset2 = mysqli_num_rows( $Recordset2 );

if ( isset( $_POST[ "upload_csv" ] ) ) {
    if ( $_FILES[ 'file_upload' ][ 'name' ] ) {
        $filename = explode( ".", $_FILES[ 'file_upload' ][ 'name' ] );
        $loop_stock = "";
        if ( $filename[ 1 ] == "csv" ) {
            $handle = fopen( $_FILES[ 'file_upload' ][ 'tmp_name' ], "r" );
            while ( $data = fgetcsv( $handle ) ) {
                $item_model_no = GetSQLValueString( $data[ 0 ], "text" );
                $total_stock = GetSQLValueString( $data[ 1 ], "text" );

                $loop_stock .= "(" . $item_model_no . "," . $total_stock . "),";
            }

            $sql = "INSERT INTO `stock`(`item_model_no`, `total_stock`)VALUES" . rtrim( $loop_stock, "," );
            mysqli_query( $car_spare, $sql );

            fclose( $handle );
            $insertGoTo = "stock.php?success=Added";
            if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
                $insertGoTo .= ( strpos( $insertGoTo, '?' ) ) ? "&" : "?";
                $insertGoTo .= $_SERVER[ 'QUERY_STRING' ];
            }
            header( sprintf( "Location: %s", $insertGoTo ) );
        }
    }
}
if ( isset( $_POST[ 'submit' ] ) && $_POST[ 'submit' ] == 'delete_file' ) {

    $deleteSQL = sprintf( "DELETE FROM `stock`" );
    $Result1 = mysqli_query( $car_spare, $deleteSQL )or die( mysqli_error( $car_spare ) );

    $deleteGoTo = "stock.php?success=Deleted";
    if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
        $deleteGoTo .= ( strpos( $deleteGoTo, '?' ) ) ? "&" : "?";
        $deleteGoTo .= $_SERVER[ 'QUERY_STRING' ];
    }
    header( sprintf( "Location: %s", $deleteGoTo ) );
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Stock</title>
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
          <h4 style="color: #fff;">Welcome <?php echo $row_Recordset1['uname']; ?></h4>
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
      <h3><strong>STOCK</strong></h3>
      <hr/>
      <div class="margin-50">
        <form enctype="multipart/form-data" action="<?php echo $editFormAction ?>" name="form1" role="form" method="post">
          <?php if(empty($totalRows_Recordset2)){ ?>
          <input type="file" name="file_upload">
          <button type="submit" name="upload_csv" class="btn btn-info" style="margin-top: 5px">Upload Stock</button>
          <?php }else{ ?>
          <button name="submit" value="delete_file" class="btn btn-danger">Delete</button>
          <?php } ?>
        </form>
        <div class="bg-white table-responsive margin-50">
          <table class="table table-striped" id="example">
            <thead>
              <tr>
                <th>Model No</th>
                <th>Stock</th>
              </tr>
            </thead>
            <tbody>
              <?php do { ?>
              <tr>
                <td><?php echo $row_Recordset2['item_model_no'] ?></td>
                <td><?php echo $row_Recordset2['total_stock'] ?></td>
              </tr>
              <?php }while($row_Recordset2 = mysqli_fetch_assoc($Recordset2)); ?>
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
