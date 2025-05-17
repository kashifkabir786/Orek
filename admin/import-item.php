<?php require_once('../Connections/orek.php'); ?>
<?php require_once('session.php'); ?>
<?php
$message = '';
$error = '';

// Process the uploaded CSV file
if (isset($_POST['submit'])) {
    // Check if a file was uploaded without errors
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
        $file_name = $_FILES['csv_file']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Check if the file is a CSV
        if ($file_ext == 'csv') {
            // Open the file for reading
            if (($handle = fopen($_FILES['csv_file']['tmp_name'], "r")) !== FALSE) {
                // Skip the header row
                $header = fgetcsv($handle, 1000, ",");
                
                $imported_count = 0;
                $skipped_count = 0;
                
                // Start a transaction
                mysqli_begin_transaction($orek);
                
                try {
                    // Process each row
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        // Prepare data (ignore item_id and date_added fields)
                        // Skip first column (item_id)
                        $item_name = mysqli_real_escape_string($orek, $data[1] ?? '');
                        $category_id = mysqli_real_escape_string($orek, $data[2] ?? '');
                        $category_level1_id = mysqli_real_escape_string($orek, $data[3] ?? '');
                        $category_level2_id = mysqli_real_escape_string($orek, $data[4] ?? 'NULL');
                        $listing_status = mysqli_real_escape_string($orek, $data[5] ?? '');
                        $size = mysqli_real_escape_string($orek, $data[6] ?? 'NULL');
                        $mood = mysqli_real_escape_string($orek, $data[7] ?? 'NULL');
                        $ocassion = mysqli_real_escape_string($orek, $data[8] ?? '');
                        $local_delivery_charge = mysqli_real_escape_string($orek, $data[9] ?? 'NULL');
                        $price = mysqli_real_escape_string($orek, $data[10] ?? '0');
                        $discount = mysqli_real_escape_string($orek, $data[11] ?? '0');
                        $description = mysqli_real_escape_string($orek, $data[12] ?? 'NULL');
                        
                        // Set default "NA" for empty image fields
                        $image_1 = mysqli_real_escape_string($orek, empty($data[13]) ? 'NA' : $data[13]);
                        $image_2 = mysqli_real_escape_string($orek, $data[14] ?? 'NULL');
                        $image_3 = mysqli_real_escape_string($orek, $data[15] ?? 'NULL');
                        $image_4 = mysqli_real_escape_string($orek, $data[16] ?? 'NULL');
                        $image_5 = mysqli_real_escape_string($orek, $data[17] ?? 'NULL');
                        
                        $tax = mysqli_real_escape_string($orek, $data[18] ?? 'NULL');
                        $hsn_code = mysqli_real_escape_string($orek, $data[19] ?? 'NULL');
                        $position = mysqli_real_escape_string($orek, $data[20] ?? 'NULL');
                        $stock_alert = mysqli_real_escape_string($orek, $data[21] ?? '5');
                        $min_stock_alert = empty($data[22]) ? "NULL" : mysqli_real_escape_string($orek, $data[22]);
                        
                        // Skip rows with empty required fields
                        if (empty($item_name) || empty($category_id) || empty($category_level1_id) || 
                            empty($listing_status) || empty($ocassion) || empty($price)) {
                            $skipped_count++;
                            continue;
                        }
                        
                        // Handle NULL values for numeric fields that can't be empty
                        $category_level2_id = (empty($data[4]) || $category_level2_id === 'NULL') ? "NULL" : intval($category_level2_id);
                        $size = ($size === 'NULL') ? "NULL" : "'$size'";
                        $mood = ($mood === 'NULL') ? "NULL" : "'$mood'";
                        $local_delivery_charge = empty($data[9]) ? "NULL" : intval($local_delivery_charge);
                        $description = ($description === 'NULL') ? "NULL" : "'$description'";
                        
                        // image_1 has 'NA' for empty values, add quotes
                        $image_1 = "'$image_1'";
                        $image_2 = ($image_2 === 'NULL') ? "NULL" : "'$image_2'";
                        $image_3 = ($image_3 === 'NULL') ? "NULL" : "'$image_3'";
                        $image_4 = ($image_4 === 'NULL') ? "NULL" : "'$image_4'";
                        $image_5 = ($image_5 === 'NULL') ? "NULL" : "'$image_5'";
                        
                        $tax = empty($data[18]) ? "NULL" : "'$tax'";
                        $hsn_code = ($hsn_code === 'NULL') ? "NULL" : "'$hsn_code'";
                        $position = ($position === 'NULL') ? "NULL" : "'$position'";
                        $stock_alert = empty($data[21]) ? 5 : intval($stock_alert);
                        $min_stock_alert = empty($data[22]) ? "NULL" : intval($min_stock_alert);
                        
                        // Insert the item
                        $query = "INSERT INTO item (
                            item_name, category_id, category_level1_id, category_level2_id, 
                            listing_status, size, mood, ocassion, local_delivery_charge, 
                            price, discount, description, image_1, image_2, image_3, image_4, image_5, 
                            tax, hsn_code, position, stock_alert, min_stock_alert
                        ) VALUES (
                            '$item_name', $category_id, $category_level1_id, $category_level2_id, 
                            '$listing_status', $size, $mood, '$ocassion', $local_delivery_charge, 
                            $price, $discount, $description, $image_1, $image_2, $image_3, $image_4, $image_5, 
                            $tax, $hsn_code, $position, $stock_alert, $min_stock_alert
                        )";
                        
                        if (mysqli_query($orek, $query)) {
                            $imported_count++;
                        } else {
                            $skipped_count++;
                        }
                    }
                    
                    // Commit the transaction
                    mysqli_commit($orek);
                    $message = "Data Import Success: $imported_count items imported, $skipped_count items skipped.";
                } catch (Exception $e) {
                    // Rollback the transaction if something went wrong
                    mysqli_rollback($orek);
                    $error = "Data Import Error: " . $e->getMessage();
                }
                
                fclose($handle);
            } else {
                $error = "File opening failed.";
            }
        } else {
            $error = "Please upload a CSV file.";
        }
    } else {
        $error = "Please select a file to upload.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />

    <title>Import Items - Orek</title>
    <meta content="" name="description" />
    <meta content="" name="keywords" />

    <!-- Favicons -->
    <link href="assets/img/logo.png" rel="icon" />
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon" />

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect" />
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.materialdesignicons.com/5.4.55/css/materialdesignicons.min.css">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet" />
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet" />
    <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet" />
    <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet" />
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet" />
    <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet" />

    <!-- Template Main CSS File -->
    <link href="assets/css/style.css" rel="stylesheet" />
</head>

<body>
    <!-- ======= Header ======= -->
    <?php require_once('menu.php'); ?>

    <!-- End Sidebar-->

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Item Import</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="item.php">Item</a></li>
                    <li class="breadcrumb-item active">Item Import</li>
                </ol>
            </nav>
        </div>
        <!-- End Page Title -->

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Import Items from CSV</h5>

                            <?php if (!empty($message)): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php echo $message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php endif; ?>

                            <div class="mb-4">
                                <h6>CSV File Format:</h6>
                                <p>Your CSV file should have the following columns (first row is header):</p>
                                <ol>
                                    <li>item_id (Ignore - will be auto-generated)</li>
                                    <li>item_name (Required)</li>
                                    <li>category_id (Required)</li>
                                    <li>category_level1_id (Required)</li>
                                    <li>category_level2_id</li>
                                    <li>listing_status (Required)</li>
                                    <li>size</li>
                                    <li>mood</li>
                                    <li>ocassion (Required)</li>
                                    <li>local_delivery_charge</li>
                                    <li>price (Required)</li>
                                    <li>discount (Required)</li>
                                    <li>description</li>
                                    <li>image_1 (Required)</li>
                                    <li>image_2</li>
                                    <li>image_3</li>
                                    <li>image_4</li>
                                    <li>image_5</li>
                                    <li>tax</li>
                                    <li>hsn_code</li>
                                    <li>position</li>
                                    <li>stock_alert</li>
                                    <li>min_stock_alert</li>
                                </ol>
                                <p class="text-muted">Note: item_id and date_added fields will be handled automatically by the system.</p>
                            </div>

                            <form method="post" enctype="multipart/form-data">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="csv_file" class="form-label">Select CSV File</label>
                                        <input class="form-control" type="file" id="csv_file" name="csv_file" accept=".csv" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <button type="submit" name="submit" class="btn btn-primary">Upload and Import</button>
                                        <a href="item.php" class="btn btn-secondary">Cancel</a>
                                    </div>
                                </div>
                            </form>

                            <div class="mt-4">
                                <h6>CSV Format Example:</h6>
                                <pre class="bg-light p-3">item_id,item_name,category_id,category_level1_id,category_level2_id,listing_status,size,mood,ocassion,local_delivery_charge,price,discount,description,image_1,image_2,image_3,image_4,image_5,tax,hsn_code,position,stock_alert,min_stock_alert
0,Flower Bouquet,1,2,NULL,active,NULL,Happy,Marriage,NULL,500,10,Beautiful Flower Bouquet,flower1.jpg,NULL,NULL,NULL,NULL,NULL,NULL,NULL,5,NULL
0,Chocolate Box,2,3,4,active,medium,Romantic,Birthday,50,300,5,Delicious Chocolate Box,chocolate.jpg,choc2.jpg,NULL,NULL,NULL,5,12345,NULL,10,5</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <!-- End #main -->

    <!-- ======= Footer ======= -->
    <footer id="footer" class="footer">
        <div class="copyright">
            &copy; Copyright <strong><span>Orek</span></strong>. All Rights Reserved
        </div>
        <div class="credits">
            Designed by <a href="https://xwaydesigns.com/website-application.html">X Way Design</a>
        </div>
    </footer><!-- End Footer -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/chart.js/chart.umd.js"></script>
    <script src="assets/vendor/echarts/echarts.min.js"></script>
    <script src="assets/vendor/quill/quill.min.js"></script>
    <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>

    <!-- Template Main JS File -->
    <script src="assets/js/main.js"></script>
</body>

</html> 