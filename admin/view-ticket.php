<?php 
require_once('../Connections/orek.php');
require_once('session.php');

$ticket_id = mysqli_real_escape_string($orek, $_GET['id']);

// Get ticket details
$query_Ticket = "SELECT t.*, u.fname, u.lname, u.email 
                 FROM tickets t 
                 JOIN user u ON t.user_id = u.user_id 
                 WHERE t.ticket_id = '$ticket_id'";
$Ticket = mysqli_query($orek, $query_Ticket) or die(mysqli_error($orek));
$row_Ticket = mysqli_fetch_assoc($Ticket);

// Get all replies
$query_Replies = "SELECT r.*, u.fname, u.lname 
                 FROM ticket_replies r
                 JOIN user u ON r.user_id = u.user_id
                 WHERE r.ticket_id = '$ticket_id'
                 ORDER BY r.created_at ASC";
$Replies = mysqli_query($orek, $query_Replies) or die(mysqli_error($orek));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />

    <title>View Tickets - Orek</title>
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

    <!-- =======================================================
  * Template Name: NiceAdmin
  * Updated: Mar 09 2023 with Bootstrap v5.2.3
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>
    <!-- ======= Header ======= -->
    <?php require_once('menu.php'); ?>

    <!-- End Sidebar-->
    <main id="main" class="main">
        <div class="pagetitle">
            <h1>View Ticket #<?php echo $ticket_id; ?></h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="ticket.php">Tickets</a></li>
                    <li class="breadcrumb-item active">View Ticket</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <?php if(isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php 
                        echo $_SESSION['success_message'];
                        unset($_SESSION['success_message']);
                    ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>

            <?php if(isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php 
                        echo $_SESSION['error_message'];
                        unset($_SESSION['error_message']);
                    ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="ticket-header">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="card-title"><?php echo htmlspecialchars($row_Ticket['subject']); ?></h5>
                                    <div class="ticket-status">
                                        <form action="update-ticket-status.php" method="post" class="d-inline">
                                            <input type="hidden" name="ticket_id" value="<?php echo $ticket_id; ?>">
                                            <select name="status" class="form-select form-select-sm"
                                                onchange="this.form.submit()">
                                                <option value="open"
                                                    <?php echo $row_Ticket['status'] == 'open' ? 'selected' : ''; ?>>
                                                    Open
                                                </option>
                                                <option value="in_progress"
                                                    <?php echo $row_Ticket['status'] == 'in_progress' ? 'selected' : ''; ?>>
                                                    In Progress</option>
                                                <option value="closed"
                                                    <?php echo $row_Ticket['status'] == 'closed' ? 'selected' : ''; ?>>
                                                    Closed</option>
                                            </select>
                                        </form>
                                    </div>
                                </div>
                                <div class="ticket-meta">
                                    <!-- <small>Created by: <?php echo $row_Ticket['fname'] . ' ' . $row_Ticket['lname']; ?>
                                        (<?php echo $row_Ticket['email']; ?>)</small><br> -->
                                    <small>Date:
                                        <?php echo date('d M Y H:i', strtotime($row_Ticket['created_at'])); ?></small>
                                </div>
                            </div>

                            <div class="ticket-chat-container">
                                <div class="ticket-conversation" id="chat-box">
                                    <!-- Original ticket message -->
                                    <div class="chat-bubble user">
                                        <div class="message-content">
                                            <?php echo nl2br(htmlspecialchars($row_Ticket['message'])); ?>
                                        </div>
                                        <div class="message-meta">
                                            <small>
                                                <?php echo $row_Ticket['fname'] . ' ' . $row_Ticket['lname']; ?> •
                                                <?php echo date('d M Y h:i A', strtotime($row_Ticket['created_at'])); ?>
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Replies -->
                                    <?php while($reply = mysqli_fetch_assoc($Replies)) {
                                    $is_admin = $reply['is_admin'] ?? 0;
                                    $bubble_class = $is_admin ? 'admin' : 'user';
                                    ?>
                                    <div class="chat-bubble <?php echo $bubble_class; ?>">
                                        <div class="message-content">
                                            <?php echo nl2br(htmlspecialchars($reply['message'])); ?>
                                        </div>
                                        <div class="message-meta">
                                            <small>
                                                <!-- <?php echo $reply['fname'] . ' ' . $reply['lname']; ?> • -->
                                                <?php echo date('d M Y h:i A', strtotime($reply['created_at'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>

                                <!-- Reply form -->
                                <form action="submit-reply.php" method="post" class="chat-reply-form">
                                    <input type="hidden" name="ticket_id" value="<?php echo $ticket_id; ?>">
                                    <input type="text" name="message" placeholder="Type a message" required>
                                    <button type="submit">Send</button>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- ======= Footer ======= -->
    <footer id="footer" class="footer">
        <div class="copyright">
            &copy; Copyright <strong><span>Orek</span></strong>. All Rights Reserved
        </div>
        <div class="credits">
            <!-- All the links in the footer should remain intact. -->
            <!-- You can delete the links only if you purchased the pro version. -->
            <!-- Licensing information: https://bootstrapmade.com/license/ -->
            <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
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
    <script>
    const chatBox = document.getElementById('chat-box');
    if (chatBox) {
        chatBox.scrollTop = chatBox.scrollHeight;
    }
    </script>
</body>

</html>