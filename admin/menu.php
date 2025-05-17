 <!-- ======= Header ======= -->
 <header id="header" class="header fixed-top d-flex align-items-center">

     <div class="d-flex align-items-center justify-content-between">
         <a href="dashboard.php" class="logo d-flex align-items-center">
             <img src="assets/img/logo.png" alt="">
             <!-- <span class="d-none d-lg-block">NiceAdmin</span> -->
         </a>
         <i class="bi bi-list toggle-sidebar-btn"></i>
     </div><!-- End Logo -->

     <div class="search-bar">
         <form class="search-form d-flex align-items-center" method="POST" action="#">
             <input type="text" name="query" placeholder="Search" title="Enter search keyword">
             <button type="submit" title="Search"><i class="bi bi-search"></i></button>
         </form>
     </div><!-- End Search Bar -->

     <nav class="header-nav ms-auto">
         <ul class="d-flex align-items-center">

             <li class="nav-item d-block d-lg-none">
                 <a class="nav-link nav-icon search-bar-toggle " href="#">
                     <i class="bi bi-search"></i>
                 </a>
             </li><!-- End Search Icon-->

             <li class="nav-item dropdown">

                 <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
                     <i class="bi bi-bell"></i>
                     <span class="badge bg-primary badge-number">4</span>
                 </a><!-- End Notification Icon -->

                 <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
                     <li class="dropdown-header">
                         You have 4 new notifications
                         <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
                     </li>
                     <li>
                         <hr class="dropdown-divider">
                     </li>

                     <li class="notification-item">
                         <i class="bi bi-exclamation-circle text-warning"></i>
                         <div>
                             <h4>Lorem Ipsum</h4>
                             <p>Quae dolorem earum veritatis oditseno</p>
                             <p>30 min. ago</p>
                         </div>
                     </li>

                     <li>
                         <hr class="dropdown-divider">
                     </li>

                     <li class="notification-item">
                         <i class="bi bi-x-circle text-danger"></i>
                         <div>
                             <h4>Atque rerum nesciunt</h4>
                             <p>Quae dolorem earum veritatis oditseno</p>
                             <p>1 hr. ago</p>
                         </div>
                     </li>

                     <li>
                         <hr class="dropdown-divider">
                     </li>

                     <li class="notification-item">
                         <i class="bi bi-check-circle text-success"></i>
                         <div>
                             <h4>Sit rerum fuga</h4>
                             <p>Quae dolorem earum veritatis oditseno</p>
                             <p>2 hrs. ago</p>
                         </div>
                     </li>

                     <li>
                         <hr class="dropdown-divider">
                     </li>

                     <li class="notification-item">
                         <i class="bi bi-info-circle text-primary"></i>
                         <div>
                             <h4>Dicta reprehenderit</h4>
                             <p>Quae dolorem earum veritatis oditseno</p>
                             <p>4 hrs. ago</p>
                         </div>
                     </li>

                     <li>
                         <hr class="dropdown-divider">
                     </li>
                     <li class="dropdown-footer">
                         <a href="#">Show all notifications</a>
                     </li>

                 </ul><!-- End Notification Dropdown Items -->

             </li><!-- End Notification Nav -->

             <li class="nav-item dropdown">

                 <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
                     <i class="bi bi-chat-left-text"></i>
                     <span class="badge bg-success badge-number">3</span>
                 </a><!-- End Messages Icon -->

                 <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow messages">
                     <li class="dropdown-header">
                         You have 3 new messages
                         <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
                     </li>
                     <li>
                         <hr class="dropdown-divider">
                     </li>

                     <li class="message-item">
                         <a href="#">
                             <img src="assets/img/messages-1.jpg" alt="" class="rounded-circle">
                             <div>
                                 <h4>Maria Hudson</h4>
                                 <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                                 <p>4 hrs. ago</p>
                             </div>
                         </a>
                     </li>
                     <li>
                         <hr class="dropdown-divider">
                     </li>

                     <li class="message-item">
                         <a href="#">
                             <img src="assets/img/messages-2.jpg" alt="" class="rounded-circle">
                             <div>
                                 <h4>Anna Nelson</h4>
                                 <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                                 <p>6 hrs. ago</p>
                             </div>
                         </a>
                     </li>
                     <li>
                         <hr class="dropdown-divider">
                     </li>

                     <li class="message-item">
                         <a href="#">
                             <img src="assets/img/messages-3.jpg" alt="" class="rounded-circle">
                             <div>
                                 <h4>David Muldon</h4>
                                 <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                                 <p>8 hrs. ago</p>
                             </div>
                         </a>
                     </li>
                     <li>
                         <hr class="dropdown-divider">
                     </li>

                     <li class="dropdown-footer">
                         <a href="#">Show all messages</a>
                     </li>

                 </ul><!-- End Messages Dropdown Items -->

             </li><!-- End Messages Nav -->

             <li class="nav-item dropdown pe-3">

                 <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                     <img src="assets/img/profile-img.jpg" alt="Profile" class="rounded-circle">
                     <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo $row_Recordset1['fname']; ?></span>
                 </a><!-- End Profile Iamge Icon -->

                 <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                     <li class="dropdown-header">
                         <h6><?php echo $row_Recordset1['fname']; ?></h6>
                         <span><?php echo $row_Recordset1['role']; ?></span>
                     </li>
                     <li>
                         <hr class="dropdown-divider">
                     </li>

                     <li>
                         <a class="dropdown-item d-flex align-items-center" href="change-password.php">
                             <i class="bi bi-key"></i>
                             <span>Change Password</span>
                         </a>
                     </li>

                     <li>
                         <hr class="dropdown-divider">
                     </li>

                     <li>
                         <a class="dropdown-item d-flex align-items-center" href="logout.php">
                             <i class="bi bi-box-arrow-right"></i>
                             <span>Sign Out</span>
                         </a>
                     </li>

                 </ul><!-- End Profile Dropdown Items -->
             </li><!-- End Profile Nav -->

         </ul>
     </nav><!-- End Icons Navigation -->

 </header><!-- End Header -->

 <!-- ======= Sidebar ======= -->
 <aside id="sidebar" class="sidebar">

     <ul class="sidebar-nav" id="sidebar-nav">

         <li class="nav-item">
             <a class="nav-link " href="dashboard.php">
                 <i class="bi bi-grid"></i>
                 <span>Dashboard</span>
             </a>
         </li><!-- End Dashboard Nav -->

         <li class="nav-item">
             <a class="nav-link collapsed" href="category.php">
                 <i class="bi bi-folder"></i>
                 <span>Category</span>
             </a>
         </li>
         <li class="nav-item">
             <a class="nav-link collapsed" href="category_level1.php">
                 <i class="bi bi-folder-plus"></i>
                 <span>Category Level 1</span>
             </a>
         </li>
         <li class="nav-item">
             <a class="nav-link collapsed" href="category_level2.php">
                 <i class="bi bi-folder-symlink"></i>
                 <span>Category Level 2</span>
             </a>
         </li>
         <li class="nav-item">
             <a class="nav-link collapsed" href="item.php">
                 <i class="bi bi-box"></i>
                 <span>Item</span>
             </a>
         </li>
         <li class="nav-item">
             <a class="nav-link collapsed" href="ticket.php">
                 <i class="bi bi-ticket-detailed"></i>
                 <span>Tickets</span>
             </a>
         </li>
         <li class="nav-item">
             <a class="nav-link collapsed" href="blog.php">
                 <i class="bi bi-file-richtext"></i>
                 <span>Blogs</span>
             </a>
         </li>
         <li class="nav-item">
             <a class="nav-link collapsed" href="user.php">
                 <i class="bi bi-people"></i>
                 <span>User</span>
             </a>
         </li>
         <li class="nav-item">
             <a class="nav-link collapsed" href="cart.php">
                 <i class="bi bi-cart"></i>
                 <span>Cart</span>
             </a>
         </li>
         <li class="nav-item">
             <a class="nav-link collapsed" href="order.php">
                 <i class="bi bi-receipt"></i>
                 <span>Order</span>
             </a>
         </li>

         <li class="nav-item">
             <a class="nav-link collapsed" href="payment.php">
                 <i class="bi bi-credit-card"></i>
                 <span>Payment</span>
             </a>
         </li>
         <li class="nav-item">
             <a class="nav-link collapsed" href="shipping.php">
                 <i class="bi bi-truck"></i>
                 <span>Shipping</span>
             </a>
         </li>
         <li class="nav-item">
             <a class="nav-link collapsed" href="banner.php">
                 <i class="bi bi-image"></i>
                 <span>Banner</span>
             </a>
         </li>
         <li class="nav-item">
             <a class="nav-link collapsed" href="offer.php">
                 <i class="bi bi-tags"></i>
                 <span>Offer</span>
             </a>
         </li>
         <li class="nav-item">
             <a class="nav-link collapsed" href="coupon.php">
                 <i class="bi bi-ticket"></i>
                 <span>Coupon</span>
             </a>
         </li>
         <li class="nav-item">
             <a class="nav-link collapsed" href="notification.php">
                 <i class="bi bi-bell"></i>
                 <span>Notification</span>
             </a>
         </li>
         <li class="nav-item">
             <a class="nav-link collapsed" href="grn.php">
                 <i class="bi bi-file-earmark-check"></i>
                 <span>GRN</span>
             </a>
         </li>
         <li class="nav-item">
             <a class="nav-link collapsed" href="report-sales.php">
                 <i class="bi bi-graph-up"></i>
                 <span>Sales Report</span>
             </a>
         </li>
         <li class="nav-item">
             <a class="nav-link collapsed" href="report-purchase.php">
                 <i class="bi bi-cart-check"></i>
                 <span>Purchase Report</span>
             </a>
         </li>

     </ul>

 </aside><!-- End Sidebar-->