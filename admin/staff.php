<?php require_once('../Connections/car_spare.php'); ?>
<?php require_once('session.php'); ?>
<?php
$query_Recordset2 = "SELECT * FROM admin WHERE role = 'Staff'";
$Recordset2 = mysqli_query( $car_spare, $query_Recordset2 )or die( mysqli_error( $car_spare ) );
$row_Recordset2 = mysqli_fetch_assoc( $Recordset2 );
$totalRows_Recordset2 = mysqli_num_rows( $Recordset2 );
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Staff</title>
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
                    <div class="padding-10 text-center"> <img src="images/footer-logo.png" width="60%">
                        <h4 style="color: #fff;">Welcome <?php echo $row_Recordset1['uname']; ?></h4>
                        <a href="logout.php" class="btn btn-danger">Logout</a> <a href="change-password.php"
                            class="btn btn-primary">Change Password</a>
                    </div>
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
                <h3><strong>Staff</strong></h3>
                <hr />
                <div class="margin-50"> <a href="add-staff.php" class="btn btn-primary">Add New Staff</a>
                    <div class="bg-white table-responsive margin-50">
                        <table class="table table-striped" id="example">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($totalRows_Recordset2 > 0) { ?>
                                <?php do { ?>
                                <tr>
                                    <td><?php echo $row_Recordset2['uname'] ?></td>
                                    <td><?php echo $row_Recordset2['fname'] ?></td>
                                    <td><?php echo $row_Recordset2['lname'] ?></td>
                                    <td><a href="add-staff.php?uname=<?php echo $row_Recordset2['uname']; ?>"><i
                                                class="fa fa-edit"></i></a> <a
                                            href="delete-staff.php?uname=<?php echo $row_Recordset2['uname']; ?>"><i
                                                class="fa fa-remove"></i></a></td>
                                </tr>
                                <?php }while($row_Recordset2 = mysqli_fetch_assoc($Recordset2)); } ?>
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
    $('#example').DataTable({
        initComplete: function() {
            this.api().columns().every(function() {
                var column = this;
                var select = $('<select><option value=""></option></select>')
                    .appendTo($(column.footer()).empty())
                    .on('change', function() {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );

                        column
                            .search(val ? '^' + val + '$' : '', true, false)
                            .draw();
                    });

                column.data().unique().sort().each(function(d, j) {
                    select.append('<option value="' + d + '">' + d + '</option>')
                });
            });
        }
    });
});
</script>

</html>