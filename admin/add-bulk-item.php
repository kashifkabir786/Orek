<?php require_once('../Connections/car_spare.php'); ?>
<?php require_once('session.php'); ?>
<?php

$editFormAction = $_SERVER[ 'PHP_SELF' ];
if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
  $editFormAction .= "?" . htmlentities( $_SERVER[ 'QUERY_STRING' ] );
}
if ( ( isset( $_POST[ "MM_insert" ] ) ) && ( $_POST[ "MM_insert" ] == "form1" ) ) {
  $csvMimes = array( 'text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain' );

  if ( !empty( $_FILES[ 'userdata' ][ 'name' ] ) && in_array( $_FILES[ 'userdata' ][ 'type' ], $csvMimes ) ) {

    // If the file is uploaded
    if ( is_uploaded_file( $_FILES[ 'userdata' ][ 'tmp_name' ] ) ) {

      // Open uploaded CSV file with read-only mode
      $csvFile = fopen( $_FILES[ 'userdata' ][ 'tmp_name' ], 'r' );

      // Skip the first line
      fgetcsv( $csvFile );

      // Parse data from CSV file line by line
      while ( ( $line = fgetcsv( $csvFile ) ) !== FALSE ) {
        // Get row data
        $sku_id = $line[ 0 ];
        $item_name = $line[ 1 ];
        $related_table = $line[ 2 ];
        $related_table_id = $line[ 3 ];
        $item_model_no = $line[ 4 ];
        $listing_status = $line[ 5 ];
        $size = $line[ 6 ];
        $ideal_for = $line[ 7 ];
        $country_of_origin = $line[ 8 ];
        $pack_of = $line[ 9 ];
        $ocassion = $line[ 10 ];
        $color = $line[ 11 ];
        $fabric = $line[ 12 ];
        $pattern = $line[ 13 ];
        $local_delivery_charge = $line[ 14 ];
        $zonal_delivery_charge = $line[ 15 ];
        $national_delivery_charge = $line[ 16 ];
        $weight = $line[ 17 ];
        $height = $line[ 18 ];
        $length = $line[ 19 ];
        $breadth = $line[ 20 ];
        $sales_package = $line[ 21 ];
        $fabric_care = $line[ 22 ];
        $price = $line[ 23 ];
        $discount = $line[ 24 ];
        $description = $line[ 25 ];
        $specification = $line[ 26 ];
        // $image_1 = isset($line[26]) ? $line[26] : '';
        // $image_2 = isset($line[27]) ? $line[27] : '';
        // $image_3 = isset($line[28]) ? $line[28] : '';
        $tax = $line[ 27 ];
        $hsn_code = $line[ 28 ];

        $insertSQL = sprintf( "INSERT INTO `item`(`sku_id`, `item_name`, `hsn_code`, `related_table`, `related_table_id`, `item_model_no`, `listing_status`, `size`, `ideal_for`, `country_of_origin`, `pack_of`, `ocassion`, `color`, `fabric`, `pattern`, `local_delivery_charge`, `zonal_delivery_charge`, `national_delivery_charge`, `weight`, `height`, `length`, `breadth`, `sales_package`, `fabric_care`, `price`, `tax`, `discount`, `description`, `specification`) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
          GetSQLValueString( $sku_id, "text" ),
          GetSQLValueString( $item_name, "text" ),
          GetSQLValueString( $hsn_code, "text" ),
          GetSQLValueString( $related_table, "text" ),
          GetSQLValueString( $related_table_id, "text" ),
          GetSQLValueString( $item_model_no, "text" ),
          GetSQLValueString( $listing_status, "text" ),
          GetSQLValueString( $size, "text" ),
          GetSQLValueString( $ideal_for, "text" ),
          GetSQLValueString( $country_of_origin, "text" ),
          GetSQLValueString( $pack_of, "text" ),
          GetSQLValueString( $ocassion, "text" ),
          GetSQLValueString( $color, "text" ),
          GetSQLValueString( $fabric, "text" ),
          GetSQLValueString( $pattern, "text" ),
          GetSQLValueString( $local_delivery_charge, "text" ),
          GetSQLValueString( $zonal_delivery_charge, "text" ),
          GetSQLValueString( $national_delivery_charge, "text" ),
          GetSQLValueString( $weight, "text" ),
          GetSQLValueString( $height, "text" ),
          GetSQLValueString( $length, "text" ),
          GetSQLValueString( $breadth, "text" ),
          GetSQLValueString( $sales_package, "text" ),
          GetSQLValueString( $fabric_care, "text" ),
          GetSQLValueString( $price, "text" ),
          GetSQLValueString( $tax, "text" ),
          GetSQLValueString( $discount, "text" ),
          GetSQLValueString( $description, "text" ),
          GetSQLValueString( $specification, "text" ) );
        $Result = mysqli_query( $car_spare, $insertSQL )or die( mysqli_error( $car_spare ) );
      }

      fclose( $csvFile );
      if ( $Result ) {
        $insertGoTo = "item.php?success=uploaded";
        if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
          $insertGoTo .= ( strpos( $insertGoTo, '?' ) ) ? "&" : "?";
          $insertGoTo .= $_SERVER[ 'QUERY_STRING' ];
        }
        header( sprintf( "Location: %s", $insertGoTo ) );
      }
    }
  }

}

if ( ( isset( $_POST[ "MM_photo" ] ) ) && ( $_POST[ "MM_photo" ] == "form2" ) ) {

  // Create a directory to store uploaded zip files and extracted images
  $uploadDir = "../assets/img/item/";
  $extractedDir = "../assets/img/item/";

  // Process uploaded zip file
  if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {
    //if (isset($_FILES['zipFile']['name']) && $_FILES['zipFile']['type'] === 'application/zip') {
    $zipFileName = $_FILES[ 'zipFile' ][ 'name' ];
    $zipFilePath = $uploadDir . $zipFileName;
    $extractedPath = $extractedDir . pathinfo( $zipFileName, PATHINFO_FILENAME ) . '/';

    // Save the zip file
    move_uploaded_file( $_FILES[ 'zipFile' ][ 'tmp_name' ], $zipFilePath );

    // Create a directory to extract the contents
    if ( !file_exists( $extractedPath ) ) {
      mkdir( $extractedPath, 0777, true );
    }

    // Extract images from the zip file
    $zip = new ZipArchive;
    if ( $zip->open( $zipFilePath ) === TRUE ) {
      $zip->extractTo( $extractedPath );
      $zip->close();

      // Move the extracted images to the desired folder
      $files = glob( $extractedPath . '*.{jpg,jpeg,png,gif}', GLOB_BRACE );
      foreach ( $files as $file ) {
        $newPath = $extractedDir . basename( $file );
        // Get SKU ID from the file name
        $fileNameParts = explode( '_', basename( $file ) );
        if ( count( $fileNameParts ) > 1 ) {
          $sku_id = $fileNameParts[ 0 ];

          $query_Recordset2 = "SELECT image_1, image_2, image_3 FROM item WHERE sku_id = '$sku_id'";
          $Recordset2 = mysqli_query( $car_spare, $query_Recordset2 )or die( mysqli_error( $car_spare ) );
          $row_Recordset2 = mysqli_fetch_assoc( $Recordset2 );
          $totalRows_Recordset2 = mysqli_num_rows( $Recordset2 );

          if ( is_null( $row_Recordset2[ 'image_1' ] ) )
            $image = 'image_1';
          if ( is_null( $row_Recordset2[ 'image_2' ] ) )
            $image = 'image_2';
          if ( is_null( $row_Recordset2[ 'image_3' ] ) )
            $image = 'image_3';

          $updateSQL = sprintf( "UPDATE `item` SET `$image` = %s WHERE `sku_id` = %s",
            GetSQLValueString( basename( $file ), "text" ),
            GetSQLValueString( $sku_id, "text" ) );
          $Result = mysqli_query( $car_spare, $updateSQL )or die( mysqli_error( $car_spare ) );
        }
        rename( $file, $newPath );
        $Photo = $newPath;
      }

      // Clean up: Remove the extracted directory
      rmdir( $extractedPath );

      if ( $Photo ) {
        $insertGoTo = "add-bulk-item.php?success=Image uploaded";
        if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
          $insertGoTo .= ( strpos( $insertGoTo, '?' ) ) ? "&" : "?";
          $insertGoTo .= $_SERVER[ 'QUERY_STRING' ];
        }
        header( sprintf( "Location: %s", $insertGoTo ) );
      }
    } else {
      echo "Failed to open the zip file.";
    }
    // Remove the uploaded zip file
    unlink( $zipFilePath );
    //} else {
    //echo "Please upload a valid zip file.";
    //}
  }
}

if ( ( isset( $_POST[ "MM_insert" ] ) ) && ( $_POST[ "MM_insert" ] == "form3" ) ) {
  $csvMimes = array( 'text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain' );

  if ( !empty( $_FILES[ 'userdata' ][ 'name' ] ) && in_array( $_FILES[ 'userdata' ][ 'type' ], $csvMimes ) ) {

    // If the file is uploaded
    if ( is_uploaded_file( $_FILES[ 'userdata' ][ 'tmp_name' ] ) ) {

      // Open uploaded CSV file with read-only mode
      $csvFile = fopen( $_FILES[ 'userdata' ][ 'tmp_name' ], 'r' );

      // Skip the first line
      fgetcsv( $csvFile );

      // Parse data from CSV file line by line
      while ( ( $line = fgetcsv( $csvFile ) ) !== FALSE ) {
        // Get row data
        $category_id = $line[ 0 ];
        $category_specification = $line[ 1 ];

        $insertSQL = sprintf( "INSERT INTO `category_specification`(`category_id`, `category_specification`) VALUES (%s, %s)",
          GetSQLValueString( $category_id, "text" ),
          GetSQLValueString( $category_specification, "text" ) );
        $Result = mysqli_query( $car_spare, $insertSQL )or die( mysqli_error( $car_spare ) );
      }

      fclose( $csvFile );
      if ( $Result ) {
        $insertGoTo = "item.php?success=uploaded";
        if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
          $insertGoTo .= ( strpos( $insertGoTo, '?' ) ) ? "&" : "?";
          $insertGoTo .= $_SERVER[ 'QUERY_STRING' ];
        }
        header( sprintf( "Location: %s", $insertGoTo ) );
      }
    }
  }

}
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Add Bulk Item</title>
    <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
</head>

<body>
    <div class="left-side height-100">
        <div class="row height-100">
            <div class="col-md-2 bg-green padding-top-bottom height-100 left-menu">
                <div class="row">
                    <div class="padding-10 text-center"><img src="images/logo.png" width="60%">
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
                <h3><strong>ADD BULK ITEM</strong></h3>
                <hr />
                <?php
      if ( isset( $_GET[ 'success' ] ) ) {
          echo '<div class="col-md-12">';
          echo '<div class="alert alert-success">Item ' . $_GET[ 'success' ] . ' Successfully</div>';
          echo '</div>';
      }
      ?>
                <div class="margin-50"> <a href="item.php" class="btn btn-primary">Back</a> <a href="item-mockup.xls"
                        class="btn btn-primary">Mockup Download</a></div>
                <form method="POST" name="form1" id="form1" enctype="multipart/form-data" role="form"
                    action="<?php echo $editFormAction; ?>">
                    <div class="form-group">
                        <label class="control-label margin-50" for="size">Upload Only CSV File:</label>
                        <input type="file" class="form-control" name="userdata" accept=".csv">
                        <button type="submit" id="save" class="btn btn-info margin-20"> Save </button>
                        <input type="hidden" name="MM_insert" value="form1">
                    </div>
                </form>
                <form method="POST" action="<?php echo $editFormAction; ?>" name="form2" role="form"
                    enctype="multipart/form-data">
                    <label for="zipFile">Select Zip File:</label>
                    <input type="file" class="form-control" name="zipFile" id="zipFile" accept=".zip">
                    <br>
                    <button type="submit" class="btn btn-info">Upload</button>
                    <input type="hidden" name="MM_photo" value="form2">
                </form>
                <form method="POST" name="form3" id="form3" enctype="multipart/form-data" role="form"
                    action="<?php echo $editFormAction; ?>">
                    <div class="form-group">
                        <label class="control-label margin-20" for="size">Upload Specification CSV File:</label>
                        <input type="file" class="form-control" name="userdata" accept=".csv">
                        <button type="submit" id="save" class="btn btn-info margin-20"> Save </button>
                        <input type="hidden" name="MM_insert" value="form3">
                    </div>
                </form>
            </div>
        </div>
</body>
<script src="js/jquery-3.2.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="https://cdn.tiny.cloud/1/6em8ymawqsqg4mwyb5rcii6zdp9ta9cov9cqngjrt502i7ns/tinymce/5/tinymce.min.js"
    referrerpolicy="origin"></script>
<script>
$('#heading').change(function() {
    $.ajax({
        url: "select-sub-heading.php",
        type: "POST",
        data: {
            "heading_id": this.value
        },
        success: function(data) {
            //alert(data);
            if (data != "Not Found") {
                $('#sub_heading').html(data);
            }
        }
    });
});

$('#filter_car').keyup(function() {
    $.ajax({
        url: "filter-car-models.php",
        type: "POST",
        data: {
            "cars": this.value
        },
        success: function(data1) {
            //alert(data1);
            if (data1 != "Not Found") {
                $('#filter-models').html(data1);
            }
        }
    });
});

tinymce.init({
    selector: '#mytextarea'
});
</script>

</html>