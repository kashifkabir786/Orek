<?php require_once('../Connections/car_spare.php'); ?>
<?php require_once('session.php'); ?>
<?php
$query_Recordset2 = "SELECT A.*, B.category_name, (SELECT GROUP_CONCAT(D.brand_name) FROM `heading_brand` AS C INNER JOIN brand AS D ON C.brand_id = D.brand_id WHERE C.heading_id=A.heading_id) AS all_brands FROM heading AS A LEFT JOIN category AS B ON A.category_id = B.category_id";
$Recordset2 = mysqli_query( $car_spare, $query_Recordset2 )or die( mysqli_error( $car_spare ) );
$row_Recordset2 = mysqli_fetch_assoc( $Recordset2 );
$totalRows_Recordset2 = mysqli_num_rows( $Recordset2 );
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Heading</title>
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
                <h3><strong>HEADING HOMEPAGE</strong></h3>
                <hr />
                <div class="margin-50"> <a href="add-heading.php" class="btn btn-primary">Add New Heading</a> <a
                        href="download-excel.php?type=heading" class="btn btn-primary">Download Master Excel</a>
                    <div class="bg-white table-responsive margin-50">
                        <table class="table table-striped" id="example">
                            <thead>
                                <tr>
                                    <th>Heading Id</th>
                                    <th>Heading Name</th>
                                    <th>Category Name</th>
                                    <th>Brand Name</th>
                                    <th>Image</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php do { ?>
                                <tr>
                                    <td><?php echo $row_Recordset2['heading_id'] ?></td>
                                    <td><?php echo $row_Recordset2['heading_name'] ?></td>
                                    <td><?php echo $row_Recordset2['category_name'] ?></td>
                                    <td><?php echo $row_Recordset2['all_brands'] ?></td>
                                    <td><a href="../assets/img/heading/<?php echo $row_Recordset2['image_1'] ?>"
                                            target="_blank"><img
                                                src="../assets/img/heading/<?php echo $row_Recordset2['image_1'] ?>"
                                                width="50"></a></td>
                                    <td><a
                                            href="add-heading.php?heading_id=<?php echo $row_Recordset2['heading_id']; ?>"><i
                                                class="fa fa-edit"></i></a><a
                                            href="delete-heading.php?heading_id=<?php echo $row_Recordset2['heading_id']; ?>"><i
                                                class="fa fa-remove"></i></a></td>
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